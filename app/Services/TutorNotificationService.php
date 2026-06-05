<?php

namespace App\Services;

use App\Models\LessonBooking;
use App\Models\Notification;
use App\Models\TutorAssistedRequest;
use App\Models\User;

class TutorNotificationService
{
    public static function notify(
        int $userId,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $audience = 'student',
        string $priority = 'normal',
        ?int $senderId = null,
        ?array $data = null
    ): void {
        Notification::sendToUser($userId, [
            'sender_id' => $senderId,
            'title' => $title,
            'message' => $message,
            'type' => 'reminder',
            'priority' => $priority,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'audience' => $audience,
            'is_read' => false,
            'data' => $data ? array_merge($data, ['module' => 'tutor_lessons']) : ['module' => 'tutor_lessons'],
        ]);
    }

    public static function notifyAdmins(string $title, string $message, ?string $actionUrl = null, ?string $actionText = null, ?int $senderId = null): void
    {
        $adminIds = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('is_active', true)
            ->pluck('id');

        if ($adminIds->isEmpty()) {
            return;
        }

        Notification::sendToUsers($adminIds->all(), [
            'sender_id' => $senderId,
            'title' => $title,
            'message' => $message,
            'type' => 'announcement',
            'priority' => 'high',
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'audience' => 'admin',
            'is_read' => false,
            'data' => ['module' => 'tutor_lessons'],
        ]);
    }

    public static function bookingRequested(LessonBooking $booking): void
    {
        $studentName = $booking->student?->name ?? 'طالب';
        $when = $booking->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';

        self::notify(
            $booking->instructor_id,
            __('tutor.notif_new_booking_title'),
            __('tutor.notif_new_booking_message', ['student' => $studentName, 'when' => $when]),
            route('instructor.tutor-lessons.bookings.show', $booking),
            __('tutor.view_booking'),
            'instructor',
            'high',
            $booking->requested_by_user_id
        );

        self::notify(
            $booking->student_id,
            __('tutor.notif_booking_sent_title'),
            __('tutor.notif_booking_sent_message', ['when' => $when]),
            route('student.tutor-lessons.bookings.show', $booking),
            __('tutor.view_booking'),
            'student',
            'normal',
            $booking->requested_by_user_id
        );
    }

    public static function bookingConfirmed(LessonBooking $booking): void
    {
        $instructorName = $booking->instructor?->name ?? 'المعلم';
        $when = $booking->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';

        self::notify(
            $booking->student_id,
            __('tutor.notif_booking_confirmed_title'),
            __('tutor.notif_booking_confirmed_message', ['teacher' => $instructorName, 'when' => $when]),
            route('student.tutor-lessons.bookings.show', $booking),
            __('tutor.enter_lesson'),
            'student',
            'high',
            $booking->instructor_id
        );

        if ($booking->parent_id) {
            self::notify(
                $booking->parent_id,
                __('tutor.notif_booking_confirmed_title'),
                __('tutor.notif_booking_confirmed_message', ['teacher' => $instructorName, 'when' => $when]),
                route('parent.tutor-lessons.bookings.show', $booking),
                __('tutor.view_booking'),
                'parent',
                'high',
                $booking->instructor_id
            );
        }
    }

    public static function bookingCancelled(LessonBooking $booking, string $byRole): void
    {
        $msg = __('tutor.notif_booking_cancelled_message', ['code' => $booking->code]);

        self::notify($booking->student_id, __('tutor.notif_booking_cancelled_title'), $msg,
            route('student.tutor-lessons.bookings.index'), __('tutor.my_lessons'), 'student');

        self::notify($booking->instructor_id, __('tutor.notif_booking_cancelled_title'), $msg,
            route('instructor.tutor-lessons.bookings.index'), __('tutor.my_lessons'), 'instructor');

        if ($booking->parent_id) {
            self::notify($booking->parent_id, __('tutor.notif_booking_cancelled_title'), $msg,
                route('parent.tutor-lessons.bookings.index'), __('tutor.my_lessons'), 'parent');
        }
    }

    public static function bookingReminder(LessonBooking $booking): void
    {
        $instructorName = $booking->instructor?->name ?? 'المعلم';
        $when = $booking->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';

        self::notify(
            $booking->student_id,
            __('tutor.notif_booking_reminder_title'),
            __('tutor.notif_booking_reminder_message', ['teacher' => $instructorName, 'when' => $when]),
            route('student.tutor-lessons.bookings.show', $booking),
            __('tutor.enter_lesson'),
            'student',
            'high',
            $booking->instructor_id
        );

        if ($booking->parent_id) {
            self::notify(
                $booking->parent_id,
                __('tutor.notif_booking_reminder_title'),
                __('tutor.notif_booking_reminder_parent_message', [
                    'student' => $booking->student?->name ?? 'الطالب',
                    'teacher' => $instructorName,
                    'when' => $when,
                ]),
                route('parent.tutor-lessons.bookings.show', $booking),
                __('tutor.view_booking'),
                'parent',
                'high',
                $booking->instructor_id
            );
        }
    }

    public static function bookingCompleted(LessonBooking $booking): void
    {
        $minutes = (int) $booking->billable_minutes;

        self::notify(
            $booking->student_id,
            __('tutor.notif_lesson_completed_title'),
            __('tutor.notif_lesson_completed_student', ['minutes' => $minutes]),
            route('student.tutor-lessons.bookings.rate', $booking),
            __('tutor.rate_lesson'),
            'student',
            'normal',
            $booking->instructor_id
        );

        self::notify(
            $booking->instructor_id,
            __('tutor.notif_lesson_completed_title'),
            __('tutor.notif_lesson_completed_instructor', ['minutes' => $minutes]),
            route('instructor.tutor-lessons.bookings.rate', $booking),
            __('tutor.rate_lesson'),
            'instructor',
            'normal',
            $booking->student_id
        );
    }

    public static function trialCompleted(int $instructorId): void
    {
        self::notify(
            $instructorId,
            __('tutor.notif_trial_done_title'),
            __('tutor.notif_trial_done_message'),
            route('instructor.tutor-lessons.hub'),
            __('tutor.open_hub'),
            'instructor',
            'high'
        );

        self::notifyAdmins(
            __('tutor.notif_trial_admin_title'),
            __('tutor.notif_trial_admin_message', ['id' => $instructorId]),
            route('admin.tutor-lessons.instructors'),
            __('tutor.review_tutors')
        );
    }

    public static function tutorApplicationSubmitted(User $user): void
    {
        self::notifyAdmins(
            __('tutor.notif_apply_admin_title'),
            __('tutor.notif_apply_admin_message', ['name' => $user->name]),
            route('admin.instructor-applications.show', $user->instructorProfile),
            __('tutor.review_application'),
            $user->id
        );
    }

    public static function instructorApplicationApproved(User $user, ?string $adminNote = null): void
    {
        $message = 'تم قبول انضمامك إلى الأكاديمية. يمكنك تسجيل الدخول من بوابة المدربين وإكمال إعداد ملفك.';
        if ($adminNote) {
            $message .= ' ملاحظة الإدارة: '.$adminNote;
        }

        self::notify(
            $user->id,
            'تم قبول طلب انضمامك',
            $message,
            route('staff.login'),
            'تسجيل الدخول',
            'instructor',
            'high'
        );
    }

    public static function instructorApplicationRejected(User $user, string $reason): void
    {
        self::notify(
            $user->id,
            'لم يُقبل طلب الانضمام',
            'لم تتم الموافقة على طلبك حالياً. السبب: '.$reason,
            route('tutor.apply'),
            'معرفة المزيد',
            'instructor',
            'high'
        );
    }

    public static function assistedRequestOpened(TutorAssistedRequest $request): void
    {
        $studentName = $request->student?->name ?? 'طالب';

        self::notifyAdmins(
            __('tutor.notif_assisted_title'),
            __('tutor.notif_assisted_message', ['student' => $studentName, 'code' => $request->code]),
            route('admin.tutor-lessons.assisted.show', $request),
            __('tutor.review_request'),
            $request->requested_by_user_id
        );

        self::notify(
            $request->requested_by_user_id,
            __('tutor.notif_assisted_user_title'),
            __('tutor.notif_assisted_user_message'),
            route('student.tutor-lessons.assisted.show', $request),
            __('tutor.view_request'),
            $request->parent_id ? 'parent' : 'student',
            'normal',
            null
        );
    }

    public static function assistedRequestAssigned(TutorAssistedRequest $request): void
    {
        $teacher = $request->assignedInstructor?->name ?? 'معلم';

        self::notify(
            $request->student_id,
            __('tutor.notif_assigned_title'),
            __('tutor.notif_assigned_message', ['teacher' => $teacher]),
            route('student.tutor-lessons.assisted.show', $request),
            __('tutor.view_request'),
            'student',
            'high'
        );

        if ($request->parent_id) {
            self::notify(
                $request->parent_id,
                __('tutor.notif_assigned_title'),
                __('tutor.notif_assigned_message', ['teacher' => $teacher]),
                route('parent.tutor-lessons.assisted.show', $request),
                __('tutor.view_request'),
                'parent',
                'high'
            );
        }
    }

    public static function workLogReminder(int $instructorId, int $minutesToday): void
    {
        self::notify(
            $instructorId,
            __('tutor.notif_work_log_title'),
            __('tutor.notif_work_log_message', ['minutes' => $minutesToday]),
            route('instructor.tutor-lessons.work-log'),
            __('tutor.open_work_log'),
            'instructor'
        );
    }
}
