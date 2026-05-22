<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'contact_support_for_pricing')) {
                $table->boolean('contact_support_for_pricing')->default(false)->after('price_after_discount');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'contact_support_for_pricing')) {
                $table->dropColumn('contact_support_for_pricing');
            }
        });
    }
};
