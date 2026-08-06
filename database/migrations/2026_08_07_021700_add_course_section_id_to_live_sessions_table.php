<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'course_section_id')) {
                $table->foreignId('course_section_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('course_sections')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('live_sessions', 'course_section_id')) {
                $table->dropConstrainedForeignId('course_section_id');
            }
        });
    }
};
