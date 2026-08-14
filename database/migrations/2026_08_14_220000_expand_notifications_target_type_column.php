<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * الهدف كان ENUM قديماً لطلاب فقط؛ إشعارات المدربين/الموظفين تفشل بـ Data truncated.
     * نحوّله إلى VARCHAR لاستيعاب كل قيم getTargetTypes().
     */
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (! Schema::hasColumn('notifications', 'target_type')) {
            return;
        }

        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `target_type` VARCHAR(40) NOT NULL DEFAULT 'individual'");

        if (Schema::hasColumn('notifications', 'type')) {
            DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee', 'instructor') DEFAULT 'general'");
        }

        if (! Schema::hasColumn('notifications', 'audience')) {
            Schema::table('notifications', function ($table) {
                $table->string('audience', 20)->nullable()->after('target_id');
                $table->index('audience');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications') || ! Schema::hasColumn('notifications', 'target_type')) {
            return;
        }

        // إعادة ENUM الطلاب فقط — قد تفشل إن وُجدت صفوف بقيم مدرب/موظف
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `target_type` ENUM('all_students', 'course_students', 'year_students', 'subject_students', 'individual') NOT NULL DEFAULT 'individual'");
    }
};
