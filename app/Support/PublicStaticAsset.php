<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * روابط الملفات الثابتة في public/ — تضمن نسخها إلى public_html عند الحاجة (Hostinger/cPanel).
 */
class PublicStaticAsset
{
    public static function url(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '') {
            return asset('');
        }

        if (! self::isAvailableInAnyWebRoot($relativePath) && File::isFile(public_path($relativePath))) {
            PublicStorageLink::publishPublicAsset($relativePath);
        }

        return asset($relativePath);
    }

    public static function isAvailableInAnyWebRoot(string $relativePath): bool
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '') {
            return false;
        }

        foreach (PublicStorageLink::webDocumentRoots() as $root) {
            $file = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            if (is_file($file)) {
                return true;
            }
        }

        return false;
    }
}
