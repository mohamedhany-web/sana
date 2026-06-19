<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * تعبيرات SQL آمنة مع ONLY_FULL_GROUP_BY في MySQL/MariaDB.
 */
final class SqlGroupExpressions
{
    public static function subscriptionPlanLabel(string $column = 'plan_name'): string
    {
        return "COALESCE(NULLIF({$column}, ''), 'غير محدد')";
    }

    public static function subscriptionTypeLabel(string $column = 'subscription_type'): string
    {
        return "COALESCE(NULLIF({$column}, ''), 'other')";
    }

    /** @return array<int, \Illuminate\Contracts\Database\Query\Expression> */
    public static function mysqlYearMonth(string $column = 'created_at'): array
    {
        return [
            DB::raw('YEAR('.$column.')'),
            DB::raw('MONTH('.$column.')'),
        ];
    }

    /** @return array<int, \Illuminate\Contracts\Database\Query\Expression> */
    public static function mysqlDate(string $column = 'created_at'): array
    {
        return [DB::raw('DATE('.$column.')')];
    }
}
