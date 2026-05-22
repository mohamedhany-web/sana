<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'consultation_request_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('consultation_request_id');
            });
        }

        Schema::dropIfExists('consultation_requests');
        Schema::dropIfExists('consultation_settings');

        if (Schema::hasTable('instructor_profiles')) {
            Schema::table('instructor_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('instructor_profiles', 'consultation_price_egp')) {
                    $table->dropColumn('consultation_price_egp');
                }
                if (Schema::hasColumn('instructor_profiles', 'consultation_duration_minutes')) {
                    $table->dropColumn('consultation_duration_minutes');
                }
            });
        }

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('name', 'manage.consultations')->delete();
        }
    }

    public function down(): void
    {
        // لا استعادة — النظام أُلغي من المنصة
    }
};
