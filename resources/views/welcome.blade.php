@php
    $brand = config('app.name');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $brand }} — تعلّم ممتع يبدأ مع سنا</title>
    <meta name="description" content="منصة {{ $brand }} التعليمية — تعلّم تفاعلي، حصص مباشرة، شهادات معتمدة للأطفال والطلاب.">
    <meta name="theme-color" content="#5B21B6">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
</head>
<body class="sana-home">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main>
    @include('landing.sana.sections.hero')
    @include('landing.sana.sections.features')
    @include('landing.sana.sections.categories')
    @include('landing.sana.sections.courses')
    @include('landing.sana.sections.teachers')
    @include('landing.sana.sections.journey')
    @include('landing.sana.sections.testimonials')
    @include('landing.sana.sections.certificates')
    @include('landing.sana.sections.faq')
</main>

@include('landing.sana.footer')

@if(isset($popupAd) && $popupAd)
    @include('partials.popup-ad', ['ad' => $popupAd])
@endif

@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>
