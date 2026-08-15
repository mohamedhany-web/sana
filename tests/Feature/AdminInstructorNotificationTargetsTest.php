<?php

namespace Tests\Feature;

use App\Models\InstructorProfile;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminInstructorNotificationTargetsTest extends TestCase
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
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_incomplete_signup_query_excludes_submitted_applications(): void
    {
        $incomplete = User::query()->create([
            'name' => 'Draft Tutor',
            'email' => 'draft@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);
        InstructorProfile::query()->create([
            'user_id' => $incomplete->id,
            'status' => InstructorProfile::STATUS_DRAFT,
            'submitted_at' => null,
        ]);

        $noProfile = User::query()->create([
            'name' => 'No Profile',
            'email' => 'noprof@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);

        $submitted = User::query()->create([
            'name' => 'Submitted Tutor',
            'email' => 'submitted@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);
        InstructorProfile::query()->create([
            'user_id' => $submitted->id,
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'submitted_at' => now(),
        ]);

        $ids = InstructorProfile::incompleteSignupUserQuery()->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$incomplete->id, $noProfile->id], $ids);
        $this->assertNotContains($submitted->id, $ids);
    }

    public function test_target_types_include_multi_and_incomplete_instructors(): void
    {
        $types = Notification::getTargetTypes();

        $this->assertArrayHasKey('individual_instructor', $types);
        $this->assertArrayHasKey('incomplete_instructors', $types);
        $this->assertSame('instructor', Notification::audienceForTargetType('incomplete_instructors'));
        $this->assertSame('instructor', Notification::audienceForTargetType('individual_instructor'));
    }

    public function test_send_to_instructors_accepts_multiple_ids(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('priority')->nullable();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('audience')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('data')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->timestamps();
        });

        $a = User::query()->create([
            'name' => 'A',
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);
        $b = User::query()->create([
            'name' => 'B',
            'email' => 'b@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
        ]);

        $count = Notification::sendToInstructors([$a->id, $b->id], [
            'sender_id' => $a->id,
            'title' => 'تذكير',
            'message' => 'أكمل بياناتك',
            'type' => 'instructor',
            'priority' => 'normal',
            'target_type' => 'individual_instructor',
            'audience' => 'instructor',
        ]);

        $this->assertSame(2, $count);
        $this->assertSame(2, DB::table('notifications')->count());
    }
}
