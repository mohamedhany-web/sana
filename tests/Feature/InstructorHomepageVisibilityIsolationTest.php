<?php

namespace Tests\Feature;

use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\TutorInstructorActivationService;
use App\Support\PublicInstructorCatalog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstructorHomepageVisibilityIsolationTest extends TestCase
{
    private User $instructor;

    private InstructorProfile $profile;

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
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createMinimalSchema();

        $this->instructor = User::create([
            'name' => 'معلم تجريبي',
            'email' => 'teacher-vis@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $this->profile = InstructorProfile::create([
            'user_id' => $this->instructor->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'show_on_homepage' => true,
            'headline' => 'معلم لغة عربية',
            'bio' => 'نبذة تعريفية كافية للملف العام',
            'offers_tutor_booking' => true,
            'tutor_activated_at' => now(),
            'tutor_onboarding_completed_at' => now(),
            'tutor_subject_ids' => [1],
            'tutor_matching_modes' => ['pick_teacher'],
            'tutor_session_types' => ['one_to_one'],
        ]);
    }

    public function test_hiding_from_homepage_does_not_change_acceptance_or_activation(): void
    {
        $this->profile->update([
            'show_on_homepage' => false,
            'reviewed_at' => now(),
        ]);

        $fresh = $this->profile->fresh();

        $this->assertFalse($fresh->show_on_homepage);
        $this->assertSame(InstructorProfile::STATUS_APPROVED, $fresh->status);
        $this->assertTrue($fresh->isTutorActivated());
        $this->assertTrue((bool) $fresh->offers_tutor_booking);
        $this->assertNotNull($fresh->tutor_activated_at);
        $this->assertTrue((bool) $fresh->user->is_active);
    }

    public function test_hidden_profile_is_not_publicly_listable_but_stays_activated(): void
    {
        $this->profile->update(['show_on_homepage' => false]);
        $fresh = $this->profile->fresh()->load('user');

        $this->assertFalse(PublicInstructorCatalog::hasMinimumPublicProfile($fresh));
        $this->assertTrue($fresh->isTutorActivated());
        $this->assertSame(InstructorProfile::STATUS_APPROVED, $fresh->status);
    }

    public function test_activation_service_still_requires_approved_status_not_homepage_flag(): void
    {
        $this->profile->update([
            'show_on_homepage' => false,
            'offers_tutor_booking' => false,
            'tutor_activated_at' => null,
        ]);

        DB::table('tutor_availabilities')->insert([
            'instructor_id' => $this->instructor->id,
            'day_of_week' => 1,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ok = TutorInstructorActivationService::attemptAutoActivate($this->profile->fresh(), $this->instructor);
        $this->assertTrue($ok, 'التفعيل يجب أن ينجح رغم إخفاء الملف عن الرئيسية');

        $fresh = $this->profile->fresh();
        $this->assertTrue($fresh->isTutorActivated());
        $this->assertFalse($fresh->show_on_homepage);
        $this->assertSame(InstructorProfile::STATUS_APPROVED, $fresh->status);
    }

    public function test_publishing_to_homepage_does_not_alter_status(): void
    {
        $this->profile->update([
            'show_on_homepage' => false,
            'status' => InstructorProfile::STATUS_APPROVED,
        ]);

        $this->profile->update(['show_on_homepage' => true]);

        $fresh = $this->profile->fresh();
        $this->assertTrue($fresh->show_on_homepage);
        $this->assertSame(InstructorProfile::STATUS_APPROVED, $fresh->status);
        $this->assertTrue($fresh->isTutorActivated());
    }

    public function test_approved_teacher_hidden_from_homepage_still_lists_for_students(): void
    {
        $this->profile->update([
            'show_on_homepage' => false,
            'status' => InstructorProfile::STATUS_APPROVED,
            'instructor_portal_mode' => InstructorProfile::PORTAL_BOTH,
        ]);

        $ids = \App\Services\LessonBookingService::studentVisibleInstructorsQuery()
            ->pluck('id');

        $this->assertTrue($ids->contains($this->profile->id));
        $this->assertFalse(PublicInstructorCatalog::hasMinimumPublicProfile($this->profile->fresh()->load('user')));
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_employee')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('show_on_homepage')->default(false);
            $table->string('instructor_portal_mode')->nullable();
            $table->boolean('offers_tutor_booking')->default(false);
            $table->timestamp('tutor_activated_at')->nullable();
            $table->timestamp('tutor_onboarding_completed_at')->nullable();
            $table->json('tutor_subject_ids')->nullable();
            $table->json('tutor_matching_modes')->nullable();
            $table->json('tutor_session_types')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
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

        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
}
