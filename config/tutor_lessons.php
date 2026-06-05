<?php

return [
    'settings_key' => 'tutor_lessons',

    'defaults' => [
        'default_student_lesson_hours' => 0,
        'self_schedule_enabled' => true,
        'booking_advance_days' => 14,
        'slot_step_minutes' => 30,
        'default_duration_minutes' => 60,
    ],

    /** مفتاح حد الساعات في feature_limits للاشتراك */
    'subscription_limit_key' => 'tutor_lesson_hours',

    /**
     * بعد حفظ الملف + جدول أسبوعي → تفعيل تلقائي للظهور أمام الطلاب.
     * عطّله (false) إذا أردت الاعتماد على الجلسة التجريبية أو تفعيل الإدارة فقط.
     */
    'auto_activate_on_setup' => env('TUTOR_AUTO_ACTIVATE_ON_SETUP', true),
];
