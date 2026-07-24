<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_bookings', function (Blueprint $table) {
            $table->uuid('group_session_key')->nullable()->after('max_group_size')->index();
        });
    }

    public function down(): void
    {
        Schema::table('lesson_bookings', function (Blueprint $table) {
            $table->dropIndex(['group_session_key']);
            $table->dropColumn('group_session_key');
        });
    }
};
