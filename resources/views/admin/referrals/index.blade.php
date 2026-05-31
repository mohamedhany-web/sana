@extends('layouts.admin')

@section('title', 'الإحالات')
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-user-friends text-emerald-500 ml-2"></i>الإحالات
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">متابعة المحيل والمحال والمكافآت والخصومات</p>
        </div>
        <a href="{{ route('admin.referral-programs.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50">
            <i class="fas fa-gift"></i> برامج الإحالة
        </a>
    </div>

    @include('admin.partials.alert-success')

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['label' => 'إجمالي', 'value' => $stats['total'], 'color' => 'text-slate-800 dark:text-white'],
            ['label' => 'مكتملة', 'value' => $stats['completed'], 'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'قيد الانتظار', 'value' => $stats['pending'], 'color' => 'text-amber-600 dark:text-amber-400'],
            ['label' => 'مكافآت', 'value' => number_format($stats['total_rewards'], 2).' '.(__('public.currency')), 'color' => 'text-violet-600 dark:text-violet-400', 'raw' => true],
            ['label' => 'خصومات', 'value' => number_format($stats['total_discounts'], 2).' '.(__('public.currency')), 'color' => 'text-rose-600 dark:text-rose-400', 'raw' => true],
        ] as $s)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</div>
            <div class="text-lg font-bold mt-1 {{ $s['color'] }}">{{ ($s['raw'] ?? false) ? $s['value'] : number_format($s['value']) }}</div>
        </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.referrals.index') }}" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم، هاتف، كود..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                <option value="completed" @selected(request('status') === 'completed')>مكتملة</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
            </select>
        </div>
        <div class="w-full sm:w-44">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">البرنامج</label>
            <select name="program_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">جميع البرامج</option>
                @foreach($programs as $program)
                <option value="{{ $program->id }}" @selected(request('program_id') == $program->id)>{{ $program->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">من</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">إلى</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 dark:bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium"><i class="fas fa-filter"></i> تطبيق</button>
        @if(request()->hasAny(['search','status','program_id','date_from','date_to']))
        <a href="{{ route('admin.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm">إعادة</a>
        @endif
    </form>

    @if($referrals->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">المحيل</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">المحال</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">البرنامج</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">خصم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">مكافأة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($referrals as $referral)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $referral->referrer->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $referral->referrer->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $referral->referred->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $referral->referred->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $referral->referralProgram->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $referral->referral_code ?? $referral->code ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $st = $referral->status; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                @if($st === 'completed') bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400
                                @elseif($st === 'pending') bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400
                                @else bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 @endif">
                                @if($st === 'completed') مكتملة @elseif($st === 'pending') انتظار @else ملغاة @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ number_format($referral->discount_amount ?? 0, 2) }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-600">{{ number_format($referral->reward_amount ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $referral->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.referrals.show', $referral) }}" class="text-emerald-600 hover:underline text-xs">تفاصيل</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())<div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">{{ $referrals->links() }}</div>@endif
    </div>
    @else
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
        <p class="text-slate-600 dark:text-slate-300 mb-4">لا توجد إحالات مطابقة</p>
        <a href="{{ route('admin.referral-programs.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold"><i class="fas fa-gift"></i> إنشاء برنامج</a>
    </div>
    @endif
</div>
@endsection

