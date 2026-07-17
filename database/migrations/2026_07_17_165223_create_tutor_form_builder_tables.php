<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_form_steps', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            /** intro | fields | review */
            $table->string('step_type', 20)->default('fields');
            $table->timestamps();
        });

        Schema::create('tutor_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('tutor_form_steps')->cascadeOnDelete();
            $table->string('field_key', 100)->unique();
            $table->string('label');
            $table->string('help_text', 500)->nullable();
            $table->string('placeholder', 255)->nullable();
            /**
             * text, textarea, email, password, tel, number, url,
             * select, multiselect, checkbox_group, radio, file, date,
             * country_phone, weekly_availability, subjects, academic_years,
             * video_pair, commitments, matching_modes, declaration, info
             */
            $table->string('field_type', 40);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            /** half | full */
            $table->string('width', 10)->default('full');
            /** static options [{value,label}] or {source: specializations|...} */
            $table->json('options')->nullable();
            /** max, min, mimes, accept, rows, ... */
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['step_id', 'sort_order']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_form_fields');
        Schema::dropIfExists('tutor_form_steps');
    }
};
