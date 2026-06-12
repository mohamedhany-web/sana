<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tutor_group_offers')) {
            Schema::create('tutor_group_offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
                $table->foreignId('academic_subject_id')->nullable()->constrained('academic_subjects')->nullOnDelete();
                $table->string('title', 160);
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('max_group_size')->default(6);
                $table->unsignedTinyInteger('min_group_size')->default(2);
                $table->unsignedSmallInteger('duration_minutes')->default(60);
                $table->decimal('display_price', 10, 2)->nullable();
                $table->json('subscription_plan_keys')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['instructor_id', 'is_active', 'sort_order'], 'tgo_inst_active_sort_idx');
            });
        }

        if (Schema::hasTable('lesson_bookings')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('lesson_bookings', 'tutor_group_offer_id')) {
                    $table->foreignId('tutor_group_offer_id')->nullable()->after('session_type')
                        ->constrained('tutor_group_offers')->nullOnDelete();
                }
                if (! Schema::hasColumn('lesson_bookings', 'max_group_size')) {
                    $table->unsignedTinyInteger('max_group_size')->nullable()->after('tutor_group_offer_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lesson_bookings')) {
            Schema::table('lesson_bookings', function (Blueprint $table) {
                if (Schema::hasColumn('lesson_bookings', 'tutor_group_offer_id')) {
                    $table->dropConstrainedForeignId('tutor_group_offer_id');
                }
                if (Schema::hasColumn('lesson_bookings', 'max_group_size')) {
                    $table->dropColumn('max_group_size');
                }
            });
        }

        Schema::dropIfExists('tutor_group_offers');
    }
};
