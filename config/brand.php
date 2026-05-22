<?php

/**
 * ألوان العلامة التجارية لمنصة Sana (الألوان الأساسية الرسمية).
 *
 * | اللون   | Hex      | RGB              |
 * |---------|----------|------------------|
 * | بنفسجي  | #6A2CFF  | 106, 44, 255     |
 * | أزرق    | #1D4EDB  | 29, 78, 219      |
 * | أصفر    | #F4B000  | 244, 176, 0      |
 */
return [
    'name' => env('BRAND_NAME', env('APP_NAME', 'Sana')),

    'colors' => [
        'purple' => '#6A2CFF',
        'purple_rgb' => '106, 44, 255',
        'purple_dark' => '#5520CC',
        'purple_light' => '#F0EBFF',

        'blue' => '#1D4EDB',
        'blue_rgb' => '29, 78, 219',
        'blue_dark' => '#1639B0',
        'blue_light' => '#E8EEFB',

        'yellow' => '#F4B000',
        'yellow_rgb' => '244, 176, 0',
        'yellow_dark' => '#D99E00',
        'yellow_light' => '#FFF8E6',
    ],
];
