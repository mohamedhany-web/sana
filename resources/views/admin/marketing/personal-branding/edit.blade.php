@extends('layouts.admin')

@section('title', 'تعديل الملف التعريفي - ' . ($personal_branding->user->name ?? ''))
@section('header', 'تعديل الملف التعريفي')

@section('content')
<div class="w-full max-w-4xl space-y-6">
    <nav class="text-sm text-slate-500">
        <a href="{{ route('admin.personal-branding.index') }}" class="text-sky-600 hover:text-sky-700">التسويق الشخصي</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.personal-branding.show', $personal_branding) }}" class="text-sky-600 hover:text-sky-700">{{ $personal_branding->user->name ?? 'مدرب' }}</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">تعديل</span>
    </nav>

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل ملف {{ $personal_branding->user->name ?? 'المدرب' }}</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $personal_branding->user->email ?? '' }}</p>
            <span class="inline-block mt-3 rounded-full px-3 py-1 text-xs font-semibold
                @if($personal_branding->status == 'approved') bg-emerald-100 text-emerald-700
                @elseif($personal_branding->status == 'pending_review') bg-amber-100 text-amber-700
                @elseif($personal_branding->status == 'rejected') bg-rose-100 text-rose-700
                @else bg-slate-100 text-slate-600
                @endif">
                {{ \App\Models\InstructorProfile::statusLabel($personal_branding->status) }}
            </span>
            <p class="text-xs text-slate-500 mt-3 leading-relaxed">تعديل المحتوى لا يغيّر الحالة تلقائياً. للموافقة أو الرفض استخدم صفحة المراجعة.</p>
        </div>

        <form method="POST" action="{{ route('admin.personal-branding.update', $personal_branding) }}" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">صورة الملف</label>
                @if($personal_branding->photo_path)
                    <div class="w-24 h-24 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 mb-2">
                        <img src="{{ $personal_branding->photo_url }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
                @error('photo')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان التعريفي</label>
                <input type="text" name="headline" value="{{ old('headline', $personal_branding->headline) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                @error('headline')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">النبذة</label>
                <textarea name="bio" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('bio', $personal_branding->bio) }}</textarea>
                @error('bio')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الخبرات في المجال</label>
                <textarea name="experience" rows="10" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('experience', $personal_branding->experience) }}</textarea>
                @error('experience')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">المهارات</label>
                <p class="text-xs text-slate-500 mb-2">سطر لكل مهارة أو مفصولة بفاصلة.</p>
                <textarea name="skills" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('skills', $personal_branding->skills) }}</textarea>
                @error('skills')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-sky-600 text-white px-6 py-2.5 text-sm font-bold hover:bg-sky-700">حفظ التعديلات</button>
                <a href="{{ route('admin.personal-branding.show', $personal_branding) }}" class="rounded-xl border border-slate-200 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
