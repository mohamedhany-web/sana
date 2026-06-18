@extends('layouts.app')

@section('title', __('student.orders_page_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $collection = $orders->getCollection();
    $pendingCount = $collection->where('status', 'pending')->count();
    $approvedCount = $collection->where('status', 'approved')->count();
    $rejectedCount = $collection->where('status', 'rejected')->count();
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.orders_page_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.orders_subtitle') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('academic-years') }}" class="sanua-page-head__btn">
                <i class="fas fa-search"></i>
                {{ __('student.browse_courses_btn') }}
            </a>
        </div>
    </header>

    @if($orders->count() > 0)
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $orders->total() }}</strong>
                    <span>إجمالي الطلبات</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-clock"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $pendingCount }}</strong>
                    <span>قيد المراجعة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $approvedCount }}</strong>
                    <span>مقبولة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                    <i class="fas fa-times-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $rejectedCount }}</strong>
                    <span>مرفوضة</span>
                </div>
            </div>
        </div>

        <section class="sanua-section">
            <h2 class="sanua-section-title">🛒 {{ __('student.orders_page_title') }}</h2>

            @foreach($orders as $order)
                <div class="sanua-order-card">
                    <div class="sanua-order-card__row">
                        <div class="sanua-order-card__main">
                            <div class="sanua-live-card__badges">
                                <span class="sanua-badge {{ $order->status == 'pending' ? 'sanua-badge--pending' : ($order->status == 'approved' ? 'sanua-badge--approved' : 'sanua-badge--rejected') }}">
                                    @if($order->status == 'pending')<i class="fas fa-clock"></i>
                                    @elseif($order->status == 'approved')<i class="fas fa-check-circle"></i>
                                    @else<i class="fas fa-times-circle"></i>
                                    @endif
                                    {{ $order->status_text }}
                                </span>
                            </div>
                            <h3 class="sanua-order-card__title">
                                @if($order->academic_year_id && $order->learningPath)
                                    {{ $order->learningPath->name ?? __('student.learning_path_label') }}
                                @else
                                    {{ $order->course->title ?? __('student.course_undefined') }}
                                @endif
                            </h3>
                            <p class="sanua-order-card__sub">
                                @if($order->academic_year_id && $order->learningPath)
                                    {{ __('student.learning_path_label') }}
                                    @if($order->learningPath->price)
                                        · {{ number_format($order->learningPath->price, 2) }} {{ __('public.currency') }}
                                    @endif
                                @elseif($order->course && ($order->course->academicYear || $order->course->academicSubject))
                                    @if($order->course->academicYear){{ $order->course->academicYear->name }}@endif
                                    @if($order->course->academicSubject) · {{ $order->course->academicSubject->name }}@endif
                                @endif
                                · {{ $order->created_at->diffForHumans() }}
                            </p>

                            <div class="sanua-metric-grid">
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.amount_label') }}</span>
                                    <span class="sanua-metric__value">{{ number_format($order->amount, 2) }} {{ __('public.currency') }}</span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.payment_method_label') }}</span>
                                    <span class="sanua-metric__value">
                                        @if($order->payment_method == 'bank_transfer') {{ __('student.bank_transfer') }}
                                        @elseif($order->payment_method == 'cash') {{ __('student.cash_label') }}
                                        @else {{ __('student.other_label') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.order_date_label') }}</span>
                                    <span class="sanua-metric__value">{{ $order->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($order->approved_at)
                                    <div class="sanua-metric">
                                        <span class="sanua-metric__label">{{ __('student.approved_date_label') }}</span>
                                        <span class="sanua-metric__value">{{ $order->approved_at->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($order->notes)
                                <div class="sanua-order-card__note">
                                    <strong>{{ __('student.your_notes') }}:</strong> {{ $order->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="sanua-order-card__actions">
                            <a href="{{ route('orders.show', $order) }}" class="sanua-btn sanua-btn--purple">
                                <i class="fas fa-eye"></i>
                                {{ __('student.view_details') }}
                            </a>
                            @if($order->status == 'approved' && $order->course)
                                <a href="{{ route('courses.show', $order->course) }}" class="sanua-btn sanua-btn--green">
                                    <i class="fas fa-play"></i>
                                    {{ __('student.enter_course') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        @if($orders->hasPages())
            <div class="sanua-pagination">{{ $orders->links() }}</div>
        @endif
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon"><i class="fas fa-shopping-cart"></i></div>
            <h3>{{ __('student.no_orders') }}</h3>
            <p>{{ __('student.no_orders_desc') }}</p>
            <a href="{{ route('academic-years') }}" class="sanua-empty__btn">
                <i class="fas fa-plus"></i>
                {{ __('student.browse_courses_btn') }}
            </a>
        </div>
    @endif
</div>
@endsection
