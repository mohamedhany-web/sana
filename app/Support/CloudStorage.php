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

    /**
     * رابط HTTPS عام لملف على r2/s3. يعتمد على AWS_URL (أو R2_PUBLIC_URL) — مطلوب لتفعيل Public Access على R2.
     */
    public static function objectPublicUrl(string $disk, string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $base = trim((string) config("filesystems.disks.{$disk}.url"));
        if ($base === '' && in_array($disk, ['r2', 's3'], true)) {
            $base = trim((string) env('R2_PUBLIC_URL', env('AWS_URL', '')));
        }

        if ($base !== '') {
            return rtrim($base, '/').'/'.$path;
        }

        try {
            $url = Storage::disk($disk)->url($path);
            if (is_string($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                return $url;
            }
        } catch (\Throwable) {
        }

        return self::localPublicStorageUrl($path);
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

            // ملف على R2 قد يكون موجوداً رغم فشل exists() — نُرجع الرابط إن وُجد AWS_URL
            if (in_array($disk, ['r2', 's3'], true) && self::hasPublicBaseUrl($disk)) {
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

    public static function hasPublicBaseUrl(string $disk = 'r2'): bool
    {
        $base = trim((string) config("filesystems.disks.{$disk}.url"));

        return $base !== '' || trim((string) env('R2_PUBLIC_URL', env('AWS_URL', ''))) !== '';
    }
}
