@extends('layouts.admin')
@section('title', 'التسويق الشخصي - ملفات المدربين')
@section('header', 'التسويق الشخصي (المدربين)')
@section('content')
<div class="w-full space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-900">مراجعة ملفات المدربين التعريفية</h1>
            <p class="text-slate-500 mt-1">الملفات المعتمدة تظهر في الصفحة الرئيسية ضمن المدربين وعند عرض كل كورس.</p>
        </div>
        <div class="p-5 sm:p-8">
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('admin.personal-branding.index', ['status' => 'pending_review']) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ request('status') == 'pending_review' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800' }}">قيد المراجعة ({{ $counts['pending'] }})</a>
                <a href="{{ route('admin.personal-branding.index', ['status' => 'approved']) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ request('status') == 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-800' }}">معتمد ({{ $counts['approved'] }})</a>
                <a href="{{ route('admin.personal-branding.index', ['status' => 'rejected']) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ request('status') == 'rejected' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-800' }}">مرفوض ({{ $counts['rejected'] }})</a>
                <a href="{{ route('admin.personal-branding.index') }}" class="rounded-2xl px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-700">الكل</a>
            </div>
            <form method="GET" class="mb-6 flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد..." class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm w-64">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <button type="submit" class="rounded-2xl bg-sky-600 text-white px-4 py-2.5 text-sm font-semibold">بحث</button>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">المدرب</th>
                            <th class="px-4 py-3">العنوان التعريفي</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">تاريخ التقديم</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($profiles as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3"><div class="font-semibold">{{ $p->user->name ?? '—' }}</div><div class="text-xs text-slate-500">{{ $p->user->email ?? '' }}</div></td>
                            <td class="px-4 py-3">{{ Str::limit($p->headline ?? '—', 40) }}</td>
                            <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold @if($p->status == 'approved') bg-emerald-100 text-emerald-700 @elseif($p->status == 'pending_review') bg-amber-100 text-amber-700 @elseif($p->status == 'rejected') bg-rose-100 text-rose-700 @else bg-slate-100 text-slate-600 @endif">{{ \App\Models\InstructorProfile::statusLabel($p->status) }}</span></td>
                            <td class="px-4 py-3">{{ $p->submitted_at ? $p->submitted_at->format('Y-m-d') : '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.personal-branding.show', $p) }}" class="inline-flex items-center rounded-lg bg-slate-100 text-slate-800 px-2.5 py-1.5 text-xs font-semibold hover:bg-slate-200">عرض</a>
                                    @if($p->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW)
                                        <form method="POST" action="{{ route('admin.personal-branding.approve', $p) }}" class="inline" onsubmit="return confirm('تأكيد الموافقة ونشر الملف للطلاب؟');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-600 text-white px-2.5 py-1.5 text-xs font-semibold hover:bg-emerald-700">موافقة</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.personal-branding.edit', $p) }}" class="inline-flex items-center rounded-lg bg-sky-50 text-sky-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-sky-100 border border-sky-200/80">تعديل</a>
                                    <form method="POST" action="{{ route('admin.personal-branding.destroy', $p) }}" class="inline" onsubmit="return confirm('حذف الملف التعريفي بالكامل لهذا المدرب؟ سيُزال من الموقع ويمكنه إنشاء ملف جديد لاحقاً.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg bg-rose-50 text-rose-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-rose-100 border border-rose-200/80">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد ملفات تعريفية.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $profiles->links() }}</div>
        </div>
    </div>
</div>
@endsection
