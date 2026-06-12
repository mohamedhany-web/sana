<?php

namespace App\Services;

use App\Support\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مرفقات نموذج طلب المعلم — تُرفع إلى Cloudflare R2 (إلزامي في الإنتاج).
 *
 * .env: USE_CLOUDFLARE_R2=true + AWS_* / R2_PUBLIC_URL
 * للتطوير المحلي بدون R2 فقط: TUTOR_APPLICATION_DISK=public
 */
class TutorApplicationStorage
{
    public const DIRECTORY = 'tutor-applications';

    public const CONFIG_KEY = 'tutor_application_disk';

    public static function resolvedDisk(): string
    {
        $configured = (string) config('filesystems.'.self::CONFIG_KEY, 'r2');

        if ($configured === 'r2') {
            if (! CloudStorage::isR2Configured()) {
                throw new \RuntimeException(__('tutor.apply_validation.storage_r2_required'));
            }

            return 'r2';
        }

        $disk = CloudStorage::resolveDisk(self::CONFIG_KEY);

        if ($disk === 'public' && config('filesystems.use_cloudflare_r2') && ! app()->environment('local')) {
            throw new \RuntimeException(__('tutor.apply_validation.storage_r2_required'));
        }

        return $disk;
    }

    public static function publicUrl(?string $path): ?string
    {
        return CloudStorage::publicUrlForPath(self::CONFIG_KEY, $path);
    }

    public static function store(UploadedFile $file, int $userId, string $subdir): string
    {
        $disk = self::resolvedDisk();
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $name = Str::uuid()->toString().'.'.$ext;
        $dir = trim(self::DIRECTORY.'/'.$userId.'/'.$subdir, '/');

        if ($disk === 'public') {
            Log::warning('tutor application file stored on local public disk — use R2 in production', [
                'user_id' => $userId,
                'subdir' => $subdir,
            ]);
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, ['visibility' => 'public']);
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException(__('tutor.apply_validation.storage_failed'));
        }

        if ($disk === 'r2' && ! Storage::disk('r2')->exists($stored)) {
            throw new \RuntimeException(__('tutor.apply_validation.storage_failed'));
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
        foreach (array_unique(['r2', 'public', 's3']) as $d) {
            try {
                if (Storage::disk($d)->exists($path)) {
                    Storage::disk($d)->delete($path);
                }
            } catch (\Throwable) {
            }
        }
    }
}
