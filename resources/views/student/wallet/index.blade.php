@extends('layouts.app')

@section('title', __('student.wallet_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $depositCount = isset($transactions)
        ? $transactions->getCollection()->filter(fn ($t) => $t->type == 'deposit' || $t->type == 'إيداع')->count()
        : 0;
    $withdrawCount = isset($transactions)
        ? $transactions->getCollection()->filter(fn ($t) => ! ($t->type == 'deposit' || $t->type == 'إيداع'))->count()
        : 0;
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.wallet_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.transactions_log') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.my_courses_link') }}
            </a>
        </div>
    </header>

    @if(isset($wallet))
        <div class="sanua-wallet-balance">
            <div>
                <p class="sanua-wallet-balance__label">{{ __('student.current_balance') }}</p>
                <p class="sanua-wallet-balance__amount">
                    {{ number_format($wallet->balance ?? 0, 2) }}
                    <span style="font-size:0.55em;font-weight:800;opacity:0.9">{{ __('public.currency') }}</span>
                </p>
            </div>
            <span class="sanua-wallet-balance__icon" aria-hidden="true">
                <i class="fas fa-wallet"></i>
            </span>
        </div>

        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-wallet"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ number_format($wallet->balance ?? 0, 0) }}</strong>
                    <span>{{ __('student.current_balance') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-arrow-down"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $depositCount }}</strong>
                    <span>إيداع (الصفحة)</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                    <i class="fas fa-arrow-up"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $withdrawCount }}</strong>
                    <span>سحب (الصفحة)</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-receipt"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $transactions->total() ?? 0 }}</strong>
                    <span>إجمالي العمليات</span>
                </div>
            </div>
        </div>
    @else
        <div class="sanua-wallet-empty">{{ __('student.no_wallet_message') }}</div>
    @endif

    @if(isset($transactions) && $transactions->count() > 0)
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3>{{ __('student.transactions_log') }}</h3>
                </div>
                <div class="sanua-panel__body sanua-tx-list">
                    @foreach($transactions as $transaction)
                        @php $isDeposit = $transaction->type == 'deposit' || $transaction->type == 'إيداع'; @endphp
                        <div class="sanua-tx-row">
                            <div class="min-w-0">
                                <p class="sanua-tx-row__title">{{ $transaction->description ?? __('student.transaction_default') }}</p>
                                <p class="sanua-tx-row__date">
                                    {{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '—' }}
                                </p>
                            </div>
                            <p class="sanua-tx-row__amount {{ $isDeposit ? 'is-in' : 'is-out' }}">
                                {{ $isDeposit ? '+' : '−' }}{{ number_format($transaction->amount ?? 0, 2) }}
                                {{ __('public.currency') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($transactions->hasPages())
                <div class="sanua-pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        </section>
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h3>{{ __('student.no_transactions') }}</h3>
            <p>ستظهر عمليات المحفظة هنا عند إجرائها</p>
        </div>
    @endif
</div>
@endsection
