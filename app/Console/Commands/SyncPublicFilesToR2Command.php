<?php

namespace App\Console\Commands;

use App\Support\CloudStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncPublicFilesToR2Command extends Command
{
    protected $signature = 'storage:sync-to-r2
                            {--prefix= : بادئة المجلد فقط (مثل site أو profile-photos)}
                            {--dry-run : عرض الملفات دون رفع}';

    protected $description = 'مزامنة ملفات storage/app/public إلى Cloudflare R2';

    public function handle(): int
    {
        if (! CloudStorage::isR2Configured()) {
            $this->error('R2 غير مضبوط. أكمل AWS_* في .env ثم config:clear');

            return self::FAILURE;
        }

        $prefix = $this->option('prefix');
        $dryRun = (bool) $this->option('dry-run');
        $files = Storage::disk('public')->allFiles($prefix ? (string) $prefix : '');
        $uploaded = 0;
        $skipped = 0;

        foreach ($files as $path) {
            $path = str_replace('\\', '/', $path);
            if (Storage::disk('r2')->exists($path)) {
                $skipped++;
                continue;
            }
            if ($dryRun) {
                $this->line("[dry-run] {$path}");
                $uploaded++;
                continue;
            }
            if (CloudStorage::copyLocalPublicToR2($path)) {
                $this->line("✓ {$path}");
                $uploaded++;
            } else {
                $this->warn("✗ {$path}");
            }
        }

        $this->info("انتهى: رُفع {$uploaded}، موجود مسبقاً {$skipped}.");

        if (! CloudStorage::hasPublicBaseUrl()) {
            $this->comment('AWS_URL غير مضبوط — الصور تُعرض عبر '.url('/storage/...').' (نفس الموقع).');
            $this->comment('اختياري: فعّل Public Access على R2 وأضف AWS_URL=https://pub-xxx.r2.dev');
        }

        return self::SUCCESS;
    }
}
