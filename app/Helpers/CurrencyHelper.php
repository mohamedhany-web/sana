<?php

if (! function_exists('currency_code')) {
    function currency_code(): string
    {
        return (string) config('currency.code', 'SAR');
    }
}

if (! function_exists('currency_label')) {
    function currency_label(): string
    {
        return (string) __('public.currency');
    }
}

if (! function_exists('currency_suffix')) {
    /** مسافة + رمز العملة للعرض بعد الأرقام */
    function currency_suffix(): string
    {
        return ' '.currency_label();
    }
}
