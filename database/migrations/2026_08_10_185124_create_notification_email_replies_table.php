<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_email_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('status', 20)->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_email_replies');
    }
};
