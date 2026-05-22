<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * قرص Cloudflare R2 (متوافق S3) — حل مشترك لروابط العرض والرفع.
 */
class CloudStorage
{
    public static function isR2Configured(): bool
    {
        return filled(config('filesystems.disks.r2.bucket'))
            && filled(config('filesystems.disks.r2.endpoint'))
            && filled(config('filesystems.disks.r2.key'))
            && filled(config('filesystems.disks.r2.secret'));
    }

    /**
     * @param  string  $configKey  مفتاح في config/filesystems.php مثل admin_branding_disk
     */
    public static function resolveDisk(string $configKey): string
    {
        $d = (string) config('filesystems.'.$configKey, 'public');

        if ($d === 'r2') {
            if (! self::isR2Configured()) {
                Log::warning("{$configKey}=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.");

                return 'public';
            }
        }

        if ($d === 's3') {
            if (empty(config('filesystems.disks.s3.bucket'))) {
                return 'public';
            }
        }

        if (! in_array($d, ['public', 'r2', 's3'], true)) {
            return 'public';
        }

        return $d;
    }

    public static function publicBaseUrl(string $disk = 'r2'): string
    {
        $base = trim((string) config("filesystems.disks.{$disk}.url"));
        if ($base !== '') {
            return rtrim($base, '/');
        }

        if (in_array($disk, ['r2', 's3'], true)) {
            return rtrim(trim((string) env('R2_PUBLIC_URL', env('AWS_URL', ''))), '/');
        }

        return '';
    }

    public static function hasPublicBaseUrl(string $disk = 'r2'): bool
    {
        return self::publicBaseUrl($disk) !== '';
    }

    /**
     * رابط عرض الملف: CDN (AWS_URL) أو نفس الموقع /storage/ (يعمل بدون symlink ولا exec).
     */
    public static function objectPublicUrl(string $disk, string $path, int $signedMinutes = 10080): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $base = self::publicBaseUrl($disk);
        if ($base !== '') {
            return $base.'/'.$path;
        }

        // بدون AWS_URL: نفس النطاق عبر Laravel (/storage/...) — يجلب من R2 أو المحلي
        if (in_array($disk, ['r2', 's3'], true) && self::preferAppStorageRoute()) {
            return self::localPublicStorageUrl($path);
        }

        if (in_array($disk, ['r2', 's3'], true)) {
            $signed = self::temporarySignedUrl($disk, $path, $signedMinutes);
            if ($signed !== null) {
                return $signed;
            }
        }

        return self::localPublicStorageUrl($path);
    }

    /**
     * عند true (الافتراضي): روابط الصور عبر /storage/ وليس روابط R2 الموقّعة.
     */
    public static function preferAppStorageRoute(): bool
    {
        $fromConfig = config('filesystems.storage_serve_via_app');
        if ($fromConfig !== null) {
            return filter_var($fromConfig, FILTER_VALIDATE_BOOLEAN);
        }

        return filter_var(env('STORAGE_SERVE_VIA_APP', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * هل الملف موجود على أي قرص عام (محلي أو R2)؟
     */
    public static function pathExistsOnAnyDisk(string $path, array $disks = ['public', 'r2', 's3']): bool
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        if ($path === '') {
            return false;
        }

        foreach (array_unique($disks) as $disk) {
            if (! in_array($disk, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($disk)->exists($path)) {
                    return true;
                }
            } catch (\Throwable) {
            }
        }

        return is_file(storage_path('app/public/'.str_replace('/', DIRECTORY_SEPARATOR, $path)));
    }

    /**
     * @return string|null رابط موقّع أو null
     */
    public static function temporarySignedUrl(string $disk, string $path, int $minutes = 10080): ?string
    {
        if (! in_array($disk, ['r2', 's3'], true)) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        if ($path === '') {
            return null;
        }

        try {
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(max(5, $minutes)));
        } catch (\Throwable $e) {
            Log::debug('temporarySignedUrl failed', ['disk' => $disk, 'path' => $path, 'error' => $e->getMessage()]);

            return null;
        }
    }

    public static function localPublicStorageUrl(string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        $req = request();
        if ($req && $req->getSchemeAndHttpHost()) {
            return $req->getSchemeAndHttpHost().'/storage/'.$path;
        }

        return rtrim((string) config('app.url'), '/').'/storage/'.$path;
    }

    /**
     * @return string|null رابط العرض أو null إن لم يُعثر على الملف
     */
    public static function publicUrlForPath(string $configKey, ?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $disk = self::resolveDisk($configKey);

        if (in_array($disk, ['r2', 's3'], true)
            && self::preferAppStorageRoute()
            && ! self::hasPublicBaseUrl($disk)
            && self::pathExistsOnAnyDisk($path)) {
            return self::localPublicStorageUrl($path);
        }

        $existsOnDisk = false;
        try {
            $existsOnDisk = Storage::disk($disk)->exists($path);
        } catch (\Throwable) {
        }

        if (! $existsOnDisk && $disk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    return self::localPublicStorageUrl($path);
                }
            } catch (\Throwable) {
            }

            $legacy = public_path($path);
            if (is_file($legacy)) {
                return asset($path);
            }

            if (in_array($disk, ['r2', 's3'], true)) {
                return self::objectPublicUrl($disk, $path);
            }

            return null;
        }

        if (! $existsOnDisk && $disk === 'public') {
            $legacy = public_path($path);
            if (is_file($legacy)) {
                return asset($path);
            }

            return null;
        }

        if ($disk === 'public') {
            return self::localPublicStorageUrl($path);
        }

        return self::objectPublicUrl($disk, $path);
    }

    /**
     * جلب محتوى ملف من أقراص سحابية أو محلية (للتمرير عبر Laravel).
     *
     * @return array{content: string, mime: string}|null
     */
    public static function readFileContents(string $path, array $disks = ['public', 'r2', 's3']): ?array
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        foreach (array_unique($disks) as $disk) {
            if (! in_array($disk, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (! Storage::disk($disk)->exists($path)) {
                    continue;
                }
                $content = Storage::disk($disk)->get($path);
                if (! is_string($content) || $content === '') {
                    continue;
                }
                $mime = 'application/octet-stream';
                try {
                    $detected = Storage::disk($disk)->mimeType($path);
                    if (is_string($detected) && $detected !== '') {
                        $mime = $detected;
                    }
                } catch (\Throwable) {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mime = match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml',
                        default => $mime,
                    };
                }

                return ['content' => $content, 'mime' => $mime];
            } catch (\Throwable) {
            }
        }

        $legacy = storage_path('app/public/'.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        if (is_file($legacy) && is_readable($legacy)) {
            $mime = @mime_content_type($legacy) ?: 'application/octet-stream';

            return ['content' => (string) file_get_contents($legacy), 'mime' => $mime];
        }

        return null;
    }

    /**
     * رفع ملف محلي موجود إلى R2 (للمزامنة بعد التفعيل).
     */
    public static function copyLocalPublicToR2(string $path): bool
    {
        if (! self::isR2Configured()) {
            return false;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        if ($path === '') {
            return false;
        }

        try {
            if (Storage::disk('r2')->exists($path)) {
                return true;
            }
            if (! Storage::disk('public')->exists($path)) {
                return false;
            }

            $stream = Storage::disk('public')->readStream($path);
            if ($stream === false) {
                return false;
            }

            return Storage::disk('r2')->writeStream($path, $stream, ['visibility' => 'public']);
        } catch (\Throwable $e) {
            Log::warning('copyLocalPublicToR2 failed', ['path' => $path, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
