<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('student_learning_profiles')) {
            return;
        }

        if (! Schema::hasColumn('student_learning_profiles', 'lesson_minutes_used')) {
            Schema::table('student_learning_profiles', function (Blueprint $table) {
                $table->unsignedInteger('lesson_minutes_used')->default(0)->after('lesson_hours_used');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_learning_profiles') && Schema::hasColumn('student_learning_profiles', 'lesson_minutes_used')) {
            Schema::table('student_learning_profiles', function (Blueprint $table) {
                $table->dropColumn('lesson_minutes_used');
            });
        }
    }
};
