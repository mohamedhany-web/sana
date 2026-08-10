<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agreement_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('agreement_payments', 'lesson_booking_id')) {
                $table->foreignId('lesson_booking_id')
                    ->nullable()
                    ->after('agreement_id')
                    ->constrained('lesson_bookings')
                    ->nullOnDelete();
                $table->unique('lesson_booking_id', 'agreement_payments_lesson_booking_unique');
            }
            if (! Schema::hasColumn('agreement_payments', 'minutes_count')) {
                $table->unsignedInteger('minutes_count')->nullable()->after('hours_count');
            }
        });

        if (Schema::hasTable('instructor_agreements') && Schema::hasColumn('instructor_agreements', 'billing_type')) {
            DB::table('instructor_agreements')
                ->where('type', 'hourly_rate')
                ->where(function ($q) {
                    $q->whereNull('billing_type')->orWhere('billing_type', '');
                })
                ->update(['billing_type' => 'hourly_lesson']);
        }
    }

    public function down(): void
    {
        Schema::table('agreement_payments', function (Blueprint $table) {
            if (Schema::hasColumn('agreement_payments', 'lesson_booking_id')) {
                $table->dropUnique('agreement_payments_lesson_booking_unique');
                $table->dropConstrainedForeignId('lesson_booking_id');
            }
            if (Schema::hasColumn('agreement_payments', 'minutes_count')) {
                $table->dropColumn('minutes_count');
            }
        });
    }
};
