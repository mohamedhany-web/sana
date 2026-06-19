<?php

namespace App\Support;

final class YouTubeUrl
{
    public static function extractVideoId(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function embedUrl(?string $url): ?string
    {
        $id = self::extractVideoId($url);

        return $id ? 'https://www.youtube.com/embed/'.$id.'?rel=0&modestbranding=1' : null;
    }

    public static function thumbnailUrl(?string $url): ?string
    {
        $id = self::extractVideoId($url);

        return $id ? 'https://img.youtube.com/vi/'.$id.'/hqdefault.jpg' : null;
    }

    public static function isValid(?string $url): bool
    {
        return self::extractVideoId($url) !== null;
    }
}
