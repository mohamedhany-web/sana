@extends('layouts.admin')

@section('title', 'مستخدمي الميزة: ' . $featureLabel)
@section('header', 'مستخدمي الميزة')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-xl {{ $featureCfg['icon_bg'] ?? 'bg-slate-100' }} {{ $featureCfg['icon_text'] ?? 'text-slate-600' }} flex items-center justify-center">
                    <i class="fas {{ $featureCfg['icon'] ?? 'fa-star' }}"></i>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">{{ $featureLabel }}</h1>
                    <p class="text-xs text-slate-500">{{ $featureKey }}</p>
                </div>
            </div>
            <a href="{{ route('admin.students-control.paid-features') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                رجوع للمزايا
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-800">المستخدمون المشتركون بهذه الميزة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs text-slate-600 uppercase">
                        <th class="px-6 py-3 text-right">المستخدم</th>
                        <th class="px-6 py-3 text-right">الهاتف</th>
                        <th class="px-6 py-3 text-right">الخطة</th>
                        <th class="px-6 py-3 text-right">ينتهي</th>
                        <th class="px-6 py-3 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php $sub = $user->subscriptions->first(); @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 text-sm font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-sm text-slate-600">{{ $user->phone ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-slate-700">{{ $sub->plan_name ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-slate-600">{{ $sub?->end_date?->format('Y-m-d') ?? 'غير محدد' }}</td>
                            <td class="px-6 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-sky-600 hover:underline">الحساب</a>
                                    @if($sub)
                                        <a href="{{ route('admin.subscriptions.consumption', $sub) }}" class="text-emerald-600 hover:underline">الاستهلاك</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">لا يوجد مستخدمون لهذه الميزة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

