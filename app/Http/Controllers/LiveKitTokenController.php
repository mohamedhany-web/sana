<?php

namespace App\Http\Controllers;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\CourseSection;
use App\Models\LiveSession;
use App\Services\LessonMeetingAccess;
use App\Services\LiveKit\LiveKitRole;
use App\Services\LiveKit\LiveKitRoomService;
use App\Services\LiveKit\LiveKitTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveKitTokenController extends Controller
{
    public function __construct(
        protected LiveKitTokenService $tokens,
        protected LiveKitRoomService $rooms,
    ) {}

    public function classroomMeeting(Request $request, ClassroomMeeting $meeting): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);

        if ($meeting->ended_at) {
            return response()->json(['ok' => false, 'message' => 'انتهى هذا الاجتماع.'], 422);
        }

        $meeting->loadMissing('user');

        $isOwner = (int) $meeting->user_id === (int) $user->id;
        $isStaffAdmin = in_array((string) ($user->role ?? ''), ['super_admin', 'admin'], true)
            || (method_exists($user, 'isEmployee') && $user->isEmployee());
        $isAdminHosted = (bool) data_get($meeting->settings, 'hosted_by_admin', false)
            || ($meeting->user && (
                in_array((string) ($meeting->user->role ?? ''), ['super_admin', 'admin'], true)
                || (bool) $meeting->user->is_employee
            ));

        if (! $isOwner && ! $isStaffAdmin) {
            abort(403);
        }

        $wantsObserve = $request->boolean('observe');
        $isLessonMeeting = LessonMeetingAccess::isLessonMeeting($meeting);
        $isCovertObserver = $isStaffAdmin
            && ! $isOwner
            && ! $isAdminHosted
            && ($wantsObserve || $isLessonMeeting);

        if ($isCovertObserver) {
            $payload = $this->tokens->issue(
                $this->rooms->forMeeting($meeting),
                $this->tokens->identityForObserver((int) $user->id),
                'Observer',
                LiveKitRole::HIDDEN_OBSERVER,
                [
                    'mute_on_join' => true,
                    'video_off_on_join' => true,
                ],
            );

            return response()->json(['ok' => true] + $payload);
        }

        // ميتينج الإدارة: أي أدمن يدخل كمضيف فعلي
        $role = ($isOwner || ($isStaffAdmin && $isAdminHosted))
            ? LiveKitRole::HOST
            : LiveKitRole::SUPERVISOR;
        $payload = $this->tokens->issue(
            $this->rooms->forMeeting($meeting),
            $this->tokens->identityForUser((int) $user->id),
            $user->name ?: 'User',
            $role,
        );

        return response()->json(['ok' => true] + $payload);
    }

    public function classroomJoin(Request $request, string $code): JsonResponse
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $meeting = ClassroomMeeting::where('code', $code)->firstOrFail();

        if ($meeting->ended_at) {
            return response()->json(['ok' => false, 'message' => 'انتهى هذا الاجتماع.'], 422);
        }

        $participantToken = (string) $request->input('token', '');
        if ($participantToken === '') {
            return response()->json(['ok' => false, 'message' => 'رمز المشارك مطلوب.'], 422);
        }

        $participant = ClassroomMeetingParticipant::where('classroom_meeting_id', $meeting->id)
            ->where('token', $participantToken)
            ->whereNull('left_at')
            ->first();

        if (! $participant) {
            return response()->json(['ok' => false, 'message' => 'مشارك غير مصرح.'], 403);
        }

        $authUser = $request->user();
        if (LessonMeetingAccess::isLessonMeeting($meeting) && ! LessonMeetingAccess::canJoin($authUser, $meeting)) {
            return response()->json(['ok' => false, 'message' => LessonMeetingAccess::denyMessage($authUser)], 403);
        }

        $isHost = $authUser && (int) $meeting->user_id === (int) $authUser->id;
        $role = $isHost ? LiveKitRole::HOST : LiveKitRole::GUEST;
        $isLessonMeeting = LessonMeetingAccess::isLessonMeeting($meeting);
        $isCovertStaff = $authUser && ! $isHost && $isLessonMeeting && LessonMeetingAccess::isStaff($authUser);

        if ($authUser && ! $isHost && ! $isCovertStaff) {
            $role = LiveKitRole::PARTICIPANT;
        }

        if ($isLessonMeeting && ! $authUser) {
            return response()->json(['ok' => false, 'message' => LessonMeetingAccess::denyMessage(null)], 403);
        }

        if ($isCovertStaff) {
            $payload = $this->tokens->issue(
                $this->rooms->forMeeting($meeting),
                $this->tokens->identityForObserver((int) $authUser->id),
                'Observer',
                LiveKitRole::HIDDEN_OBSERVER,
                [
                    'mute_on_join' => true,
                    'video_off_on_join' => true,
                ],
            );

            return response()->json(['ok' => true] + $payload);
        }

        $identity = $authUser
            ? $this->tokens->identityForUser((int) $authUser->id)
            : $this->tokens->identityForGuest($participantToken);

        $payload = $this->tokens->issue(
            $this->rooms->forMeeting($meeting),
            $identity,
            $participant->display_name ?: ($authUser?->name ?: 'ضيف'),
            $role,
        );

        return response()->json(['ok' => true] + $payload);
    }

    public function liveSession(Request $request, LiveSession $liveSession): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);

        if ($liveSession->status === 'ended' || $liveSession->ended_at) {
            return response()->json(['ok' => false, 'message' => 'انتهت هذه الجلسة.'], 422);
        }

        $isHost = (int) $liveSession->instructor_id === (int) $user->id;
        $isSupervisor = $user->isAdmin() || (method_exists($user, 'isEmployee') && $user->isEmployee());

        if (! $isHost && ! $isSupervisor) {
            if (method_exists($liveSession, 'canUserJoin') && ! $liveSession->canUserJoin($user)) {
                abort(403, 'ليس لديك صلاحية دخول هذه الجلسة');
            }
        }

        $role = $isHost ? LiveKitRole::HOST : ($isSupervisor ? LiveKitRole::SUPERVISOR : LiveKitRole::PARTICIPANT);

        $payload = $this->tokens->issue(
            $this->rooms->forLiveSession($liveSession),
            $this->tokens->identityForUser((int) $user->id),
            $user->name ?: 'User',
            $role,
            [
                'mute_on_join' => (bool) $liveSession->mute_on_join && $role !== LiveKitRole::HOST,
                'video_off_on_join' => (bool) $liveSession->video_off_on_join && $role !== LiveKitRole::HOST,
            ],
        );

        return response()->json(['ok' => true] + $payload);
    }

    public function courseSection(Request $request, CourseSection $section): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $section->loadMissing('course');
        $course = $section->course;
        abort_unless($course, 404);

        $isInstructor = (int) ($course->instructor_id ?? 0) === (int) $user->id
            || (method_exists($course, 'instructors') && $course->instructors()->where('users.id', $user->id)->exists());

        $session = LiveSession::query()
            ->where('course_section_id', $section->id)
            ->whereIn('status', ['live', 'scheduled'])
            ->whereNull('ended_at')
            ->latest('id')
            ->first();

        if (! $session) {
            return response()->json([
                'ok' => false,
                'message' => 'لا توجد جلسة مباشرة مفتوحة لهذه الوحدة.',
            ], 404);
        }

        if (! $isInstructor) {
            $enrolled = $this->userEnrolledInCourse($user->id, (int) $course->id);
            if (! $enrolled) {
                abort(403, 'يجب أن تكون مسجلاً في الكورس.');
            }
        }

        $role = $isInstructor ? LiveKitRole::HOST : LiveKitRole::PARTICIPANT;

        $payload = $this->tokens->issue(
            $this->rooms->forLiveSession($session),
            $this->tokens->identityForUser((int) $user->id),
            $user->name ?: 'User',
            $role,
            [
                'mute_on_join' => (bool) $session->mute_on_join && $role !== LiveKitRole::HOST,
                'video_off_on_join' => (bool) $session->video_off_on_join && $role !== LiveKitRole::HOST,
            ],
        );

        return response()->json(['ok' => true, 'live_session_id' => $session->id] + $payload);
    }

    protected function userEnrolledInCourse(int $userId, int $courseId): bool
    {
        if ($courseId < 1) {
            return false;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('student_course_enrollments')) {
            return \Illuminate\Support\Facades\DB::table('student_course_enrollments')
                ->where('user_id', $userId)
                ->where('advanced_course_id', $courseId)
                ->where(function ($q) {
                    $q->whereNull('status')->orWhereIn('status', ['active', 'completed', 'enrolled']);
                })
                ->exists();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('enrollments')) {
            return \Illuminate\Support\Facades\DB::table('enrollments')
                ->where('user_id', $userId)
                ->where(function ($q) use ($courseId) {
                    $q->where('advanced_course_id', $courseId)
                        ->orWhere('course_id', $courseId);
                })
                ->exists();
        }

        return false;
    }
}
