<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee', 'instructor') DEFAULT 'general'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee') DEFAULT 'general'");
    }
};
