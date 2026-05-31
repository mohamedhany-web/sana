@extends('layouts.admin')

@section('title', 'برامج الإحالة')
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-gift text-emerald-500 ml-2"></i>برامج الإحالة
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">تحديد خصم المحال ومكافأة المحيل — يُطبَّق عند التسجيل برابط الإحالة</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <i class="fas fa-user-friends"></i> الإحالات
            </a>
            <a href="{{ route('admin.referral-programs.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm shadow-lg shadow-emerald-500/20 transition-all">
                <i class="fas fa-plus"></i> برنامج جديد
            </a>
        </div>
    </div>

    @include('admin.partials.alert-success')
    @include('admin.partials.alert-errors')
    @include('admin.partials.referral-program-how-it-works')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'إجمالي البرامج', 'value' => $stats['total'], 'class' => 'text-slate-800 dark:text-white'],
            ['label' => 'نشطة', 'value' => $stats['active'], 'class' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'معطّلة', 'value' => $stats['inactive'], 'class' => 'text-rose-600 dark:text-rose-400'],
            ['label' => 'صالحة الآن', 'value' => $stats['valid_now'] ?? 0, 'class' => 'text-violet-600 dark:text-violet-400'],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $card['label'] }}</div>
            <div class="text-2xl font-bold mt-1 {{ $card['class'] }}">{{ number_format($card['value']) }}</div>
        </div>
        @endforeach
    </div>

    @if($programs->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">البرنامج</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">افتراضي</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">إحالات</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">خصم المحال</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">مكافأة المحيل</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($programs as $program)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.referral-programs.show', $program) }}" class="font-semibold text-slate-900 dark:text-white hover:text-emerald-600">{{ $program->name }}</a>
                            @if($program->description)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ \Illuminate\Support\Str::limit($program->description, 48) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($program->is_default)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">افتراضي</span>
                            @else
                            <form action="{{ route('admin.referral-programs.set-default', $program) }}" method="POST" class="inline" onsubmit="return confirm('تعيين كبرنامج افتراضي؟');">
                                @csrf
                                <button type="submit" class="text-xs text-violet-600 hover:underline disabled:opacity-40" @disabled(!$program->is_active || !$program->isValid())>تعيين</button>
                            </form>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-200">{{ number_format($program->referrals_count ?? 0) }}</td>
                        <td class="px-4 py-3 text-slate-800 dark:text-slate-100">
                            @if($program->discount_type === 'percentage')
                                {{ number_format($program->discount_value, 0) }}%
                            @else
                                {{ number_format($program->discount_value, 2) }} {{ __('public.currency') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-800 dark:text-slate-100">
                            @if($program->referrer_reward_value)
                                @if($program->referrer_reward_type === 'percentage') {{ number_format($program->referrer_reward_value, 0) }}%
                                @elseif($program->referrer_reward_type === 'points') {{ number_format($program->referrer_reward_value, 0) }} نقطة
                                @else {{ number_format($program->referrer_reward_value, 2) }} {{ __('public.currency') }}
                                @endif
                            @else — @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($program->is_active && $program->isValid()) ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300' }}">
                                {{ ($program->is_active && $program->isValid()) ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.referral-programs.show', $program) }}" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.referral-programs.edit', $program) }}" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.referral-programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('حذف البرنامج؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">{{ $programs->links() }}</div>
        @endif
    </div>
    @else
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center shadow-sm">
        <i class="fas fa-gift text-4xl text-slate-300 dark:text-slate-600 mb-4"></i>
        <p class="text-slate-600 dark:text-slate-300 font-medium mb-1">لا توجد برامج إحالة بعد</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">أنشئ برنامجاً يحدد خصم الصديق ومكافأة المحيل</p>
        <a href="{{ route('admin.referral-programs.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
            <i class="fas fa-plus"></i> إنشاء برنامج
        </a>
    </div>
    @endif
</div>
@endsection


