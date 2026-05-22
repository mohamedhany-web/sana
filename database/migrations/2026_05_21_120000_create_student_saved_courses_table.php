<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_saved_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advanced_course_id')->constrained('advanced_courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'advanced_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_saved_courses');
    }
};
