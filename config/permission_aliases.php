<?php

/**
 * أسماء الصلاحيات في السايدبار والمسارات (manage.* / view.* من PermissionsSeeder)
 * مقابل الأسماء القديمة من PermissionsAndRolesSeeder (courses.*، users.*، …).
 *
 * عند التحقق من صلاحية «حديثة» يُقبل أيضاً امتلاك أي اسم قديم مُدرَج هنا.
 */
return [
    'legacy_names_for_canonical' => [
        'manage.users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
        'manage.courses' => ['courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.manage_own'],
        'manage.lectures' => ['lectures.view', 'lectures.create', 'lectures.edit', 'lectures.delete', 'lectures.manage_own'],
        'manage.assignments' => ['assignments.view', 'assignments.create', 'assignments.grade', 'assignments.delete'],
        'manage.exams' => ['exams.view', 'exams.create', 'exams.edit', 'exams.delete'],
        'manage.invoices' => ['invoices.view', 'invoices.create', 'invoices.edit'],
        'manage.payments' => ['payments.view', 'payments.create'],
        'manage.wallets' => ['wallets.view', 'wallets.manage'],
        'manage.tasks' => ['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete'],
        'manage.notifications' => ['notifications.view', 'notifications.send'],
        'manage.certificates' => ['certificates.view', 'certificates.generate'],
        'manage.about-page' => ['pages.manage'],
        'view.reports' => ['reports.view'],
        'view.financial-reports' => ['reports.financial'],
        'view.statistics' => ['reports.view'],
        'manage.user-permissions' => ['users.permissions'],
        // وسيط المسارات permission:users.permissions (أدوار، صلاحيات، صلاحيات المستخدمين)
        'users.permissions' => ['manage.user-permissions', 'manage.roles', 'manage.permissions'],

        // أدوار قديمة كانت تمنح «مستخدمين» دون صلاحية manage.students-accounts الصريحة
        'manage.students-accounts' => ['users.view', 'users.create', 'users.edit', 'users.delete'],

        // تسجيلات الطلاب كانت تُدار غالباً ضمن صلاحيات الكورسات القديمة
        'manage.enrollments' => ['courses.view', 'courses.create', 'courses.edit', 'courses.delete'],

        // تقارير النشاط / لوحة التقارير العامة
        'view.activity-log' => ['reports.view'],

        'view.wallets' => ['wallets.view', 'wallets.manage'],

        'view.academic-reports' => ['reports.view'],
    ],
];
