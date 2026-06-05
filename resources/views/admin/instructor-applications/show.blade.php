@extends('layouts.admin')

@section('title', 'مراجعة طلب معلم - ' . config('app.name', 'Sana'))
@section('header', 'مراجعة طلب انضمام معلم')

@section('content')
@php
    $user = $application->user;
    $statusLabels = [
        \App\Models\InstructorProfile::STATUS_PENDING_REVIEW => ['بانتظار الموافقة', 'bg-amber-100 text-amber-800'],
        \App\Models\InstructorProfile::STATUS_APPROVED => ['مقبول', 'bg-emerald-100 text-emerald-800'],
        \App\Models\InstructorProfile::STATUS_REJECTED => ['مرفوض', 'bg-rose-100 text-rose-800'],
    ];
    [$statusLabel, $statusClass] = $statusLabels[$application->status] ?? [$application->status, 'bg-slate-100 text-slate-700'];
    $accountActive = (bool) ($user?->is_active);
    $canManageAccount = $user && !\App\Services\InstructorApplicationService::mustKeepAccountActive($user);
@endphp

<div class="space-y-6 sm:space-y-8">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3">{{ session('info') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.instructor-applications.index') }}" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
        @if($user)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $accountActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                <i class="fas {{ $accountActive ? 'fa-circle-check' : 'fa-ban' }} ml-1"></i>
                {{ $accountActive ? 'الحساب مفعّل' : 'الحساب موقوف' }}
            </span>
        @endif
        <a href="{{ route('admin.instructor-applications.edit', $application) }}" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl bg-sky-50 text-sky-700 px-4 py-2 text-sm font-semibold hover:bg-sky-100">
            <i class="fas fa-pen"></i>
            تعديل
        </a>
    </div>

    {{-- إدارة الحساب --}}
    @if($user)
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-user-shield text-sky-600"></i>
            إدارة الحساب
        </h3>
        <div class="flex flex-wrap gap-3">
            @if($canManageAccount)
                @if($accountActive)
                    <form method="POST" action="{{ route('admin.instructor-applications.deactivate-account', $application) }}" data-turbo="false">
                        @csrf
                        <button type="submit" onclick="return confirm('إيقاف حساب هذا المعلم؟ لن يتمكن من تسجيل الدخول.')"
                                class="inline-flex items-center gap-2 rounded-2xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                            <i class="fas fa-pause-circle"></i>
                            إيقاف الحساب
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.instructor-applications.activate-account', $application) }}" data-turbo="false">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                            <i class="fas fa-play-circle"></i>
                            تفعيل الحساب
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.instructor-applications.toggle-account', $application) }}" data-turbo="false">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-sync-alt"></i>
                        تبديل حالة الحساب
                    </button>
                </form>
            @else
                <p class="text-sm text-slate-500">هذا الحساب محمي (إداري/موظف) — لا يمكن إيقافه من هنا.</p>
            @endif

            @if($application->status !== \App\Models\InstructorProfile::STATUS_PENDING_REVIEW)
                <form method="POST" action="{{ route('admin.instructor-applications.reopen', $application) }}" data-turbo="false">
                    @csrf
                    <button type="submit" onclick="return confirm('إعادة الطلب لقائمة المراجعة؟')"
                            class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                        <i class="fas fa-redo"></i>
                        إعادة للمراجعة
                    </button>
                </form>
            @endif

            @if($canManageAccount)
                <form method="POST" action="{{ route('admin.instructor-applications.destroy', $application) }}" data-turbo="false"
                      onsubmit="return confirm('حذف الطلب نهائياً؟ سيتم حذف الملف التعريفي وإيقاف الحساب.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                        <i class="fas fa-trash-alt"></i>
                        حذف الطلب
                    </button>
                </form>
            @endif
        </div>
    </section>
    @endif

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-900">{{ $user?->name ?? 'معلم' }}</h2>
            <p class="text-sm text-slate-500 mt-1">قدّم الطلب {{ $application->submitted_at?->diffForHumans() ?? '—' }}</p>
        </div>
        <div class="p-5 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest">بيانات الحساب</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                        <dt class="text-slate-500">البريد</dt>
                        <dd class="font-medium text-slate-900" dir="ltr">{{ $user?->email }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                        <dt class="text-slate-500">الجوال</dt>
                        <dd class="font-medium text-slate-900" dir="ltr">{{ $user?->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                        <dt class="text-slate-500">الحساب مفعّل</dt>
                        <dd class="font-medium {{ $accountActive ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $accountActive ? 'نعم' : 'لا' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                        <dt class="text-slate-500">سنوات الخبرة</dt>
                        <dd class="font-medium text-slate-900">{{ $application->tutor_years_experience ?? '—' }}</dd>
                    </div>
                    @if($application->status === \App\Models\InstructorProfile::STATUS_APPROVED)
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                        <dt class="text-slate-500">نوع لوحة المعلم</dt>
                        <dd class="font-medium text-slate-900">{{ $application->portalModeLabel() }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest">الملف التعريفي</h3>
                <p class="text-sm font-semibold text-slate-900">{{ $application->headline }}</p>
                <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $application->bio }}</p>
            </div>
            <div class="lg:col-span-2 space-y-3">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest">المواد والمسارات</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($subjects as $s)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-800">{{ $s->name }}</span>
                    @empty
                        <span class="text-sm text-slate-500">—</span>
                    @endforelse
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    @forelse($years as $y)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-800">{{ $y->name }}</span>
                    @empty
                        <span class="text-sm text-slate-500">—</span>
                    @endforelse
                </div>
            </div>
            <div class="lg:col-span-2 grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <h4 class="font-semibold text-slate-700 mb-2">أنماط الحجز</h4>
                    <ul class="list-disc list-inside text-slate-600 space-y-1">
                        @foreach($application->tutor_matching_modes ?? [] as $mode)
                            <li>{{ __('tutor.matching_'.$mode) }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-700 mb-2">أنواع الحصص</h4>
                    <ul class="list-disc list-inside text-slate-600 space-y-1">
                        @foreach($application->tutor_session_types ?? [] as $type)
                            <li>{{ __('tutor.session_'.$type) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if($application->rejection_reason)
            <div class="lg:col-span-2 rounded-2xl bg-rose-50 border border-rose-200 p-4">
                <p class="text-xs font-semibold text-rose-700 mb-1">سبب الرفض السابق</p>
                <p class="text-sm text-rose-900">{{ $application->rejection_reason }}</p>
            </div>
            @endif
            @if($application->reviewed_at)
            <div class="lg:col-span-2 text-xs text-slate-500">
                آخر مراجعة: {{ $application->reviewed_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                @if($application->reviewedByUser)
                    — {{ $application->reviewedByUser->name }}
                @endif
            </div>
            @endif
        </div>
    </section>

    @if($application->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="rounded-3xl bg-white/95 border border-emerald-200 shadow-lg p-6 sm:p-8">
            <h3 class="text-lg font-bold text-emerald-800 mb-4">قبول الطلب</h3>
            <p class="text-sm text-slate-600 mb-4">سيتم تفعيل حساب المعلم ويمكنه تسجيل الدخول من بوابة المدربين فوراً.</p>
            <form method="POST" action="{{ route('admin.instructor-applications.approve', $application) }}" class="space-y-4" data-turbo="false">
                @csrf
                @include('admin.instructor-applications.partials.portal-mode-fields')
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">ملاحظة للمعلم (اختياري)</label>
                    <textarea name="admin_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"
                              placeholder="رسالة ترسل مع إشعار القبول">{{ old('admin_note') }}</textarea>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700"
                        onclick="return confirm('تأكيد قبول هذا المعلم وتفعيل حسابه؟')">
                    <i class="fas fa-check"></i>
                    قبول وتفعيل الحساب
                </button>
            </form>
        </section>

        <section class="rounded-3xl bg-white/95 border border-rose-200 shadow-lg p-6 sm:p-8">
            <h3 class="text-lg font-bold text-rose-800 mb-4">رفض الطلب</h3>
            <form method="POST" action="{{ route('admin.instructor-applications.reject', $application) }}" class="space-y-4" data-turbo="false">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">سبب الرفض <span class="text-rose-600">*</span></label>
                    <textarea name="rejection_reason" rows="4" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"
                              placeholder="يُرسل للمعلم في الإشعار">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-4 py-3 text-sm font-bold text-white hover:bg-rose-700"
                        onclick="return confirm('تأكيد رفض هذا الطلب؟')">
                    <i class="fas fa-times"></i>
                    رفض الطلب
                </button>
            </form>
        </section>
    </div>
    @elseif($application->status === \App\Models\InstructorProfile::STATUS_REJECTED)
    <section class="rounded-3xl bg-white/95 border border-emerald-200 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-bold text-emerald-800 mb-4">إعادة قبول الطلب</h3>
        <p class="text-sm text-slate-600 mb-4">يمكنك قبول هذا المعلم رغم الرفض السابق — سيتم تفعيل حسابه.</p>
        <form method="POST" action="{{ route('admin.instructor-applications.approve', $application) }}" class="space-y-4" data-turbo="false">
            @csrf
            @include('admin.instructor-applications.partials.portal-mode-fields')
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">ملاحظة للمعلم (اختياري)</label>
                <textarea name="admin_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"></textarea>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700"
                    onclick="return confirm('تأكيد قبول هذا المعلم؟')">
                <i class="fas fa-check"></i>
                قبول الطلب
            </button>
        </form>
    </section>
    @endif
</div>
@endsection
