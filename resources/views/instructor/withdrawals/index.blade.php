@extends('layouts.app')

@section('title', __('instructor.withdrawal_requests') . ' - ' . config('app.name', 'Sana'))
@section('header', __('instructor.withdrawal_requests'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">{{ __('instructor.withdrawal_requests') }}</h1>
                <p class="text-sm text-slate-500">متابعة طلبات السحب، المبالغ المتاحة، وحالة التحويلات المالية.</p>
            </div>
            @if($stats['available_amount'] > 0)
                <a href="{{ route('instructor.withdrawals.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold transition-colors shadow-sm">
                    <i class="fas fa-plus"></i>
                    {{ __('instructor.new_withdrawal_request') }}
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 inline-flex items-center justify-center">
                    <i class="fas fa-sack-dollar"></i>
                </span>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg">مالي</span>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ __('instructor.total_earned') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['total_earned'], 2) }} {{ __('public.currency') }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 inline-flex items-center justify-center">
                    <i class="fas fa-arrow-down"></i>
                </span>
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-lg">مصروف</span>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ __('instructor.total_withdrawn') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['total_withdrawn'], 2) }} {{ __('public.currency') }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 inline-flex items-center justify-center">
                    <i class="fas fa-clock"></i>
                </span>
                <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg">معلّق</span>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ __('instructor.pending_withdrawals') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['pending_withdrawals'], 2) }} {{ __('public.currency') }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 inline-flex items-center justify-center">
                    <i class="fas fa-wallet"></i>
                </span>
                <span class="text-xs font-semibold text-violet-700 bg-violet-50 px-2 py-1 rounded-lg">متاح</span>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ __('instructor.available_amount') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['available_amount'], 2) }} {{ __('public.currency') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-money-check-dollar text-amber-600"></i>
                {{ __('instructor.withdrawal_requests') }}
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('instructor.request_number') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('instructor.amount') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('instructor.payment_method') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('common.status') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('instructor.request_date') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold tracking-wider text-slate-700 uppercase">{{ __('instructor.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900">{{ $withdrawal->request_number ?? '#' . $withdrawal->id }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 text-lg">{{ number_format($withdrawal->amount, 2) }} {{ __('public.currency') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                @if($withdrawal->payment_method == 'bank_transfer')
                                    <i class="fas fa-university ml-1"></i> {{ __('instructor.bank_transfer') }}
                                @elseif($withdrawal->payment_method == 'wallet')
                                    <i class="fas fa-wallet ml-1"></i> {{ __('instructor.wallet') }}
                                @elseif($withdrawal->payment_method == 'cash')
                                    <i class="fas fa-money-bill ml-1"></i> {{ __('instructor.cash') }}
                                @else
                                    <i class="fas fa-ellipsis-h ml-1"></i> {{ __('instructor.other') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($withdrawal->status == 'completed') bg-emerald-100 text-emerald-700
                                @elseif($withdrawal->status == 'processing') bg-blue-100 text-blue-700
                                @elseif($withdrawal->status == 'approved') bg-amber-100 text-amber-700
                                @elseif($withdrawal->status == 'pending') bg-slate-100 text-slate-700
                                @elseif($withdrawal->status == 'rejected') bg-rose-100 text-rose-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                                @if($withdrawal->status == 'completed') {{ __('instructor.completed') }}
                                @elseif($withdrawal->status == 'processing') {{ __('instructor.processing') }}
                                @elseif($withdrawal->status == 'approved') {{ __('instructor.approved') }}
                                @elseif($withdrawal->status == 'pending') {{ __('instructor.pending_status') }}
                                @elseif($withdrawal->status == 'rejected') {{ __('instructor.rejected') }}
                                @elseif($withdrawal->status == 'cancelled') {{ __('instructor.cancelled') }}
                                @else {{ $withdrawal->status }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600">{{ $withdrawal->created_at->format('Y-m-d H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('instructor.withdrawals.show', $withdrawal) }}" 
                                   class="inline-flex items-center justify-center w-10 h-10 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-xl transition-colors"
                                   title="{{ __('common.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(in_array($withdrawal->status, ['pending', 'approved']))
                                <form action="{{ route('instructor.withdrawals.cancel', $withdrawal) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('{{ __('instructor.confirm_cancel_withdrawal') }}');"
                                      class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl transition-colors"
                                            title="{{ __('instructor.cancel') }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-slate-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ __('instructor.no_withdrawals') }}</p>
                                    <p class="text-sm text-slate-600 mt-1">{{ __('instructor.no_withdrawals_description') }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
