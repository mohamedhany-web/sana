<?php

namespace App\Console\Commands;

use App\Services\AdminPanelBranding;
use Illuminate\Console\Command;

class PublishBrandingImagesCommand extends Command
{
    protected $signature = 'storage:publish-branding';

    protected $description = 'نسخ شعار لوحة التحكم إلى public/images/branding (يعمل على Hostinger بدون /storage/)';

    public function handle(): int
    {
        if (AdminPanelBranding::publishWebLogo()) {
            $this->info('تم النشر: '.(AdminPanelBranding::logoPublicUrl() ?? '—'));

            return self::SUCCESS;
        }

        $this->error('لم يُعثر على ملف الشعار في التخزين أو R2. ارفع الشعار من إعدادات النظام أولاً.');

        return self::FAILURE;
    }
}
