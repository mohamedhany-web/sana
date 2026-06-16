<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\PublicContactInfo;
use Illuminate\Support\Facades\Cache;

class PublicFooterSettings
{
    public const CACHE_KEY = 'public_footer_payload_v1';

    /** @return array<string, string> */
    public static function defaults(): array
    {
        return [
            'footer_brand_tagline' => 'منصة تطوير المعلم العربي',
            'footer_blurb' => 'تجربة تعليمية عربية تركز على التمكين المهني للمعلم عبر التدريب العملي وأدوات التدريس الحديثة.',
            'footer_email' => (string) config('contact.email', 'info@sanaedu.com'),
            'footer_phone' => (string) (config('contact.phone') ?? ''),
            'footer_whatsapp_url' => (string) (config('contact.whatsapp_url') ?? ''),
            'footer_address' => (string) config('contact.address', 'الرياض، المملكة العربية السعودية'),
            'footer_support_hours' => (string) config('contact.support.hours_full', 'الأحد – الخميس: 9 ص – 9 م'),
            'footer_bottom_tagline' => 'تعليم عربي احترافي يركز على النتائج',
            'social_facebook_url' => '',
            'social_x_url' => '',
            'social_instagram_url' => '',
            'social_youtube_url' => '',
            'social_linkedin_url' => '',
            'social_tiktok_url' => '',
            'social_telegram_url' => '',
            'social_snapchat_url' => '',
        ];
    }

    /** @return list<string> */
    public static function editableKeys(): array
    {
        return array_keys(self::defaults());
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * بيانات الفوتر للواجهة العامة (مدمجة مع القيم الافتراضية).
     *
     * @return array{
     *   brand_tagline: string,
     *   blurb: string,
     *   email: string,
     *   phone: string,
     *   whatsapp_url: string,
     *   address: string,
     *   support_hours: string,
     *   bottom_tagline: string,
     *   socials: list<array{url: string, icon: string, label: string}>
     * }
     */
    public static function payload(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $defaults = self::defaults();
            $stored = Setting::query()
                ->whereIn('key', array_keys($defaults))
                ->pluck('value', 'key')
                ->all();

            $merged = [];
            foreach ($defaults as $key => $default) {
                if (array_key_exists($key, $stored) && $stored[$key] !== null && $stored[$key] !== '') {
                    $merged[$key] = (string) $stored[$key];
                } else {
                    $merged[$key] = $default;
                }
            }

            $socialMap = [
                ['key' => 'social_facebook_url', 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
                ['key' => 'social_x_url', 'icon' => 'fab fa-x-twitter', 'label' => 'X / Twitter'],
                ['key' => 'social_instagram_url', 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
                ['key' => 'social_youtube_url', 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
                ['key' => 'social_linkedin_url', 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
                ['key' => 'social_tiktok_url', 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
                ['key' => 'social_telegram_url', 'icon' => 'fab fa-telegram-plane', 'label' => 'Telegram'],
                ['key' => 'social_snapchat_url', 'icon' => 'fab fa-snapchat-ghost', 'label' => 'Snapchat'],
            ];

            $socials = [];
            foreach ($socialMap as $meta) {
                $url = trim((string) ($merged[$meta['key']] ?? ''));
                if ($url !== '') {
                    $socials[] = [
                        'url' => $url,
                        'icon' => $meta['icon'],
                        'label' => $meta['label'],
                    ];
                }
            }

            $phone = PublicContactInfo::sanitizePhone((string) $merged['footer_phone']);
            $whatsapp = PublicContactInfo::normalizeWhatsappInput((string) $merged['footer_whatsapp_url']);
            if ($whatsapp === '' && $phone !== '') {
                $whatsapp = PublicContactInfo::normalizeWhatsappInput($phone);
            }

            return [
                'brand_tagline' => (string) $merged['footer_brand_tagline'],
                'blurb' => (string) $merged['footer_blurb'],
                'email' => PublicContactInfo::sanitizeEmail((string) $merged['footer_email']),
                'phone' => $phone,
                'whatsapp_url' => $whatsapp,
                'address' => trim((string) $merged['footer_address']),
                'support_hours' => trim((string) $merged['footer_support_hours']),
                'bottom_tagline' => (string) $merged['footer_bottom_tagline'],
                'socials' => $socials,
            ];
        });
    }
}
