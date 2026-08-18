<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\LessonBooking;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * صلاحية دخول ميتينج حصة محجوزة: المعلم المحجوز + الطالب/طلاب المجموعة فقط.
 * ميتينجات الإدارة/الفصل العامة تبقى كما هي (رابط كود).
 */
class LessonMeetingAccess
{
    public static function isLessonMeeting(?ClassroomMeeting $meeting): bool
    {
        if (! $meeting) {
            return false;
        }

        if ($meeting->lesson_booking_id) {
            return true;
        }

        return LessonBooking::query()->where('classroom_meeting_id', $meeting->id)->exists();
    }

    /** @return Collection<int, LessonBooking> */
    public static function bookingsFor(ClassroomMeeting $meeting): Collection
    {
        return LessonBooking::query()
            ->where(function ($q) use ($meeting) {
                $q->where('classroom_meeting_id', $meeting->id);
                if ($meeting->lesson_booking_id) {
                    $q->orWhere('id', $meeting->lesson_booking_id);
                }
            })
            ->get()
            ->unique('id')
            ->values();
    }

    /** @return list<int> */
    public static function allowedUserIds(ClassroomMeeting $meeting): array
    {
        $ids = [(int) $meeting->user_id];
        foreach (self::bookingsFor($meeting) as $booking) {
            $ids[] = (int) $booking->instructor_id;
            $ids[] = (int) $booking->student_id;
        }

        return array_values(array_unique(array_filter($ids)));
    }

    public static function isStaff(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array((string) ($user->role ?? ''), ['super_admin', 'admin'], true)
            || (method_exists($user, 'isEmployee') && $user->isEmployee());
    }

    public static function hasOpenBookings(ClassroomMeeting $meeting): bool
    {
        return self::bookingsFor($meeting)->contains(
            fn (LessonBooking $booking) => in_array($booking->status, [
                LessonBooking::STATUS_CONFIRMED,
                LessonBooking::STATUS_IN_PROGRESS,
            ], true)
        );
    }

    public static function isClosedForJoin(ClassroomMeeting $meeting): bool
    {
        if (! self::isLessonMeeting($meeting)) {
            return (bool) $meeting->ended_at;
        }

        return ! self::hasOpenBookings($meeting);
    }

    public static function reopenIfStillBooked(ClassroomMeeting $meeting): ClassroomMeeting
    {
        if (! $meeting->ended_at || ! self::isLessonMeeting($meeting) || ! self::hasOpenBookings($meeting)) {
            return $meeting;
        }

        $meeting->update(['ended_at' => null]);

        return $meeting->fresh() ?? $meeting;
    }

    public static function canJoin(?User $user, ClassroomMeeting $meeting): bool
    {
        if (! self::isLessonMeeting($meeting)) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if (self::isStaff($user)) {
            return true;
        }

        return in_array((int) $user->id, self::allowedUserIds($meeting), true);
    }

    public static function denyMessage(?User $user): string
    {
        if (! $user) {
            return 'يجب تسجيل الدخول بحساب الطالب أو المعلم المحجوز لهذه الحصة.';
        }

        return 'هذه الحصة مغلقة على المعلم والطالب المحجوزين فقط. لا يمكن الدخول برابط مشترك.';
    }
}
