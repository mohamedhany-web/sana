<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lesson_booking_ratings')) {
            return;
        }

        Schema::table('lesson_booking_ratings', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_booking_ratings', 'lesson_rating')) {
                $table->unsignedTinyInteger('lesson_rating')->nullable()->after('rating');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lesson_booking_ratings')) {
            return;
        }

        Schema::table('lesson_booking_ratings', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_booking_ratings', 'lesson_rating')) {
                $table->dropColumn('lesson_rating');
            }
        });
    }
};
