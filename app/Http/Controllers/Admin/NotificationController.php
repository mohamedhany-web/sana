<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SearchInput;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Mail\CommunityNotificationMail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات المرسلة
     * محمي من: XSS, SQL Injection, Unauthorized Access
     */
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'sender'])
                            ->where('sender_id', Auth::id());

        $audience = strip_tags(trim((string) $request->input('audience', '')));
        if ($audience && array_key_exists($audience, Notification::getAudiences())) {
            $query->where('audience', $audience);
        }

        // فلترة حسب النوع - حماية من SQL Injection
        if ($request->filled('type')) {
            $type = strip_tags(trim($request->type));
            $validTypes = array_keys(Notification::getTypes());
            if (in_array($type, $validTypes)) {
                $query->where('type', $type);
            }
        }

        // فلترة حسب الحالة - حماية من SQL Injection
        if ($request->filled('status')) {
            $status = strip_tags(trim($request->status));
            if ($status === 'read') {
                $query->where('is_read', true);
            } elseif ($status === 'unread') {
                $query->where('is_read', false);
            }
        }

        // البحث - حماية من XSS و SQL Injection
        if ($request->filled('search')) {
            $search = SearchInput::sanitizeForLike((string) $request->search);
            if ($search !== '') {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('message', 'like', '%' . $search . '%');
                });
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $baseStatsQuery = Notification::where('sender_id', Auth::id());
        if ($audience && array_key_exists($audience, Notification::getAudiences())) {
            $baseStatsQuery->where('audience', $audience);
        }

        // إحصائيات
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'unread' => (clone $baseStatsQuery)->unread()->count(),
            'today' => (clone $baseStatsQuery)->whereDate('created_at', today())->count(),
            'by_type' => (clone $baseStatsQuery)
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type'),
            'by_audience' => [
                'student' => Notification::where('sender_id', Auth::id())->where('audience', 'student')->count(),
                'instructor' => Notification::where('sender_id', Auth::id())->where('audience', 'instructor')->count(),
                'employee' => Notification::where('sender_id', Auth::id())->where('audience', 'employee')->count(),
            ],
        ];

        $notificationTypes = Notification::getTypes();
        $audiences = Notification::getAudiences();

        return view('admin.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'audiences', 'audience'));
    }

    /**
     * عرض صفحة إرسال إشعار جديد
     * محمي من: Unauthorized Access
     */
    public function create(Request $request)
    {
        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();
        $targetTypes = Notification::getTargetTypes();
        $audiences = Notification::getAudiences();
        $selectedAudience = strip_tags(trim((string) $request->input('audience', 'student')));
        if (! array_key_exists($selectedAudience, $audiences)) {
            $selectedAudience = 'student';
        }
        
        $academicYears = AcademicYear::active()->orderBy('order')->get();
        $academicSubjects = AcademicSubject::active()->orderBy('name')->get();
        $courses = AdvancedCourse::active()->with(['academicSubject'])->orderBy('title')->get();
        $students = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get();
        $instructors = User::whereIn('role', ['instructor', 'teacher'])->where('is_active', true)->orderBy('name')->get();
        $employees = User::where('is_employee', true)->where('is_active', true)->orderBy('name')->get();

        return view('admin.notifications.create', compact(
            'notificationTypes', 'priorities', 'targetTypes', 'audiences', 'selectedAudience',
            'academicYears', 'academicSubjects', 'courses', 'students', 'instructors', 'employees'
        ));
    }

    /**
     * إرسال إشعار جديد
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Validation Bypass
     */
    public function store(Request $request)
    {
        // Rate Limiting - حماية من Brute Force
        $key = 'notification_send_' . Auth::id();
        $maxAttempts = 20;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors(['rate_limit' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية."])
                ->withInput();
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // Sanitization - تنقية البيانات من XSS
        $sanitizedData = [
            'title' => strip_tags(trim($request->input('title', ''))),
            'message' => strip_tags(trim($request->input('message', ''))),
            'type' => strip_tags(trim($request->input('type', ''))),
            'priority' => strip_tags(trim($request->input('priority', ''))),
            'target_type' => strip_tags(trim($request->input('target_type', ''))),
            'target_id' => filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null,
            'action_url' => filter_var($request->input('action_url'), FILTER_SANITIZE_URL) ?: null,
            'action_text' => strip_tags(trim($request->input('action_text', ''))),
            'expires_at' => $request->input('expires_at') ?: null,
        ];

        // Validation محسن
        $validator = Validator::make($sanitizedData, [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\p{N}a-zA-Z0-9@.-]+$/u',
            ],
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
            'type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTypes())),
            ],
            'priority' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getPriorities())),
            ],
            'target_type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTargetTypes())),
            ],
            'target_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'action_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'action_text' => [
                'nullable',
                'string',
                'max:100',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.regex' => 'العنوان يحتوي على أحرف غير مسموحة',
            'message.required' => 'نص الإشعار مطلوب',
            'message.max' => 'النص يجب ألا يتجاوز 2000 حرف',
            'type.required' => 'نوع الإشعار مطلوب',
            'type.in' => 'نوع الإشعار المحدد غير صحيح',
            'priority.required' => 'أولوية الإشعار مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صحيحة',
            'target_type.required' => 'نوع المستهدفين مطلوب',
            'target_type.in' => 'نوع المستهدفين المحدد غير صحيح',
            'action_url.url' => 'رابط الإجراء غير صحيح',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
        ]);

        if ($validator->fails()) {
            RateLimiter::clear($key);
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // التحقق من وجود المستهدفين
            $targetId = $sanitizedData['target_id'];
            $needsTargetId = in_array($sanitizedData['target_type'], [
                'course_students', 'year_students', 'subject_students', 'individual',
                'individual_instructor', 'individual_employee',
            ], true);
            if ($needsTargetId && !$targetId) {
                DB::rollBack();
                RateLimiter::clear($key);
                return back()
                    ->withErrors(['target_id' => 'يجب اختيار المستهدفين المحددين'])
                    ->withInput();
            }

            // التحقق من صحة action_url إذا كان موجوداً
            if ($sanitizedData['action_url']) {
                $parsedUrl = parse_url($sanitizedData['action_url']);
                if (!$parsedUrl || !isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    return back()
                        ->withErrors(['action_url' => 'رابط الإجراء يجب أن يبدأ بـ http:// أو https://'])
                        ->withInput();
                }
            }

            $audience = Notification::audienceForTargetType($sanitizedData['target_type']);
            $type = $sanitizedData['type'];
            if ($audience === 'employee' && $type === 'general') {
                $type = 'employee';
            } elseif ($audience === 'instructor' && $type === 'general') {
                $type = 'instructor';
            }

            $data = [
                'sender_id' => Auth::id(),
                'title' => $sanitizedData['title'],
                'message' => $sanitizedData['message'],
                'type' => $type,
                'priority' => $sanitizedData['priority'],
                'target_type' => $sanitizedData['target_type'],
                'target_id' => $targetId,
                'action_url' => $sanitizedData['action_url'],
                'action_text' => $sanitizedData['action_text'],
                'expires_at' => $sanitizedData['expires_at'],
                'data' => null,
                'audience' => $audience,
            ];

            // تحديد المستهدفين وإرسال الإشعارات داخل المنصة
            $sentCount = $this->sendNotificationToTargets($sanitizedData['target_type'], $targetId, $data);

            $sendEmail = $request->boolean('send_email');
            $emailSent = 0;
            $emailFailed = 0;
            if ($sendEmail && $sentCount > 0) {
                $emailStats = $this->emailNotificationToTargets(
                    $sanitizedData['target_type'],
                    $targetId,
                    $sanitizedData['title'],
                    $sanitizedData['message']
                );
                $emailSent = $emailStats['sent'];
                $emailFailed = $emailStats['failed'];
            }

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_sent',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => [
                    'target_type' => $sanitizedData['target_type'],
                    'audience' => $audience,
                    'sent_count' => $sentCount,
                    'send_email' => $sendEmail,
                    'email_sent' => $emailSent,
                    'email_failed' => $emailFailed,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            DB::commit();
            RateLimiter::clear($key);

            $audienceLabel = Notification::getAudiences()[$audience] ?? 'مستلم';
            $success = "تم إرسال الإشعار داخل المنصة إلى {$sentCount} من {$audienceLabel}";
            if ($sendEmail) {
                $success .= "، والبريد إلى {$emailSent} مستلم";
                if ($emailFailed > 0) {
                    $success .= " (تعذّر {$emailFailed})";
                }
            }

            return redirect()->route('admin.notifications.index', ['audience' => $audience])
                ->with('success', $success);

        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error sending notification: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء إرسال الإشعار. يرجى المحاولة مرة أخرى.'])
                ->withInput();
        }
    }

    /**
     * وارد الإشعارات (ما يصل للمشرف: تذاكر دعم، تنبيهات، إلخ) — لأي مستخدم يملك admin.access.
     * يختلف عن index الذي يعرض فقط الإشعارات التي أرسلها super_admin للطلاب.
     */
    public function inbox(Request $request)
    {
        if (! Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $userId = Auth::id();

        $query = Notification::query()
            ->where('user_id', $userId)
            ->valid();

        if ($request->filled('status')) {
            $status = strip_tags(trim((string) $request->status));
            if ($status === 'read') {
                $query->where('is_read', true);
            } elseif ($status === 'unread') {
                $query->where('is_read', false);
            }
        }

        $notifications = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $stats = [
            'unread' => Notification::where('user_id', $userId)->unread()->valid()->count(),
            'total' => Notification::where('user_id', $userId)->valid()->count(),
        ];

        return view('admin.notifications.inbox', compact('notifications', 'stats'));
    }

    /**
     * تعليم كل وارد الإشعارات كمقروء للمستخدم الحالي.
     */
    public function inboxMarkAllRead(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id();

        $count = Notification::where('user_id', $userId)
            ->unread()
            ->valid()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'marked' => $count,
        ]);
    }

    /**
     * حذف إشعار من وارد المستخدم الحالي (المستقبل).
     */
    public function inboxDestroy(Notification $notification)
    {
        if (! Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        if ((int) $notification->user_id !== (int) Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا الإشعار');
        }

        $key = 'notification_inbox_delete_' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.");
        }

        RateLimiter::hit($key, 60);

        try {
            $notificationId = $notification->id;
            $notification->delete();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_inbox_deleted',
                'model_type' => 'Notification',
                'model_id' => $notificationId,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);

            RateLimiter::clear($key);

            return redirect()->route('admin.notifications.inbox')
                ->with('success', 'تم حذف الإشعار من الوارد');
        } catch (\Exception $e) {
            RateLimiter::clear($key);
            Log::error('Error deleting inbox notification: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('error', 'حدث خطأ أثناء حذف الإشعار. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * بيانات الجرس في النافبار (تحديث تلقائي + صوت) — JSON.
     */
    public function navPoll()
    {
        if (! Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id();

        $count = Notification::where('user_id', $userId)
            ->unread()
            ->valid()
            ->count();

        $notifications = Notification::where('user_id', $userId)
            ->unread()
            ->valid()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $items = $notifications->map(function (Notification $n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'priority' => $n->priority,
                'href' => route('admin.notifications.show', $n),
                'time' => $n->created_at->diffForHumans(),
                'icon' => $n->type_icon,
            ];
        })->values();

        return response()->json([
            'unread_count' => $count,
            'items' => $items,
        ]);
    }

    /**
     * فتح تذكرة دعم من الإشعار وتعليم الإشعار كمقروء (جرس الأدمن).
     */
    public function openSupportTicket(Notification $notification)
    {
        if (! Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        if ((int) $notification->user_id !== (int) Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا الإشعار');
        }

        $ticketId = is_array($notification->data) ? ($notification->data['support_ticket_id'] ?? null) : null;
        if (! $ticketId || ! SupportTicket::whereKey($ticketId)->exists()) {
            abort(404, 'التذكرة غير موجودة');
        }

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        return redirect()->route('admin.support-tickets.show', $ticketId);
    }

    public function show(Notification $notification)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // السماح برؤية الإشعار إذا كان المستخدم هو المرسل أو المستقبل
        if ($notification->sender_id !== Auth::id() && $notification->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا الإشعار');
        }

        // تعليم الإشعار كمقروء إذا كان المستخدم هو المستقبل
        if ($notification->user_id === Auth::id() && !$notification->is_read) {
            $notification->markAsRead();
        }

        $notification->load(['user', 'sender', 'emailReplies.user']);
        $replyTarget = $notification->resolveReplyEmail();
        
        return view('admin.notifications.show', compact('notification', 'replyTarget'));
    }

    /**
     * الرد بالبريد من داخل المنصة على إشعار يحتوي بريداً (مثل رسائل التواصل).
     */
    public function replyEmail(Request $request, Notification $notification, \App\Services\EmailNotificationService $emailService)
    {
        if (! Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        if ($notification->sender_id !== Auth::id() && $notification->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالرد على هذا الإشعار');
        }

        $key = 'notification_email_reply_' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->with('error', "تم تجاوز عدد المحاولات. حاول بعد {$seconds} ثانية.");
        }
        RateLimiter::hit($key, 300);

        $replyTarget = $notification->resolveReplyEmail();
        if (! $replyTarget) {
            return back()->with('error', 'لا يوجد بريد إلكتروني مرتبط بهذا الإشعار للرد عليه.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ], [
            'subject.required' => 'موضوع الرسالة مطلوب',
            'body.required' => 'نص الرد مطلوب',
        ]);

        $subject = strip_tags(trim($validated['subject']));
        $body = strip_tags(trim($validated['body']));

        $result = $emailService->sendToAddress(
            $replyTarget['email'],
            $body,
            $subject,
            ['notification_id' => $notification->id, 'admin_id' => Auth::id()]
        );

        \App\Models\NotificationEmailReply::create([
            'notification_id' => $notification->id,
            'user_id' => Auth::id(),
            'to_email' => $replyTarget['email'],
            'to_name' => $replyTarget['name'] ?? null,
            'subject' => $subject,
            'body' => $body,
            'status' => $result['success']
                ? \App\Models\NotificationEmailReply::STATUS_SENT
                : \App\Models\NotificationEmailReply::STATUS_FAILED,
            'error_message' => $result['success'] ? null : ($result['error'] ?? 'فشل الإرسال'),
        ]);

        if ($result['success']) {
            $data = is_array($notification->data) ? $notification->data : [];
            if (! empty($data['contact_message_id'])) {
                $contact = \App\Models\ContactMessage::find($data['contact_message_id']);
                if ($contact) {
                    $contact->update([
                        'status' => 'replied',
                        'replied_by' => Auth::id(),
                        'replied_at' => now(),
                        'admin_notes' => trim(($contact->admin_notes ? $contact->admin_notes."\n\n" : '').'رد عبر الإشعار: '.$subject),
                        'read_at' => $contact->read_at ?? now(),
                    ]);
                }
            }

            RateLimiter::clear($key);

            return back()->with('success', 'تم إرسال الرد بالبريد الإلكتروني بنجاح.');
        }

        return back()->withInput()->with('error', $result['error'] ?? 'تعذر إرسال البريد.');
    }

    /**
     * حذف الإشعار
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Unauthorized Access, Ownership
     */
    public function destroy(Notification $notification)
    {
        // التحقق من ملكية الإشعار
        if ($notification->sender_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا الإشعار');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'notification_delete_' . Auth::id();
        $maxAttempts = 30;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.");
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            $notificationId = $notification->id;
            $notification->delete();

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_deleted',
                'model_type' => 'Notification',
                'model_id' => $notificationId,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent(), 0, 255),
            ]);

            RateLimiter::clear($key);

            return redirect()->route('admin.notifications.index', array_filter([
                    'audience' => $notification->audience,
                ]))
                ->with('success', 'تم حذف الإشعار بنجاح');

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error deleting notification: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('error', 'حدث خطأ أثناء حذف الإشعار. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * إرسال الإشعارات للمستهدفين
     */
    private function sendNotificationToTargets($targetType, $targetId, $data)
    {
        switch ($targetType) {
            case 'all_students':
                Notification::sendToAllStudents($data);
                return User::where('role', 'student')->where('is_active', true)->count();

            case 'course_students':
                if ($targetId) {
                    Notification::sendToCourseStudents($targetId, $data);
                    return \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                             ->where('status', 'active')
                                                             ->count();
                }
                break;

            case 'year_students':
                if ($targetId) {
                    Notification::sendToYearStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'subject_students':
                if ($targetId) {
                    Notification::sendToSubjectStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'individual':
                if ($targetId) {
                    Notification::sendToUser($targetId, $data);
                    return 1;
                }
                break;

            case 'all_instructors':
                Notification::sendToAllInstructors($data);
                return User::whereIn('role', ['instructor', 'teacher'])->where('is_active', true)->count();

            case 'individual_instructor':
                if ($targetId) {
                    return Notification::sendToInstructor((int) $targetId, $data);
                }
                break;

            case 'all_employees':
                return Notification::sendToAllEmployees($data);

            case 'individual_employee':
                if ($targetId) {
                    return Notification::sendToEmployee($targetId, $data);
                }
                break;
        }

        return 0;
    }

    /**
     * مستخدمو الهدف لإرسال البريد (نفس شرائح إشعار المنصة).
     *
     * @return Collection<int, User>
     */
    private function resolveTargetUsers(string $targetType, ?int $targetId): Collection
    {
        $query = User::query()->where('is_active', true)->whereNotNull('email');

        switch ($targetType) {
            case 'all_students':
                return $query->where('role', 'student')->get();

            case 'course_students':
                if (! $targetId) {
                    return collect();
                }
                $ids = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                    ->where('status', 'active')
                    ->pluck('user_id');

                return $query->whereIn('id', $ids)->get();

            case 'year_students':
                if (! $targetId) {
                    return collect();
                }
                $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                $ids = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                    ->where('status', 'active')
                    ->pluck('user_id')
                    ->unique();

                return $query->whereIn('id', $ids)->get();

            case 'subject_students':
                if (! $targetId) {
                    return collect();
                }
                $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                $ids = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                    ->where('status', 'active')
                    ->pluck('user_id')
                    ->unique();

                return $query->whereIn('id', $ids)->get();

            case 'individual':
                return $targetId
                    ? $query->where('id', $targetId)->where('role', 'student')->get()
                    : collect();

            case 'all_instructors':
                return $query->whereIn('role', ['instructor', 'teacher'])->get();

            case 'individual_instructor':
                return $targetId
                    ? $query->where('id', $targetId)->whereIn('role', ['instructor', 'teacher'])->get()
                    : collect();

            case 'all_employees':
                return $query->where('is_employee', true)->get();

            case 'individual_employee':
                return $targetId
                    ? $query->where('id', $targetId)->where('is_employee', true)->get()
                    : collect();
        }

        return collect();
    }

    /**
     * إرسال نسخة بريد للمستهدفين.
     *
     * @return array{sent: int, failed: int}
     */
    private function emailNotificationToTargets(string $targetType, ?int $targetId, string $title, string $message): array
    {
        $sent = 0;
        $failed = 0;

        foreach ($this->resolveTargetUsers($targetType, $targetId) as $user) {
            $email = trim((string) $user->email);
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failed++;
                continue;
            }

            try {
                Mail::to($email)->send(new CommunityNotificationMail(
                    $title,
                    $message,
                    $user->name
                ));
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error('Admin notification email failed', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * إرسال إشعار سريع
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Validation Bypass
     */
    public function quickSend(Request $request)
    {
        // Rate Limiting - حماية من Brute Force
        $key = 'notification_quick_send_' . Auth::id();
        $maxAttempts = 30;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // Sanitization - تنقية البيانات
        $sanitizedData = [
            'title' => strip_tags(trim($request->input('title', ''))),
            'message' => strip_tags(trim($request->input('message', ''))),
            'target_type' => strip_tags(trim($request->input('target_type', ''))),
            'target_id' => filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null,
        ];

        // Validation محسن
        $validator = Validator::make($sanitizedData, [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\p{N}a-zA-Z0-9@.-]+$/u',
            ],
            'message' => [
                'required',
                'string',
                'max:1000',
            ],
            'target_type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTargetTypes())),
            ],
            'target_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
            'target_type.required' => 'نوع المستهدفين مطلوب',
        ]);

        if ($validator->fails()) {
            RateLimiter::clear($key);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // التحقق من وجود المستهدفين
            if (in_array($sanitizedData['target_type'], ['course_students', 'year_students', 'subject_students', 'individual']) && !$sanitizedData['target_id']) {
                DB::rollBack();
                RateLimiter::clear($key);
                return response()->json([
                    'success' => false,
                    'message' => 'يجب اختيار المستهدفين المحددين',
                ], 422);
            }

            $data = [
                'sender_id' => Auth::id(),
                'title' => $sanitizedData['title'],
                'message' => $sanitizedData['message'],
                'type' => 'general',
                'priority' => 'normal',
                'target_type' => $sanitizedData['target_type'],
                'target_id' => $sanitizedData['target_id'],
                'audience' => 'student',
            ];

            $sentCount = $this->sendNotificationToTargets($sanitizedData['target_type'], $sanitizedData['target_id'], $data);

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_quick_sent',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => [
                    'target_type' => $sanitizedData['target_type'],
                    'sent_count' => $sentCount,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            DB::commit();
            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعار إلى {$sentCount} طالب",
                'sent_count' => $sentCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error sending quick notification: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    /**
     * الحصول على عدد المستهدفين
     * محمي من: XSS, SQL Injection, Unauthorized Access
     */
    public function getTargetCount(Request $request)
    {
        // Sanitization - تنقية البيانات
        $targetType = strip_tags(trim($request->input('target_type', '')));
        $targetId = filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null;

        // التحقق من النوع المسموح
        $validTargetTypes = array_keys(Notification::getTargetTypes());
        if (!in_array($targetType, $validTargetTypes)) {
            return response()->json(['count' => 0], 422);
        }

        $count = 0;

        try {
            switch ($targetType) {
                case 'all_students':
                    $count = User::where('role', 'student')->where('is_active', true)->count();
                    break;

                case 'course_students':
                    if ($targetId && $targetId > 0) {
                        $count = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                                  ->where('status', 'active')
                                                                  ->count();
                    }
                    break;

                case 'year_students':
                    if ($targetId && $targetId > 0) {
                        $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                        $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                                  ->where('status', 'active')
                                                                  ->distinct('user_id')
                                                                  ->count();
                    }
                    break;

                case 'subject_students':
                    if ($targetId && $targetId > 0) {
                        $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                        $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                                  ->where('status', 'active')
                                                                  ->distinct('user_id')
                                                                  ->count();
                    }
                    break;

                case 'individual':
                    if ($targetId && $targetId > 0) {
                        $count = User::where('id', $targetId)
                                    ->where('role', 'student')
                                    ->where('is_active', true)
                                    ->exists() ? 1 : 0;
                    }
                    break;

                case 'all_instructors':
                    $count = User::whereIn('role', ['instructor', 'teacher'])->where('is_active', true)->count();
                    break;

                case 'individual_instructor':
                    if ($targetId && $targetId > 0) {
                        $count = User::where('id', $targetId)
                            ->whereIn('role', ['instructor', 'teacher'])
                            ->where('is_active', true)
                            ->exists() ? 1 : 0;
                    }
                    break;

                case 'all_employees':
                    $count = User::where('is_employee', true)->where('is_active', true)->count();
                    break;

                case 'individual_employee':
                    if ($targetId && $targetId > 0) {
                        $count = User::where('id', $targetId)
                            ->where('is_employee', true)
                            ->where('is_active', true)
                            ->exists() ? 1 : 0;
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error getting target count: ' . $e->getMessage());
            return response()->json(['count' => 0], 500);
        }

        return response()->json(['count' => max(0, (int)$count)]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     * محمي من: SQL Injection, CSRF, Brute Force, Unauthorized Access
     */
    public function markAllAsRead(Request $request)
    {
        // Rate Limiting
        $key = 'notification_mark_read_' . Auth::id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            $query = Notification::where('sender_id', Auth::id())->unread();

            // Sanitization - التحقق من user_id إذا كان موجوداً
            if ($request->filled('user_id')) {
                $userId = filter_var($request->input('user_id'), FILTER_VALIDATE_INT);
                if ($userId && $userId > 0) {
                    $query->where('user_id', $userId);
                }
            }

            $count = $query->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم تحديد {$count} إشعار كمقروء",
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية',
            ], 500);
        }
    }

    /**
     * حذف الإشعارات القديمة
     * محمي من: SQL Injection, CSRF, Brute Force, Unauthorized Access
     */
    public function cleanup(Request $request)
    {
        // Rate Limiting
        $key = 'notification_cleanup_' . Auth::id();
        $maxAttempts = 5;
        $decayMinutes = 10;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            // Sanitization - التحقق من عدد الأيام
            $days = filter_var($request->input('days', 30), FILTER_VALIDATE_INT);
            if (!$days || $days < 1 || $days > 365) {
                $days = 30; // القيمة الافتراضية الآمنة
            }

            $count = Notification::where('sender_id', Auth::id())
                                ->where('created_at', '<', now()->subDays($days))
                                ->where('is_read', true)
                                ->delete();

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notifications_cleaned',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => ['deleted_count' => $count, 'days' => $days],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$count} إشعار قديم",
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            Log::error('Error cleaning up notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية',
            ], 500);
        }
    }

    /**
     * إحصائيات الإشعارات
     * محمي من: Unauthorized Access
     */
    public function statistics()
    {
        $stats = [
            'overview' => [
                'total_sent' => Notification::where('sender_id', Auth::id())->count(),
                'total_read' => Notification::where('sender_id', Auth::id())->read()->count(),
                'total_unread' => Notification::where('sender_id', Auth::id())->unread()->count(),
                'today_sent' => Notification::where('sender_id', Auth::id())->whereDate('created_at', today())->count(),
            ],
            'by_type' => Notification::where('sender_id', Auth::id())
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->get(),
            'by_priority' => Notification::where('sender_id', Auth::id())
                                       ->selectRaw('priority, count(*) as count')
                                       ->groupBy('priority')
                                       ->get(),
            'by_target' => Notification::where('sender_id', Auth::id())
                                     ->selectRaw('target_type, count(*) as count')
                                     ->groupBy('target_type')
                                     ->get(),
            'recent_activity' => Notification::where('sender_id', Auth::id())
                                            ->selectRaw('DATE(created_at) as date, count(*) as count')
                                            ->groupBy(...\App\Support\SqlGroupExpressions::mysqlDate())
                                            ->orderBy('date', 'desc')
                                            ->take(7)
                                            ->get(),
        ];

        return view('admin.notifications.statistics', compact('stats'));
    }
}
