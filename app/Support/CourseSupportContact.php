<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use App\Services\PublicFooterSettings;

class CourseSupportContact
{
    public static function whatsappBaseUrl(): string
    {
        $configured = trim((string) (PublicFooterSettings::payload()['whatsapp_url'] ?? ''));
        if ($configured !== '' && preg_match('#^https?://#i', $configured)) {
            return strtok($configured, '?') ?: $configured;
        }

        $digits = preg_replace('/\D+/', '', (string) config('services.platform.support_phone', ''));
        if ($digits === '') {
            return 'https://wa.me/';
        }

        return 'https://wa.me/'.$digits;
    }

    public static function urlForCourse(AdvancedCourse $course): string
    {
        $base = rtrim(self::whatsappBaseUrl(), '/');
        $message = 'مرحباً، أريد الاستفسار عن كورس: '.($course->title ?? 'كورس').' (رقم #'.$course->id.')';

        return $base.'?text='.rawurlencode($message);
    }
}
