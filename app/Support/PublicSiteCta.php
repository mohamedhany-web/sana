<?php

namespace App\Support;

/**
 * أزرار الدعوة لإجراء الموحّدة للموقع العام.
 */
final class PublicSiteCta
{
    public static function primaryLabel(bool $hero = false): string
    {
        return $hero
            ? (string) __('public.cta_assessment_free')
            : (string) __('public.cta_assessment');
    }

    public static function secondaryLabel(): string
    {
        return (string) __('public.cta_whatsapp');
    }

    public static function assessmentUrl(): string
    {
        return route('public.contact', ['topic' => 'assessment']).'#contact-form';
    }

    public static function familiesPathUrl(): string
    {
        return route('home').'#paths';
    }

    public static function teachersPathUrl(): string
    {
        return route('tutor.apply');
    }

    public static function howItWorksUrl(): string
    {
        return route('public.how_it_works');
    }

    public static function whatsappUrl(): string
    {
        $url = trim(PublicContactInfo::payload()['whatsapp_url'] ?? '');

        return $url !== '' ? $url : route('public.contact');
    }

    public static function hasWhatsapp(): bool
    {
        return trim(PublicContactInfo::payload()['whatsapp_url'] ?? '') !== '';
    }

    /** @return array{primary_label: string, primary_label_hero: string, secondary_label: string, assessment_url: string, whatsapp_url: string, has_whatsapp: bool, families_path_url: string, teachers_path_url: string, how_it_works_url: string} */
    public static function payload(): array
    {
        return [
            'primary_label' => self::primaryLabel(),
            'primary_label_hero' => self::primaryLabel(true),
            'secondary_label' => self::secondaryLabel(),
            'assessment_url' => self::assessmentUrl(),
            'whatsapp_url' => self::whatsappUrl(),
            'has_whatsapp' => self::hasWhatsapp(),
            'families_path_url' => self::familiesPathUrl(),
            'teachers_path_url' => self::teachersPathUrl(),
            'how_it_works_url' => self::howItWorksUrl(),
        ];
    }
}
