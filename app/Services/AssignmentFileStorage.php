<?php

namespace App\Services;

use App\Support\CloudStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مرفقات الواجبات (ملفات المدرب مع الواجب + تسليمات الطلاب) — محلي أو Cloudflare R2 / S3.
 *
 * في .env: ASSIGNMENT_FILES_DISK=r2 (أو public)
 */
class AssignmentFileStorage
{
    public const DIRECTORY_RESOURCES = 'assignments/resources';

    public const DIRECTORY_SUBMISSIONS = 'assignments/submissions';

    public static function resolvedDisk(): string
    {
        $d = CloudStorage::resolveDisk('assignment_files_disk');
        if ($d === 'public') {
            $legacy = (string) config('filesystems.portfolio_disk', 'public');
            if ($legacy !== '' && $legacy !== '0' && in_array($legacy, ['public', 'r2', 's3'], true)) {
                return $legacy;
            }
        }

        return $d;
    }

    public static function publicUrl(?string $path): ?string
    {
        return CloudStorage::publicUrlForPath('assignment_files_disk', $path);
    }

    /**
     * رفع ملف مورد للواجب (من المدرب).
     */
    public static function storeResource(UploadedFile $file): string
    {
        return self::storeUnderDirectory($file, self::DIRECTORY_RESOURCES);
    }

    /**
     * رفع ملف تسليم طالب.
     */
    public static function storeSubmission(UploadedFile $file, int $assignmentId, int $userId): string
    {
        $sub = trim(self::DIRECTORY_SUBMISSIONS, '/').'/'.$assignmentId.'/'.$userId;

        return self::storeUnderDirectory($file, $sub);
    }

    private static function storeUnderDirectory(UploadedFile $file, string $directory): string
    {
        $disk = self::resolvedDisk();
        $dir = trim($directory, '/');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $name = Str::uuid()->toString().'.'.$ext;

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, ['visibility' => 'public']);
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ الملف.');
        }

        return $stored;
    }

    /**
     * @param  array<int, array{path?: string, original_name?: string}>  $items
     * @return array<int, array{path?: string, original_name?: string}>
     */
    public static function deleteMany(array $items): void
    {
        foreach ($items as $item) {
            $path = is_array($item) ? ($item['path'] ?? null) : null;
            if (is_string($path) && $path !== '') {
                self::deletePath($path);
            }
        }
    }

    public static function deletePath(?string $path): void
    {
        if (! is_string($path) || $path === '') {
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
    }
}
