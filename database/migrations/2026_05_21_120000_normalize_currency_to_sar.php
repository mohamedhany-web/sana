<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'wallets',
        'wallet_transactions',
        'transactions',
        'payments',
        'invoices',
        'expenses',
        'orders',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'currency')) {
                continue;
            }
            DB::table($table)->where('currency', 'EGP')->update(['currency' => 'SAR']);
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'currency')) {
                continue;
            }
            DB::table($table)->where('currency', 'SAR')->update(['currency' => 'EGP']);
        }
    }
};
