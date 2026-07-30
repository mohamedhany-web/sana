<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contact_messages')) {
            return;
        }

        Schema::table('contact_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_messages', 'source')) {
                $table->string('source', 40)->default('contact_page')->after('message');
            }
            if (! Schema::hasColumn('contact_messages', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('source');
            }
            if (! Schema::hasColumn('contact_messages', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('contact_messages', 'referrer')) {
                $table->string('referrer', 500)->nullable()->after('user_agent');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contact_messages')) {
            return;
        }

        Schema::table('contact_messages', function (Blueprint $table) {
            foreach (['source', 'ip_address', 'user_agent', 'referrer'] as $col) {
                if (Schema::hasColumn('contact_messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
