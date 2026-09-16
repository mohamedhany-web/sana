<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\InstructorApplicationsController;
use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminInstructorApplicationsFiltersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
            'cache.default' => 'array',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createMinimalSchema();
    }

    public function test_filters_by_status_account_and_subject(): void
    {
        [$pendingMath, $approvedScience, $inactive] = $this->seedProfiles();

        $controller = app(InstructorApplicationsController::class);

        $statusQuery = InstructorProfile::query();
        $controller->applyIndexFilters($statusQuery, Request::create('/', 'GET', [
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $statusQuery->pluck('id')->all());

        $accountQuery = InstructorProfile::query();
        $controller->applyIndexFilters($accountQuery, Request::create('/', 'GET', [
            'account' => 'inactive',
        ]));
        $this->assertEqualsCanonicalizing([$inactive->id], $accountQuery->pluck('id')->all());

        $subjectQuery = InstructorProfile::query();
        $controller->applyIndexFilters($subjectQuery, Request::create('/', 'GET', [
            'subject_id' => 1,
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $subjectQuery->pluck('id')->all());

        $this->assertContains($approvedScience->id, InstructorProfile::query()->pluck('id')->all());
    }

    public function test_filters_by_application_data_and_search(): void
    {
        [$pendingMath, $approvedScience] = array_slice($this->seedProfiles(), 0, 2);

        $controller = app(InstructorApplicationsController::class);

        $specQuery = InstructorProfile::query();
        $controller->applyIndexFilters($specQuery, Request::create('/', 'GET', [
            'specialization' => 'math',
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $specQuery->pluck('id')->all());

        $curriculumQuery = InstructorProfile::query();
        $controller->applyIndexFilters($curriculumQuery, Request::create('/', 'GET', [
            'curriculum' => 'saudi',
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $curriculumQuery->pluck('id')->all());

        $stageQuery = InstructorProfile::query();
        $controller->applyIndexFilters($stageQuery, Request::create('/', 'GET', [
            'stage' => 'high',
        ]));
        $this->assertEqualsCanonicalizing([$approvedScience->id], $stageQuery->pluck('id')->all());

        $nationalityQuery = InstructorProfile::query();
        $controller->applyIndexFilters($nationalityQuery, Request::create('/', 'GET', [
            'nationality' => 'مصري',
        ]));
        $this->assertEqualsCanonicalizing([$approvedScience->id], $nationalityQuery->pluck('id')->all());

        $searchQuery = InstructorProfile::query();
        $controller->applyIndexFilters($searchQuery, Request::create('/', 'GET', [
            'search' => 'الرياض',
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $searchQuery->pluck('id')->all());

        $videoQuery = InstructorProfile::query();
        $controller->applyIndexFilters($videoQuery, Request::create('/', 'GET', [
            'has_video' => 'yes',
        ]));
        $this->assertEqualsCanonicalizing([$pendingMath->id], $videoQuery->pluck('id')->all());

        $bookingQuery = InstructorProfile::query();
        $controller->applyIndexFilters($bookingQuery, Request::create('/', 'GET', [
            'booking' => 'activated',
        ]));
        $this->assertEqualsCanonicalizing([$approvedScience->id], $bookingQuery->pluck('id')->all());

        $experienceQuery = InstructorProfile::query();
        $controller->applyIndexFilters($experienceQuery, Request::create('/', 'GET', [
            'experience_min' => 8,
        ]));
        $this->assertEqualsCanonicalizing([$approvedScience->id], $experienceQuery->pluck('id')->all());
    }

    /**
     * @return array{0: InstructorProfile, 1: InstructorProfile, 2: InstructorProfile}
     */
    private function seedProfiles(): array
    {
        $pendingUser = User::create([
            'name' => 'معلم رياضيات',
            'email' => 'math@test.local',
            'phone' => '0501111111',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $approvedUser = User::create([
            'name' => 'معلم علوم',
            'email' => 'science@test.local',
            'phone' => '0502222222',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $inactiveUser = User::create([
            'name' => 'معلم موقوف',
            'email' => 'off@test.local',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => false,
        ]);

        $pendingMath = InstructorProfile::create([
            'user_id' => $pendingUser->id,
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'submitted_at' => now()->subDay(),
            'headline' => 'معلم رياضيات بالرياض',
            'tutor_subject_ids' => [1],
            'tutor_academic_year_ids' => [10],
            'tutor_years_experience' => 3,
            'offers_tutor_booking' => false,
            'application_data' => [
                'personal' => [
                    'nationality' => 'سعودي',
                    'country_city' => 'الرياض',
                ],
                'teaching' => [
                    'specializations' => ['math'],
                    'curricula' => ['saudi'],
                    'stages' => ['primary'],
                    'lesson_formats' => ['one_to_one'],
                ],
                'video' => [
                    'link' => 'https://youtube.com/watch?v=demo',
                ],
            ],
        ]);

        $approvedScience = InstructorProfile::create([
            'user_id' => $approvedUser->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'submitted_at' => now()->subDays(2),
            'headline' => 'معلم علوم',
            'tutor_subject_ids' => [2],
            'tutor_academic_year_ids' => [20],
            'tutor_years_experience' => 10,
            'offers_tutor_booking' => true,
            'tutor_activated_at' => now(),
            'show_on_homepage' => true,
            'application_data' => [
                'personal' => [
                    'nationality' => 'مصري',
                    'country_city' => 'القاهرة',
                ],
                'teaching' => [
                    'specializations' => ['sciences'],
                    'curricula' => ['american'],
                    'stages' => ['high'],
                    'lesson_formats' => ['small_group'],
                ],
                'video' => [
                    'link' => '',
                    'file_path' => '',
                ],
            ],
        ]);

        $inactive = InstructorProfile::create([
            'user_id' => $inactiveUser->id,
            'status' => InstructorProfile::STATUS_REJECTED,
            'submitted_at' => now()->subDays(5),
            'headline' => 'موقوف',
            'tutor_subject_ids' => [3],
            'offers_tutor_booking' => false,
        ]);

        return [$pendingMath, $approvedScience, $inactive];
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('instructor');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_employee')->default(false);
            $table->timestamps();
        });

        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('show_on_homepage')->default(false);
            $table->string('instructor_portal_mode')->default('both');
            $table->boolean('offers_tutor_booking')->default(false);
            $table->timestamp('tutor_activated_at')->nullable();
            $table->json('tutor_matching_modes')->nullable();
            $table->json('tutor_session_types')->nullable();
            $table->json('tutor_subject_ids')->nullable();
            $table->json('tutor_academic_year_ids')->nullable();
            $table->unsignedInteger('tutor_years_experience')->nullable();
            $table->json('application_data')->nullable();
            $table->json('application_evaluation')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }
}
