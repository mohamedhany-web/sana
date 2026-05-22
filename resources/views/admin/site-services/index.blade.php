@extends('layouts.admin')

@section('title', 'خدمات الموقع - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'خدمات الموقع')

@section('content')
@php
    $totalServices = \App\Models\SiteService::count();
    $activeServices = \App\Models\SiteService::where('is_active', true)->count();
    $inactiveServices = max(0, $totalServices - $activeServices);
@endphp

<div class="admin-dashboard admin-list-page space-y-7">

    @include('admin.partials.alert-success')

    <x-admin.page-hero
        title="خدمات الموقع"
        subtitle="تظهر في الصفحة العامة /services وفي شريط التنقل. أضف الاسم والمقدمة والتفاصيل لكل خدمة."
        icon="fas fa-concierge-bell"
    >
        <a href="{{ route('admin.site-services.create') }}" class="admin-btn admin-btn--primary">
            <i class="fas fa-plus"></i>
            خدمة جديدة
        </a>
    </x-admin.page-hero>

    <div class="admin-mini-stats admin-mini-stats--3">
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">إجمالي الخدمات</div>
            <div class="admin-mini-stat__value">{{ number_format($totalServices) }}</div>
            <div class="admin-mini-stat__meta">في قاعدة البيانات</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">نشطة</div>
            <div class="admin-mini-stat__value">{{ number_format($activeServices) }}</div>
            <div class="admin-mini-stat__meta">ظاهرة في الموقع</div>
        </div>
        <div class="admin-mini-stat {{ $inactiveServices > 0 ? 'admin-mini-stat--highlight' : '' }}">
            <div class="admin-mini-stat__label">معطّلة</div>
            <div class="admin-mini-stat__value">{{ number_format($inactiveServices) }}</div>
            <div class="admin-mini-stat__meta">مخفية عن الزوار</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h3><i class="fas fa-filter"></i> تصفية وبحث</h3>
        </div>
        <div class="admin-panel__body">
            <form method="GET" action="{{ route('admin.site-services.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="admin-field md:col-span-1">
                    <label>بحث</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="الاسم أو الرابط..."
                           class="admin-input">
                </div>
                <div class="admin-field">
                    <label>الحالة</label>
                    <select name="status" class="admin-input">
                        <option value="">كل الحالات</option>
                        <option value="active" @selected(request('status') === 'active')>نشط</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>معطل</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn admin-btn--primary flex-1">
                        <i class="fas fa-filter"></i>
                        تصفية
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.site-services.index') }}" class="admin-btn admin-btn--outline" title="مسح">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-list"></i> قائمة الخدمات</h2>
                <p class="admin-panel__sub">{{ $services->total() }} خدمة</p>
            </div>
            <a href="{{ route('public.services.index') }}" target="_blank" rel="noopener" class="admin-btn admin-btn--outline text-xs">
                <i class="fas fa-external-link-alt"></i>
                معاينة الموقع
            </a>
        </div>

        @if($services->count() > 0)
            <div class="admin-panel__body--flush overflow-x-auto">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>صورة</th>
                            <th>الاسم</th>
                            <th>الرابط</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr class="{{ ! $service->is_active ? '' : '' }}">
                                <td class="font-semibold text-slate-600">{{ $service->sort_order }}</td>
                                <td>
                                    @if($service->publicImageUrl())
                                        <img src="{{ $service->publicImageUrl() }}" alt="" class="w-14 h-10 object-cover rounded-lg border border-slate-200">
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td>
                                    <p class="font-bold text-slate-800">{{ $service->name }}</p>
                                    @if($service->summary)
                                        <p class="text-xs text-slate-500 line-clamp-1 max-w-xs">{{ $service->summary }}</p>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('public.services.show', $service) }}" target="_blank" rel="noopener"
                                       class="text-xs font-mono font-semibold" style="color: var(--admin-primary);">
                                        /services/{{ $service->slug }}
                                    </a>
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="admin-badge admin-badge--success">نشط</span>
                                    @else
                                        <span class="admin-badge admin-badge--warn">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('admin.site-services.edit', $service) }}" class="admin-icon-btn" title="تعديل">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.site-services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذه الخدمة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">
                {{ $services->links() }}
            </div>
        @else
            <div class="admin-empty">
                <i class="fas fa-concierge-bell"></i>
                <p class="text-sm font-bold text-slate-600 mb-1">لا توجد خدمات</p>
                <p class="text-xs mb-3">ابدأ بإضافة أول خدمة للموقع العام.</p>
                <a href="{{ route('admin.site-services.create') }}" class="admin-btn admin-btn--primary">
                    <i class="fas fa-plus"></i>
                    إضافة خدمة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
