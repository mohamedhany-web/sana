<?php

namespace Tests\Feature;

use App\Models\ClassroomMeeting;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Models\Notification;
use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Services\InstructorApplicationService;
use App\Services\LessonBookingService;
use App\Services\TutorLessonQuotaService;
use App\Support\PublicInstructorCatalog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminInstructorStudentCycleTest extends TestCase
{
    private User $admin;

    private User $instructor;

    private InstructorProfile $profile;

    private User $student;

    private User $otherStudent;

    private User $parent;

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
            'tutor_lessons.auto_activate_on_setup' => true,
            'tutor_lessons.defaults.default_student_lesson_hours' => 0,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createMinimalSchema();
        $this->seedActors();
        Mail::fake();
        TutorLessonQuotaService::clearSettingsCache();

        $this->withoutMiddleware([
            \App\Http\Middleware\PreventConcurrentSessions::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\LogActivityMiddleware::class,
        ]);
    }

    public function test_incomplete_instructor_is_not_bookable_or_on_homepage_or_join_list(): void
    {
        $this->assertTrue(
            InstructorProfile::incompleteSignupUserQuery()->where('users.id', $this->instructor->id)->exists()
        );

        $this->assertFalse(
            InstructorProfile::query()
                ->where(function ($q) {
                    $q->whereNotNull('submitted_at')
                        ->orWhere('status', InstructorProfile::STATUS_APPROVED)
                        ->orWhere(function ($inner) {
                            $inner->where('offers_tutor_booking', true)
                                ->whereNotNull('tutor_activated_at');
                        });
                })
                ->whereKey($this->profile->id)
                ->exists(),
            'Incomplete drafts must not appear in admin join-applications'
        );

        $this->assertFalse($this->profile->isTutorActivated());
        $this->assertFalse(
            LessonBookingService::bookableInstructorsQuery()->where('user_id', $this->instructor->id)->exists()
        );
        $this->assertFalse(PublicInstructorCatalog::hasMinimumPublicProfile($this->profile->fresh()->load('user')));
    }

    public function test_admin_approval_activates_booking_and_public_listing(): void
    {
        $this->submitAndApproveInstructor();

        $fresh = $this->profile->fresh()->load('user');

        $this->assertSame(InstructorProfile::STATUS_APPROVED, $fresh->status);
        $this->assertTrue($fresh->isTutorActivated());
        $this->assertTrue($fresh->show_on_homepage);
        $this->assertTrue((bool) $fresh->user->is_active);

        $this->assertTrue(
            LessonBookingService::bookableInstructorsQuery(
                StudentLearningProfile::MODE_PICK_TEACHER,
                1
            )->where('user_id', $this->instructor->id)->exists()
        );

        $this->assertTrue(PublicInstructorCatalog::hasMinimumPublicProfile($fresh));
        $this->assertFalse(
            InstructorProfile::incompleteSignupUserQuery()->where('users.id', $this->instructor->id)->exists()
        );
    }

    public function test_student_without_hours_cannot_book(): void
    {
        $this->submitAndApproveInstructor();
        $this->seedWeeklyAvailability();

        $this->expectException(ValidationException::class);

        try {
            app(LessonBookingService::class)->createBooking([
                'student_id' => $this->student->id,
                'instructor_id' => $this->instructor->id,
                'matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER,
                'session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
                'scheduled_at' => $this->nextSlot(),
                'duration_minutes' => 60,
            ], $this->student);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('scheduled_at', $e->errors());
            $this->assertStringContainsString(
                (string) __('tutor.insufficient_hours'),
                implode(' ', $e->errors()['scheduled_at'])
            );
            throw $e;
        }
    }

    public function test_full_admin_teacher_student_cycle_books_confirms_completes_and_rates(): void
    {
        $this->submitAndApproveInstructor();
        $this->seedWeeklyAvailability();
        TutorLessonQuotaService::addBonusHours($this->student, 8);

        $scheduledAt = $this->nextSlot();

        $bookResponse = $this->actingAs($this->student)
            ->post(route('student.tutor-lessons.book.store', $this->instructor), [
                'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
                'session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            ]);

        $booking = LessonBooking::query()->where('student_id', $this->student->id)->first();
        $this->assertNotNull($booking, 'Student booking should be created');
        $bookResponse->assertRedirect(route('student.tutor-lessons.bookings.show', $booking));
        $this->assertSame(LessonBooking::STATUS_PENDING, $booking->status);

        $this->assertTrue(
            Notification::query()
                ->where('user_id', $this->instructor->id)
                ->where('audience', 'instructor')
                ->exists(),
            'Instructor should be notified of the new booking'
        );

        $confirm = $this->actingAs($this->instructor)
            ->post(route('instructor.tutor-lessons.bookings.confirm', $booking));
        $confirm->assertRedirect();

        $booking = $booking->fresh();
        $this->assertSame(LessonBooking::STATUS_CONFIRMED, $booking->status);
        $this->assertNotNull($booking->classroom_meeting_id);
        $this->assertTrue(ClassroomMeeting::query()->whereKey($booking->classroom_meeting_id)->exists());

        $this->assertTrue(
            Notification::query()
                ->where('user_id', $this->student->id)
                ->where('audience', 'student')
                ->where('title', __('tutor.notif_booking_confirmed_title'))
                ->exists()
        );

        $show = $this->actingAs($this->student)->get(route('student.tutor-lessons.bookings.show', $booking));
        $show->assertOk();
        $show->assertSee('حجز #'.$booking->code, false);
        $show->assertSee(__('tutor.enter_lesson'), false);

        $forbidden = $this->actingAs($this->otherStudent)
            ->get(route('student.tutor-lessons.bookings.show', $booking));
        $forbidden->assertForbidden();

        $complete = $this->actingAs($this->instructor)
            ->post(route('instructor.tutor-lessons.bookings.complete', $booking));
        $complete->assertRedirect(route('instructor.tutor-lessons.bookings.rate', $booking));

        $booking = $booking->fresh();
        $this->assertSame(LessonBooking::STATUS_COMPLETED, $booking->status);
        $this->assertTrue((bool) $booking->hours_deducted);

        $instructorRate = $this->actingAs($this->instructor)
            ->post(route('instructor.tutor-lessons.bookings.rate.store', $booking), [
                'rating' => 5,
                'lesson_rating' => 4,
                'comment' => 'الطالب متفاعل وملتزم خلال الحصة ويحتاج مراجعة.',
            ]);
        $instructorRate->assertRedirect(route('instructor.tutor-lessons.bookings.show', $booking));
        $this->assertTrue($booking->fresh()->hasInstructorEvaluation());

        $studentRate = $this->actingAs($this->student)
            ->post(route('student.tutor-lessons.bookings.rate.store', $booking), [
                'rating' => 5,
                'comment' => 'حصة ممتازة',
            ]);
        $studentRate->assertRedirect(route('student.tutor-lessons.bookings.show', $booking));

        $this->assertTrue(
            LessonBookingRating::query()
                ->where('lesson_booking_id', $booking->id)
                ->where('rater_id', $this->student->id)
                ->exists()
        );

        $hub = $this->actingAs($this->student)->get(route('student.tutor-lessons.hub'));
        $hub->assertOk();
        $hub->assertSee(__('tutor.student_hub_title'), false);
    }

    public function test_hiding_from_homepage_does_not_block_internal_booking(): void
    {
        $this->submitAndApproveInstructor();
        $this->profile->update(['show_on_homepage' => false]);

        $fresh = $this->profile->fresh()->load('user');
        $this->assertFalse(PublicInstructorCatalog::hasMinimumPublicProfile($fresh));
        $this->assertTrue($fresh->isTutorActivated());
        $this->assertTrue(
            LessonBookingService::bookableInstructorsQuery()->where('user_id', $this->instructor->id)->exists()
        );
    }

    public function test_cancel_notifies_parent_using_existing_parent_route(): void
    {
        $this->submitAndApproveInstructor();
        $this->seedWeeklyAvailability();
        TutorLessonQuotaService::addBonusHours($this->student, 4);

        $booking = app(LessonBookingService::class)->createBooking([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'parent_id' => $this->parent->id,
            'matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER,
            'session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'scheduled_at' => $this->nextSlot(),
            'duration_minutes' => 60,
        ], $this->student);

        $cancel = $this->actingAs($this->student)
            ->post(route('student.tutor-lessons.bookings.cancel', $booking));
        $cancel->assertRedirect();

        $this->assertSame(LessonBooking::STATUS_CANCELLED, $booking->fresh()->status);

        $parentNotif = Notification::query()
            ->where('user_id', $this->parent->id)
            ->where('audience', 'parent')
            ->where('title', __('tutor.notif_booking_cancelled_title'))
            ->first();

        $this->assertNotNull($parentNotif);
        $this->assertSame(
            route('parent.tutor-lessons.bookings.show', $booking),
            $parentNotif->action_url
        );
    }

    private function submitAndApproveInstructor(): void
    {
        $this->profile->update([
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'submitted_at' => now(),
            'headline' => 'معلم لغة عربية',
            'bio' => 'نبذة كافية للملف العام',
            'tutor_subject_ids' => [1],
            'tutor_matching_modes' => [StudentLearningProfile::MODE_PICK_TEACHER],
            'tutor_session_types' => [StudentLearningProfile::SESSION_ONE_TO_ONE],
            'offers_tutor_booking' => false,
            'tutor_activated_at' => null,
            'show_on_homepage' => false,
        ]);

        InstructorApplicationService::approve(
            $this->profile->fresh(),
            $this->admin,
            'مقبول للاختبار',
            InstructorProfile::PORTAL_BOTH
        );

        $this->profile->refresh();
    }

    private function seedWeeklyAvailability(): void
    {
        for ($day = 0; $day <= 6; $day++) {
            DB::table('tutor_availabilities')->insert([
                'instructor_id' => $this->instructor->id,
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => '22:00:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function nextSlot(): \Carbon\Carbon
    {
        $slot = now()->addDay()->setTime(10, 0, 0);
        if ($slot->lte(now())) {
            $slot->addDay();
        }

        return $slot;
    }

    private function seedActors(): void
    {
        $this->admin = User::create([
            'name' => 'مدير الاختبار',
            'email' => 'admin.cycle@test.local',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->instructor = User::create([
            'name' => 'معلم الدورة',
            'email' => 'instructor.cycle@test.local',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'طالب الدورة',
            'email' => 'student.cycle@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->otherStudent = User::create([
            'name' => 'طالب آخر',
            'email' => 'student.other@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->parent = User::create([
            'name' => 'ولي أمر الدورة',
            'email' => 'parent.cycle@test.local',
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

        $this->profile = InstructorProfile::create([
            'user_id' => $this->instructor->id,
            'status' => InstructorProfile::STATUS_DRAFT,
            'instructor_portal_mode' => InstructorProfile::PORTAL_BOTH,
            'offers_tutor_booking' => false,
            'show_on_homepage' => false,
        ]);

        StudentLearningProfile::create([
            'user_id' => $this->student->id,
            'matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER,
            'preferred_session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'lesson_hours_quota' => 0,
            'lesson_hours_used' => 0,
            'lesson_hours_bonus' => 0,
            'subject_ids' => [1],
        ]);

        StudentLearningProfile::create([
            'user_id' => $this->otherStudent->id,
            'matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER,
            'preferred_session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'lesson_hours_quota' => 0,
            'lesson_hours_used' => 0,
            'subject_ids' => [1],
        ]);
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
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('profile_image')->nullable();
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
            $table->string('status')->default('draft');
            $table->boolean('show_on_homepage')->default(false);
            $table->string('instructor_portal_mode')->default('both');
            $table->boolean('offers_tutor_booking')->default(false);
            $table->timestamp('tutor_activated_at')->nullable();
            $table->timestamp('tutor_trial_completed_at')->nullable();
            $table->timestamp('tutor_onboarding_completed_at')->nullable();
            $table->json('tutor_matching_modes')->nullable();
            $table->json('tutor_session_types')->nullable();
            $table->json('tutor_subject_ids')->nullable();
            $table->json('tutor_academic_year_ids')->nullable();
            $table->json('application_data')->nullable();
            $table->unsignedInteger('tutor_default_duration_minutes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('student_learning_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('matching_mode')->nullable();
            $table->string('preferred_session_type')->nullable();
            $table->unsignedInteger('lesson_hours_quota')->default(0);
            $table->unsignedInteger('lesson_hours_used')->default(0);
            $table->integer('lesson_hours_bonus')->default(0);
            $table->json('subject_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('tutor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
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
            $table->boolean('hours_deducted')->default(false);
            $table->timestamp('co_presence_started_at')->nullable();
            $table->timestamp('co_presence_ended_at')->nullable();
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
            $table->unsignedInteger('participants_peak')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('settings')->nullable();
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

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('expired');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('features')->nullable();
            $table->json('feature_limits')->nullable();
            $table->string('teacher_plan_key')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
        });

        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advanced_course_id');
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->timestamps();
        });

        Schema::create('instructor_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->string('status')->default('draft');
            $table->string('type')->nullable();
            $table->string('billing_type')->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('advanced_course_id');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('learning_path_instructor', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->text('assigned_courses')->nullable();
        });
    }
}
