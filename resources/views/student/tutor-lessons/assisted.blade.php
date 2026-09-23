@extends('layouts.app')
@section('title', __('مساعدة في إيجاد معلم'))
@section('header', __('مساعدة في إيجاد معلم'))
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page w-full pb-8 max-w-2xl">
    <div class="sd-hero mb-6">
        <div class="sd-hero-main relative z-[1]">
            <p class="text-xs font-bold sd-tag mb-2">{{ __('للصغار ومع أولياء الأمور') }}</p>
            <h1 class="font-heading text-xl font-black text-slate-800">{{ __('طلب مساعدة في إيجاد معلم') }}</h1>
            <p class="text-sm text-slate-600 mt-2">{{ __('سيتواصل معك فريق المنصة عبر الإشعارات داخل النظام.') }}</p>
        </div>
    </div>
    <form method="post" action="{{ route('student.tutor-lessons.assisted.store') }}" class="sd-panel sd-form">
        <div class="sd-panel-body space-y-4">@csrf
            <div><label>{{ __('المواد') }}</label><div class="flex flex-wrap gap-2">@foreach($subjects as $s)<label class="sd-chip"><input type="checkbox" name="subject_ids[]" value="{{ $s->id }}"> {{ $s->name }}</label>@endforeach</div></div>
            <div><label>{{ __('نوع الحصة') }}</label>
                <label class="sd-chip"><input type="radio" name="preferred_session_type" value="one_to_one" checked> {{ __('فردي') }}</label>
                <label class="sd-chip"><input type="radio" name="preferred_session_type" value="small_group"> {{ __('مجموعة') }}</label>
            </div>
            <div><label>{{ __('رسالتك / احتياجاتك') }}</label><textarea name="message" rows="4" required></textarea></div>
            <button type="submit" class="sd-btn-primary w-full">{{ __('إرسال الطلب') }}</button>
        </div>
    </form>
</div>
@endsection
