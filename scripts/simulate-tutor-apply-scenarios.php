<?php

/**
 * Full tutor-apply scenario simulation (MySQL + transaction rollback).
 * Run: php scripts/simulate-tutor-apply-scenarios.php
 */

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\TutorApplicationStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config([
    'filesystems.tutor_application_disk' => 'public',
    'filesystems.use_cloudflare_r2' => false,
]);

Storage::fake('public');
Notification::fake();

$results = [];
$pass = 0;
$fail = 0;

$scenario = function (string $name, callable $fn) use (&$results, &$pass, &$fail): void {
    DB::beginTransaction();
    Auth::logout();
    try {
        $fn();
        $results[] = ['ok' => true, 'name' => $name];
        $pass++;
        echo "[PASS] {$name}\n";
    } catch (Throwable $e) {
        $results[] = ['ok' => false, 'name' => $name, 'detail' => $e->getMessage()];
        $fail++;
        echo "[FAIL] {$name}\n       -> ".$e->getMessage()."\n";
    } finally {
        DB::rollBack();
        Auth::logout();
    }
};

function assert_true(bool $cond, string $msg): void
{
    if (! $cond) {
        throw new RuntimeException($msg);
    }
}

function seedCatalog(): array
{
    $year = AcademicYear::query()->where('is_active', true)->orderBy('id')->first();
    if (! $year) {
        $year = AcademicYear::create([
            'name' => 'Sim Year '.Str::random(4),
            'code' => 'SIM'.Str::upper(Str::random(4)),
            'order' => 99,
            'is_active' => true,
        ]);
    }

    $subject = AcademicSubject::query()->where('is_active', true)->orderBy('id')->first();
    if (! $subject) {
        $subject = AcademicSubject::create([
            'academic_year_id' => $year->id,
            'name' => 'Sim Subject',
            'code' => 'SIMSUB'.Str::upper(Str::random(3)),
            'order' => 1,
            'is_active' => true,
        ]);
    }

    return [$year, $subject];
}

function pdfFile(string $name = 'doc.pdf'): UploadedFile
{
    return UploadedFile::fake()->create($name, 120, 'application/pdf');
}

function imageFile(string $name = 'photo.jpg'): UploadedFile
{
    return UploadedFile::fake()->image($name, 400, 300);
}

function videoFile(string $name = 'demo.mp4'): UploadedFile
{
    return UploadedFile::fake()->create($name, 500, 'video/mp4');
}

function basePayload(AcademicYear $year, AcademicSubject $subject, array $overrides = []): array
{
    $commitments = [];
    foreach (array_keys(config('tutor_application.commitments', [])) as $key) {
        $commitments[$key] = '1';
    }

    $payload = [
        'name' => 'Sim Tutor '.Str::random(5),
        'email' => 'sim.'.Str::lower(Str::random(8)).'@example.test',
        'nationality' => 'Saudi',
        'country_city' => 'Riyadh',
        'country_code' => '+966',
        'phone' => '5'.random_int(10000000, 99999999),
        'linkedin_url' => null,
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'degree_qualification' => 'Bachelor',
        'specialization' => 'Math',
        'years_experience' => 3,
        'last_workplace' => 'Sim School',
        'grades_taught' => 'Middle and High',
        'curricula_experience_text' => 'Saudi curriculum',
        'headline' => 'Math teacher - High school',
        'bio' => 'Enough bio text for simulation validation path.',
        'specializations' => ['math'],
        'specializations_other' => 'none',
        'curricula' => ['saudi'],
        'stages' => ['middle', 'high'],
        'lesson_formats' => ['one_to_one'],
        'subject_ids' => [$subject->id],
        'academic_year_ids' => [$year->id],
        'matching_modes' => ['pick_teacher'],
        'tech_skills' => ['zoom', 'google_meet'],
        'video_topic_title' => 'Equations intro',
        'video_grade_level' => 'High',
        'demo_video_link' => null,
        'video_use_external_link' => '0',
        'why_sana' => 'I want to join Sana academy for online tutoring.',
        'weak_student_approach' => 'Start from foundations with gradual practice.',
        'online_interactivity' => 'Live questions and interactive board.',
        'teaching_tools' => 'Zoom Canva PowerPoint',
        'expected_rate' => '150 SAR / hour',
        'available_start_date' => 'Immediately',
        'commitments' => $commitments,
        'declaration_agreed' => '1',
        'declaration_name' => 'Sim Tutor',
        'declaration_signature' => 'Sim Tutor',
        'cv' => pdfFile('cv.pdf'),
        'degree_photo' => imageFile('degree.jpg'),
        'id_photo' => imageFile('id.jpg'),
        'experience_certs' => pdfFile('exp.pdf'),
        'training_certs' => pdfFile('train.pdf'),
        'portfolio_file' => pdfFile('portfolio.pdf'),
        'demo_video' => videoFile('demo.mp4'),
    ];

    return array_replace_recursive($payload, $overrides);
}

function postApply(array $payload)
{
    $files = array_filter([
        'cv' => $payload['cv'] ?? null,
        'degree_photo' => $payload['degree_photo'] ?? null,
        'id_photo' => $payload['id_photo'] ?? null,
        'experience_certs' => $payload['experience_certs'] ?? null,
        'training_certs' => $payload['training_certs'] ?? null,
        'portfolio_file' => $payload['portfolio_file'] ?? null,
        'demo_video' => $payload['demo_video'] ?? null,
    ]);

    $input = collect($payload)->except(array_keys($files))->all();

    $request = Illuminate\Http\Request::create('/tutor/apply', 'POST', $input, [], $files);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'text/html');

    try {
        return app(\App\Http\Controllers\Public\TutorApplyController::class)->store($request);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()
            ->to(route('tutor.apply'))
            ->withErrors($e->errors())
            ->withInput(collect($input)->except(['password', 'password_confirmation'])->all());
    }
}

echo "=== Tutor apply scenario simulation ===\n";
echo 'DB: '.config('database.connections.'.config('database.default').'.database')."\n";
echo 'Disk: '.TutorApplicationStorage::resolvedDisk()."\n\n";

[$year, $subject] = seedCatalog();

$scenario('01 success with uploaded video file', function () use ($year, $subject) {
    $payload = basePayload($year, $subject);
    $email = $payload['email'];
    $fullPhone = $payload['country_code'].$payload['phone'];

    $response = postApply($payload);
    assert_true(in_array($response->getStatusCode(), [302, 303], true), 'Expected redirect, got '.$response->getStatusCode());

    $user = User::where('email', $email)->first();
    assert_true($user !== null, 'User not created');
    assert_true($user->role === 'instructor', 'Role should be instructor');
    assert_true((int) $user->is_active === 0, 'User should be inactive');
    assert_true($user->phone === $fullPhone, 'Full phone mismatch: '.$user->phone);

    $profile = InstructorProfile::where('user_id', $user->id)->first();
    assert_true($profile !== null, 'Profile missing');
    assert_true($profile->status === InstructorProfile::STATUS_PENDING_REVIEW, 'Status not pending_review');
    assert_true(! empty($profile->application_data['video']['file_path'] ?? null), 'video.file_path missing');
});

$scenario('02 success with external video link only', function () use ($year, $subject) {
    $payload = basePayload($year, $subject, [
        'demo_video_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'video_use_external_link' => '1',
    ]);
    unset($payload['demo_video']);

    $response = postApply($payload);
    assert_true(in_array($response->getStatusCode(), [302, 303], true), 'Expected redirect, got '.$response->getStatusCode());

    $user = User::where('email', $payload['email'])->first();
    assert_true($user !== null, 'User not created for link scenario');
    $data = $user->instructorProfile->application_data ?? [];
    assert_true(($data['video']['link'] ?? null) === 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'External video link not saved');
    assert_true(empty($data['video']['file_path']), 'File path should be empty for link-only');
});

$scenario('03 reject when no video and no link', function () use ($year, $subject) {
    $payload = basePayload($year, $subject, [
        'demo_video_link' => null,
        'video_use_external_link' => '0',
    ]);
    unset($payload['demo_video']);

    $before = User::count();
    $response = postApply($payload);
    assert_true(in_array($response->getStatusCode(), [302, 422], true), 'Expected validation redirect');
    assert_true(User::count() === $before, 'User should not be created');
});

$scenario('04 reject duplicate email (student account)', function () use ($year, $subject) {
    $existing = User::create([
        'name' => 'Existing Student',
        'email' => 'dup.email.'.Str::random(6).'@example.test',
        'phone' => '+9665'.random_int(10000000, 99999999),
        'password' => Hash::make('Password123!'),
        'role' => 'student',
        'is_active' => true,
    ]);

    $payload = basePayload($year, $subject, ['email' => $existing->email]);
    postApply($payload);
    assert_true(User::where('email', $existing->email)->count() === 1, 'Duplicate email user created');
});

$scenario('05 pending instructor email resumes without new user', function () use ($year, $subject) {
    $email = 'pending.'.Str::random(6).'@example.test';
    $user = User::create([
        'name' => 'Pending Tutor',
        'email' => $email,
        'phone' => '+9665'.random_int(10000000, 99999999),
        'password' => Hash::make('Password123!'),
        'role' => 'instructor',
        'is_active' => false,
    ]);
    InstructorProfile::create([
        'user_id' => $user->id,
        'headline' => 'Tutor',
        'bio' => 'bio',
        'status' => InstructorProfile::STATUS_PENDING_REVIEW,
        'submitted_at' => now(),
        'application_data' => [],
    ]);

    $before = User::count();
    $payload = basePayload($year, $subject, ['email' => $email]);
    $response = postApply($payload);
    assert_true($response->isRedirect(), 'Expected redirect');
    assert_true(User::count() === $before, 'Should not create another user');
});

$scenario('06 duplicate phone error on phone field (NOT demo_video)', function () use ($year, $subject) {
    $phoneNational = '5'.random_int(10000000, 99999999);
    $fullPhone = '+966'.$phoneNational;

    User::create([
        'name' => 'Phone Owner',
        'email' => 'phone.owner.'.Str::random(5).'@example.test',
        'phone' => $fullPhone,
        'password' => Hash::make('Password123!'),
        'role' => 'student',
        'is_active' => true,
    ]);

    $payload = basePayload($year, $subject, [
        'country_code' => '+966',
        'phone' => $phoneNational,
    ]);

    $before = User::where('role', 'instructor')->count();

    $files = [
        'cv' => $payload['cv'],
        'degree_photo' => $payload['degree_photo'],
        'id_photo' => $payload['id_photo'],
        'experience_certs' => $payload['experience_certs'],
        'training_certs' => $payload['training_certs'],
        'portfolio_file' => $payload['portfolio_file'],
        'demo_video' => $payload['demo_video'],
    ];
    $input = collect($payload)->except(array_keys($files))->all();
    $request = Illuminate\Http\Request::create('/tutor/apply', 'POST', $input, [], $files);
    $request->setLaravelSession(app('session.store'));

    try {
        app(\App\Http\Controllers\Public\TutorApplyController::class)->store($request);
        throw new RuntimeException('Expected ValidationException for duplicate phone');
    } catch (\Illuminate\Validation\ValidationException $e) {
        $errors = $e->errors();
        assert_true(isset($errors['phone']), 'Error should be on phone, got: '.json_encode(array_keys($errors)));
        assert_true(! isset($errors['demo_video']), 'Must NOT attribute phone duplicate to demo_video');
        $msg = $errors['phone'][0] ?? '';
        assert_true(! str_contains($msg, 'SQLSTATE'), 'Must not expose SQL: '.$msg);
    }

    assert_true(User::where('role', 'instructor')->count() === $before, 'No new instructor on phone conflict');
});

$scenario('07 pending instructor phone resumes without new user', function () use ($year, $subject) {
    $phoneNational = '5'.random_int(10000000, 99999999);
    $fullPhone = '+966'.$phoneNational;
    $user = User::create([
        'name' => 'Pending Phone Tutor',
        'email' => 'pend.phone.'.Str::random(5).'@example.test',
        'phone' => $fullPhone,
        'password' => Hash::make('Password123!'),
        'role' => 'instructor',
        'is_active' => false,
    ]);
    InstructorProfile::create([
        'user_id' => $user->id,
        'headline' => 'Tutor',
        'bio' => 'bio',
        'status' => InstructorProfile::STATUS_PENDING_REVIEW,
        'submitted_at' => now(),
        'application_data' => [],
    ]);

    $before = User::count();
    $payload = basePayload($year, $subject, [
        'country_code' => '+966',
        'phone' => $phoneNational,
        'email' => 'other.'.Str::random(5).'@example.test',
    ]);
    $response = postApply($payload);
    assert_true($response->isRedirect(), 'Expected redirect');
    assert_true(User::count() === $before, 'No extra user for pending phone');
});

$scenario('08 admin form preview renders preview mode', function () {
    $admin = User::create([
        'name' => 'Admin Sim',
        'email' => 'admin.sim.'.Str::random(5).'@example.test',
        'phone' => '+9665'.random_int(10000000, 99999999),
        'password' => Hash::make('Password123!'),
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    Auth::login($admin);
    $view = app(\App\Http\Controllers\Admin\InstructorApplicationsController::class)->formPreview();
    assert_true($view instanceof \Illuminate\View\View, 'Expected view from formPreview');
    $html = $view->render();
    assert_true(str_contains($html, 'وضع المعاينة'), 'Preview banner missing');
});

$scenario('09 reject mismatched password confirmation', function () use ($year, $subject) {
    $payload = basePayload($year, $subject, [
        'password' => 'Password123!',
        'password_confirmation' => 'Different999!',
    ]);
    $before = User::count();
    postApply($payload);
    assert_true(User::count() === $before, 'User must not be created');
});

$scenario('10 uploaded attachments exist on public disk', function () use ($year, $subject) {
    $payload = basePayload($year, $subject);
    $response = postApply($payload);
    assert_true($response->isRedirect(), 'Expected success redirect');
    $user = User::where('email', $payload['email'])->first();
    assert_true($user !== null, 'User missing');
    $path = $user->instructorProfile->application_data['video']['file_path'] ?? null;
    assert_true(is_string($path) && $path !== '', 'video path missing');
    assert_true(Storage::disk('public')->exists($path), 'Stored video file not found: '.$path);
});

echo "\n=== RESULT: {$pass} passed / {$fail} failed ===\n";
exit($fail > 0 ? 1 : 0);
