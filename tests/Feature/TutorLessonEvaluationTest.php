<?php

namespace Tests\Feature;

use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Models\Notification;
use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorNotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TutorLessonEvaluationTest extends TestCase
{
    private User $instructor;

    private User $student;

    private User $parent;

    private LessonBooking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
            'cache.default' => 'array',
            'queue.default' => 'sync',
            'mail.default' => 'array',
            'livekit.api_key' => '',
            'livekit.api_secret' => '',
            'livekit.url' => '',
            'livekit.public_url' => '',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createMinimalSchema();
        $this->seedActorsAndBooking();

        $this->withoutMiddleware([
            \App\Http\Middleware\PreventConcurrentSessions::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\LogActivityMiddleware::class,
        ]);
    }

    public function test_complete_redirects_instructor_to_required_evaluation_form(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('instructor.tutor-lessons.bookings.complete', $this->booking));

        $response->assertRedirect(route('instructor.tutor-lessons.bookings.rate', $this->booking));
        $this->assertSame(LessonBooking::STATUS_COMPLETED, $this->booking->fresh()->status);
    }

    public function test_show_redirects_to_rate_when_evaluation_missing(): void
    {
        $this->booking->update([
            'status' => LessonBooking::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.tutor-lessons.bookings.show', $this->booking));

        $response->assertRedirect(route('instructor.tutor-lessons.bookings.rate', $this->booking));
    }

    public function test_rate_validation_requires_student_lesson_and_comment(): void
    {
        $this->booking->update([
            'status' => LessonBooking::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->instructor)
            ->from(route('instructor.tutor-lessons.bookings.rate', $this->booking))
            ->post(route('instructor.tutor-lessons.bookings.rate.store', $this->booking), []);

        $response->assertSessionHasErrors(['rating', 'lesson_rating', 'comment']);
        $this->assertFalse($this->booking->fresh()->hasInstructorEvaluation());
    }

    public function test_instructor_evaluation_is_saved_and_parent_is_notified(): void
    {
        $this->booking->update([
            'status' => LessonBooking::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->instructor)
            ->post(route('instructor.tutor-lessons.bookings.rate.store', $this->booking), [
                'rating' => 5,
                'lesson_rating' => 4,
                'comment' => 'الطالب متفاعل وملتزم خلال الحصة ويحتاج مراجعة التمارين.',
            ]);

        $response->assertRedirect(route('instructor.tutor-lessons.bookings.show', $this->booking));

        $evaluation = $this->booking->fresh()->instructorEvaluation();
        $this->assertNotNull($evaluation);
        $this->assertSame(5, (int) $evaluation->rating);
        $this->assertSame(4, (int) $evaluation->lesson_rating);
        $this->assertStringContainsString('متفاعل', (string) $evaluation->comment);

        $parentNotif = Notification::query()
            ->where('user_id', $this->parent->id)
            ->where('audience', 'parent')
            ->where('title', __('tutor.notif_evaluation_ready_title'))
            ->first();

        $this->assertNotNull($parentNotif, 'Parent should receive evaluation notification');
        $this->assertStringContainsString('5', (string) $parentNotif->message);
        $this->assertStringContainsString('4', (string) $parentNotif->message);
    }

    public function test_parent_can_see_instructor_evaluation_on_booking_page(): void
    {
        $this->booking->update([
            'status' => LessonBooking::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        LessonBookingRating::create([
            'lesson_booking_id' => $this->booking->id,
            'rater_id' => $this->instructor->id,
            'rated_user_id' => $this->student->id,
            'rating' => 4,
            'lesson_rating' => 5,
            'comment' => 'أداء ممتاز في الحصة مع ملاحظات للمنزل.',
        ]);

        $this->actingAs($this->parent);
        $view = app(\App\Http\Controllers\Parent\TutorLessonsController::class)
            ->bookingsShow($this->booking->fresh());

        $this->assertSame('parent.tutor-lessons.booking-show', $view->name());
        $evaluation = $view->getData()['instructorEvaluation'] ?? null;
        $this->assertNotNull($evaluation);
        $this->assertSame(4, (int) $evaluation->rating);
        $this->assertSame(5, (int) $evaluation->lesson_rating);
        $this->assertStringContainsString('أداء ممتاز', (string) $evaluation->comment);
    }

    public function test_parent_hub_shows_evaluation_badge(): void
    {
        $this->booking->update([
            'status' => LessonBooking::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        LessonBookingRating::create([
            'lesson_booking_id' => $this->booking->id,
            'rater_id' => $this->instructor->id,
            'rated_user_id' => $this->student->id,
            'rating' => 3,
            'lesson_rating' => 3,
            'comment' => 'يحتاج تركيزاً أكثر في الواجبات المنزلية.',
        ]);

        $this->actingAs($this->parent);
        $view = app(\App\Http\Controllers\Parent\TutorLessonsController::class)->hub();

        $this->assertSame('parent.tutor-lessons.hub', $view->name());
        $bookings = $view->getData()['bookings'] ?? collect();
        $booking = $bookings->firstWhere('id', $this->booking->id);
        $this->assertNotNull($booking);
        $this->assertTrue($booking->hasInstructorEvaluation());
        $this->assertSame(3, (int) $booking->instructorEvaluation()->rating);
    }

    public function test_booking_completed_notifies_parent_and_instructor_with_rate_link(): void
    {
        Notification::query()->delete();

        $this->booking->update(['billable_minutes' => 45]);
        TutorNotificationService::bookingCompleted($this->booking->fresh(['student', 'instructor']));

        $this->assertTrue(
            Notification::query()
                ->where('user_id', $this->instructor->id)
                ->where('action_url', route('instructor.tutor-lessons.bookings.rate', $this->booking))
                ->exists()
        );

        $this->assertTrue(
            Notification::query()
                ->where('user_id', $this->parent->id)
                ->where('audience', 'parent')
                ->where('title', __('tutor.notif_lesson_completed_title'))
                ->exists()
        );
    }

    public function test_parent_ids_include_guardian_and_booking_parent(): void
    {
        $ids = TutorNotificationService::parentIdsForBooking($this->booking->fresh(['student']));
        $this->assertContains($this->parent->id, $ids);
    }

    public function test_complete_service_marks_booking_completed(): void
    {
        app(LessonBookingService::class)->complete($this->booking);

        $fresh = $this->booking->fresh();
        $this->assertSame(LessonBooking::STATUS_COMPLETED, $fresh->status);
        $this->assertNotNull($fresh->completed_at);
    }

    public function test_lesson_meeting_allows_only_booked_teacher_and_student(): void
    {
        $meeting = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'lesson_booking_id' => $this->booking->id,
            'code' => 'TSTROOM1',
            'room_name' => 'sana-TSTROOM1',
            'title' => 'حصة اختبار',
            'max_participants' => 2,
        ]);
        $this->booking->update(['classroom_meeting_id' => $meeting->id]);

        $stranger = User::create([
            'name' => 'غريب',
            'email' => 'stranger.eval@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->assertTrue(\App\Services\LessonMeetingAccess::isLessonMeeting($meeting));
        $this->assertTrue(\App\Services\LessonMeetingAccess::canJoin($this->instructor, $meeting));
        $this->assertTrue(\App\Services\LessonMeetingAccess::canJoin($this->student, $meeting));
        $this->assertFalse(\App\Services\LessonMeetingAccess::canJoin($this->parent, $meeting));
        $this->assertFalse(\App\Services\LessonMeetingAccess::canJoin($stranger, $meeting));
        $this->assertFalse(\App\Services\LessonMeetingAccess::canJoin(null, $meeting));
    }

    public function test_lesson_join_http_rejects_guests_parents_and_strangers(): void
    {
        $meeting = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'lesson_booking_id' => $this->booking->id,
            'code' => 'HTTPJOIN1',
            'room_name' => 'sana-HTTPJOIN1',
            'title' => 'حصة قفل الدخول',
            'started_at' => now(),
            'planned_duration_minutes' => 60,
            'max_participants' => 2,
        ]);
        $this->booking->update(['classroom_meeting_id' => $meeting->id, 'status' => LessonBooking::STATUS_CONFIRMED]);

        $this->assertTrue(\Illuminate\Support\Facades\Route::has('classroom.join'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('classroom.join.enter'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('classroom.join.heartbeat'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('classroom.join.leave'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('api.livekit.webhook'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('student.live-recordings.lesson'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('instructor.classroom.heartbeat'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('instructor.classroom.leave-presence'));

        $this->get(route('classroom.join', 'HTTPJOIN1'))
            ->assertRedirect();

        $stranger = User::create([
            'name' => 'طالب آخر',
            'email' => 'other.join@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->actingAs($stranger)
            ->get(route('classroom.join', 'HTTPJOIN1'))
            ->assertOk()
            ->assertSee('الحصة مغلقة', false);

        $this->actingAs($this->parent)
            ->postJson(route('classroom.join.enter', 'HTTPJOIN1'), ['display_name' => 'ولي'])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->postJson(route('classroom.join.enter', 'HTTPJOIN1'), ['display_name' => 'غريب'])
            ->assertForbidden();

        $enter = $this->actingAs($this->student)
            ->postJson(route('classroom.join.enter', 'HTTPJOIN1'), ['display_name' => 'طالب اختبار']);
        $enter->assertOk()->assertJsonPath('ok', true);
        $this->assertNotEmpty($enter->json('token'));
        $this->assertFalse((bool) $enter->json('billed_running'));

        $token = (string) $enter->json('token');
        $this->actingAs($this->student)
            ->postJson(route('classroom.join.heartbeat', 'HTTPJOIN1'), ['token' => $token])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('billed_running', false);

        $attendance = app(\App\Services\TutorAttendanceService::class);
        $attendance->ensureInstructorPresence($meeting->fresh(), $this->instructor);

        $beat = $this->actingAs($this->student)
            ->postJson(route('classroom.join.heartbeat', 'HTTPJOIN1'), ['token' => $token]);
        $beat->assertOk()->assertJsonPath('billed_running', true);
        $this->assertGreaterThanOrEqual(2, (int) $beat->json('active_participants'));

        $this->actingAs($this->student)
            ->postJson(route('classroom.join.leave', 'HTTPJOIN1'), ['token' => $token])
            ->assertOk();

        $this->actingAs($this->student)
            ->postJson(route('classroom.join.heartbeat', 'HTTPJOIN1'), ['token' => $token])
            ->assertStatus(404);

        $this->postJson(route('api.livekit.webhook'), ['event' => 'egress_ended'])
            ->assertStatus(503);

        $recording = \App\Models\LessonSessionRecording::create([
            'classroom_meeting_id' => $meeting->id,
            'lesson_booking_id' => $this->booking->id,
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'status' => \App\Models\LessonSessionRecording::STATUS_READY,
            'disk' => 'live_recordings_r2',
            'file_path' => 'lesson-recordings/test.mp4',
            'mime_type' => 'video/mp4',
        ]);

        $this->actingAs($stranger)
            ->get(route('student.live-recordings.lesson', $recording))
            ->assertForbidden();
    }

    public function test_lesson_recording_object_paths_are_unique_per_meeting(): void
    {
        $meetingA = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'lesson_booking_id' => $this->booking->id,
            'code' => 'PATHAAA1',
            'room_name' => 'sana-PATHAAA1',
            'title' => 'أ',
        ]);
        $meetingB = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'code' => 'PATHBBB2',
            'room_name' => 'sana-PATHBBB2',
            'title' => 'ب',
        ]);

        $service = app(\App\Services\LessonRecordingService::class);
        $method = new \ReflectionMethod($service, 'uniqueObjectPath');
        $pathA = $method->invoke($service, $meetingA, $this->booking->id);
        $pathB = $method->invoke($service, $meetingB, 99);

        $this->assertStringContainsString('m'.$meetingA->id.'-i'.$this->instructor->id, $pathA);
        $this->assertStringContainsString('m'.$meetingB->id.'-i'.$this->instructor->id, $pathB);
        $this->assertNotSame($pathA, $pathB);
    }

    public function test_billable_time_pauses_when_student_or_teacher_leaves(): void
    {
        $meeting = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'lesson_booking_id' => $this->booking->id,
            'code' => 'TSTROOM2',
            'room_name' => 'sana-TSTROOM2',
            'title' => 'حصة حضور',
            'started_at' => now(),
            'planned_duration_minutes' => 60,
            'max_participants' => 2,
        ]);
        $this->booking->update([
            'classroom_meeting_id' => $meeting->id,
            'billable_minutes' => 0,
            'billable_seconds' => 0,
            'status' => LessonBooking::STATUS_CONFIRMED,
        ]);

        $attendance = app(\App\Services\TutorAttendanceService::class);
        $teacher = \App\Models\ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'user_id' => $this->instructor->id,
            'participant_role' => 'instructor',
            'token' => 't-teacher',
            'display_name' => 'معلم',
            'joined_at' => now(),
            'last_seen_at' => now(),
        ]);
        $attendance->handleParticipantJoined($meeting, $teacher);
        $this->booking->refresh();
        $this->assertNull($this->booking->co_presence_started_at);

        $student = \App\Models\ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'user_id' => $this->student->id,
            'participant_role' => 'student',
            'token' => 't-student',
            'display_name' => 'طالب',
            'joined_at' => now(),
            'last_seen_at' => now(),
        ]);
        $attendance->handleParticipantJoined($meeting, $student);
        $this->booking->refresh();
        $this->assertNotNull($this->booking->co_presence_started_at);

        $this->booking->update(['co_presence_started_at' => now()->subMinutes(12)]);
        $student->update(['left_at' => now()]);
        $attendance->handleParticipantLeft($meeting, $student->fresh());
        $this->booking->refresh();

        $this->assertNull($this->booking->co_presence_started_at);
        $this->assertGreaterThanOrEqual(700, (int) $this->booking->billable_seconds);
        $this->assertLessThan(800, (int) $this->booking->billable_seconds);

        $minutes = $attendance->resolveBillableMinutes($this->booking->fresh());
        $this->assertSame(12, $minutes);

        app(LessonBookingService::class)->complete($this->booking->fresh());
        $profile = StudentLearningProfile::where('user_id', $this->student->id)->first();
        $this->assertSame(12, (int) $profile->lesson_minutes_used);
    }

    public function test_stale_instructor_presence_does_not_inflate_short_overlap(): void
    {
        $joined = now()->subMinutes(10);
        $meeting = \App\Models\ClassroomMeeting::create([
            'user_id' => $this->instructor->id,
            'lesson_booking_id' => $this->booking->id,
            'code' => 'TSTZOMB1',
            'room_name' => 'sana-TSTZOMB1',
            'title' => 'حصة قصيرة',
            'started_at' => $joined,
            'ended_at' => now(),
            'planned_duration_minutes' => 60,
            'max_participants' => 2,
        ]);
        $this->booking->update([
            'classroom_meeting_id' => $meeting->id,
            'billable_minutes' => 0,
            'billable_seconds' => 0,
            'status' => LessonBooking::STATUS_IN_PROGRESS,
        ]);

        \App\Models\ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'user_id' => $this->instructor->id,
            'participant_role' => 'instructor',
            'token' => 'z-teacher',
            'display_name' => 'معلم',
            'joined_at' => $joined,
            'last_seen_at' => $joined,
            'left_at' => now(),
        ]);
        \App\Models\ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'user_id' => $this->student->id,
            'participant_role' => 'student',
            'token' => 'z-student',
            'display_name' => 'طالب',
            'joined_at' => $joined,
            'last_seen_at' => $joined->copy()->addSeconds(25),
            'left_at' => $joined->copy()->addSeconds(25),
        ]);

        $attendance = app(\App\Services\TutorAttendanceService::class);
        $seconds = $attendance->resolveBillableSeconds($this->booking->fresh());
        $minutes = \App\Services\TutorAttendanceService::secondsToMinutes($seconds);

        $this->assertLessThan(60, $seconds);
        $this->assertSame(0, $minutes);
    }

    private function createMinimalSchema(): void
    {
        Schema::dropAllTables();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->default('student');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_employee')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('parent_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->string('relation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('approved');
            $table->string('instructor_portal_mode')->default('both');
            $table->boolean('offers_tutor_booking')->default(true);
            $table->timestamp('tutor_activated_at')->nullable();
            $table->timestamp('tutor_trial_completed_at')->nullable();
            $table->timestamp('tutor_onboarding_completed_at')->nullable();
            $table->json('tutor_matching_modes')->nullable();
            $table->json('tutor_session_types')->nullable();
            $table->json('tutor_subject_ids')->nullable();
            $table->json('tutor_academic_year_ids')->nullable();
            $table->json('application_data')->nullable();
            $table->timestamps();
        });

        Schema::create('student_learning_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('matching_mode')->nullable();
            $table->string('preferred_session_type')->nullable();
            $table->unsignedInteger('lesson_hours_quota')->default(10);
            $table->unsignedInteger('lesson_hours_used')->default(0);
            $table->unsignedInteger('lesson_minutes_used')->default(0);
            $table->json('subject_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('requested_by_user_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedBigInteger('tutor_assisted_request_id')->nullable();
            $table->string('matching_mode')->nullable();
            $table->string('session_type')->nullable();
            $table->unsignedBigInteger('tutor_group_offer_id')->nullable();
            $table->unsignedInteger('max_group_size')->nullable();
            $table->string('group_session_key')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_trial')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedBigInteger('classroom_meeting_id')->nullable();
            $table->text('student_notes')->nullable();
            $table->text('instructor_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('instructor_rated_at')->nullable();
            $table->unsignedInteger('billable_minutes')->default(0);
            $table->unsignedInteger('billable_seconds')->default(0);
            $table->boolean('hours_deducted')->default(false);
            $table->timestamp('co_presence_started_at')->nullable();
            $table->timestamp('co_presence_ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_booking_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_booking_id');
            $table->unsignedBigInteger('rater_id');
            $table->unsignedBigInteger('rated_user_id');
            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('lesson_rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['lesson_booking_id', 'rater_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->string('priority')->nullable();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('audience')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });

        Schema::create('tutor_work_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->date('work_date');
            $table->unsignedInteger('minutes')->default(0);
            $table->string('source', 64)->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['instructor_id', 'work_date', 'source']);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('classroom_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_booking_id')->nullable();
            $table->string('code')->unique();
            $table->string('room_name')->nullable();
            $table->string('title')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->unsignedInteger('max_participants')->default(2);
            $table->unsignedInteger('participants_peak')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('recording_disk')->nullable();
            $table->string('recording_path', 600)->nullable();
            $table->string('recording_mime_type')->nullable();
            $table->unsignedBigInteger('recording_size')->nullable();
            $table->unsignedInteger('recording_duration_seconds')->nullable();
            $table->timestamp('recording_uploaded_at')->nullable();
            $table->string('recording_egress_id', 120)->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('classroom_meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_meeting_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('participant_role')->nullable();
            $table->string('token')->nullable();
            $table->string('display_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_session_recordings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_meeting_id')->index();
            $table->unsignedBigInteger('lesson_booking_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('egress_id', 120)->nullable()->unique();
            $table->string('status', 32)->default('starting');
            $table->string('disk', 64)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('mime_type', 80)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    private function seedActorsAndBooking(): void
    {
        $this->instructor = User::create([
            'name' => 'معلم اختبار',
            'email' => 'instructor.eval@test.local',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'طالب اختبار',
            'email' => 'student.eval@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'ولي أمر اختبار',
            'email' => 'parent.eval@test.local',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'is_active' => true,
        ]);

        DB::table('parent_students')->insert([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->id,
            'relation' => 'father',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        InstructorProfile::create([
            'user_id' => $this->instructor->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'instructor_portal_mode' => InstructorProfile::PORTAL_BOTH,
            'offers_tutor_booking' => true,
            'tutor_activated_at' => now(),
            'headline' => 'معلم رياضيات',
            'bio' => 'خبرة في التدريس',
        ]);

        StudentLearningProfile::create([
            'user_id' => $this->student->id,
            'matching_mode' => 'pick_teacher',
            'preferred_session_type' => 'one_to_one',
            'lesson_hours_quota' => 20,
            'lesson_hours_used' => 0,
        ]);

        $this->booking = LessonBooking::create([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'parent_id' => $this->parent->id,
            'requested_by_user_id' => $this->parent->id,
            'matching_mode' => 'pick_teacher',
            'session_type' => 'one_to_one',
            'status' => LessonBooking::STATUS_IN_PROGRESS,
            'scheduled_at' => now()->subHour(),
            'duration_minutes' => 60,
            'billable_minutes' => 45,
            'is_trial' => false,
        ]);
    }
}
