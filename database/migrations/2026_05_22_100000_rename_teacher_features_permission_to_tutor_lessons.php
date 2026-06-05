<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('permissions')) {
            return;
        }

        $old = DB::table('permissions')->where('name', 'manage.teacher-features')->first();
        if (! $old) {
            return;
        }

        $exists = DB::table('permissions')->where('name', 'manage.tutor-lessons')->exists();
        if ($exists) {
            return;
        }

        DB::table('permissions')->where('id', $old->id)->update([
            'name' => 'manage.tutor-lessons',
            'display_name' => 'رقابة حصص المعلمين وباقات الاشتراك',
            'description' => 'حصص الطلاب، الحجوزات، باقات المدرب، وإعدادات الساعات',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('permissions')) {
            return;
        }

        $row = DB::table('permissions')->where('name', 'manage.tutor-lessons')->first();
        if (! $row) {
            return;
        }

        DB::table('permissions')->where('id', $row->id)->update([
            'name' => 'manage.teacher-features',
            'display_name' => 'إدارة مزايا اشتراك المدربين',
            'description' => 'إدارة مزايا ومستويات اشتراك المدربين',
            'updated_at' => now(),
        ]);
    }
};
