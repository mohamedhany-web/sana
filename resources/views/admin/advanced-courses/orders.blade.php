@extends('layouts.admin')

@section('title', 'طلبات الكورس')
@section('header', __('admin.courses_management'))

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <div class="section-card">
        <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-sky-600">{{ __('admin.dashboard') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-sky-600">{{ __('admin.courses_management') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}" class="hover:text-sky-600 truncate">{{ Str::limit($advancedCourse->title, 30) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700">الطلبات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mt-1">طلبات التسجيل</h1>
                <p class="text-sm text-slate-600 mt-1 truncate">{{ $advancedCourse->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.orders.index') }}"
                   class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-list"></i>
                    جميع الطلبات
                </a>
                <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 font-medium text-slate-700 hover:bg-slate-50 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورس
                </a>
            </div>
        </div>
    </div>

    <div class="section-card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 bg-sky-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-2xl text-sky-600"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-slate-800 truncate">{{ $advancedCourse->title }}</h3>
                    <p class="text-sm text-slate-500">
                        {{ $advancedCourse->category ?? '—' }} · {{ $advancedCourse->instructor?->name ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="text-center px-4 py-2 bg-sky-50 rounded-xl border border-sky-100">
                <div class="text-2xl font-bold text-sky-600">{{ $orders->total() }}</div>
                <div class="text-sm text-slate-600 font-medium">إجمالي الطلبات</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card p-5">
            <div class="flex items-center gap-4">
                <div class="stat-icon bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 w-12 h-12">
                    <i class="fas fa-clock text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-slate-800">{{ $orders->where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-slate-500">معلقة</p>
                </div>
            </div>
        </div>
        <div class="stat-card p-5">
            <div class="flex items-center gap-4">
                <div class="stat-icon bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0 w-12 h-12">
                    <i class="fas fa-check text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-slate-800">{{ $orders->where('status', 'approved')->count() }}</p>
                    <p class="text-sm text-slate-500">مقبولة</p>
                </div>
            </div>
        </div>
        <div class="stat-card p-5">
            <div class="flex items-center gap-4">
                <div class="stat-icon bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0 w-12 h-12">
                    <i class="fas fa-times text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-slate-800">{{ $orders->where('status', 'rejected')->count() }}</p>
                    <p class="text-sm text-slate-500">مرفوضة</p>
                </div>
            </div>
        </div>
        <div class="stat-card p-5">
            <div class="flex items-center gap-4">
                <div class="stat-icon bg-sky-500 rounded-xl flex items-center justify-center flex-shrink-0 w-12 h-12">
                    <i class="fas fa-shopping-cart text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-slate-800">{{ $orders->total() }}</p>
                    <p class="text-sm text-slate-500">إجمالي</p>
                </div>
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="section-card overflow-hidden">
            <div class="section-card-header">
                <h4 class="text-lg font-bold text-slate-800">طلبات التسجيل</h4>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">طريقة الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">تاريخ الطلب</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($orders as $order)
                            @php
                                $statusClass = $order->status == 'pending' ? 'bg-amber-100 text-amber-800' : ($order->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                                $paymentLabel = $order->payment_method == 'whatsapp' ? 'واتساب' : ($order->payment_method == 'bank_transfer' ? 'تحويل بنكي' : ($order->payment_method == 'cash' ? 'كاش' : $order->payment_method));
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <span class="text-sky-600 font-semibold">{{ substr($order->user->name ?? '', 0, 1) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-800">{{ $order->user->name ?? '—' }}</div>
                                            <div class="text-sm text-slate-500 truncate max-w-[180px]">{{ $order->user->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-medium">{{ $paymentLabel }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">{{ number_format($order->amount, 2) }} {{ __('public.currency') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-100 transition-colors" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status == 'pending')
                                            <form action="{{ route('admin.orders.approve', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('هل تريد الموافقة على هذا الطلب؟');"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="موافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('هل تريد رفض هذا الطلب؟');"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="رفض">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="section-card p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">لا توجد طلبات</h3>
            <p class="text-slate-600 mb-6">لم يتم تقديم أي طلبات تسجيل لهذا الكورس بعد</p>
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-list"></i>
                عرض جميع الطلبات
            </a>
        </div>
    @endif
</div>
@endsection
