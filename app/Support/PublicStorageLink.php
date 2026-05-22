<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * ربط public/storage بـ storage/app/public بدون exec() (ممنوع على بعض الاستضافات).
 */
class PublicStorageLink
{
    public static function targetPath(): string
    {
        return storage_path('app/public');
    }

    public static function linkPath(): string
    {
        return public_path('storage');
    }

    public static function isValidLink(): bool
    {
        $link = self::linkPath();
        if (! is_link($link)) {
            return false;
        }
        $target = self::targetPath();
        $resolved = realpath(readlink($link) ?: $link);

        return $resolved !== false && realpath($target) !== false
            && strcasecmp($resolved, realpath($target)) === 0;
    }

    /**
     * @return 'symlink'|'mirrored'|'route_only'|false
     */
    public static function establish(bool $mirrorFallback = true): string|false
    {
        $link = self::linkPath();
        $target = self::targetPath();

        if (! is_dir($target)) {
            File::makeDirectory($target, 0755, true);
        }

        if (self::isValidLink()) {
            return 'symlink';
        }

        if (is_dir($link) && ! is_link($link)) {
            self::removeLinkPath($link);
        } elseif (is_file($link)) {
            @unlink($link);
        }

        if (self::trySymlink($target, $link)) {
            return 'symlink';
        }

        if ($mirrorFallback) {
            if (self::mirrorDirectory($target, $link)) {
                return 'mirrored';
            }
        }

        if (is_dir($link) || is_link($link)) {
            self::removeLinkPath($link);
        }

        return 'route_only';
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

    public static function modeLabel(string|false $mode): string
    {
        return match ($mode) {
            'symlink' => 'رابط symbolic (symlink)',
            'mirrored' => 'نسخة مرآة (mirror) من الملفات',
            'route_only' => 'عرض عبر Laravel فقط (/storage/...)',
            default => 'فشل',
        };
    }
}
