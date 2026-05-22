<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminPanelBranding
{
    public const SETTING_KEY = 'admin_panel_logo_path';

    public static function resolvedDisk(): string
    {
        return CloudStorage::resolveDisk('admin_branding_disk');
    }

    public static function logoPublicUrl(): ?string
    {
        $path = Setting::getValue(self::SETTING_KEY);

        return CloudStorage::publicUrlForPath('admin_branding_disk', is_string($path) ? $path : null);
    }

    public static function removeLogo(): void
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            self::deletePhysicalFile($path);
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
