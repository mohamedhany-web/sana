@extends('layouts.admin')

@section('title', 'إضافة عميل محتمل')
@section('header', 'إضافة Lead جديد')

@section('content')
<div class="space-y-6 max-w-3xl">
    <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> العودة للقائمة
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.sales.leads.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الاسم <span class="text-rose-600">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">البريد</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    @error('email')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    @error('phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الشركة</label>
                <input type="text" name="company" value="{{ old('company') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">المصدر <span class="text-rose-600">*</span></label>
                    <select name="source" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                        @foreach(\App\Models\SalesLead::sourceLabels() as $val => $label)
                            <option value="{{ $val }}" {{ old('source', \App\Models\SalesLead::SOURCE_OTHER) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('source')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة الأولية</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="{{ \App\Models\SalesLead::STATUS_NEW }}" {{ old('status', \App\Models\SalesLead::STATUS_NEW) === \App\Models\SalesLead::STATUS_NEW ? 'selected' : '' }}>جديد</option>
                        <option value="{{ \App\Models\SalesLead::STATUS_CONTACTED }}" {{ old('status') === \App\Models\SalesLead::STATUS_CONTACTED ? 'selected' : '' }}>تم التواصل</option>
                        <option value="{{ \App\Models\SalesLead::STATUS_QUALIFIED }}" {{ old('status') === \App\Models\SalesLead::STATUS_QUALIFIED ? 'selected' : '' }}>مؤهل</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">مسؤول المبيعات</label>
                <select name="assigned_to" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">— بدون تعيين —</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" {{ (string) old('assigned_to') === (string) $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">كورس مهتم به</label>
                <select name="interested_advanced_course_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">—</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ (string) old('interested_advanced_course_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm">
                    <i class="fas fa-save ml-1"></i> حفظ
                </button>
                <a href="{{ route('admin.sales.leads.index') }}" class="px-5 py-2.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
