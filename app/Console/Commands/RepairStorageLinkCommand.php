<?php

namespace App\Console\Commands;

use App\Services\AdminPanelBranding;
use App\Support\CloudStorage;
use App\Support\PublicStorageLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RepairStorageLinkCommand extends Command
{
    protected $signature = 'storage:repair-link
                            {--no-mirror : عدم نسخ الملفات إن فشل symlink}
                            {--mirror-only : نسخ الملفات فقط إلى كل جذور الويب}';

    protected $description = 'إصلاح public/storage و public_html/storage بدون exec()';

    public function handle(): int
    {
        $target = PublicStorageLink::targetPath();

        if (! is_dir($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info('تم إنشاء: '.$target);
        }

        $roots = PublicStorageLink::webDocumentRoots();
        $this->line('جذور الويب:');
        foreach ($roots as $root) {
            $this->line('  • '.$root);
        }

        if ($this->option('mirror-only')) {
            $mode = 'mirrored';
            foreach (PublicStorageLink::storageLinkPaths() as $link) {
                PublicStorageLink::removeLinkPath($link);
                if (! PublicStorageLink::mirrorDirectory($target, $link)) {
                    $this->warn('تعذّر النسخ إلى: '.$link);
                } else {
                    $this->info('✓ mirror → '.$link);
                }
            }
            PublicStorageLink::publishBundledStaticImages();
        } else {
            $mode = PublicStorageLink::establish(! $this->option('no-mirror'));
            foreach (PublicStorageLink::storageLinkPaths() as $link) {
                $label = is_link($link) ? 'symlink' : (is_dir($link) ? 'mirror/dir' : '—');
                $this->line("  {$label}: {$link}");
            }
        }

        $this->info('وضع التخزين: '.PublicStorageLink::modeLabel($mode));

        if ($mode === 'route_only') {
            $this->warn('العرض عبر Laravel فقط — تأكد أن .htaccess يمرّر /storage/ و /media/ إلى index.php');
            $this->line('ثم: php artisan storage:sync-to-r2 --prefix=site');
        }

        if (AdminPanelBranding::publishWebLogo()) {
            $webLogo = AdminPanelBranding::logoPublicUrl();
            $this->info('✓ نُشر الشعار إلى images/branding: '.($webLogo ?? '—'));
        } else {
            $this->warn('تعذّر نشر الشعار إلى images/branding — تحقق من site/admin-panel-logo في التخزين');
        }

        $prefix = trim((string) config('filesystems.public_route_prefix', 'storage'), '/');
        $this->info('جرّب الشعار (ثابت): '.url('images/branding/admin-panel-logo.png'));
        $this->info('جرّب عبر Laravel: '.url($prefix.'/site/admin-panel-logo.png'));
        $this->info('جرّب الهيرو: '.url('/images/hero-intro.png'));

        if (! CloudStorage::hasPublicBaseUrl()) {
            $this->comment('AWS_URL غير مضبوط — العرض عبر /storage/ على نفس الموقع (طبيعي).');
        }

        $custom = trim((string) env('PUBLIC_WEB_ROOT', ''));
        if ($custom === '' && ! in_array(realpath(base_path('public_html')) ?: '', array_map(static fn ($r) => realpath($r) ?: $r, $roots), true)) {
            $this->comment('إن كان الموقع يُخدم من public_html أضف في .env:');
            $this->comment('PUBLIC_WEB_ROOT=/home/USER/domains/sanaedu.com/public_html');
        }

        return self::SUCCESS;
    }
}
