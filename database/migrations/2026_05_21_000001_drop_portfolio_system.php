<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('portfolio_project_images');
        Schema::dropIfExists('portfolio_projects');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'portfolio_profile_reviewed_by')) {
                $table->dropForeign(['portfolio_profile_reviewed_by']);
            }
        });

        $userPortfolioColumns = [
            'portfolio_marketing_published',
            'portfolio_profile_rejected_reason',
            'portfolio_profile_reviewed_by',
            'portfolio_profile_reviewed_at',
            'portfolio_profile_submitted_at',
            'portfolio_profile_status',
            'portfolio_intro_video_url',
            'portfolio_social_links',
            'portfolio_skills',
            'portfolio_about',
            'portfolio_headline',
        ];

        Schema::table('users', function (Blueprint $table) use ($userPortfolioColumns) {
            foreach ($userPortfolioColumns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        // لا استعادة تلقائية — نظام البورتفوليو أُزيل من المنصة.
    }
};
