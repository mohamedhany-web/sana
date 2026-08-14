@extends('layouts.admin')

@section('title', 'مراجعة الملف التعريفي - ' . ($personal_branding->user->name ?? ''))
@section('header', 'مراجعة الملف التعريفي')

@section('content')
<div class="w-full space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif
    <nav class="text-sm text-slate-500 mb-2">
        <a href="{{ route('admin.personal-branding.index') }}" class="text-sky-600 hover:text-sky-700">التسويق الشخصي</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">{{ $personal_branding->user->name ?? 'مدرب' }}</span>
    </nav>

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900">الملف التعريفي — {{ $personal_branding->user->name }}</h1>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.personal-branding.edit', $personal_branding) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-sky-600 text-white px-4 py-2 text-sm font-bold hover:bg-sky-700 shadow-sm">
                    <i class="fas fa-pen text-xs"></i>
                    تعديل الملف
                </a>
                <form method="POST" action="{{ route('admin.personal-branding.destroy', $personal_branding) }}" class="inline" onsubmit="return confirm('حذف الملف التعريفي بالكامل؟ سيُزال من الموقع ويمكن للمدرب إنشاء ملف جديد لاحقاً.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 px-4 py-2 text-sm font-bold hover:bg-rose-100">
                        <i class="fas fa-trash text-xs"></i>
                        حذف الملف
                    </button>
                </form>
                <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $personal_branding->show_on_homepage ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $personal_branding->show_on_homepage ? 'ظاهر على الرئيسية' : 'مخفي عن الرئيسية' }}
                </span>
                <span class="rounded-full px-3 py-1 text-sm font-semibold
                    @if($personal_branding->status === \App\Models\InstructorProfile::STATUS_APPROVED) bg-sky-100 text-sky-700
                    @elseif($personal_branding->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW) bg-amber-100 text-amber-700
                    @elseif($personal_branding->status === \App\Models\InstructorProfile::STATUS_REJECTED) bg-rose-100 text-rose-700
                    @else bg-slate-100 text-slate-600
                    @endif">
                    قبول: {{ \App\Models\InstructorProfile::statusLabel($personal_branding->status) }}
                </span>
            </div>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            <div class="flex flex-wrap gap-4 items-start">
                @if($personal_branding->photo_path)
                    @php $photoPath = str_replace('\\', '/', trim($personal_branding->photo_path)); @endphp
                    <div class="w-28 h-28 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                        <img src="{{ public_storage_url($photoPath) }}" alt="صورة المدرب" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                    </div>
                @else
                    <div class="w-28 h-28 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                @endif
                <div>
                    <p class="text-slate-500 text-sm">البريد: {{ $personal_branding->user->email ?? '—' }}</p>
                    <p class="text-slate-500 text-sm mt-1">تاريخ التقديم: {{ $personal_branding->submitted_at ? $personal_branding->submitted_at->format('Y-m-d H:i') : '—' }}</p>
                    @if($personal_branding->reviewed_at)
                        <p class="text-slate-500 text-sm">تمت المراجعة: {{ $personal_branding->reviewed_at->format('Y-m-d H:i') }} — {{ $personal_branding->reviewedByUser->name ?? '' }}</p>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">العنوان التعريفي</h3>
                <p class="text-slate-900">{{ $personal_branding->headline ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">النبذة</h3>
                <p class="text-slate-900 whitespace-pre-line">{{ $personal_branding->bio ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">الخبرات في المجال</h3>
                @if(count($personal_branding->experience_list) > 0)
                <ul class="space-y-2">
                    @foreach($personal_branding->experience_list as $item)
                    <li class="flex gap-2 text-slate-900">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">•</span>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-slate-900">{{ $personal_branding->experience ?: '—' }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">المهارات</h3>
                @if(count($personal_branding->skills_list) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($personal_branding->skills_list as $skill)
                    <span class="inline-flex items-center rounded-xl bg-sky-50 text-sky-800 px-3 py-1.5 text-sm font-medium border border-sky-200">{{ $skill }}</span>
                    @endforeach
                </div>
                @else
                <p class="text-slate-900">{{ $personal_branding->skills ?: '—' }}</p>
                @endif
            </div>
            @if($personal_branding->rejection_reason)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200">
                <h3 class="text-sm font-semibold text-rose-700 mb-1">سبب الرفض</h3>
                <p class="text-rose-900">{{ $personal_branding->rejection_reason }}</p>
            </div>
            @endif

        </div>
        <div class="px-5 py-6 sm:px-8 border-t border-slate-200 bg-slate-50/80">
            <h3 class="text-sm font-bold text-slate-700 mb-2">الظهور على الصفحة الرئيسية</h3>
            <p class="text-xs text-slate-500 mb-4">هذه الأزرار تُظهر أو تُخفي الملف للجمهور فقط. <strong>لا تغيّر قبول المعلم</strong> (حالة القبول حالياً: {{ \App\Models\InstructorProfile::statusLabel($personal_branding->status) }}).</p>
            <div class="flex flex-wrap items-center gap-3">
                @if($personal_branding->show_on_homepage)
                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-800">ظاهر الآن على الرئيسية</span>
                    <form method="POST" action="{{ route('admin.personal-branding.send-back', $personal_branding) }}" class="inline" onsubmit="return confirm('إخفاء الملف من الصفحة الرئيسية؟ قبول المعلم لن يتأثر.');">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-slate-800 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-900">إخفاء من الرئيسية</button>
                    </form>
                @else
                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-slate-200 text-slate-700">مخفي عن الرئيسية</span>
                    <form method="POST" action="{{ route('admin.personal-branding.approve', $personal_branding) }}" class="inline" onsubmit="return confirm('إظهار هذا الملف على الصفحة الرئيسية وقائمة المعلمين؟');">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-emerald-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-emerald-700 shadow-sm">إظهار على الرئيسية</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
