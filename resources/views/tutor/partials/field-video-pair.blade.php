@php
    $videoMaxMb = \App\Services\TutorApplicationFormService::videoMaxMb();
    $videoUseExternal = filter_var(old('video_use_external_link', false), FILTER_VALIDATE_BOOLEAN);
@endphp
<div class="sm:col-span-2 space-y-3" x-data="tutorVideoStep({{ $videoMaxMb }}, @json($videoUseExternal))">
    <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-xs text-sky-900 space-y-1">
        <p class="font-bold m-0">{{ $field->label }}</p>
        <p class="m-0">اشرح مفهوماً بسيطاً من تخصصك (٣–٥ دقائق) — صوت وصورة واضحان.</p>
        <p class="m-0">الحد الأقصى لرفع الملف: <strong x-text="maxMb"></strong> ميجابايت. إن كان أكبر، استخدم رابطاً خارجياً.</p>
        @if($field->help_text)<p class="m-0">{{ $field->help_text }}</p>@endif
    </div>

    <input type="hidden" name="video_use_external_link" :value="useExternalLink ? '1' : '0'">

    <div x-show="!useExternalLink" x-cloak class="space-y-2">
        <label class="ta-label">رفع ملف الفيديو (MP4/MOV/WebM)</label>
        <input type="file" name="demo_video" class="ta-field" accept="video/mp4,video/quicktime,video/webm,video/*"
               :disabled="useExternalLink" @change="onVideoFile($event)">
        <p x-show="fileTooLarge" x-cloak class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 m-0">
            الملف يتجاوز {{ $videoMaxMb }} ميجابايت. استخدم رابطاً خارجياً.
        </p>
    </div>

    <label class="ta-check-item cursor-pointer">
        <input type="checkbox" x-model="useExternalLink" @change="onToggleExternal()">
        <span>سأستخدم رابطاً خارجياً (YouTube / Drive)</span>
    </label>

    <div>
        <label class="ta-label">
            رابط الفيديو
            <span x-show="useExternalLink" class="text-rose-600">*</span>
            <span x-show="!useExternalLink" class="text-slate-400 font-normal text-xs">(اختياري)</span>
        </label>
        <input type="url" name="demo_video_link" class="ta-field" dir="ltr" placeholder="https://"
               value="{{ old('demo_video_link') }}"
               :required="useExternalLink">
    </div>
</div>
