<?php

namespace App\Support;

use App\Services\PublicFooterSettings;

/**
 * بيانات التواصل العامة — مصدر واحد مع تنقية القيم القديمة غير المتسقة.
 */
class PublicContactInfo
{
    /** @var list<string> */
    private const LEGACY_EMAILS = ['info@sana.edu'];

    /** @var list<string> */
    private const LEGACY_PHONE_DIGITS = ['01044610507', '201044610507'];

    public static function officialEmail(): string
    {
        return trim((string) config('contact.email', 'info@sanaedu.com'));
    }

    public static function domain(): string
    {
        return trim((string) config('contact.domain', 'sanaedu.com'));
    }

    public static function supportDays(): string
    {
        return (string) config('contact.support.days', 'الأحد – الخميس');
    }

    public static function supportHours(): string
    {
        return (string) config('contact.support.hours', '9 ص – 9 م');
    }

    public static function supportHoursFull(): string
    {
        return (string) config('contact.support.hours_full', 'الأحد – الخميس: 9 ص – 9 م');
    }

    public static function timezoneLabel(): string
    {
        return (string) config('contact.support.timezone', 'توقيت السعودية (GMT+3)');
    }

    public static function responseSla(): string
    {
        return (string) config('contact.support.response_sla', 'خلال 24 ساعة');
    }

    public static function serviceScope(): string
    {
        return (string) config('contact.service_scope', '');
    }

    public static function sanitizeEmail(?string $email): string
    {
        $email = trim(strtolower((string) $email));

        if ($email === '' || in_array($email, self::LEGACY_EMAILS, true)) {
            return self::officialEmail();
        }

        if (str_ends_with($email, '@sana.edu')) {
            return self::officialEmail();
        }

        return trim((string) $email);
    }

    public static function sanitizePhone(?string $phone): string
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '' || in_array($digits, self::LEGACY_PHONE_DIGITS, true)) {
            return '';
        }

        if (str_starts_with($digits, '01') || str_starts_with($digits, '201')) {
            return '';
        }

        return $phone;
    }

    public static function sanitizeWhatsappUrl(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        foreach (self::LEGACY_PHONE_DIGITS as $legacy) {
            if (str_contains($url, $legacy)) {
                return '';
            }
        }

        if (preg_match('#wa\.me/20\d+#', $url)) {
            return '';
        }

        return $url;
    }

    /**
     * يقبل رابط wa.me كاملاً أو رقماً بصيغة +966… ويُرجع رابطاً آمناً للعرض.
     */
    public static function normalizeWhatsappInput(?string $input): string
    {
        $input = trim((string) $input);
        if ($input === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $input)) {
            return self::sanitizeWhatsappUrl($input);
        }

        $digits = preg_replace('/\D+/', '', $input) ?? '';
        if ($digits === '' || in_array($digits, self::LEGACY_PHONE_DIGITS, true)) {
            return '';
        }

        if (str_starts_with($digits, '01') || str_starts_with($digits, '201')) {
            return '';
        }

        return self::sanitizeWhatsappUrl('https://wa.me/'.$digits);
    }

    /** رابط واتساب مع رسالة مسبقة — يعود لصفحة التواصل إن لم يُضبط الرقم من الإدارة. */
    public static function whatsappShareUrl(string $message): string
    {
        $base = trim(self::payload()['whatsapp_url'] ?? '');
        if ($base === '') {
            return route('public.contact');
        }

        $base = strtok($base, '?') ?: $base;

        return rtrim($base, '/').'?text='.rawurlencode($message);
    }

    /**
     * @return array{
     *   email: string,
     *   phone: string,
     *   whatsapp_url: string,
     *   address: string,
     *   domain: string,
     *   support_days: string,
     *   support_hours: string,
     *   support_hours_full: string,
     *   timezone_label: string,
     *   response_sla: string,
     *   service_scope: string,
     *   socials: list<array{url: string, icon: string, label: string}>,
     *   has_phone: bool,
     *   has_email: bool,
     *   has_whatsapp: bool
     * }
     */
    public static function payload(): array
    {
        $footer = PublicFooterSettings::payload();

        $email = $footer['email'] ?: self::officialEmail();
        $phone = $footer['phone'];
        $whatsappUrl = $footer['whatsapp_url'];

        $address = trim((string) ($footer['address'] ?? ''));
        if ($address === '') {
            $address = trim((string) config('contact.address', ''));
        }

        $supportHoursFull = trim((string) ($footer['support_hours'] ?? ''));
        if ($supportHoursFull === '') {
            $supportHoursFull = self::supportHoursFull();
        }

        return [
            'email' => $email,
            'phone' => $phone,
            'whatsapp_url' => $whatsappUrl,
            'address' => $address,
            'domain' => self::domain(),
            'support_days' => self::supportDays(),
            'support_hours' => self::supportHours(),
            'support_hours_full' => $supportHoursFull,
            'timezone_label' => self::timezoneLabel(),
            'response_sla' => self::responseSla(),
            'service_scope' => self::serviceScope(),
            'socials' => $footer['socials'],
            'has_phone' => $phone !== '',
            'has_email' => $email !== '',
            'has_whatsapp' => $whatsappUrl !== '',
        ];
    }

    /** @return list<array{icon: string, title: string, value: string, desc: string}> */
    public static function responseExpectations(): array
    {
        $contact = self::payload();
        $channels = 2;
        if ($contact['has_phone']) {
            $channels++;
        }
        if ($contact['has_whatsapp']) {
            $channels++;
        }

        return [
            [
                'icon' => 'fa-bolt',
                'title' => 'متوسط الرد',
                'value' => self::responseSla(),
                'desc' => 'لبريد النموذج — واتساب أسرع في أوقات العمل.',
            ],
            [
                'icon' => 'fa-clock',
                'title' => 'أوقات الدعم',
                'value' => self::supportHours(),
                'desc' => self::supportDays().' — '.self::timezoneLabel().'.',
            ],
            [
                'icon' => 'fa-calendar-week',
                'title' => 'أيام العمل',
                'value' => self::supportDays(),
                'desc' => 'دعم محدود في عطلة نهاية الأسبوع.',
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'قنوات الدعم',
                'value' => $channels.'+ قنوات',
                'desc' => 'بريد، نموذج تواصل، واتساب/هاتف عند التفعيل.',
            ],
        ];
    }
}
