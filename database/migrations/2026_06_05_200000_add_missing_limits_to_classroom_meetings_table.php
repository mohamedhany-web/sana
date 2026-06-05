<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('classroom_meetings')) {
            return;
        }

        Schema::table('classroom_meetings', function (Blueprint $table) {
            if (! Schema::hasColumn('classroom_meetings', 'max_participants')) {
                $table->unsignedInteger('max_participants')->nullable()->after('title');
            }
            if (! Schema::hasColumn('classroom_meetings', 'participants_peak')) {
                $table->unsignedInteger('participants_peak')->default(0)->after('max_participants');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('classroom_meetings')) {
            return;
        }

        Schema::table('classroom_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('classroom_meetings', 'participants_peak')) {
                $table->dropColumn('participants_peak');
            }
            if (Schema::hasColumn('classroom_meetings', 'max_participants')) {
                $table->dropColumn('max_participants');
            }
        });
    }
};
