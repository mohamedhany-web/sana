<?php

/**
 * بيانات التواصل العامة — مصدر واحد للفوتر وصفحة «تواصل معنا».
 * يُفضّل البريد على نطاق الموقع (مثل info@sanaedu.com) ورقم سعودي بصيغة +966.
 */
return [
    'domain' => env('CONTACT_DOMAIN', 'sanaedu.com'),

    'email' => env('CONTACT_EMAIL', 'info@sanaedu.com'),

    /** رقم سعودي بصيغة دولية — اتركه فارغاً حتى يُضبط من .env أو لوحة الإدارة */
    'phone' => env('CONTACT_PHONE'),

    /** مثال: https://wa.me/9665XXXXXXXX */
    'whatsapp_url' => env('CONTACT_WHATSAPP_URL'),

    'address' => env('CONTACT_ADDRESS', 'الرياض، المملكة العربية السعودية'),

    'service_scope' => 'مقرّنا في الرياض — نخدم العائلات في السعودية ومصر عن بُعد. الهاتف وواتساب عبر رقم سعودي في أوقات العمل (توقيت GMT+3).',

    'support' => [
        'days' => 'الأحد – الخميس',
        'hours' => '9 ص – 9 م',
        'hours_full' => 'الأحد – الخميس: 9 ص – 9 م',
        'timezone' => 'توقيت السعودية (GMT+3)',
        'response_sla' => 'خلال 24 ساعة',
    ],
];
