<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: (config('app.name') . ' - ' . __('landing.nav.brand'));
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'منصة عربية لتأهيل وتطوير المعلمين للعمل أونلاين باحتراف.';
        $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: 'دروس أونلاين, تعليم عن بُعد, ' . config('app.name', 'Sana');
        $seoImage = trim($__env->yieldContent('meta_image')) ?: asset('images/og-image.jpg');
        $seoType = trim($__env->yieldContent('meta_type')) ?: 'website';
        $seoCanonical = trim($__env->yieldContent('canonical_url')) ?: url()->current();
        $seoAltBase = url()->current();
    @endphp
    @include('components.seo-meta', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'keywords' => $seoKeywords,
        'image' => $seoImage,
        'type' => $seoType,
        'url' => $seoCanonical,
    ])
    <link rel="alternate" hreflang="ar" href="{{ $seoAltBase }}">
    <meta name="theme-color" content="#0F172A">
    @include('partials.force-light-theme')

    @include('partials.favicon-links')

    <!-- الخطوط العربية - تحميل غير معطل للرسم (تحسين FCP/LCP) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap"></noscript>
    
    <!-- Resource Hints للأداء -->
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - محسّن -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    @include('partials.rtl-base')

    <!-- Custom Styles from welcome.blade.php -->
    @include('layouts.public-styles')
    @stack('styles')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900 transition-colors"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">
    
    @include('components.unified-navbar')

    <!-- Main Content -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Footer - نفس فوتر الصفحة الرئيسية -->
    @include('components.unified-footer')

    @stack('scripts')

    <script>
        // تأثير الناف بار عند السكرول - محسّن للأداء
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                let ticking = false;
                let isScrolled = false;
                
                function handleScroll() {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                            const shouldBeScrolled = currentScroll > 100;
                            
                            if (shouldBeScrolled !== isScrolled) {
                                if (shouldBeScrolled) {
                                    navbar.classList.add('scrolled');
                                } else {
                                    navbar.classList.remove('scrolled');
                                }
                                isScrolled = shouldBeScrolled;
                            }
                            
                            ticking = false;
                        });
                        ticking = true;
                    }
                }
                
                window.addEventListener('scroll', handleScroll, { passive: true });
            }
        });
    </script>
</body>
</html>

