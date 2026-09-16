<?php

namespace Tests\Feature;

use App\Models\InstructorProfile;
use App\Models\User;
use App\Support\PublicInstructorCatalog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicInstructorCatalogFiltersTest extends TestCase
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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('role')->default('instructor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('approved');
            $table->boolean('show_on_homepage')->default(true);
            $table->boolean('offers_tutor_booking')->default(true);
            $table->timestamp('tutor_activated_at')->nullable();
            $table->json('tutor_subject_ids')->nullable();
            $table->json('tutor_academic_year_ids')->nullable();
            $table->json('tutor_session_types')->nullable();
            $table->unsignedInteger('tutor_years_experience')->nullable();
            $table->json('application_data')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
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

        DB::table('academic_subjects')->insert([
            ['id' => 1, 'name' => 'رياضيات', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'علوم', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('academic_years')->insert([
            ['id' => 10, 'name' => 'ابتدائي', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => 'ثانوي', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_public_filters_narrow_instructors_for_students(): void
    {
        $math = $this->makeProfile('معلم رياضيات', [
            'tutor_subject_ids' => [1],
            'tutor_academic_year_ids' => [10],
            'tutor_session_types' => ['one_to_one'],
            'tutor_years_experience' => 2,
            'application_data' => [
                'teaching' => [
                    'specializations' => ['math'],
                    'curricula' => ['saudi'],
                    'stages' => ['primary'],
                ],
                'video' => ['link' => 'https://www.youtube.com/watch?v=abcdefghijk'],
            ],
        ]);

        $science = $this->makeProfile('معلم علوم', [
            'tutor_subject_ids' => [2],
            'tutor_academic_year_ids' => [20],
            'tutor_session_types' => ['small_group'],
            'tutor_years_experience' => 8,
            'application_data' => [
                'teaching' => [
                    'specializations' => ['sciences'],
                    'curricula' => ['american'],
                    'stages' => ['high'],
                ],
            ],
        ]);

        $profiles = PublicInstructorCatalog::enrichProfiles(collect([$math, $science]));

        $bySubject = PublicInstructorCatalog::applyPublicFilters(
            $profiles,
            Request::create('/', 'GET', ['subject_id' => 1])
        );
        $this->assertCount(1, $bySubject);
        $this->assertSame($math->id, $bySubject->first()->id);

        $byCurriculum = PublicInstructorCatalog::applyPublicFilters(
            $profiles,
            Request::create('/', 'GET', ['curriculum' => 'american'])
        );
        $this->assertCount(1, $byCurriculum);
        $this->assertSame($science->id, $byCurriculum->first()->id);

        $byExperience = PublicInstructorCatalog::applyPublicFilters(
            $profiles,
            Request::create('/', 'GET', ['experience_min' => 5])
        );
        $this->assertCount(1, $byExperience);
        $this->assertSame($science->id, $byExperience->first()->id);

        $byVideo = PublicInstructorCatalog::applyPublicFilters(
            $profiles,
            Request::create('/', 'GET', ['has_video' => 'yes'])
        );
        $this->assertCount(1, $byVideo);
        $this->assertSame($math->id, $byVideo->first()->id);

        $bySearch = PublicInstructorCatalog::applyPublicFilters(
            $profiles,
            Request::create('/', 'GET', ['q' => 'علوم'])
        );
        $this->assertCount(1, $bySearch);
        $this->assertSame($science->id, $bySearch->first()->id);

        $options = PublicInstructorCatalog::publicFilterOptions($profiles);
        $this->assertNotEmpty($options['subjects']);
        $this->assertSame(1, $options['video_count']);
    }

    private function makeProfile(string $name, array $attrs): InstructorProfile
    {
        $user = User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).'@test.local',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
        ]);

        return InstructorProfile::create(array_merge([
            'user_id' => $user->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'show_on_homepage' => true,
            'headline' => $name,
            'bio' => 'نبذة',
            'offers_tutor_booking' => true,
            'tutor_activated_at' => now(),
        ], $attrs))->load('user');
    }
}
