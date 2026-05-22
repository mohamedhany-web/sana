<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('portfolio_projects')) {
            return;
        }

        Schema::table('portfolio_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('portfolio_projects', 'content_type')) {
                $table->string('content_type')->default('gallery')->after('project_type'); // gallery, video, text, link
            }
            if (!Schema::hasColumn('portfolio_projects', 'video_url')) {
                $table->string('video_url')->nullable()->after('github_url');
            }
            if (!Schema::hasColumn('portfolio_projects', 'content_text')) {
                $table->longText('content_text')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('portfolio_projects')) {
            return;
        }

        Schema::table('portfolio_projects', function (Blueprint $table) {
            $cols = ['content_type', 'video_url', 'content_text'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('portfolio_projects', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};

