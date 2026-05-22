<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('users')) {
            return;
        }

        $replacements = [
            'MuallimX' => 'Sana',
            'Muallimx' => 'Sana',
            'muallimx' => 'Sana',
            'MUALLIMX' => 'Sana',
            'معلمكس' => 'Sana',
            'معليمكس' => 'Sana',
        ];

        DB::table('users')
            ->select('id', 'name')
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($replacements) {
                foreach ($users as $user) {
                    $name = (string) ($user->name ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $updated = $name;
                    foreach ($replacements as $from => $to) {
                        $updated = str_ireplace($from, $to, $updated);
                    }

                    if (preg_match('/مدير\s+منص[ةة]\s+Sana/ui', $updated)) {
                        $updated = preg_replace('/مدير\s+منص[ةة]\s+Sana/ui', 'مدير المنصة', $updated) ?? $updated;
                    }

                    $updated = trim(preg_replace('/\s+/u', ' ', $updated) ?? $updated);

                    if ($updated !== $name && $updated !== '') {
                        DB::table('users')->where('id', $user->id)->update(['name' => $updated]);
                    }
                }
            });
    }

    public function down(): void
    {
        // لا استرجاع تلقائي لأسماء المستخدمين
    }
};
