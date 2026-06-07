<?php

use App\Support\CloudStorage;
use App\Support\PublicStaticAsset;

if (! function_exists('public_static_url')) {
    /**
     * رابط ملف ثابت في public/ — يُنسخ تلقائياً إلى public_html عند النشر على Hostinger.
     */
    function public_static_url(string $relativePath): string
    {
        return PublicStaticAsset::url($relativePath);
    }
}

if (! function_exists('public_storage_url')) {
    /**
     * رابط عرض ملف مرفوع على storage/app/public أو R2 (دورات، صور، إيصالات…).
     * يمر عبر /storage/ أو /media/ على نفس الموقع — يعمل بدون symlink على الاستضافة المشتركة.
     */
    function public_storage_url(?string $path): ?string
    {
        return CloudStorage::publicUploadUrl($path);
    }
}

if (! function_exists('storage_public_url')) {
    /**
     * رابط عرض ملف مخزّن (public محلي أو R2/S3) حسب قرص محدد في الإعدادات.
     *
     * @param  string  $configKey  مثل admin_branding_disk أو site_services_disk
     */
    function storage_public_url(?string $path, string $configKey = 'site_services_disk'): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim(trim($path), '/'));
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $url = CloudStorage::publicUrlForPath($configKey, $path);

        return $url ?? public_storage_url($path);
    }
}

if (!function_exists('community_disk')) {
    /**
     * قرص تخزين ملفات المجتمع (تقديمات المساهمين).
     * يُفضّل القراءة من .env إن وُجدت لتجنب مشكلة كاش الإعدادات.
     *
     * @return string 'r2' أو 'local'
     */
    function community_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_COMMUNITY');
        if ($envDisk !== null && $envDisk !== '' && in_array($envDisk, ['r2', 'local'], true)) {
            return $envDisk;
        }
        return config('filesystems.community_disk', 'local');
    }
}
