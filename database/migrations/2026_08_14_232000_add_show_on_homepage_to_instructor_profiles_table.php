<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('instructor_profiles')) {
            return;
        }

        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_profiles', 'show_on_homepage')) {
                $table->boolean('show_on_homepage')->default(false)->after('status')
                    ->comment('إظهار الملف في الصفحة الرئيسية/قائمة المعلمين — مستقل عن قبول الطلب');
            }
        });

        // من كانوا «معتمدين» سابقاً كانوا يظهرون للجمهور — نحفظ نفس الظهور
        if (Schema::hasColumn('instructor_profiles', 'show_on_homepage')) {
            DB::table('instructor_profiles')
                ->where('status', 'approved')
                ->update(['show_on_homepage' => true]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('instructor_profiles')) {
            return;
        }

        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('instructor_profiles', 'show_on_homepage')) {
                $table->dropColumn('show_on_homepage');
            }
        });
    }
};
