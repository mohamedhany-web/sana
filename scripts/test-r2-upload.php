<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\CloudStorage;
use Illuminate\Support\Facades\Storage;

$disk = 'r2';
$testPath = 'tests/r2-upload-test-'.date('Y-m-d-His').'.txt';
$content = 'R2 test OK at '.now()->toIso8601String();

echo "admin_branding_disk config: ".config('filesystems.admin_branding_disk')."\n";
echo "USE_CLOUDFLARE_R2: ".(config('filesystems.use_cloudflare_r2') ? 'true' : 'false')."\n";
echo "Bucket: ".config('filesystems.disks.r2.bucket')."\n";
echo "Endpoint: ".config('filesystems.disks.r2.endpoint')."\n";
echo "AWS_URL configured: ".(CloudStorage::hasPublicBaseUrl($disk) ? 'yes' : 'NO — images will not display in browser')."\n\n";

try {
    $ok = Storage::disk($disk)->put($testPath, $content, 'public');
    $exists = Storage::disk($disk)->exists($testPath);
    $url = CloudStorage::objectPublicUrl($disk, $testPath);

    echo "Upload (put): ".($ok ? 'SUCCESS' : 'FAILED')."\n";
    echo "Exists check: ".($exists ? 'yes' : 'no')."\n";
    echo "Public URL: {$url}\n";

    if (CloudStorage::hasPublicBaseUrl($disk)) {
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'method' => 'HEAD']]);
        $headers = @get_headers($url, true, $ctx);
        $status = is_array($headers) ? ($headers[0] ?? 'unknown') : 'unknown';
        echo "HTTP HEAD: {$status}\n";
    }

    Storage::disk($disk)->delete($testPath);
    echo "Cleanup: deleted test file\n";
    echo "\nResult: R2 upload works.\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: ".$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
    exit(1);
}
