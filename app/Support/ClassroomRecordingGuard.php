<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * تحققات موحّدة لرفع تسجيلات Classroom (فيديو/صوت) لتقليل حفظ ملفات فارغة أو تالفة.
 */
class ClassroomRecordingGuard
{
    /** أقل حجم معقول لملف webm/mp3 (رؤوس الملف + بضع ثوانٍ صوت). */
    public const MIN_BYTES = 4096;

    /** إن ادّعى العميل مدة أطول من هذه والحجم أصغر من MIN_BYTES_PER_MINUTE × دقائق → مرفوض. */
    public const MIN_BYTES_PER_MINUTE = 800;

    public static function validateSize(int $size, ?int $durationSeconds = null, string $label = 'الملف'): ?string
    {
        if ($size <= 0) {
            return $label.' المرفوع فارغ. أوقف التسجيل من الزر الأحمر وانتظر اكتمال الرفع، ولا تغلق التبويب قبل ظهور «تم الرفع». استخدم Chrome أو Edge.';
        }

        if ($size < self::MIN_BYTES) {
            return 'حجم '.$label.' صغير جداً ('.number_format($size).' بايت). تأكد من الميكروفون ومن عدم إغلاق مشاركة الشاشة قبل الإيقاف، ثم أعد التسجيل.';
        }

        $duration = max(0, (int) $durationSeconds);
        if ($duration >= 120) {
            $expectedMin = (int) max(self::MIN_BYTES, ($duration / 60) * self::MIN_BYTES_PER_MINUTE);
            if ($size < $expectedMin) {
                return 'مدة التسجيل المُرسلة ('.self::formatDuration($duration).') لا تتوافق مع حجم '
                    .$label.' ('.self::formatBytes($size).'). يبدو أن الملف تالف — أعد التسجيل ولا تغلق التبويب أثناء الرفع.';
            }
        }

        return null;
    }

    /**
     * انتظار ظهور الملف على R2 بعد PUT (تأخير نسخ لحظي).
     */
    public static function waitForObjectSize(Filesystem $disk, string $path, int $attempts = 6, int $sleepMs = 400): int
    {
        $size = 0;
        for ($i = 0; $i < $attempts; $i++) {
            if ($disk->exists($path)) {
                $size = (int) $disk->size($path);
                if ($size > 0) {
                    return $size;
                }
            }
            if ($i < $attempts - 1) {
                usleep($sleepMs * 1000);
            }
        }

        return $size;
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }

    public static function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }

        return sprintf('%d:%02d', $m, $s);
    }
}
