<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // نظام البورتفوليو أُزيل من المنصة — لا إضافة أعمدة مراجعة الملف التسويقي.
        if (! Schema::hasColumn('users', 'portfolio_intro_video_url')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'portfolio_profile_status')) {
                $table->string('portfolio_profile_status', 32)->nullable()->after('portfolio_intro_video_url');
            }
            if (! Schema::hasColumn('users', 'portfolio_profile_submitted_at')) {
                $table->timestamp('portfolio_profile_submitted_at')->nullable()->after('portfolio_profile_status');
            }
            if (! Schema::hasColumn('users', 'portfolio_profile_reviewed_at')) {
                $table->timestamp('portfolio_profile_reviewed_at')->nullable()->after('portfolio_profile_submitted_at');
            }
            if (! Schema::hasColumn('users', 'portfolio_profile_reviewed_by')) {
                $table->foreignId('portfolio_profile_reviewed_by')->nullable()->after('portfolio_profile_reviewed_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'portfolio_profile_rejected_reason')) {
                $table->text('portfolio_profile_rejected_reason')->nullable()->after('portfolio_profile_reviewed_by');
            }
            if (! Schema::hasColumn('users', 'portfolio_marketing_published')) {
                $table->json('portfolio_marketing_published')->nullable()->after('portfolio_profile_rejected_reason');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'portfolio_profile_status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'portfolio_profile_reviewed_by')) {
                $table->dropForeign(['portfolio_profile_reviewed_by']);
            }
            foreach ([
                'portfolio_marketing_published',
                'portfolio_profile_rejected_reason',
                'portfolio_profile_reviewed_by',
                'portfolio_profile_reviewed_at',
                'portfolio_profile_submitted_at',
                'portfolio_profile_status',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
