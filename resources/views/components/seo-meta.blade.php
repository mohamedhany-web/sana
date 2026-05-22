{{-- SEO Meta Tags Component — Sana --}}
@php
    $title       = $title       ?? 'Sana — منصة تأهيل المعلمين للعمل أونلاين باحتراف';
    $description = $description ?? 'Sana منصة عربية متخصصة في تأهيل وتطوير المعلمين للعمل أونلاين — تدريب تطبيقي، أدوات AI للتحضير، مناهج جاهزة، وبناء بروفايل يفتح فرص عمل حقيقية.';
    $keywords    = $keywords    ?? 'تأهيل المعلمين, تدريب المعلمين أونلاين, أدوات AI للمعلم, مولد خطة الدرس, بناء بروفايل المعلم, توظيف المعلمين, دبلومات تعليمية, مناهج تفاعلية, Sana';
    $image       = $image       ?? asset('images/og-image.jpg');
    $imageAlt    = $imageAlt    ?? $title;
    $url         = $url         ?? url()->current();
    $type        = $type        ?? 'website';
    $locale      = app()->getLocale();
    $ogLocale    = $locale === 'ar' ? 'ar_AR' : 'en_US';
    $ogLocaleAlt = $locale === 'ar' ? 'en_US' : 'ar_AR';
    $langCode    = $locale === 'ar' ? 'Arabic' : 'English';
@endphp

<!-- ═══ Primary Meta Tags ═══ -->
<title>{{ $title }}</title>
<meta name="title"          content="{{ $title }}">
<meta name="description"    content="{{ $description }}">
<meta name="keywords"       content="{{ $keywords }}">
<meta name="author"         content="Sana">
<meta name="robots"         content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="language"       content="{{ $langCode }}">
<meta name="revisit-after"  content="7 days">
<meta name="rating"         content="general">

<!-- ═══ Canonical URL ═══ -->
<link rel="canonical" href="{{ $url }}">

<!-- ═══ Open Graph / Facebook ═══ -->
<meta property="og:type"              content="{{ $type }}">
<meta property="og:url"               content="{{ $url }}">
<meta property="og:title"             content="{{ $title }}">
<meta property="og:description"       content="{{ $description }}">
<meta property="og:image"             content="{{ $image }}">
<meta property="og:image:alt"         content="{{ $imageAlt }}">
<meta property="og:image:width"       content="1200">
<meta property="og:image:height"      content="630">
<meta property="og:locale"            content="{{ $ogLocale }}">
<meta property="og:locale:alternate"  content="{{ $ogLocaleAlt }}">
<meta property="og:site_name"         content="Sana">

<!-- ═══ Twitter / X Card ═══ -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@Sana">
<meta name="twitter:creator"     content="@Sana">
<meta name="twitter:url"         content="{{ $url }}">
<meta name="twitter:title"       content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image"       content="{{ $image }}">
<meta name="twitter:image:alt"   content="{{ $imageAlt }}">
