<?php

namespace App\Console\Commands;

use App\Support\PublicStorageLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RepairStorageLinkCommand extends Command
{
    protected $signature = 'storage:repair-link
                            {--no-mirror : عدم نسخ الملفات إلى public/storage إن فشل symlink}
                            {--mirror-only : نسخ الملفات فقط (بدون محاولة symlink)}';

    protected $description = 'إصلاح public/storage بدون exec() — symlink أو mirror أو عرض عبر /storage/';

    public function handle(): int
    {
        $target = PublicStorageLink::targetPath();
        $link = PublicStorageLink::linkPath();

        if (! is_dir($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info('تم إنشاء: '.$target);
        }

        if (PublicStorageLink::isValidLink()) {
            $this->info('الرابط موجود: '.$link);

            return self::SUCCESS;
        }

        if ($this->option('mirror-only')) {
            PublicStorageLink::removeLinkPath($link);
            $mode = PublicStorageLink::mirrorDirectory($target, $link) ? 'mirrored' : 'route_only';
        } else {
            $mode = PublicStorageLink::establish(! $this->option('no-mirror'));
        }

        if ($mode === false) {
            $mode = 'route_only';
        }

        $this->info('وضع التخزين: '.PublicStorageLink::modeLabel($mode));

        if ($mode === 'route_only') {
            $this->warn('لا يوجد مجلد public/storage — العرض بالكامل عبر Laravel (/storage/...).');
            $this->line('هذا طبيعي على استضافة تمنع exec/symlink. نفّذ: php artisan storage:sync-to-r2');
        } else {
            $this->info('جرّب: '.url('/storage/site/admin-panel-logo.png'));
        }

        if (! \App\Support\CloudStorage::hasPublicBaseUrl()) {
            $this->comment('AWS_URL غير مضبوط — الروابط تستخدم /storage/ على نفس الموقع (موصى به).');
        }

        return self::SUCCESS;
    }
}
