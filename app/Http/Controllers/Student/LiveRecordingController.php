<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonSessionRecording;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveRecordingController extends Controller
{
    /**
     * قائمة التسجيلات المنشورة للجلسات التي يمكن للطالب الوصول إليها.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $enrolledCourseIds = collect();
        if (Schema::hasTable('student_course_enrollments')) {
            $enrollQuery = DB::table('student_course_enrollments')
                ->where('user_id', $user->id);

            if (Schema::hasColumn('student_course_enrollments', 'status')) {
                $enrollQuery->where('status', 'active');
            }

            $enrolledCourseIds = $enrollQuery->pluck('advanced_course_id');
        } elseif (Schema::hasTable('online_enrollments')) {
            $enrollQuery = DB::table('online_enrollments')
                ->where('user_id', $user->id);
            if (Schema::hasColumn('online_enrollments', 'is_active')) {
                $enrollQuery->where('is_active', true);
            }
            $enrolledCourseIds = $enrollQuery->pluck('advanced_course_id');
        }

        $sessionIds = LiveSession::where('status', 'ended')
            ->where(function ($q) use ($enrolledCourseIds) {
                $q->whereIn('course_id', $enrolledCourseIds)
                    ->orWhere('require_enrollment', false)
                    ->orWhereNull('course_id');
            })
            ->pluck('id');

        $recordings = LiveRecording::with(['session.course', 'session.instructor'])
            ->whereIn('session_id', $sessionIds)
            ->where('status', 'ready')
            ->where('is_published', true)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $lessonRecordings = collect();
        if (Schema::hasTable('lesson_session_recordings')) {
            try {
                app(\App\Services\LessonRecordingService::class)->finalizePendingForStudent((int) $user->id);
            } catch (\Throwable $e) {
                \Log::info('Student lesson recording finalize skipped', ['message' => $e->getMessage()]);
            }

            $lessonRecordings = LessonSessionRecording::query()
                ->with(['instructor', 'student', 'booking'])
                ->where(function ($q) use ($user) {
                    $q->where('student_id', $user->id)
                        ->orWhereHas('booking', function ($booking) use ($user) {
                            $booking->where('student_id', $user->id);
                        });
                })
                ->whereIn('status', [
                    LessonSessionRecording::STATUS_READY,
                    LessonSessionRecording::STATUS_UPLOADING,
                    LessonSessionRecording::STATUS_RECORDING,
                ])
                ->latest()
                ->limit(40)
                ->get();
        }

        return view('student.live-recordings.index', compact('recordings', 'lessonRecordings'));
    }

    public function showLesson(LessonSessionRecording $recording)
    {
        $recording->loadMissing('booking');
        if ((int) $recording->student_id !== (int) auth()->id()
            && (int) ($recording->booking?->student_id) !== (int) auth()->id()) {
            abort(403, 'ليس لديك صلاحية مشاهدة هذا التسجيل');
        }
        if (! $recording->isReady()) {
            abort(404);
        }
        $url = $recording->getUrl();
        if (! $url) {
            abort(404, 'رابط التسجيل غير متوفر حالياً');
        }

        return view('student.live-recordings.show-lesson', compact('recording', 'url'));
    }

    /**
     * مشاهدة تسجيل (مع تحقق الصلاحية).
     */
    public function show(LiveRecording $liveRecording)
    {
        $liveRecording->load('session');
        $session = $liveRecording->session;

        if (!$session || !$session->canUserJoin(auth()->user())) {
            abort(403, 'ليس لديك صلاحية مشاهدة هذا التسجيل');
        }
        if ($liveRecording->status !== 'ready' || !$liveRecording->is_published) {
            abort(404);
        }

        $url = $liveRecording->getUrl();
        if (!$url) {
            abort(404, 'رابط التسجيل غير متوفر حالياً');
        }

        return view('student.live-recordings.show', compact('liveRecording', 'url'));
    }
}
