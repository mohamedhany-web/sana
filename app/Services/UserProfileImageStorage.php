<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\User;
use App\Support\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
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
        $url = CloudStorage::publicUrlForPath('user_profile_disk', $path);
        if ($url) {
            return $url;
        }

        // احتياطي: دائماً عبر /storage حتى لو فشل exists() على السحابة مؤقتاً
        if (is_string($path) && $path !== '' && ! str_starts_with($path, 'http')) {
            return CloudStorage::localPublicStorageUrl(ltrim(str_replace('\\', '/', $path), '/'));
        }

        return null;
    }

    /**
     * مزامنة صورة الحساب مع صورة الملف التعريفي العام للمعلم.
     */
    public static function syncInstructorDisplayPhoto(int $userId, string $relativePath): void
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '') {
            return;
        }

        User::query()->whereKey($userId)->update(['profile_image' => $relativePath]);

        $profile = InstructorProfile::query()->where('user_id', $userId)->first();
        if ($profile) {
            $profile->update(['photo_path' => $relativePath]);

            return;
        }

        InstructorProfile::query()->create([
            'user_id' => $userId,
            'status' => InstructorProfile::STATUS_DRAFT,
            'photo_path' => $relativePath,
        ]);
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
        $preferredDisk = self::resolvedDisk();
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
        $relativePath = $dir.'/'.$name;

        $source = $file->getRealPath() ?: $file->getPathname();
        $binary = is_string($source) && $source !== '' ? @file_get_contents($source) : false;
        if ($binary === false || $binary === '') {
            throw new \RuntimeException('تعذّر قراءة ملف الصورة المرفوع.');
        }

        $wroteLocal = false;
        try {
            Storage::disk('public')->makeDirectory($dir);
            $wroteLocal = Storage::disk('public')->put($relativePath, $binary, 'public');
        } catch (\Throwable $e) {
            Log::warning('profile image local store failed', ['error' => $e->getMessage()]);
        }

        $wroteCloud = false;
        if ($preferredDisk !== 'public' && CloudStorage::isR2Configured()) {
            try {
                $wroteCloud = (bool) Storage::disk($preferredDisk)->put($relativePath, $binary, [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType() ?: 'image/jpeg',
                ]);
            } catch (\Throwable $e) {
                Log::warning('profile image cloud store failed; keeping local copy', [
                    'disk' => $preferredDisk,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! $wroteLocal && ! $wroteCloud) {
            throw new \RuntimeException('فشل حفظ صورة الملف الشخصي.');
        }

        Log::info('profile image stored', [
            'disk' => $preferredDisk,
            'path' => $relativePath,
            'local' => (bool) $wroteLocal,
            'cloud' => (bool) $wroteCloud,
            'url' => self::publicUrl($relativePath),
        ]);

        return $relativePath;
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
