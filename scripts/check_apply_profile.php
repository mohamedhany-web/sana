<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = (int) ($argv[1] ?? 0);
$profile = \App\Models\InstructorProfile::with('user')->find($id);
if (! $profile) {
    echo "profile not found\n";
    exit(1);
}
echo "email: ".$profile->user?->email."\n";
echo "status: ".$profile->status."\n";
echo "has application_data: ".(empty($profile->application_data) ? 'no' : 'yes')."\n";
try {
    view('admin.instructor-applications.partials.application-details', ['application' => $profile])->render();
    echo "admin partial: ok\n";
} catch (Throwable $e) {
    echo "admin partial FAIL: ".$e->getMessage()."\n";
    exit(1);
}
