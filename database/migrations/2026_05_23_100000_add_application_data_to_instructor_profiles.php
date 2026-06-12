<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_profiles', 'application_data')) {
                $table->json('application_data')->nullable()->after('submitted_at');
            }
            if (! Schema::hasColumn('instructor_profiles', 'application_evaluation')) {
                $table->json('application_evaluation')->nullable()->after('application_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('instructor_profiles', 'application_evaluation')) {
                $table->dropColumn('application_evaluation');
            }
            if (Schema::hasColumn('instructor_profiles', 'application_data')) {
                $table->dropColumn('application_data');
            }
        });
    }
};
