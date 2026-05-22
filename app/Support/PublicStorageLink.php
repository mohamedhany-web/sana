<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * ربط public/storage (و public_html/storage على Hostinger) بـ storage/app/public بدون exec().
 */
class PublicStorageLink
{
    public static function targetPath(): string
    {
        return storage_path('app/public');
    }

    /**
     * جذور الويب التي يُخدم منها الموقع (public + public_html + PUBLIC_WEB_ROOT).
     *
     * @return list<string>
     */
    public static function webDocumentRoots(): array
    {
        $roots = [];

        $custom = trim((string) env('PUBLIC_WEB_ROOT', ''));
        if ($custom !== '' && is_dir($custom)) {
            $roots[] = rtrim(str_replace('\\', '/', $custom), '/');
        }

        $public = public_path();
        if (is_dir($public)) {
            $roots[] = rtrim(str_replace('\\', '/', $public), '/');
        }

        foreach ([
            base_path('public_html'),
            dirname(base_path()).'/public_html',
            base_path('../public_html'),
        ] as $candidate) {
            $candidate = str_replace('\\', '/', $candidate);
            if ($candidate !== '' && is_dir($candidate)) {
                $resolved = realpath($candidate);
                $roots[] = rtrim(str_replace('\\', '/', $resolved ?: $candidate), '/');
            }
        }

        return array_values(array_unique($roots));
    }

    /**
     * @return list<string> مسارات مجلد storage داخل كل جذر ويب
     */
    public static function storageLinkPaths(): array
    {
        return array_map(
            static fn (string $root) => $root.'/storage',
            self::webDocumentRoots()
        );
    }

    public static function linkPath(): string
    {
        $paths = self::storageLinkPaths();

        return $paths[0] ?? public_path('storage');
    }

    public static function isValidLinkAt(string $link): bool
    {
        if (! is_link($link)) {
            return false;
        }
        $target = self::targetPath();
        $resolved = realpath(readlink($link) ?: $link);

        return $resolved !== false && realpath($target) !== false
            && strcasecmp($resolved, realpath($target)) === 0;
    }

    /**
     * @return 'symlink'|'mirrored'|'route_only'
     */
    public static function establish(bool $mirrorFallback = true): string
    {
        $target = self::targetPath();

        if (! is_dir($target)) {
            File::makeDirectory($target, 0755, true);
        }

        $anySymlink = false;
        $anyMirror = false;

        foreach (self::storageLinkPaths() as $link) {
            if (self::isValidLinkAt($link)) {
                $anySymlink = true;

                continue;
            }

            if (is_dir($link) && ! is_link($link)) {
                self::removeLinkPath($link);
            } elseif (is_file($link)) {
                @unlink($link);
            }

            if (self::trySymlink($target, $link)) {
                $anySymlink = true;

                continue;
            }

            if ($mirrorFallback && self::mirrorDirectory($target, $link)) {
                $anyMirror = true;
            }
        }

        self::publishBundledStaticImages();

        if ($anySymlink) {
            return 'symlink';
        }
        if ($anyMirror) {
            return 'mirrored';
        }

        return 'route_only';
    }

    /**
     * نسخ صور ثابتة (هيرو، خلفية تسجيل الدخول) إلى كل جذر ويب — مهم عند نشر public/ داخل public_html.
     */
    public static function publishBundledStaticImages(): void
    {
        $files = [
            'images/hero-intro.png',
            'images/brainstorm-meeting.jpg',
            'images/brainstorm-meeting.png',
        ];

        $sourceRoot = public_path();
        if (! is_dir($sourceRoot)) {
            return;
        }

        foreach (self::webDocumentRoots() as $root) {
            if (realpath($root) === realpath($sourceRoot)) {
                continue;
            }
            foreach ($files as $rel) {
                $src = $sourceRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
                if (! is_file($src)) {
                    continue;
                }
                $dest = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
                $destDir = dirname($dest);
                if (! is_dir($destDir)) {
                    File::makeDirectory($destDir, 0755, true);
                }
                if (! is_file($dest) || filesize($dest) !== filesize($src)) {
                    @copy($src, $dest);
                }
            }
        }
    }

    public static function trySymlink(string $target, string $link): bool
    {
        if (! function_exists('symlink')) {
            return false;
        }

        $target = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $target);
        $link = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $link);

        try {
            return @symlink($target, $link);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function mirrorDirectory(string $source, string $destination): bool
    {
        try {
            if (is_link($destination)) {
                @unlink($destination);
            } elseif (is_dir($destination)) {
                if (! self::deleteDirectorySafe($destination)) {
                    return false;
                }
            } elseif (is_file($destination)) {
                @unlink($destination);
            }

            File::ensureDirectoryExists($destination);

            if (! is_dir($source)) {
                return false;
            }

            self::copyTree($source, $destination);

            return is_dir($destination) && count(glob($destination.DIRECTORY_SEPARATOR.'*')) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function copyTree(string $source, string $destination): void
    {
        File::ensureDirectoryExists($destination);
        foreach (scandir($source) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $from = $source.DIRECTORY_SEPARATOR.$entry;
            $to = $destination.DIRECTORY_SEPARATOR.$entry;
            if (is_dir($from)) {
                self::copyTree($from, $to);
            } elseif (is_file($from)) {
                @copy($from, $to);
            }
        }
    }

    private static function deleteDirectorySafe(string $dir): bool
    {
        if (is_link($dir)) {
            return @unlink($dir);
        }

        try {
            File::deleteDirectory($dir);

            return ! is_dir($dir);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function removeLinkPath(string $link): void
    {
        if (is_link($link)) {
            @unlink($link);

            return;
        }
        if (is_dir($link)) {
            File::deleteDirectory($link);
        }
    }

    public static function modeLabel(string $mode): string
    {
        return match ($mode) {
            'symlink' => 'رابط symbolic (symlink)',
            'mirrored' => 'نسخة مرآة (mirror) من الملفات',
            'route_only' => 'عرض عبر Laravel فقط (/storage/ و /media/)',
            default => 'فشل',
        };
    }
}
