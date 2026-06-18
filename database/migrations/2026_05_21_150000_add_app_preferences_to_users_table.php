<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'app_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('app_preferences')->nullable()->after('onboarding_preferences');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'app_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('app_preferences');
            });
        }
    }
};
