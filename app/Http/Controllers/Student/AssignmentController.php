<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\AssignmentFileStorage;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    private const MAX_SUBMISSION_ATTACHMENTS = 10;

    private const MAX_SUBMISSION_FILE_KB = 40960;

    /** @var array<string, string> */
    private const ALLOWED_ASSIGNMENT_MIMES = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'application/vnd.rar' => 'rar',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    /**
     * قائمة الواجبات المنشورة لكورسات الطالب النشطة.
     */
    public function index()
    {
        $user = Auth::user();
        $courseIds = $user->activeCourses()->pluck('advanced_courses.id');
        if ($courseIds->isEmpty()) {
            return view('student.assignments.index', ['assignments' => collect()]);
        }

        $assignments = Assignment::query()
            ->where('status', 'published')
            ->where(function ($q) use ($courseIds) {
                $q->whereIn('advanced_course_id', $courseIds)
                    ->orWhereIn('course_id', $courseIds);
            })
            ->with(['course', 'lesson'])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date', 'asc')
            ->orderByDesc('created_at')
            ->get();

        $submissions = AssignmentSubmission::query()
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->where('student_id', $user->id)
            ->get()
            ->keyBy('assignment_id');

        $assignments->each(function ($assignment) use ($submissions) {
            $assignment->my_submission = $submissions->get($assignment->id);
        });

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * تفاصيل الواجب وتسليم الطالب.
     */
    public function show(Assignment $assignment)
    {
        $user = Auth::user();

        if (! $this->assignmentAllowsStudentAccess($assignment, $user)) {
            abort(403, __('غير مصرح لك بالوصول لهذا الواجب'));
        }

        $assignment->load(['course', 'lesson', 'teacher']);

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();

        $canSubmit = true;
        $submitBlockReason = null;

        if (! $this->submissionDeadlineOpen($assignment)) {
            $canSubmit = false;
            $submitBlockReason = 'انتهى موعد التسليم لهذا الواجب.';
        }

        if ($submission && $submission->status === 'graded') {
            $canSubmit = false;
            $submitBlockReason = 'تم تقييم التسليم ولا يمكن تعديله.';
        }

        if ($submission && $submission->status === 'submitted') {
            $canSubmit = false;
            $submitBlockReason = 'تم تسليم الواجب مرة واحدة. يمكنك حذف التسليم قبل انتهاء الموعد لإرسال نسخة جديدة، أو انتظار إرجاعه للتعديل من المُدرِّس.';
        }

        $canDeleteSubmission = (bool) $submission
            && $submission->status !== 'graded'
            && $this->submissionDeadlineOpen($assignment);

        $assignmentDisk = AssignmentFileStorage::resolvedDisk();
        $directUploadToCloud = in_array($assignmentDisk, ['r2', 's3'], true)
            && $this->submissionDiskProvidesDirectUpload($assignmentDisk);

        return view('student.assignments.show', compact(
            'assignment',
            'submission',
            'canSubmit',
            'submitBlockReason',
            'canDeleteSubmission',
            'directUploadToCloud'
        ));
    }

    /**
     * رفع مباشر من المتصفح إلى Cloudflare R2 / S3 (روابط موقّعة).
     */
    public function presignSubmissionUpload(Request $request, Assignment $assignment)
    {
        @set_time_limit(120);
        $user = Auth::user();
        if (! $this->assignmentAllowsStudentAccess($assignment, $user)) {
            abort(403);
        }
        if (! $this->submissionDeadlineOpen($assignment)) {
            return response()->json(['message' => __('انتهى موعد التسليم.')], 422);
        }
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();
        if ($submission && $submission->status === 'submitted') {
            return response()->json(['message' => __('تم التسليم مسبقاً. احذف التسليم أو انتظر إرجاعه للتعديل.')], 422);
        }
        if ($submission && $submission->status === 'graded') {
            return response()->json(['message' => __('تم تقييم التسليم.')], 422);
        }

        $diskName = AssignmentFileStorage::resolvedDisk();
        if (! in_array($diskName, ['r2', 's3'], true) || ! $this->submissionDiskProvidesDirectUpload($diskName)) {
            return response()->json([
                'direct_upload' => false,
                'message' => __('التخزين الحالي لا يدعم الرفع المباشر؛ استخدم رفع الملفات من النموذج.'),
            ]);
        }

        $validated = $request->validate([
            'content_type' => ['nullable', 'string', 'max:191'],
            'original_name' => ['nullable', 'string', 'max:255'],
        ]);

        $mime = $this->normalizeAssignmentSubmissionMime(
            (string) ($validated['content_type'] ?? ''),
            isset($validated['original_name']) ? (string) $validated['original_name'] : null
        );
        $ext = $this->mimeToAssignmentExt($mime);
        $disk = Storage::disk($diskName);

        $subDir = trim(AssignmentFileStorage::DIRECTORY_SUBMISSIONS, '/').'/'.$assignment->id.'/'.$user->id;
        $fileName = Str::uuid()->toString().'.'.$ext;
        $newPath = $subDir.'/'.$fileName;

        $uploadToken = Str::random(64);
        Cache::put(
            'assignment_sub_presign:'.$uploadToken,
            [
                'path' => $newPath,
                'assignment_id' => (int) $assignment->id,
                'user_id' => (int) $user->id,
                'mime' => $mime,
                'disk' => $diskName,
            ],
            now()->addMinutes(75)
        );

        try {
            $signed = $disk->temporaryUploadUrl(
                $newPath,
                now()->addMinutes(70),
                [
                    'ContentType' => $mime,
                ]
            );
        } catch (\Throwable $e) {
            Cache::forget('assignment_sub_presign:'.$uploadToken);
            \Log::error('Assignment submission presign failed', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'direct_upload' => false,
                'message' => __('تعذر تجهيز رابط الرفع. تحقق من إعدادات التخزين أو جرّب الرفع من النموذج.'),
            ], 503);
        }

        return response()->json([
            'direct_upload' => true,
            'upload_url' => $signed['url'],
            'upload_token' => $uploadToken,
            'content_type' => $mime,
            'headers' => $signed['headers'] ?? [],
        ]);
    }

    /**
     * بعد PUT الناجح: تخزين مؤقت لربط الملف بالتسليم عند الإرسال النهائي.
     */
    public function completeSubmissionDirectUpload(Request $request, Assignment $assignment)
    {
        @set_time_limit(120);
        $user = Auth::user();
        if (! $this->assignmentAllowsStudentAccess($assignment, $user)) {
            abort(403);
        }

        $validated = $request->validate([
            'upload_token' => ['required', 'string', 'size:64'],
            'original_name' => ['required', 'string', 'max:255'],
        ]);

        $cacheKey = 'assignment_sub_presign:'.$validated['upload_token'];
        $payload = Cache::pull($cacheKey);
        if (! is_array($payload)
            || (int) ($payload['assignment_id'] ?? 0) !== (int) $assignment->id
            || (int) ($payload['user_id'] ?? 0) !== (int) $user->id) {
            return response()->json([
                'message' => __('انتهت صلاحية الرفع أو أنه غير صالح. أعد المحاولة.'),
            ], 422);
        }

        $path = (string) ($payload['path'] ?? '');
        $mime = (string) ($payload['mime'] ?? '');
        $diskName = (string) ($payload['disk'] ?? AssignmentFileStorage::resolvedDisk());
        if ($path === '' || str_contains($path, '..') || ! in_array($diskName, ['r2', 's3'], true)) {
            return response()->json(['message' => __('مسار التخزين غير صالح.')], 422);
        }

        $disk = Storage::disk($diskName);
        if (! $disk->exists($path)) {
            return response()->json([
                'message' => __('الملف غير ظاهر بعد على التخزين. انتظر قليلاً ثم أعد تأكيد الرفع.'),
            ], 422);
        }

        $size = (int) $disk->size($path);
        $maxBytes = self::MAX_SUBMISSION_FILE_KB * 1024;
        if ($size <= 0) {
            try {
                $disk->delete($path);
            } catch (\Throwable) {
            }

            return response()->json(['message' => __('الملف فارغ.')], 422);
        }
        if ($size > $maxBytes) {
            try {
                $disk->delete($path);
            } catch (\Throwable) {
            }

            return response()->json(['message' => __('حجم الملف يتجاوز الحد المسموح (٤٠ ميجابايت).')], 422);
        }

        $originalName = basename(str_replace(['\\', "\0"], '', $validated['original_name']));
        if ($originalName === '') {
            $originalName = 'file.'.$this->mimeToAssignmentExt($mime);
        }

        $fileToken = Str::random(64);
        Cache::put(
            'assignment_sub_commit:'.$fileToken,
            [
                'path' => $path,
                'original_name' => $originalName,
                'mime' => $mime,
                'assignment_id' => (int) $assignment->id,
                'user_id' => (int) $user->id,
                'disk' => $diskName,
                'size' => $size,
            ],
            now()->addHours(2)
        );

        return response()->json([
            'message' => __('تم تأكيد الملف.'),
            'file_token' => $fileToken,
            'original_name' => $originalName,
            'size' => $size,
        ]);
    }

    /**
     * إلغاء ملف جرى رفعه مباشرة ولم يُرسَل بعد ضمن التسليم.
     */
    public function abandonSubmissionDirectUpload(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (! $this->assignmentAllowsStudentAccess($assignment, $user)) {
            abort(403);
        }

        $validated = $request->validate([
            'file_token' => ['required', 'string', 'size:64'],
        ]);

        $payload = Cache::pull('assignment_sub_commit:'.$validated['file_token']);
        if (! is_array($payload)
            || (int) ($payload['assignment_id'] ?? 0) !== (int) $assignment->id
            || (int) ($payload['user_id'] ?? 0) !== (int) $user->id) {
            return response()->json(['message' => __('غير موجود أو منتهٍ.')], 422);
        }

        $path = (string) ($payload['path'] ?? '');
        $diskName = (string) ($payload['disk'] ?? '');
        if ($path !== '' && in_array($diskName, ['r2', 's3'], true)) {
            try {
                Storage::disk($diskName)->delete($path);
            } catch (\Throwable) {
            }
        }

        return response()->json(['message' => __('تم حذف الملف.')]);
    }

    /**
     * حذف التسليم بالكامل (قبل انتهاء الموعد وبشرط ألا يكون مُقيَّماً).
     */
    public function destroySubmission(Assignment $assignment)
    {
        $user = Auth::user();
        if (! $this->assignmentAllowsStudentAccess($assignment, $user)) {
            abort(403);
        }
        if (! $this->submissionDeadlineOpen($assignment)) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', __('انتهى موعد التسليم ولا يمكن حذف التسليم.'));
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();

        if (! $submission) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', __('لا يوجد تسليم لحذفه.'));
        }

        if ($submission->status === 'graded') {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', __('لا يمكن حذف تسليم تم تقييمه.'));
        }

        AssignmentFileStorage::deleteMany(is_array($submission->attachments) ? $submission->attachments : []);
        $submission->delete();

        return redirect()
            ->route('student.assignments.show', $assignment)
            ->with('success', __('تم حذف التسليم. يمكنك إرسال تسليم جديد مرة واحدة.'));
    }

    /**
     * إرسال التسليم (مرة واحدة ما دام الحالة «مُرسَل»؛ يُسمح بالتعديل عند «مُعاد للتعديل»).
     */
    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        $courseId = $assignment->advanced_course_id ?? $assignment->course_id;

        if (! $courseId || ! $user->isEnrolledIn($courseId)) {
            abort(403);
        }

        if ($assignment->status !== 'published') {
            abort(404);
        }

        if (! $this->submissionDeadlineOpen($assignment)) {
            return back()->with('error', __('انتهى موعد التسليم لهذا الواجب.'));
        }

        $submission = AssignmentSubmission::firstOrNew([
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
        ]);

        if ($submission->exists && $submission->status === 'graded') {
            return back()->with('error', __('تم تقييم التسليم ولا يمكن تعديله.'));
        }

        if ($submission->exists && $submission->status === 'submitted') {
            return back()->with('error', __('تم تسليم الواجب مسبقاً. احذف التسليم قبل انتهاء الموعد لإعادة الإرسال مرة واحدة، أو انتظر إرجاعه من المُدرِّس.'));
        }

        $validated = $request->validate([
            'content' => 'nullable|string|max:65535',
            'attachments' => 'nullable|array|max:'.self::MAX_SUBMISSION_ATTACHMENTS,
            'attachments.*' => 'file|max:'.self::MAX_SUBMISSION_FILE_KB.'|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png,gif,webp',
            'direct_file_tokens' => 'nullable|array|max:'.self::MAX_SUBMISSION_ATTACHMENTS,
            'direct_file_tokens.*' => 'string|size:64',
        ], [
            'attachments.max' => 'لا يمكن رفع أكثر من '.self::MAX_SUBMISSION_ATTACHMENTS.' ملفات.',
            'attachments.*.max' => 'حجم الملف كبير جداً (الحد 40 ميجابايت لكل ملف).',
            'attachments.*.mimes' => 'نوع الملف غير مسموح.',
        ]);

        $existingAttachments = [];
        if ($submission->exists && $submission->status === 'returned') {
            $existingAttachments = is_array($submission->attachments) ? $submission->attachments : [];
        }

        $merged = $existingAttachments;

        $tokens = array_values(array_unique(array_filter($validated['direct_file_tokens'] ?? [])));
        foreach ($tokens as $tok) {
            $payload = Cache::pull('assignment_sub_commit:'.$tok);
            if (! is_array($payload)
                || (int) ($payload['assignment_id'] ?? 0) !== (int) $assignment->id
                || (int) ($payload['user_id'] ?? 0) !== (int) $user->id) {
                return back()->with('error', __('أحد الملفات المرفوعة مباشرة منتهٍ أو غير صالح. أعد اختيار الملفات.'))->withInput();
            }
            $path = (string) ($payload['path'] ?? '');
            $diskName = (string) ($payload['disk'] ?? '');
            if ($path === '' || ! in_array($diskName, ['r2', 's3'], true)) {
                return back()->with('error', __('بيانات مرفق غير صالحة.'))->withInput();
            }
            try {
                if (! Storage::disk($diskName)->exists($path)) {
                    return back()->with('error', __('ملف مفقود من التخزين. أعد رفعه.'))->withInput();
                }
            } catch (\Throwable) {
                return back()->with('error', __('تعذر التحقق من الملف على التخزين.'))->withInput();
            }

            $merged[] = [
                'path' => $path,
                'original_name' => (string) ($payload['original_name'] ?? basename($path)),
                'mime' => (string) ($payload['mime'] ?? 'application/octet-stream'),
            ];
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments', []) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }
                $path = AssignmentFileStorage::storeSubmission($file, (int) $assignment->id, (int) $user->id);
                $merged[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ];
            }
        }

        if (count($merged) > self::MAX_SUBMISSION_ATTACHMENTS) {
            return back()->with('error', 'لا يمكن تجاوز '.self::MAX_SUBMISSION_ATTACHMENTS.' مرفقات في تسليم واحد.')->withInput();
        }

        $content = isset($validated['content']) ? trim((string) $validated['content']) : '';
        if ($content === '' && count($merged) === 0) {
            return back()->with('error', __('أدخل نص التسليم أو أرفق ملفاً واحداً على الأقل.'))->withInput();
        }

        if ($submission->exists && $submission->status === 'returned') {
            $submission->score = null;
            $submission->feedback = null;
            $submission->graded_at = null;
            $submission->graded_by = null;
        }

        $submission->content = $content !== '' ? $validated['content'] : null;
        $submission->attachments = count($merged) > 0 ? $merged : null;
        $submission->submitted_at = now();
        $submission->status = 'submitted';
        $submission->save();

        return redirect()
            ->route('student.assignments.show', $assignment)
            ->with('success', __('تم إرسال التسليم بنجاح.'));
    }

    private function assignmentAllowsStudentAccess(Assignment $assignment, $user): bool
    {
        if ($assignment->status !== 'published') {
            return false;
        }
        $courseId = $assignment->advanced_course_id ?? $assignment->course_id;

        return (bool) ($courseId && $user->isEnrolledIn($courseId));
    }

    private function submissionDeadlineOpen(Assignment $assignment): bool
    {
        if (! $assignment->due_date) {
            return true;
        }
        if ($assignment->due_date->isPast() && ! $assignment->allow_late_submission) {
            return false;
        }

        return true;
    }

    private function submissionDiskProvidesDirectUpload(string $diskName): bool
    {
        if (! in_array($diskName, ['r2', 's3'], true)) {
            return false;
        }
        try {
            $disk = Storage::disk($diskName);

            return $disk instanceof AwsS3V3Adapter;
        } catch (\Throwable) {
            return false;
        }
    }

    private function normalizeAssignmentSubmissionMime(string $mime, ?string $originalName = null): string
    {
        $mime = strtolower(trim($mime));
        if ($mime === '' || $mime === 'application/octet-stream' || $mime === 'binary/octet-stream') {
            $guess = $this->mimeFromOriginalFilename($originalName);
            if ($guess !== null) {
                return $guess;
            }

            return 'application/pdf';
        }
        if (array_key_exists($mime, self::ALLOWED_ASSIGNMENT_MIMES)) {
            return $mime;
        }

        $guess = $this->mimeFromOriginalFilename($originalName);

        return $guess ?? 'application/pdf';
    }

    private function mimeFromOriginalFilename(?string $originalName): ?string
    {
        if (! is_string($originalName) || $originalName === '') {
            return null;
        }
        $ext = strtolower(pathinfo(str_replace(["\0", '\\'], '', $originalName), PATHINFO_EXTENSION));
        $extToMime = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $extToMime[$ext] ?? null;
    }

    private function mimeToAssignmentExt(string $mime): string
    {
        $mime = strtolower(trim($mime));

        return self::ALLOWED_ASSIGNMENT_MIMES[$mime] ?? 'pdf';
    }
}
