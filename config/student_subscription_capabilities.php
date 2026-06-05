<?php

/**
 * قدرات اشتراك الطالب في النظام الحالي (ليست مزايا باقات المدرب).
 */
return [
    'tutor_lessons' => [
        'label' => 'حصص مع المعلمين',
        'icon' => 'fa-chalkboard-user',
        'icon_bg' => 'bg-violet-100',
        'icon_text' => 'text-violet-600',
        'description' => 'رصيد ساعات من الباقة (feature_limits.tutor_lesson_hours)',
    ],
    'support' => [
        'label' => 'الدعم الفني',
        'icon' => 'fa-headset',
        'icon_bg' => 'bg-sky-100',
        'icon_text' => 'text-sky-600',
        'description' => 'تذاكر الدعم (ميزة support أو direct_support في الاشتراك)',
        'feature_keys' => ['support', 'direct_support'],
    ],
];
