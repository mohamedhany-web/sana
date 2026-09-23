@extends('layouts.employee')

@section('title', __('تعديل مرشح'))
@section('header', __('تعديل بيانات مرشح'))

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('employee.hr.recruitment.candidates.show', $candidate) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> العرض</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('employee.hr.recruitment.candidates.update', $candidate) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الاسم الكامل *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $candidate->full_name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">البريد *</label>
                    <input type="email" name="email" value="{{ old('email', $candidate->email) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $candidate->phone) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">رابط معرض أعمال</label>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $candidate->portfolio_url) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المصدر *</label>
                <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @foreach(\App\Models\HrCandidate::sourceLabels() as $k => $lbl)
                        <option value="{{ $k }}" {{ old('source', $candidate->source) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">استبدال السيرة (اختياري)</label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" class="w-full text-sm">
                @if($candidate->cv_path)
                    <p class="text-xs text-gray-500 mt-1">الملف الحالي: <a href="{{ $candidate->cvUrl() }}" target="_blank" class="text-violet-700 underline">تحميل</a></p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes', $candidate->notes) }}</textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-800 text-white font-bold text-sm">حفظ</button>
        </form>
    </div>
</div>
@endsection
