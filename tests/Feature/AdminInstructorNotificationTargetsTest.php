<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\NotificationController;
use App\Models\InstructorProfile;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
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
        [$incomplete, $noProfile, $submitted] = $this->seedInstructors();

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
        $this->createNotificationsTable();
        [$a, $b] = $this->twoActiveInstructors();

        $count = Notification::sendToInstructors([$a->id, $b->id], $this->payload('individual_instructor', $a->id));

        $this->assertSame(2, $count);
        $this->assertSame(2, DB::table('notifications')->count());
        $this->assertEqualsCanonicalizing(
            [$a->id, $b->id],
            DB::table('notifications')->pluck('user_id')->all()
        );
    }

    public function test_target_count_for_multiple_instructors(): void
    {
        [$a, $b] = $this->twoActiveInstructors();
        $controller = app(NotificationController::class);

        $response = $controller->getTargetCount(Request::create('/count', 'GET', [
            'target_type' => 'individual_instructor',
            'target_ids' => [$a->id, $b->id],
        ]));

        $this->assertSame(2, $response->getData(true)['count']);
    }

    public function test_target_count_incomplete_empty_selection_is_zero(): void
    {
        $this->seedInstructors();
        $controller = app(NotificationController::class);

        $response = $controller->getTargetCount(Request::create('/count', 'GET', [
            'target_type' => 'incomplete_instructors',
            'target_ids' => '',
        ]));

        $this->assertSame(0, $response->getData(true)['count']);
    }

    public function test_target_count_incomplete_without_ids_counts_all(): void
    {
        $this->seedInstructors();
        $controller = app(NotificationController::class);

        $response = $controller->getTargetCount(Request::create('/count', 'GET', [
            'target_type' => 'incomplete_instructors',
        ]));

        $this->assertSame(2, $response->getData(true)['count']);
    }

    public function test_resolve_ids_keeps_multiple_instructors_and_drops_students(): void
    {
        [$a, $b] = $this->twoActiveInstructors();
        $student = User::query()->create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $ids = $this->resolveIds(Request::create('/store', 'POST', [
            'target_type' => 'individual_instructor',
            'target_ids' => [$a->id, $b->id, $student->id],
        ]), 'individual_instructor');

        $this->assertEqualsCanonicalizing([$a->id, $b->id], $ids);
    }

    public function test_resolve_incomplete_ids_drops_submitted_applications(): void
    {
        [$incomplete, $noProfile, $submitted] = $this->seedInstructors();

        $ids = $this->resolveIds(Request::create('/store', 'POST', [
            'target_type' => 'incomplete_instructors',
            'target_ids' => [$incomplete->id, $submitted->id, $noProfile->id],
        ]), 'incomplete_instructors');

        $this->assertEqualsCanonicalizing([$incomplete->id, $noProfile->id], $ids);
        $this->assertNotContains($submitted->id, $ids);
    }

    public function test_send_notification_to_selected_and_incomplete_instructors(): void
    {
        $this->createNotificationsTable();
        [$incomplete, $noProfile, $submitted] = $this->seedInstructors();
        $controller = app(NotificationController::class);
        $method = new ReflectionMethod(NotificationController::class, 'sendNotificationToTargets');
        $method->setAccessible(true);

        $selectedCount = $method->invoke(
            $controller,
            'individual_instructor',
            $incomplete->id,
            $this->payload('individual_instructor', $incomplete->id),
            [$incomplete->id, $submitted->id]
        );
        $this->assertSame(2, $selectedCount);

        DB::table('notifications')->delete();

        $incompleteCount = $method->invoke(
            $controller,
            'incomplete_instructors',
            null,
            $this->payload('incomplete_instructors'),
            [$incomplete->id, $noProfile->id]
        );
        $this->assertSame(2, $incompleteCount);
        $this->assertEqualsCanonicalizing(
            [$incomplete->id, $noProfile->id],
            DB::table('notifications')->pluck('user_id')->all()
        );
    }

    /**
     * @return array{0: User, 1: User, 2: User}
     */
    private function seedInstructors(): array
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

        return [$incomplete, $noProfile, $submitted];
    }

    /**
     * @return array{0: User, 1: User}
     */
    private function twoActiveInstructors(): array
    {
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

        return [$a, $b];
    }

    private function createNotificationsTable(): void
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
    }

    /**
     * @return list<int>
     */
    private function resolveIds(Request $request, string $targetType): array
    {
        $method = new ReflectionMethod(NotificationController::class, 'resolveRequestTargetIds');
        $method->setAccessible(true);

        return $method->invoke(app(NotificationController::class), $request, $targetType);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $targetType, ?int $targetId = null): array
    {
        return [
            'sender_id' => 1,
            'title' => 'تذكير',
            'message' => 'أكمل بياناتك',
            'type' => 'instructor',
            'priority' => 'normal',
            'target_type' => $targetType,
            'target_id' => $targetId,
            'audience' => 'instructor',
        ];
    }
}
