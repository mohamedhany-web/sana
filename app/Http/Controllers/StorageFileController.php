<?php

namespace App\Http\Controllers;

use App\Support\CloudStorage;
use App\Support\PublicStorageLink;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageFileController extends Controller
{
    public function show(string $path): Response|BinaryFileResponse
    {
        $path = rawurldecode($path);
        $path = str_replace('..', '', $path);
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($path === '') {
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
            $headers = [
                'Content-Type' => $remote['mime'],
                'Cache-Control' => 'public, max-age=86400',
            ];
            if ($remote['mime'] === 'application/pdf') {
                $headers['Content-Disposition'] = 'inline; filename="'.basename($path).'"';
            }

            return response($remote['content'], 200, $headers);
        }

        abort(404, 'File not found');
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
                'svg' => 'image/svg+xml',
                default => 'application/octet-stream',
            };
        }

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ];

        return response()->file($realPath, $headers);
    }
}
