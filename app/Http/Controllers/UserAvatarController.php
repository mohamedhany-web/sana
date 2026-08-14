<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CloudStorage;
use App\Support\PublicStorageLink;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * عرض صورة الملف الشخصي عبر Laravel (يتجاوز حجب /storage على بعض الاستضافات، ويجلب من R2 عند الحاجة).
 */
class UserAvatarController extends Controller
{
    public function show(User $user): Response|BinaryFileResponse
    {
        $raw = trim((string) ($user->profile_image ?? ''));
        if ($raw === '') {
            abort(404);
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return redirect()->away($raw);
        }

        $path = ltrim(str_replace('\\', '/', $raw), '/');
        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        foreach (PublicStorageLink::storageLinkPaths() as $mirrorBase) {
            $candidate = $mirrorBase.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
            if (is_file($candidate) && is_readable($candidate)) {
                return $this->fileResponse($candidate);
            }
        }

        $basePath = storage_path('app/public');
        $filePath = $basePath.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        if (is_file($filePath) && is_readable($filePath)) {
            $realPath = realpath($filePath) ?: $filePath;
            $allowedPath = realpath($basePath) ?: $basePath;
            if ($allowedPath !== '' && str_starts_with(
                str_replace('\\', '/', $realPath),
                str_replace('\\', '/', $allowedPath)
            )) {
                return $this->fileResponse($realPath);
            }
        }

        $remote = CloudStorage::readFileContents($path, ['r2', 's3', 'public']);
        if ($remote !== null) {
            return response($remote['content'], 200, [
                'Content-Type' => $remote['mime'],
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404);
    }

    private function fileResponse(string $realPath): BinaryFileResponse
    {
        $mimeType = @mime_content_type($realPath);
        if (! $mimeType) {
            $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
            $mimeType = match ($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => 'application/octet-stream',
            };
        }

        return response()->file($realPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
