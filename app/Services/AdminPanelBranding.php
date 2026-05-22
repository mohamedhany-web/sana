<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\CloudStorage;
use App\Support\PublicStorageLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminPanelBranding
{
    public const SETTING_KEY = 'admin_panel_logo_path';

    /** نسخة ثابتة في public — تعمل مثل /images/hero-intro.png على Hostinger */
    public const WEB_LOGO_BASENAME = 'images/branding/admin-panel-logo';

    public static function resolvedDisk(): string
    {
        return CloudStorage::resolveDisk('admin_branding_disk');
    }

    public static function logoPublicUrl(): ?string
    {
        $webRel = self::publicWebLogoRelativePath();
        if ($webRel !== null) {
            return asset($webRel);
        }

        $path = Setting::getValue(self::SETTING_KEY);

        return CloudStorage::publicUrlForPath('admin_branding_disk', is_string($path) ? $path : null);
    }

    /**
     * مسار نسبي داخل public إن وُجدت نسخة الشعار (png/webp/jpg/gif).
     */
    public static function publicWebLogoRelativePath(): ?string
    {
        foreach (['png', 'webp', 'jpg', 'jpeg', 'gif'] as $ext) {
            $rel = self::WEB_LOGO_BASENAME.'.'.$ext;
            foreach (PublicStorageLink::webDocumentRoots() as $root) {
                if (is_file($root.'/'.str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
                    return $rel;
                }
            }
        }

        return null;
    }

    /**
     * نسخ الشعار من التخزين (محلي/R2) إلى images/branding في كل جذر ويب.
     */
    public static function publishWebLogo(?string $storagePath = null): bool
    {
        $storagePath = $storagePath ?? Setting::getValue(self::SETTING_KEY);
        if (! is_string($storagePath) || $storagePath === '') {
            return false;
        }

        $storagePath = str_replace('\\', '/', ltrim($storagePath, '/'));
        $binary = null;
        $ext = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION) ?: 'png');

        foreach (array_unique([self::resolvedDisk(), 'public', 'r2', 's3']) as $disk) {
            if (! in_array($disk, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($disk)->exists($storagePath)) {
                    $binary = Storage::disk($disk)->get($storagePath);
                    break;
                }
            } catch (\Throwable) {
            }
        }

        if (! is_string($binary) || $binary === '') {
            $local = storage_path('app/public/'.str_replace('/', DIRECTORY_SEPARATOR, $storagePath));
            if (is_file($local) && is_readable($local)) {
                $binary = (string) file_get_contents($local);
            }
        }

        if (! is_string($binary) || $binary === '') {
            return false;
        }

        if (! in_array($ext, ['png', 'webp', 'jpg', 'jpeg', 'gif'], true)) {
            $ext = 'png';
        }

        $rel = self::WEB_LOGO_BASENAME.'.'.$ext;

        foreach (PublicStorageLink::webDocumentRoots() as $root) {
            $dest = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
            File::ensureDirectoryExists(dirname($dest));
            @file_put_contents($dest, $binary);
        }

        foreach (['png', 'webp', 'jpg', 'jpeg', 'gif'] as $oldExt) {
            if ($oldExt === $ext) {
                continue;
            }
            $oldRel = self::WEB_LOGO_BASENAME.'.'.$oldExt;
            foreach (PublicStorageLink::webDocumentRoots() as $root) {
                $oldFile = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $oldRel);
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
        }

        return true;
    }

    public static function removeLogo(): void
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            self::deletePhysicalFile($path);
        }
        foreach (['png', 'webp', 'jpg', 'jpeg', 'gif'] as $ext) {
            $rel = self::WEB_LOGO_BASENAME.'.'.$ext;
            foreach (PublicStorageLink::webDocumentRoots() as $root) {
                $file = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        Setting::setValue(self::SETTING_KEY, null);
    }

    public static function storeLogo(UploadedFile $file): void
    {
        $disk = self::resolvedDisk();

        $ext = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png'),
        };
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'png';
        }

        $name = 'admin-panel-logo.'.$ext;
        $oldPath = Setting::getValue(self::SETTING_KEY);

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory('site');
            $stored = $file->storeAs('site', $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs('site', $file, $name, 'public');
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ ملف الشعار.');
        }

        Setting::setValue(self::SETTING_KEY, $stored);

        if ($disk === 'public' && CloudStorage::isR2Configured()
            && filter_var(config('filesystems.use_cloudflare_r2'), FILTER_VALIDATE_BOOLEAN)) {
            CloudStorage::copyLocalPublicToR2($stored);
        }

        self::publishWebLogo($stored);

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::deletePhysicalFile($oldPath);
        }
    }

    private static function deletePhysicalFile(string $path): void
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        foreach (array_unique([self::resolvedDisk(), 'public', 'r2', 's3']) as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($d)->exists($path)) {
                    Storage::disk($d)->delete($path);
                }
            } catch (\Throwable) {
            }
        }
    }
}
