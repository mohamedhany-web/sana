<?php

use App\Models\Setting;
use App\Services\PublicFooterSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists(Setting::class)) {
            return;
        }

        $email = Setting::query()->where('key', 'footer_email')->first();
        if ($email && in_array(trim((string) $email->value), ['info@sana.edu', 'info@Sana.edu'], true)) {
            $email->update(['value' => (string) config('contact.email', 'info@sanaedu.com')]);
        }

        $phone = Setting::query()->where('key', 'footer_phone')->first();
        if ($phone && in_array(preg_replace('/\D+/', '', (string) $phone->value), ['01044610507', '201044610507'], true)) {
            $phone->delete();
        }

        $wa = Setting::query()->where('key', 'footer_whatsapp_url')->first();
        if ($wa && str_contains((string) $wa->value, '201044610507')) {
            $wa->delete();
        }

        PublicFooterSettings::forgetCache();
    }

    public function down(): void
    {
        // لا استرجاع للقيم القديمة غير المتسقة
    }
};
