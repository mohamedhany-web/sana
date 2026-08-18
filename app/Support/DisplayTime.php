<?php

namespace App\Support;

use Carbon\Carbon;

class DisplayTime
{
    public static function timezone(): string
    {
        return (string) config('app.display_timezone', 'Asia/Riyadh');
    }

    public static function format(mixed $value, string $format = 'Y-m-d H:i'): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return Carbon::parse($value)->timezone(self::timezone())->format($format);
    }
}
