<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RepairStorageLinkCommand extends Command
{
    protected $signature = 'storage:repair-link';

    protected $description = 'إصلاح رابط public/storage → storage/app/public (يحل مشكلة الصور المكسورة محلياً)';

    public function handle(): int
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (! is_dir($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info('تم إنشاء: '.$target);
        }

        if (is_link($link)) {
            $this->info('الرابط موجود ويعمل: '.$link);

            return self::SUCCESS;
        }

        if (is_dir($link)) {
            $isEmpty = count(glob($link.DIRECTORY_SEPARATOR.'*')) === 0
                && count(glob($link.DIRECTORY_SEPARATOR.'.*')) <= 2;
            if ($isEmpty) {
                File::deleteDirectory($link);
                $this->warn('تم حذف مجلد public/storage الفارغ (لم يكن symlink).');
            } else {
                $backup = public_path('storage_backup_'.date('Ymd_His'));
                if (@rename($link, $backup)) {
                    $this->warn("تم نقل public/storage القديم إلى: {$backup}");
                } else {
                    $this->error('تعذّر استبدال public/storage — احذفه يدوياً ثم أعد تشغيل الأمر.');

                    return self::FAILURE;
                }
            }
        }

        $this->call('storage:link');
        $this->info('تم إنشاء رابط التخزين. جرّب فتح /storage/site/admin-panel-logo.png');

        return self::SUCCESS;
    }
}
