<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CloudStorage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserAvatarServingTest extends TestCase
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
            'filesystems.storage_serve_via_app' => true,
            'filesystems.public_route_prefix' => 'media',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('instructor');
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function test_profile_image_url_uses_avatar_route(): void
    {
        $user = User::query()->create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'profile_image' => 'profile-photos/demo.jpg',
        ]);

        $url = $user->fresh()->profile_image_url;

        $this->assertNotNull($url);
        $this->assertStringContainsString('/avatars/'.$user->id, $url);
    }

    public function test_avatar_route_streams_local_file(): void
    {
        $dir = storage_path('app/public/profile-photos');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $relative = 'profile-photos/avatar-test-'.uniqid('', true).'.jpg';
        $full = storage_path('app/public/'.str_replace('/', DIRECTORY_SEPARATOR, $relative));
        file_put_contents($full, 'fake-image-bytes');

        $user = User::query()->create([
            'name' => 'Teacher',
            'email' => 'avatar@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'profile_image' => $relative,
        ]);

        try {
            $this->get(route('user.avatar', $user))->assertOk();
        } finally {
            @unlink($full);
        }
    }

    public function test_object_public_url_prefers_app_storage_when_enabled(): void
    {
        config([
            'filesystems.storage_serve_via_app' => true,
            'filesystems.disks.r2.url' => 'https://cdn.example.test',
            'filesystems.public_route_prefix' => 'media',
        ]);

        $url = CloudStorage::objectPublicUrl('r2', 'profile-photos/x.jpg');

        $this->assertStringContainsString('/media/profile-photos/x.jpg', $url);
        $this->assertStringNotContainsString('cdn.example.test', $url);
    }
}
