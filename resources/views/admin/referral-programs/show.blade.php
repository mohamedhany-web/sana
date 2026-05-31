@extends('layouts.admin')

@section('title', 'برنامج إحالة: ' . $referralProgram->name)
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.referral-programs.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                    <i class="fas fa-gift text-emerald-500 ml-2"></i>{{ $referralProgram->name }}
                </h1>
                @if($referralProgram->description)
                <p class="text-slate-600 dark:text-slate-300 mt-1">{{ $referralProgram->description }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($referralProgram->is_default)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">البرنامج الافتراضي</span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $referralProgram->is_active && $referralProgram->isValid() ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300' }}">
                        {{ $referralProgram->is_active && $referralProgram->isValid() ? 'نشط' : 'معطّل أو منتهي' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(!$referralProgram->is_default && $referralProgram->is_active && $referralProgram->isValid())
            <form action="{{ route('admin.referral-programs.set-default', $referralProgram) }}" method="POST" onsubmit="return confirm('تعيين هذا البرنامج كافتراضي؟');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 dark:border-violet-800 text-violet-700 dark:text-violet-300 text-sm font-semibold hover:bg-violet-50 dark:hover:bg-violet-900/20">
                    <i class="fas fa-star"></i> تعيين كافتراضي
                </button>
            </form>
            @endif
            <a href="{{ route('admin.referral-programs.edit', $referralProgram) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-edit"></i> تعديل
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['label' => 'إجمالي الإحالات', 'value' => $stats['total_referrals'], 'color' => 'text-slate-800 dark:text-white'],
            ['label' => 'مكتملة', 'value' => $stats['completed_referrals'], 'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'قيد الانتظار', 'value' => $stats['pending_referrals'], 'color' => 'text-amber-600 dark:text-amber-400'],
            ['label' => 'إجمالي الخصومات', 'value' => number_format($stats['total_discount_given'], 2).' '.(__('public.currency')), 'color' => 'text-violet-600 dark:text-violet-400', 'raw' => true],
            ['label' => 'إجمالي المكافآت', 'value' => number_format($stats['total_rewards_given'], 2).' '.(__('public.currency')), 'color' => 'text-rose-600 dark:text-rose-400', 'raw' => true],
        ] as $s)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</div>
            <div class="text-lg font-bold mt-1 {{ $s['color'] }}">{{ ($s['raw'] ?? false) ? $s['value'] : number_format($s['value']) }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-emerald-500"></i> تفاصيل البرنامج</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">افتراضي للتسجيل</dt><dd class="font-medium {{ $referralProgram->is_default ? 'text-violet-600 dark:text-violet-400' : 'text-slate-600 dark:text-slate-400' }}">{{ $referralProgram->is_default ? 'نعم' : 'لا' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">نوع الخصم للمحال</dt><dd class="font-medium text-slate-800 dark:text-white">{{ $referralProgram->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">قيمة الخصم</dt><dd class="font-bold text-slate-800 dark:text-white">
                    @if($referralProgram->discount_type == 'percentage')
                        {{ number_format($referralProgram->discount_value, 0) }}%
                    @else
                        {{ number_format($referralProgram->discount_value, 2) }} {{ __('public.currency') }}
                    @endif
                </dd></div>
                @if($referralProgram->maximum_discount)
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأقصى للخصم</dt><dd class="font-mono">{{ number_format($referralProgram->maximum_discount, 2) }} {{ __('public.currency') }}</dd></div>
                @endif
                @if($referralProgram->minimum_order_amount)
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأدنى للطلب</dt><dd class="font-mono">{{ number_format($referralProgram->minimum_order_amount, 2) }} {{ __('public.currency') }}</dd></div>
                @endif
                <div class="flex justify-between gap-4"><dt class="text-slate-500">مدة صلاحية الخصم</dt><dd>{{ $referralProgram->discount_valid_days }} يوم</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأقصى لاستخدام الخصم</dt><dd>{{ $referralProgram->max_discount_uses_per_referred }} مرة</dd></div>
                @if($referralProgram->max_referrals_per_user)
                <div class="flex justify-between gap-4"><dt class="text-slate-500">حد الإحالات لكل مستخدم</dt><dd>{{ $referralProgram->max_referrals_per_user }}</dd></div>
                @endif
                @if($referralProgram->referrer_reward_value)
                <div class="flex justify-between gap-4"><dt class="text-slate-500">مكافأة المحيل</dt><dd class="font-bold text-emerald-600 dark:text-emerald-400">
                    @if($referralProgram->referrer_reward_type == 'percentage')
                        {{ number_format($referralProgram->referrer_reward_value, 0) }}%
                    @elseif($referralProgram->referrer_reward_type == 'points')
                        {{ number_format($referralProgram->referrer_reward_value, 0) }} نقطة
                    @else
                        {{ number_format($referralProgram->referrer_reward_value, 2) }} {{ __('public.currency') }}
                    @endif
                </dd></div>
                @endif
                @if($referralProgram->starts_at || $referralProgram->expires_at)
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الفترة</dt><dd class="text-xs">
                    @if($referralProgram->starts_at) من {{ $referralProgram->starts_at->format('Y-m-d') }} @endif
                    @if($referralProgram->expires_at) إلى {{ $referralProgram->expires_at->format('Y-m-d') }} @endif
                </dd></div>
                @endif
            </dl>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2"><i class="fas fa-list text-emerald-500"></i> آخر الإحالات</h2>
                <a href="{{ route('admin.referrals.index', ['program_id' => $referralProgram->id]) }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">عرض الكل</a>
            </div>
            @php $recentReferrals = $referralProgram->referrals()->latest()->take(10)->get(); @endphp
            @if($recentReferrals->count() > 0)
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($recentReferrals as $referral)
                <a href="{{ route('admin.referrals.show', $referral) }}" class="block p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/30 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
                    <div class="flex justify-between items-start gap-2 mb-2">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">{{ $referral->referred->name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-slate-500">محال من: {{ $referral->referrer->name ?? 'غير معروف' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $referral->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                            {{ $referral->status == 'completed' ? 'مكتملة' : 'قيد الانتظار' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>خصم: {{ number_format($referral->discount_amount ?? 0, 2) }} {{ __('public.currency') }}</span>
                        <span>{{ $referral->created_at->format('d/m/Y') }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-10 text-slate-500 dark:text-slate-400">
                <i class="fas fa-user-friends text-3xl mb-2 opacity-40"></i>
                <p class="text-sm">لا توجد إحالات لهذا البرنامج بعد</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
