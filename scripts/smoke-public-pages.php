<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$paths = [
    ['GET', '/'],
    ['GET', '/courses'],
    ['GET', '/course/2'],
    ['GET', '/instructors'],
    ['GET', '/pricing'],
    ['GET', '/about'],
    ['GET', '/contact'],
    ['GET', '/contact?topic=assessment'],
    ['GET', '/login'],
    ['GET', '/register'],
    ['GET', '/faq'],
    ['GET', '/help'],
    ['GET', '/certificates'],
    ['GET', '/privacy'],
    ['GET', '/terms'],
    ['GET', '/teacher-policy'],
    ['GET', '/tutor/policy'],
    ['GET', '/how-it-works'],
    ['GET', '/refund'],
    ['GET', '/sitemap.xml'],
];

$failed = [];

foreach ($paths as [$method, $path]) {
    $req = Illuminate\Http\Request::create($path, $method);
    $res = null;
    try {
        $res = $kernel->handle($req);
        $status = $res->getStatusCode();
        $loc = $res->headers->get('Location', '');
        $body = $status >= 500 ? substr($res->getContent(), 0, 500) : '';
        echo str_pad((string) $status, 3, ' ', STR_PAD_LEFT).' '.$path.($loc ? ' -> '.$loc : '').PHP_EOL;
        if ($body !== '') {
            echo '    '.trim(preg_replace('/\s+/', ' ', strip_tags($body))).PHP_EOL;
        }
        if ($status >= 500) {
            $failed[] = "$path => HTTP $status";
        }
    } catch (Throwable $e) {
        echo 'ERR '.$path.' => '.$e->getMessage().PHP_EOL;
        $failed[] = "$path => ".$e->getMessage();
    } finally {
        if ($res !== null) {
            $kernel->terminate($req, $res);
        }
    }
}

// Support classes smoke
$checks = [
    'PublicCourseCatalog::hasPublicCourses' => fn () => App\Support\PublicCourseCatalog::hasPublicCourses(),
    'PublicInstructorCatalog::hasPublicInstructors' => fn () => App\Support\PublicInstructorCatalog::hasPublicInstructors(),
    'PublicTrustMetrics::payload' => fn () => App\Support\PublicTrustMetrics::payload(),
    'PublicSiteCta::payload' => fn () => App\Support\PublicSiteCta::payload(),
    'PublicContactInfo::payload' => fn () => App\Support\PublicContactInfo::payload(),
    'PublicLegalInfo::payload' => fn () => App\Support\PublicLegalInfo::payload(),
    'PlatformFaqDefaults::items' => fn () => count(App\Support\PlatformFaqDefaults::items()),
];

echo PHP_EOL.'--- Support classes ---'.PHP_EOL;
foreach ($checks as $name => $fn) {
    try {
        $fn();
        echo "OK  $name".PHP_EOL;
    } catch (Throwable $e) {
        echo "ERR $name => ".$e->getMessage().PHP_EOL;
        $failed[] = "$name => ".$e->getMessage();
    }
}

// Translation keys
$keys = [
    'public.home_hero_title',
    'public.home_trust_1_title',
    'public.cta_assessment_free',
    'public.cta_whatsapp',
    'public.courses_launch_hint',
    'public.how_it_works_page_title',
];
echo PHP_EOL.'--- Translation keys ---'.PHP_EOL;
foreach ($keys as $key) {
    $val = __($key);
    if ($val === $key) {
        echo "MISSING $key".PHP_EOL;
        $failed[] = "missing translation: $key";
    } else {
        echo "OK  $key".PHP_EOL;
    }
}

// Content spot-checks (home + courses launch)
echo PHP_EOL.'--- Content spot-checks ---'.PHP_EOL;
$contentChecks = [
    ['/', 'منصة', 'home title fragment'],
    ['/', 'احجز تقييم مستوى مجاني', 'home CTA'],
    ['/', 'دفعات تعليمية محدودة', 'home trust stat'],
    ['/courses', 'الدورات المسجلة ستتوفر قريباً', 'courses launch'],
    ['/certificates', 'قابلة للتحقق', 'certificates wording'],
    ['/teacher-policy', '/tutor/policy', 'teacher-policy redirect'],
];
foreach ($contentChecks as [$path, $needle, $label]) {
    $req = Illuminate\Http\Request::create($path, 'GET');
    $res = $kernel->handle($req);
    $body = $res->getContent();
    $loc = $res->headers->get('Location', '');
    $ok = str_contains($body, $needle) || str_contains($loc, $needle);
    echo ($ok ? 'OK  ' : 'FAIL')." $label ($path)".PHP_EOL;
    if (! $ok) {
        $failed[] = "content check failed: $label";
    }
    $kernel->terminate($req, $res);
}

exit($failed === [] ? 0 : 1);
