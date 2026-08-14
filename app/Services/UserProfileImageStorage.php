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

        $disksToTry = array_values(array_unique([$preferredDisk, 'public']));
        $lastError = null;

        foreach ($disksToTry as $disk) {
            try {
                if ($disk === 'public') {
                    Storage::disk('public')->makeDirectory($dir);
                    $stored = $file->storeAs($dir, $name, 'public');
                } else {
                    $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, [
                        'visibility' => 'public',
                    ]);
                }

                if (! is_string($stored) || $stored === '') {
                    throw new \RuntimeException("putFileAs returned empty on disk [{$disk}]");
                }

                // إن رُفع إلى R2 بنجاح نحاول نسخة محلية اختيارية لتحسين العرض السريع (لا تفشل العملية إن فشلت)
                if ($disk !== 'public' && CloudStorage::isR2Configured()) {
                    try {
                        $contents = Storage::disk($disk)->get($stored);
                        if (is_string($contents) && $contents !== '') {
                            Storage::disk('public')->makeDirectory($dir);
                            Storage::disk('public')->put($stored, $contents, 'public');
                        }
                    } catch (\Throwable $mirrorError) {
                        Log::info('profile image R2 mirror to public skipped', [
                            'path' => $stored,
                            'error' => $mirrorError->getMessage(),
                        ]);
                    }
                }

                Log::info('profile image stored', [
                    'disk' => $disk,
                    'path' => $stored,
                    'url' => self::publicUrl($stored),
                ]);

                return $stored;
            } catch (\Throwable $e) {
                $lastError = $e;
                Log::warning('profile image store failed on disk', [
                    'disk' => $disk,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        throw new \RuntimeException(
            'فشل حفظ صورة الملف الشخصي.'.($lastError ? ' '.$lastError->getMessage() : '')
        );
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
