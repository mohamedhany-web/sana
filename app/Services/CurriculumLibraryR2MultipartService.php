<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * رفع متعدد الأجزاء إلى Cloudflare R2 (واجهة S3).
 */
class CurriculumLibraryR2MultipartService
{
    public function __construct(
        protected string $diskName = 'r2'
    ) {}

    public function client(): S3Client
    {
        $c = config('filesystems.disks.'.$this->diskName);

        return new S3Client([
            'version' => 'latest',
            'region' => $c['region'] ?? 'auto',
            'credentials' => [
                'key' => $c['key'] ?? '',
                'secret' => $c['secret'] ?? '',
            ],
            'endpoint' => $c['endpoint'] ?? null,
            'use_path_style_endpoint' => (bool) ($c['use_path_style_endpoint'] ?? true),
        ]);
    }

    public function bucket(): string
    {
        $c = config('filesystems.disks.'.$this->diskName);

        return (string) ($c['bucket'] ?? '');
    }

    /**
     * @return array{UploadId: string, Key: string, Bucket: string}
     */
    public function createMultipartUpload(string $key, string $contentType): array
    {
        $client = $this->client();
        $bucket = $this->bucket();
        $result = $client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        return [
            'UploadId' => (string) $result['UploadId'],
            'Key' => $key,
            'Bucket' => $bucket,
        ];
    }

    /**
     * رؤوس التوقيع من AWS SDK قد تتضمّن Host وغيرها؛ المتصفح يمنع بعضها أو يفسد الطلب عبر CORS.
     * نُرجع فقط رؤوساً آمنة يجب تمريرها مع PUT (مثل x-amz-*).
     *
     * @param  array<string, array<int, string>|string>  $raw
     * @return array<string, array<int, string>>
     */
    public static function filterPresignedUploadHeadersForBrowser(array $raw): array
    {
        $strip = [
            'host', 'content-length', 'user-agent', 'connection', 'upgrade',
            'expect', 'te', 'trailer', 'accept', 'accept-encoding',
        ];
        $out = [];
        foreach ($raw as $name => $values) {
            $lower = strtolower((string) $name);
            if (in_array($lower, $strip, true)) {
                continue;
            }
            $out[$name] = is_array($values) ? $values : [$values];
        }

        return $out;
    }

    /**
     * @return array{url: string, headers: array<string, array<int, string>>}
     */
    public function presignedUploadPart(string $bucket, string $key, string $uploadId, int $partNumber, string $expires = '+24 hours'): array
    {
        $cmd = $this->client()->getCommand('UploadPart', [
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'PartNumber' => $partNumber,
        ]);
        $signed = $this->client()->createPresignedRequest($cmd, $expires);

        return [
            'url' => (string) $signed->getUri(),
            'headers' => self::filterPresignedUploadHeadersForBrowser($signed->getHeaders()),
        ];
    }

    /**
     * @param  array<int, array{PartNumber: int, ETag: string}>  $parts
     */
    public function completeMultipartUpload(string $bucket, string $key, string $uploadId, array $parts): void
    {
        $this->client()->completeMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'MultipartUpload' => [
                'Parts' => $parts,
            ],
        ]);
    }

    public function abortMultipartUpload(string $bucket, string $key, string $uploadId): void
    {
        try {
            $this->client()->abortMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
            ]);
        } catch (Throwable $e) {
            Log::warning('R2 multipart abort', ['message' => $e->getMessage()]);
        }
    }
}
