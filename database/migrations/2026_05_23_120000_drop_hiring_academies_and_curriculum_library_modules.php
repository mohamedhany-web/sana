<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('recruitment_teacher_presentations');
        Schema::dropIfExists('academy_opportunity_applications');
        Schema::dropIfExists('academy_opportunities');
        Schema::dropIfExists('hiring_academies');

        Schema::dropIfExists('curriculum_library_preview_opens');
        Schema::dropIfExists('curriculum_library_materials');
        Schema::dropIfExists('curriculum_library_sections');
        Schema::dropIfExists('curriculum_library_item_files');
        Schema::dropIfExists('curriculum_library_category_user');
        Schema::dropIfExists('curriculum_library_items');
        Schema::dropIfExists('curriculum_library_categories');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // لا يُعاد إنشاء الجداول — الوحدات أُزيلت من المنصة.
    }
};
