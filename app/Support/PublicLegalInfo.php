<?php

namespace App\Support;

/**
 * بيانات قانونية للصفحات العامة (الخصوصية، الشروط).
 */
final class PublicLegalInfo
{
    /**
     * @return array{
     *   entity_name: string,
     *   entity_name_en: string,
     *   privacy_email: string,
     *   jurisdiction: string,
     *   law_framework: string,
     *   official_email: string,
     *   address: string,
     *   retention: array<string, string>
     * }
     */
    public static function payload(): array
    {
        $domain = PublicContactInfo::domain();
        $privacyEmail = trim((string) config('legal.privacy_email', 'privacy@'.$domain));
        if ($privacyEmail === '' || ! str_contains($privacyEmail, '@')) {
            $privacyEmail = 'privacy@'.$domain;
        }

        return [
            'entity_name' => trim((string) config('legal.entity_name', config('app.name', 'Sana'))),
            'entity_name_en' => trim((string) config('legal.entity_name_en', 'Sana Educational')),
            'privacy_email' => $privacyEmail,
            'jurisdiction' => trim((string) config('legal.jurisdiction', 'المملكة العربية السعودية')),
            'law_framework' => trim((string) config('legal.law_framework', '')),
            'official_email' => PublicContactInfo::officialEmail(),
            'address' => trim((string) config('contact.address', '')),
            'retention' => (array) config('legal.retention', []),
        ];
    }
}
