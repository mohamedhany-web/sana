<?php

namespace App\Http\Middleware;

use App\Services\SecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    protected $securityService;

    /** حقول لا تُفحص بـ SQL/XSS (كلمات مرور، رموز، روابط، نصوص طويلة طبيعية). */
    private const SKIP_THREAT_FIELDS = [
        '_token',
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'new_password_confirmation',
        // روابط شائعة قد تحتوي # أو استعلامات
        'linkedin_url',
        'demo_video_link',
        'url',
        'website',
        'website_url',
        'video_url',
        'external_url',
        'redirect',
        'callback',
        // نصوص طويلة في نموذج المعلمين وغيرها
        'experience',
        'bio',
        'skills',
        'rejection_reason',
        'admin_note',
        'headline',
        'bio_ar',
        'bio_en',
        'why_sana',
        'curricula_experience_text',
        'grades_taught',
        'last_workplace',
        'specialization',
        'degree_qualification',
        'video_topic_title',
        'video_grade_level',
        'country_city',
        'nationality',
        'message',
        'body',
        'content',
        'description',
        'notes',
        'comment',
        'reply',
    ];

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * تنظيف وتأمين جميع المدخلات
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Webhooks خارجية (n8n، تسجيلات البث) — مدخلات JSON قد تحتوي كلمات تقنية تُصادَف خطأً كـ SQL/XSS
        if ($request->is('api/n8n/*') || $request->is('api/live-recordings/register')) {
            return $next($request);
        }

        // نموذج توظيف المعلمين: نصوص وروابط وكلمات مرور من الموبايل تُرفض خطأً كـ 403
        $softSanitizeOnly = $request->is('tutor/apply', 'tutor/apply/*', 'register', 'register/*');

        foreach ($request->all() as $key => $value) {
            if (! is_string($value)) {
                continue;
            }

            if ($this->shouldSkipThreatScan((string) $key)) {
                continue;
            }

            $looksSql = $this->securityService->detectSQLInjection($value);
            $looksXss = $this->securityService->detectXSS($value);

            if (! $looksSql && ! $looksXss) {
                continue;
            }

            $type = $looksSql ? 'SQL Injection Attempt' : 'XSS Attempt';
            $this->securityService->logSuspiciousActivity($type, $request, "Field: {$key}");

            if ($softSanitizeOnly) {
                // لا نمنع التسجيل — ننظّف المدخل فقط
                continue;
            }

            abort(403, 'طلب غير صالح');
        }

        // تنظيف المدخلات النصية (بدون كلمات المرور والروابط حتى لا يُفسد htmlspecialchars الاستعلامات مثل & و #)
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (! is_string($value) || $this->shouldSkipSanitize((string) $key)) {
                continue;
            }
            $input[$key] = $this->securityService->sanitizeInput($value);
        }
        $request->merge($input);

        return $next($request);
    }

    private function shouldSkipThreatScan(string $key): bool
    {
        if (in_array($key, self::SKIP_THREAT_FIELDS, true)) {
            return true;
        }

        // حقول مخصصة من منشئ النماذج أو روابط عامة
        if (str_ends_with($key, '_url') || str_ends_with($key, '_link') || str_contains($key, 'password')) {
            return true;
        }

        return false;
    }

    private function shouldSkipSanitize(string $key): bool
    {
        if (in_array($key, ['_token', 'password', 'password_confirmation', 'current_password', 'new_password', 'new_password_confirmation'], true)) {
            return true;
        }

        if (str_ends_with($key, '_url') || str_ends_with($key, '_link') || str_contains($key, 'password')) {
            return true;
        }

        return in_array($key, ['linkedin_url', 'demo_video_link', 'url', 'website', 'website_url', 'video_url', 'external_url'], true);
    }
}
