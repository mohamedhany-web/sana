<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'parent_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('role');
                $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('parent_students')) {
            Schema::create('parent_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->enum('relation', ['father', 'mother', 'guardian'])->default('guardian');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->unique(['parent_id', 'student_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'parent_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }

        Schema::dropIfExists('parent_students');
    }
};
