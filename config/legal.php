<?php

/**
 * بيانات قانونية عامة — الكيان المسؤول، الخصوصية، والاحتفاظ بالبيانات.
 */
return [
    'entity_name' => env('LEGAL_ENTITY_NAME', 'سنا التعليمية'),

    'entity_name_en' => env('LEGAL_ENTITY_NAME_EN', 'Sana Educational'),

    /** بريد مخصّص لطلبات الخصوصية وحذف البيانات */
    'privacy_email' => env('PRIVACY_EMAIL', 'privacy@sanaedu.com'),

    'jurisdiction' => env('LEGAL_JURISDICTION', 'المملكة العربية السعودية'),

    'law_framework' => 'نظام حماية البيانات الشخصية الصادر عن SDAIA والأنظمة ذات الصلة في المملكة',

    'retention' => [
        'account_active' => 'طوال مدة نشاط الحساب',
        'account_after_closure' => '12 شهراً بعد إغلاق الحساب (ما لم يُلزمنا القانون بالاحتفاظ أطول)',
        'payment_records' => '7 سنوات وفق متطلبات المحاسبة والضريبة',
        'server_logs_ip' => '90 يوماً لعناوين IP وسجلات الأمان والوصول',
        'support_messages' => '24 شهراً لمراسلات الدعم',
    ],
];
