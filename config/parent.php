<?php

return [
    /** كلمة المرور الافتراضية لحسابات أولياء الأمور الجدد */
    'default_password' => env('PARENT_DEFAULT_PASSWORD', 'Sana@2025'),

    /** ألوان لوحة ولي الأمر (teal + amber) */
    'colors' => [
        'primary' => '#0d9488',
        'primary_dark' => '#0f766e',
        'accent' => '#d97706',
        'soft' => '#ecfdf5',
        'soft_dark' => '#134e4a',
    ],
];
