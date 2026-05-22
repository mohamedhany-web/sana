<?php

/**
 * مزايا باقات المدرب (وما يُعرض في القائمة الجانبية عند تفعيلها). Classroom للمدرب فقط.
 * مفتاح المصفوفة = feature_key كما في subscription.features
 * route = اسم المسار (استخدم student.features.show مع معامل {feature} للمزايا العامة، أو مسار مخصص)
 */
return [
    'library_access' => [
        'icon' => 'fa-book-open',
        'icon_bg' => 'bg-indigo-100 dark:bg-indigo-900/40',
        'icon_text' => 'text-indigo-600 dark:text-indigo-400',
        'route' => 'curriculum-library.index',
        'route_params' => [],
    ],
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
    'visible_to_academies' => [
        'icon' => 'fa-building',
        'icon_bg' => 'bg-teal-100 dark:bg-teal-900/40',
        'icon_text' => 'text-teal-600 dark:text-teal-400',
        'route' => 'student.academies.visibility',
        'route_params' => [],
    ],
    'can_apply_opportunities' => [
        'icon' => 'fa-briefcase',
        'icon_bg' => 'bg-rose-100 dark:bg-rose-900/40',
        'icon_text' => 'text-rose-600 dark:text-rose-400',
        'route' => 'student.opportunities.index',
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
    'recommended_to_academies' => [
        'icon' => 'fa-hand-holding-heart',
        'icon_bg' => 'bg-pink-100 dark:bg-pink-900/40',
        'icon_text' => 'text-pink-600 dark:text-pink-400',
        'route' => 'student.features.show',
        'route_params' => ['feature' => 'recommended_to_academies'],
    ],
    'priority_opportunities' => [
        'icon' => 'fa-bolt',
        'icon_bg' => 'bg-orange-100 dark:bg-orange-900/40',
        'icon_text' => 'text-orange-600 dark:text-orange-400',
        'route' => 'student.features.show',
        'route_params' => ['feature' => 'priority_opportunities'],
    ],
    'direct_support' => [
        'icon' => 'fa-life-ring',
        'icon_bg' => 'bg-cyan-100 dark:bg-cyan-900/40',
        'icon_text' => 'text-cyan-600 dark:text-cyan-400',
        'route' => 'student.support.index',
        'route_params' => [],
    ],
];
