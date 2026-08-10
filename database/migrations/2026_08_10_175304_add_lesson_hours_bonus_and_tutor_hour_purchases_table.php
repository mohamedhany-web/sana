<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('student_learning_profiles') && ! Schema::hasColumn('student_learning_profiles', 'lesson_hours_bonus')) {
            Schema::table('student_learning_profiles', function (Blueprint $table) {
                $table->unsignedInteger('lesson_hours_bonus')->default(0)->after('lesson_hours_used');
            });
        }

        if (! Schema::hasTable('tutor_hour_purchases')) {
            Schema::create('tutor_hour_purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('plan_key', 64);
                $table->string('plan_name');
                $table->unsignedInteger('hours');
                $table->decimal('price', 10, 2)->default(0);
                $table->string('billing_cycle', 32)->nullable();
                $table->string('payment_method', 32);
                $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
                $table->string('payment_proof')->nullable();
                $table->string('status', 32)->default('pending');
                $table->text('notes')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_hour_purchases');

        if (Schema::hasTable('student_learning_profiles') && Schema::hasColumn('student_learning_profiles', 'lesson_hours_bonus')) {
            Schema::table('student_learning_profiles', function (Blueprint $table) {
                $table->dropColumn('lesson_hours_bonus');
            });
        }
    }
};
