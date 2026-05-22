<?php

namespace App\Services;

use App\Support\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * صور ملف المستخدم (المعلم/الطالب…) — storage/public أو Cloudflare R2 / S3.
 * يُفضّل ضبط USER_PROFILE_DISK=r2 في .env للإنتاج.
 */
class UserProfileImageStorage
{
    public const DIRECTORY = 'profile-photos';

    public static function resolvedDisk(): string
    {
        return CloudStorage::resolveDisk('user_profile_disk');
    }

    public static function publicUrl(?string $path): ?string
    {
        return CloudStorage::publicUrlForPath('user_profile_disk', $path);
    }

    /**
     * @return string مسار نسبي مثل profile-photos/uuid.jpg
     */
    public static function store(UploadedFile $file, ?string $directory = null): string
    {
        return self::storeInDirectory($file, $directory ?? self::DIRECTORY);
    }

    public static function storeInDirectory(UploadedFile $file, string $directory): string
    {
        $disk = self::resolvedDisk();

        $ext = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg'),
        };
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'jpg';
        }

        $name = Str::uuid()->toString().'.'.$ext;
        $dir = trim($directory, '/');

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, 'public');
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ صورة الملف الشخصي.');
        }

        return $stored;
    }

    public static function delete(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

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

        $legacy = public_path($path);
        if (is_file($legacy)) {
            @unlink($legacy);
        }
    }
}
