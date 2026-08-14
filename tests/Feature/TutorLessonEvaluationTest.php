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
            $table->unsignedInteger('billable_minutes')->default(0);
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
