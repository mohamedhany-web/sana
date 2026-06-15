<?php

namespace App\Support;

use App\Models\AdvancedCourse;

class CourseSupportContact
{
    public static function whatsappBaseUrl(): string
    {
        $contact = PublicContactInfo::payload();
        $configured = trim((string) ($contact['whatsapp_url'] ?? ''));
        if ($configured !== '') {
            return strtok($configured, '?') ?: $configured;
        }

        $digits = preg_replace('/\D+/', '', (string) ($contact['phone'] ?? ''));
        if ($digits !== '' && ! in_array($digits, ['01044610507', '201044610507'], true)) {
            return 'https://wa.me/'.$digits;
        }

        return route('public.contact');
    }

    public static function urlForCourse(AdvancedCourse $course): string
    {
        $base = rtrim(self::whatsappBaseUrl(), '/');
        if (! str_starts_with($base, 'http')) {
            return $base;
        }

        $message = 'مرحباً، أريد الاستفسار عن كورس: '.($course->title ?? 'كورس').' (رقم #'.$course->id.')';

        return $base.'?text='.rawurlencode($message);
    }
}
