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
            return self::absoluteUrl('');
        }

        $source = public_path($relativePath);
        if (File::isFile($source) && ! self::isAvailableInAnyWebRoot($relativePath)) {
            PublicStorageLink::publishPublicAsset($relativePath);
        }

        return self::absoluteUrl($relativePath);
    }

    public static function exists(string $relativePath): bool
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '') {
            return false;
        }

        if (self::isAvailableInAnyWebRoot($relativePath)) {
            return true;
        }

        return File::isFile(public_path($relativePath));
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

    /**
     * رابط مطلق يستخدم نطاق الطلب الحالي — يعمل حتى لو APP_URL خاطئ على السيرفر.
     */
    public static function absoluteUrl(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $encoded = $relativePath === ''
            ? ''
            : implode('/', array_map('rawurlencode', explode('/', $relativePath)));

        $req = request();
        if ($req && $req->getSchemeAndHttpHost()) {
            return $req->getSchemeAndHttpHost().($encoded !== '' ? '/'.$encoded : '');
        }

        return asset($relativePath);
    }
}
