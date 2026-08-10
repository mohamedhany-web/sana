<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_bookings', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('lesson_bookings', 'hours_deducted')) {
                $table->boolean('hours_deducted')->default(false)->after('billable_minutes');
            }
            if (! Schema::hasColumn('lesson_bookings', 'instructor_rated_at')) {
                $table->timestamp('instructor_rated_at')->nullable()->after('reminder_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lesson_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_bookings', 'instructor_rated_at')) {
                $table->dropColumn('instructor_rated_at');
            }
            if (Schema::hasColumn('lesson_bookings', 'hours_deducted')) {
                $table->dropColumn('hours_deducted');
            }
            if (Schema::hasColumn('lesson_bookings', 'reminder_sent_at')) {
                $table->dropColumn('reminder_sent_at');
            }
        });
    }
};
