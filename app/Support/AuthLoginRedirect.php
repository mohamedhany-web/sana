<?php

namespace App\Support;

class AuthLoginRedirect
{
    /** @var list<string> */
    private const PUBLIC_ROLES = ['student', 'parent'];

    /**
     * أين نوجّه ضيفاً حسب أدوار البوابة المطلوبة.
     */
    public static function guestLoginUrl(?string $rolesPipe = null): string
    {
        if ($rolesPipe === null || trim($rolesPipe) === '') {
            return route('staff.login');
        }

        $roles = array_values(array_filter(array_map('trim', explode('|', $rolesPipe))));

        if ($roles !== [] && empty(array_diff($roles, self::PUBLIC_ROLES))) {
            return route('login');
        }

        return route('staff.login');
    }
}
