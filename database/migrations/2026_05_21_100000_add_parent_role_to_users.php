<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'instructor', 'student', 'parent') DEFAULT 'student'");
            } catch (\Throwable) {
                // قد يكون العمود varchar أو محدّثاً مسبقاً
            }
        }

        if (! Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('must_change_password')->default(false)->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('must_change_password');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement("UPDATE users SET role = 'student' WHERE role = 'parent'");
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'instructor', 'student') DEFAULT 'student'");
            } catch (\Throwable) {
                //
            }
        }
    }
};
