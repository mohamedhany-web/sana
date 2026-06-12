<?php

/**
 * اختبار رفع مرفقات نموذج المعلم إلى Cloudflare R2.
 * php scripts/test_tutor_r2_upload.php
 */

use App\Services\TutorApplicationStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$disk = TutorApplicationStorage::resolvedDisk();
$r2Configured = \App\Support\CloudStorage::isR2Configured();
$configDisk = config('filesystems.tutor_application_disk');

echo "=== Tutor Application R2 Upload Test ===\n";
echo "config tutor_application_disk: {$configDisk}\n";
echo "resolved disk: {$disk}\n";
echo "R2 configured: ".($r2Configured ? 'yes' : 'no')."\n\n";

if ($disk !== 'r2') {
    fwrite(STDERR, "FAIL: Expected disk 'r2', got '{$disk}'\n");
    exit(1);
}

$tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'tutor-r2-test-'.Str::random(6);
mkdir($tmpDir, 0777, true);
$userId = 999999; // test namespace

$fixtures = [
    'cv' => ['cv.pdf', "%PDF-1.4\n% CV R2 test\n", 'application/pdf'],
    'degree' => ['degree.pdf', "%PDF-1.4\n% Degree R2 test\n", 'application/pdf'],
    'demo-video' => ['demo.mp4', "fake-mp4-content-for-r2-test", 'video/mp4'],
    'id' => ['id.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='), 'image/png'],
];

$storedPaths = [];
$failed = false;

foreach ($fixtures as $subdir => [$filename, $content, $mime]) {
    $path = $tmpDir.DIRECTORY_SEPARATOR.$filename;
    file_put_contents($path, $content);
    $upload = new UploadedFile($path, $filename, $mime, null, true);

    try {
        $stored = TutorApplicationStorage::store($upload, $userId, $subdir);
        $storedPaths[$subdir] = $stored;

        $onR2 = Storage::disk('r2')->exists($stored);
        $onPublic = Storage::disk('public')->exists($stored);
        $url = TutorApplicationStorage::publicUrl($stored);

        echo "[{$subdir}]\n";
        echo "  path: {$stored}\n";
        echo "  on R2: ".($onR2 ? 'YES' : 'NO')."\n";
        echo "  on public (should be NO): ".($onPublic ? 'YES - BAD' : 'no')."\n";
        echo "  public URL: ".($url ?: '(none)')."\n";

        if (! $onR2 || $onPublic) {
            $failed = true;
            echo "  => FAIL\n";
        } else {
            echo "  => OK\n";
        }
        echo "\n";
    } catch (Throwable $e) {
        $failed = true;
        echo "[{$subdir}] EXCEPTION: ".$e->getMessage()."\n\n";
    }
}

// Full form simulation with real file uploads (no video link)
echo "=== Full form POST with file uploads ===\n";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$subjectIds = \App\Models\AcademicSubject::where('is_active', true)->limit(1)->pluck('id')->all();
$yearIds = \App\Models\AcademicYear::where('is_active', true)->limit(1)->pluck('id')->all();
$email = 'tutor-r2-'.Str::lower(Str::random(8)).'@sana-test.local';
$token = 'r2-test-'.Str::random(16);

$commitments = [];
foreach (array_keys(config('tutor_application.commitments', [])) as $key) {
    $commitments[$key] = '1';
}
$weekly = [];
foreach (array_keys(config('tutor_application.weekdays', [])) as $day) {
    $weekly[$day] = ['periods' => '5-9pm', 'notes' => ''];
}

$formCv = $tmpDir.'/form-cv.pdf';
$formDeg = $tmpDir.'/form-deg.pdf';
$formVid = $tmpDir.'/form-demo.mp4';
file_put_contents($formCv, "%PDF-1.4\n% form cv\n");
file_put_contents($formDeg, "%PDF-1.4\n% form deg\n");
file_put_contents($formVid, hex2bin('000000186674797069736F6D0000020069736F6D69736F32617663316D703431'));

$payload = [
    '_token' => $token,
    'name' => 'R2 Upload Test',
    'email' => $email,
    'nationality' => 'Saudi',
    'country_city' => 'Riyadh',
    'country_code' => '+966',
    'phone' => '599999999',
    'password' => 'TestPass123!',
    'password_confirmation' => 'TestPass123!',
    'degree_qualification' => 'BSc',
    'specialization' => 'Math',
    'years_experience' => '4',
    'last_workplace' => 'Test School',
    'grades_taught' => 'High',
    'curricula_experience_text' => 'Saudi',
    'headline' => 'R2 test tutor',
    'bio' => 'Testing Cloudflare R2 file uploads for tutor application form.',
    'specializations' => ['math'],
    'curricula' => ['saudi'],
    'stages' => ['high'],
    'lesson_formats' => ['one_to_one'],
    'subject_ids' => $subjectIds,
    'academic_year_ids' => $yearIds,
    'matching_modes' => ['pick_teacher'],
    'weekly_availability' => $weekly,
    'tech_skills' => ['zoom'],
    'video_topic_title' => 'R2 video test',
    'video_grade_level' => 'Grade 11',
    'why_sana' => 'R2 test',
    'weak_student_approach' => 'R2 test',
    'online_interactivity' => 'R2 test',
    'teaching_tools' => 'R2 test',
    'expected_rate' => '100',
    'available_start_date' => 'now',
    'commitments' => $commitments,
    'declaration_agreed' => '1',
    'declaration_name' => 'R2 Upload Test',
    'declaration_signature' => 'R2 Upload Test',
];

$files = [
    'cv' => new UploadedFile($formCv, 'cv.pdf', 'application/pdf', null, true),
    'degree_photo' => new UploadedFile($formDeg, 'degree.pdf', 'application/pdf', null, true),
    'demo_video' => new UploadedFile($formVid, 'demo.mp4', 'video/mp4', null, true),
];

$request = Illuminate\Http\Request::create('/tutor/apply', 'POST', $payload, [], $files);
$request->setLaravelSession($app->make('session')->driver());
$request->session()->start();
$request->session()->put('_token', $token);

try {
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $location = $response->headers->get('Location', '');
    echo "HTTP: {$status}\n";
    echo "Redirect: {$location}\n";

    $user = \App\Models\User::where('email', $email)->first();
    if (! $user) {
        echo "FAIL: user not created\n";
        if ($status === 302 && ! str_contains($location, 'thanks')) {
            echo "Likely validation redirect back to form\n";
        }
        if ($status === 422 || str_contains($response->getContent(), 'ta-alert-err')) {
            if (preg_match('/ta-alert-err[^>]*>(.*?)<\/div/s', $response->getContent(), $m)) {
                echo "Errors: ".trim(strip_tags($m[1]))."\n";
            }
        }
        // Try validate directly for diagnostics
        try {
            \App\Services\TutorApplicationFormService::validate($request);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            echo "Validation: ".json_encode($ve->errors(), JSON_UNESCAPED_UNICODE)."\n";
        }
        $failed = true;
    } else {
        $profile = \App\Models\InstructorProfile::where('user_id', $user->id)->first();
        $docs = $profile?->application_data['documents'] ?? [];
        $videoPath = $profile?->application_data['video']['file_path'] ?? null;

        $checks = [
            'cv' => $docs['cv'] ?? null,
            'degree_photo' => $docs['degree_photo'] ?? null,
            'demo_video' => $videoPath,
        ];

        foreach ($checks as $label => $path) {
            if (! $path) {
                echo "FAIL: {$label} path missing in DB\n";
                $failed = true;
                continue;
            }
            $onR2 = Storage::disk('r2')->exists($path);
            $onPublic = Storage::disk('public')->exists($path);
            echo "{$label}: {$path}\n";
            echo "  R2: ".($onR2 ? 'YES' : 'NO')." | public: ".($onPublic ? 'YES' : 'no')."\n";
            if (! $onR2 || $onPublic) {
                $failed = true;
            }
        }
    }
} catch (Throwable $e) {
    echo "FORM EXCEPTION: ".$e->getMessage()."\n";
    $failed = true;
}

// Cleanup test files from R2
echo "\n=== Cleanup test R2 objects ===\n";
foreach ($storedPaths as $subdir => $path) {
    TutorApplicationStorage::delete($path);
    echo "deleted {$subdir}: ".(Storage::disk('r2')->exists($path) ? 'still exists' : 'ok')."\n";
}

foreach (glob($tmpDir.'/*') ?: [] as $f) {
    @unlink($f);
}
@rmdir($tmpDir);

echo "\n".($failed ? "OVERALL: FAIL\n" : "OVERALL: SUCCESS — all files on Cloudflare R2\n");
exit($failed ? 1 : 0);
