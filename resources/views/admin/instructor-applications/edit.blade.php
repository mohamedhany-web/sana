@extends('layouts.admin')

@section('title', 'تعديل طلب معلم - ' . config('app.name', 'Sana'))
@section('header', 'تعديل طلب انضمام معلم')

@section('content')
@php
    $user = $application->user;
    $selectedSubjects = old('subject_ids', $application->tutor_subject_ids ?? []);
    $selectedYears = old('academic_year_ids', $application->tutor_academic_year_ids ?? []);
    $selectedModes = old('matching_modes', $application->tutor_matching_modes ?? []);
    $selectedSessions = old('session_types', $application->tutor_session_types ?? []);
@endphp

<div class="space-y-6 sm:space-y-8">
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.instructor-applications.show', $application) }}" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            العودة للتفاصيل
        </a>
    </div>

    <form method="POST" action="{{ route('admin.instructor-applications.update', $application) }}" data-turbo="false"
          class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">بيانات الحساب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الاسم</label>
                    <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البريد</label>
                    <input type="email" name="email" value="{{ old('email', $user?->email) }}" required dir="ltr"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    @error('email')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الجوال</label>
                    <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}" dir="ltr"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    @error('phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">سنوات الخبرة</label>
                    <input type="number" name="years_experience" min="0" max="50"
                           value="{{ old('years_experience', $application->tutor_years_experience) }}" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    @error('years_experience')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">الملف التعريفي</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">العنوان المختصر</label>
                <input type="text" name="headline" value="{{ old('headline', $application->headline) }}" required
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                @error('headline')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">نبذة</label>
                <textarea name="bio" rows="5" required
                          class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('bio', $application->bio) }}</textarea>
                @error('bio')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">المواد والمراحل</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">المواد</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($subjects as $subject)
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                   @checked(in_array($subject->id, $selectedSubjects))>
                            <span>{{ $subject->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('subject_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">المراحل / المسارات</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($years as $year)
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="academic_year_ids[]" value="{{ $year->id }}"
                                   @checked(in_array($year->id, $selectedYears))>
                            <span>{{ $year->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('academic_year_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">تفضيلات الحجز</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">أنماط الحجز</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['assisted', 'self_schedule', 'pick_teacher'] as $mode)
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="matching_modes[]" value="{{ $mode }}"
                                   @checked(in_array($mode, $selectedModes))>
                            <span>{{ __('tutor.matching_'.$mode) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('matching_modes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">أنواع الحصص</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['one_to_one', 'small_group'] as $type)
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="session_types[]" value="{{ $type }}"
                                   @checked(in_array($type, $selectedSessions))>
                            <span>{{ __('tutor.session_'.$type) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('session_types')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-6 py-3 text-sm font-bold text-white hover:bg-sky-700">
                <i class="fas fa-save"></i>
                حفظ التعديلات
            </button>
            <a href="{{ route('admin.instructor-applications.show', $application) }}" data-turbo="false"
               class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
