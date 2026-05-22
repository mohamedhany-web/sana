<?php

namespace App\Support;

use App\Models\Setting;

class PlatformBranding
{
    public const PLATFORM_NAME_SETTING = 'platform_display_name';

    public static function displayName(): string
    {
        $custom = Setting::getValue(self::PLATFORM_NAME_SETTING);
        if (is_string($custom) && trim($custom) !== '') {
            return trim($custom);
        }

        $brand = config('brand.name');
        if (is_string($brand) && trim($brand) !== '') {
            return trim($brand);
        }

        return (string) config('app.name', 'Sana');
    }

    public static function displayInitial(): string
    {
        return mb_substr(self::displayName(), 0, 1);
    }

    /**
     * اسم العرض في الترحيب — يستبدل العلامة القديمة (Muallimx) باسم المنصة الحالية.
     */
    public static function greetingDisplayName(?string $name): string
    {
        if (! is_string($name) || trim($name) === '') {
            return '';
        }

        $clean = trim($name);
        $legacy = ['MuallimX', 'Muallimx', 'muallimx', 'MUALLIMX', 'معلمكس', 'معليمكس'];
        $platform = self::displayName();

        foreach ($legacy as $token) {
            $clean = str_ireplace($token, $platform, $clean);
        }

        return trim(preg_replace('/\s+/u', ' ', $clean) ?? $clean);
    }
}
