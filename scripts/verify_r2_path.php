<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = $argv[1] ?? '';
if ($path === '') {
    fwrite(STDERR, "usage: php verify_r2_path.php <path>\n");
    exit(1);
}
$exists = Illuminate\Support\Facades\Storage::disk('r2')->exists($path);
echo $exists ? "r2-exists\n" : "r2-missing\n";
exit($exists ? 0 : 1);
