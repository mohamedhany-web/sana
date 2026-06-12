<?php

/**
 * محاكاة إرسال نموذج /tutor/apply — للاختبار اليدوي فقط.
 * php scripts/simulate_tutor_apply.php
 */

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$subjectIds = \App\Models\AcademicSubject::where('is_active', true)->limit(1)->pluck('id')->all();
$yearIds = \App\Models\AcademicYear::where('is_active', true)->limit(1)->pluck('id')->all();

if ($subjectIds === [] || $yearIds === []) {
    fwrite(STDERR, "FAIL: لا توجد مواد أو مراحل نشطة في قاعدة البيانات.\n");
    exit(1);
}

$tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'tutor-apply-sim-'.Str::random(8);
mkdir($tmpDir, 0777, true);

$cvPath = $tmpDir.'/cv.pdf';
file_put_contents($cvPath, "%PDF-1.4\n% Simulated CV\n");

$degreePath = $tmpDir.'/degree.pdf';
file_put_contents($degreePath, "%PDF-1.4\n% Simulated degree\n");

$email = 'tutor-sim-'.Str::lower(Str::random(8)).'@sana-test.local';
$token = 'test-csrf-'.Str::random(16);

$commitments = [];
foreach (array_keys(config('tutor_application.commitments', [])) as $key) {
    $commitments[$key] = '1';
}

$weekly = [];
foreach (array_keys(config('tutor_application.weekdays', [])) as $day) {
    $weekly[$day] = ['periods' => '4م - 8م', 'notes' => ''];
}

$payload = [
    '_token' => $token,
    'name' => 'معلم تجريبي للاختبار',
    'email' => $email,
    'nationality' => 'سعودي',
    'country_city' => 'الرياض، السعودية',
    'country_code' => '+966',
    'phone' => '512345678',
    'linkedin_url' => '',
    'password' => 'TestPass123!',
    'password_confirmation' => 'TestPass123!',
    'degree_qualification' => 'بكالوريوس رياضيات',
    'specialization' => 'رياضيات',
    'years_experience' => '5',
    'last_workplace' => 'مدرسة تجريبية',
    'grades_taught' => 'متوسط وثانوي',
    'curricula_experience_text' => 'المنهج السعودي',
    'headline' => 'معلم رياضيات — ثانوي',
    'bio' => 'نبذة تجريبية لاختبار نموذج التقديم في أكاديمية سنا. خبرة في التدريس الأونلاين والحضوري.',
    'specializations' => ['math'],
    'curricula' => ['saudi'],
    'stages' => ['high'],
    'lesson_formats' => ['one_to_one', 'small_group'],
    'subject_ids' => $subjectIds,
    'academic_year_ids' => $yearIds,
    'matching_modes' => ['pick_teacher'],
    'weekly_availability' => $weekly,
    'tech_skills' => ['zoom', 'google_meet'],
    'demo_video_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'video_topic_title' => 'شرح المعادلة الخطية',
    'video_grade_level' => 'الصف الثالث ثانوي',
    'why_sana' => 'أرغب بالعمل مع فريق محترف.',
    'weak_student_approach' => 'أبدأ من الأساسيات بخطوات صغيرة.',
    'online_interactivity' => 'أسئلة واستطلاعات خلال الحصة.',
    'teaching_tools' => 'سبورة رقمية وZoom',
    'expected_rate' => '150 ريال للحصة',
    'available_start_date' => 'فوراً',
    'commitments' => $commitments,
    'declaration_agreed' => '1',
    'declaration_name' => 'معلم تجريبي للاختبار',
    'declaration_signature' => 'معلم تجريبي للاختبار',
];

$files = [
    'cv' => new UploadedFile($cvPath, 'cv.pdf', 'application/pdf', null, true),
    'degree_photo' => new UploadedFile($degreePath, 'degree.pdf', 'application/pdf', null, true),
];

$request = Illuminate\Http\Request::create(
    '/tutor/apply',
    'POST',
    $payload,
    [],
    $files,
    ['HTTP_ACCEPT' => 'text/html']
);

$request->setLaravelSession($app->make('session')->driver());
$request->session()->start();
$request->session()->put('_token', $token);
$request->headers->set('X-CSRF-TOKEN', $token);

try {
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $location = $response->headers->get('Location', '');
    $body = $response->getContent();

    echo "HTTP Status: {$status}\n";
    echo "Redirect: {$location}\n";

    $user = User::where('email', $email)->first();
    if ($user) {
        $profile = InstructorProfile::where('user_id', $user->id)->first();
        echo "OK: User created id={$user->id} role={$user->role} active=".($user->is_active ? '1' : '0')."\n";
        if ($profile) {
            $appData = $profile->application_data ?? [];
            $docs = $appData['documents'] ?? [];
            echo "OK: Profile id={$profile->id} status={$profile->status}\n";
            echo "CV path: ".($docs['cv'] ?? 'MISSING')."\n";
            echo "Degree path: ".($docs['degree_photo'] ?? 'MISSING')."\n";
            echo "Video link: ".($appData['video']['link'] ?? 'MISSING')."\n";
            if (! empty($docs['cv']) && \App\Services\TutorApplicationStorage::publicUrl($docs['cv'])) {
                echo "OK: CV public URL resolves\n";
            } else {
                echo "WARN: CV URL could not be resolved\n";
            }
        } else {
            echo "FAIL: InstructorProfile not created\n";
            exit(1);
        }
    } else {
        echo "FAIL: User not created\n";
        if (str_contains($body, 'ta-alert-err') || str_contains($body, 'errors')) {
            if (preg_match('/ta-alert-err[^>]*>(.*?)<\/div/s', $body, $m)) {
                echo "Errors: ".strip_tags($m[1])."\n";
            }
        }
        exit(1);
    }

    if ($status >= 300 && $status < 400 && str_contains($location, 'thanks')) {
        echo "\nSUCCESS: Full tutor apply simulation completed.\n";
        exit(0);
    }

    echo "\nWARN: Unexpected response (expected redirect to thanks).\n";
    exit(1);
} catch (Throwable $e) {
    fwrite(STDERR, "EXCEPTION: ".$e->getMessage()."\n".$e->getTraceAsString()."\n");
    exit(1);
} finally {
    @unlink($cvPath);
    @unlink($degreePath);
    @rmdir($tmpDir);
}
