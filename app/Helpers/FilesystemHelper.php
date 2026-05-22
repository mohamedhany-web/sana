<?php

use App\Support\CloudStorage;

if (! function_exists('storage_public_url')) {
    /**
     * رابط عرض ملف مخزّن (public محلي أو R2/S3).
     *
     * @param  string  $configKey  مثل admin_branding_disk أو site_services_disk
     */
    function storage_public_url(?string $path, string $configKey = 'site_services_disk'): ?string
    {
        return CloudStorage::publicUrlForPath($configKey, $path);
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
