<?php

/**
 * مزايا باقات المدرب (وما يُعرض في القائمة الجانبية عند تفعيلها). Classroom للمدرب فقط.
 */
return [
    'ai_tools' => [
        'icon' => 'fa-robot',
        'icon_bg' => 'bg-violet-100 dark:bg-violet-900/40',
        'icon_text' => 'text-violet-600 dark:text-violet-400',
        'route' => 'student.features.show',
        'route_params' => ['feature' => 'ai_tools'],
    ],
    'classroom_access' => [
        'icon' => 'fa-chalkboard-teacher',
        'icon_bg' => 'bg-blue-100 dark:bg-blue-900/40',
        'icon_text' => 'text-blue-600 dark:text-blue-400',
        'route' => 'instructor.classroom.index',
        'route_params' => [],
    ],
    'support' => [
        'icon' => 'fa-headset',
        'icon_bg' => 'bg-amber-100 dark:bg-amber-900/40',
        'icon_text' => 'text-amber-600 dark:text-amber-400',
        'route' => 'student.support.index',
        'route_params' => [],
    ],
    'full_ai_suite' => [
        'icon' => 'fa-wand-magic-sparkles',
        'icon_bg' => 'bg-purple-100 dark:bg-purple-900/40',
        'icon_text' => 'text-purple-600 dark:text-purple-400',
        'route' => 'student.features.show',
        'route_params' => ['feature' => 'full_ai_suite'],
    ],
    'teacher_evaluation' => [
        'icon' => 'fa-star',
        'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/40',
        'icon_text' => 'text-yellow-600 dark:text-yellow-400',
        'route' => 'student.features.show',
        'route_params' => ['feature' => 'teacher_evaluation'],
    ],
    'direct_support' => [
        'icon' => 'fa-life-ring',
        'icon_bg' => 'bg-cyan-100 dark:bg-cyan-900/40',
        'icon_text' => 'text-cyan-600 dark:text-cyan-400',
        'route' => 'student.support.index',
        'route_params' => [],
    ],
];
