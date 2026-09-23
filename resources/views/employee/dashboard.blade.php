@extends('layouts.employee')

@section('title', __('لوحة تحكم الموظف'))
@section('header', __('لوحة تحكم الموظف'))

@push('styles')
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(44, 169, 189, 0.2);
        border-color: rgba(44, 169, 189, 0.4);
    }

    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 32px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .task-card {
        transition: all 0.2s;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .task-card:hover {
        transform: translateX(-4px);
        background: linear-gradient(to right, rgba(44, 169, 189, 0.05), transparent);
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 4px 12px rgba(44, 169, 189, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="welcome-section dashboard-card relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2 text-gray-900">{{ __('مرحباً، ') }}{{ $user->name }}</h2>
                    <p class="text-gray-600 text-base sm:text-lg font-medium">{{ __('إليك نظرة عامة على مهامك ونشاطك اليوم') }}</p>
                    @if($user->employeeJob)
                        <p class="text-gray-500 text-sm mt-2 flex items-center gap-2">
                            <i class="fas fa-briefcase"></i>
                            <span>{{ $user->employeeJob->name }}</span>
                            @if($user->employee_code)
                                <span class="mr-2">({{ $user->employee_code }})</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-tie text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($jobInsights))
    <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-l from-indigo-50/90 to-white p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-indigo-600"></i>
                {{ $jobInsights['label'] }}
            </h3>
            @php
                $deskRoute = match ($jobCode ?? '') {
                    'accountant' => route('employee.accountant-desk.index'),
                    'sales' => route('employee.sales.desk'),
                    'hr' => route('employee.hr-desk.index'),
                    'general_supervision' => route('employee.supervision-desk.index'),
                    'supervisor' => route('employee.supervision-desk.index'),
                    default => null,
                };
            @endphp
            @if($deskRoute)
                <a href="{{ $deskRoute }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-700 hover:text-indigo-900">
                    {{ __('فتح لوحة الوظيفة التفصيلية') }} <i class="fas fa-arrow-left text-xs"></i>
                </a>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($jobInsights['items'] as $item)
                <div class="rounded-xl bg-white border border-gray-200 px-4 py-3 flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-gray-600">{{ $item['text'] }}</span>
                    <span class="text-2xl font-black tabular-nums
                        @if(($item['color'] ?? '') === 'amber') text-amber-700
                        @elseif(($item['color'] ?? '') === 'emerald') text-emerald-700
                        @elseif(($item['color'] ?? '') === 'sky') text-sky-700
                        @elseif(($item['color'] ?? '') === 'indigo') text-indigo-700
                        @elseif(($item['color'] ?? '') === 'rose') text-rose-700
                        @elseif(($item['color'] ?? '') === 'blue') text-blue-700
                        @elseif(($item['color'] ?? '') === 'red') text-red-700
                        @else text-gray-900 @endif">{{ number_format($item['value']) }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('إجمالي المهام') }}</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('معلقة') }}</p>
                        <p class="text-3xl font-black text-yellow-700">{{ $stats['pending_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('قيد التنفيذ') }}</p>
                        <p class="text-3xl font-black text-blue-700">{{ $stats['in_progress_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-spinner text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('مكتملة') }}</p>
                        <p class="text-3xl font-black text-green-700">{{ $stats['completed_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('متأخرة') }}</p>
                        <p class="text-3xl font-black text-red-700">{{ $stats['overdue_tasks'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المهام الأخيرة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">{{ __('المهام الأخيرة') }}</h3>
                    <p class="text-xs text-gray-600 font-medium mt-1">{{ __('آخر 10 مهام مخصصة لك') }}</p>
                </div>
            </div>
            <a href="{{ route('employee.tasks.index') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-list mr-2"></i>
                {{ __('عرض جميع المهام') }}
            </a>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
            <div class="task-card px-6 py-4 hover:bg-gray-50 transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-base font-bold text-gray-900">{{ $task->title }}</h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($task->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($task->priority === 'urgent') {{ __('عاجل') }}
                                @elseif($task->priority === 'high') {{ __('عالي') }}
                                @elseif($task->priority === 'medium') {{ __('متوسط') }}
                                @else {{ __('منخفض') }}
                                @endif
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($task->status === 'completed') {{ __('مكتملة') }}
                                @elseif($task->status === 'in_progress') {{ __('قيد التنفيذ') }}
                                @elseif($task->status === 'pending') {{ __('معلقة') }}
                                @else {{ $task->status }}
                                @endif
                            </span>
                        </div>
                        @if($task->description)
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 100) }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-gray-500 flex-wrap">
                            @if($task->assigner)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user-tie"></i>
                                    {{ $task->assigner->name }}
                                </span>
                            @endif
                            @if($task->deadline)
                                <span class="flex items-center gap-1 {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600 font-semibold' : '' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $task->deadline->format('Y-m-d') }}
                                </span>
                            @endif
                            @if($task->progress !== null)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-line"></i>
                                    {{ $task->progress }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('employee.tasks.show', $task) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-eye mr-2"></i>{{ __('عرض') }}
                    </a>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-tasks text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-lg mb-1">{{ __('لا توجد مهام حالياً') }}</p>
                        <p class="text-sm text-gray-600 font-medium">{{ __('سيتم إشعارك عند تعيين مهام جديدة لك') }}</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- إجراءات سريعة حسب صلاحيات الوظيفة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($user->employeeCan('tasks'))
        <a href="{{ route('employee.tasks.index') }}"
           class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm mb-3">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('مهامي') }}</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">{{ __('متابعة المهام المسندة إليك') }}</p>
        </a>
        @endif

        @if($user->employeeCan('desk_accountant'))
        <a href="{{ route('employee.accountant-desk.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-amber-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shadow-sm mb-3"><i class="fas fa-calculator text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('لوحة المحاسب') }}</h4>
            <p class="text-xs text-gray-600">{{ __('طلبات الدفع، الاتفاقيات، دفعات الرواتب') }}</p>
        </a>
        @endif
        @if($user->employeeCan('sales_desk'))
        <a href="{{ route('employee.sales.desk') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shadow-sm mb-3"><i class="fas fa-shopping-cart text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('لوحة المبيعات') }}</h4>
            <p class="text-xs text-gray-600">{{ __('طلبات الكورسات والإيرادات') }}</p>
        </a>
        @endif
        @if($user->employeeCan('hr_desk'))
        <a href="{{ route('employee.hr-desk.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-rose-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shadow-sm mb-3"><i class="fas fa-users text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('لوحة الموارد البشرية') }}</h4>
            <p class="text-xs text-gray-600">{{ __('دليل الموظفين، مراجعة الإجازات، وسجل HR') }}</p>
        </a>
        @endif
        @if($user->employeeCan('supervision_desk'))
        <a href="{{ route('employee.supervision-desk.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-indigo-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 shadow-sm mb-3"><i class="fas fa-clipboard-check text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('لوحة الإشراف') }}</h4>
            <p class="text-xs text-gray-600">{{ __('مهام الفريق والمتأخرات') }}</p>
        </a>
        @endif
        @if($user->employeeCan('academic_supervision_desk'))
        <a href="{{ route('employee.academic-supervision.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-teal-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-700 shadow-sm mb-3"><i class="fas fa-user-graduate text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('الإشراف الأكاديمي') }}</h4>
            <p class="text-xs text-gray-600">{{ __('متابعة الطلاب المعيّنين لك') }}</p>
        </a>
        @endif

        @if($user->employeeCan('leaves'))
        <a href="{{ route('employee.leaves.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-cyan-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center text-cyan-700 shadow-sm mb-3"><i class="fas fa-umbrella-beach text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('إجازاتي') }}</h4>
            <p class="text-xs text-gray-600">{{ __('طلبات الإجازة والمتابعة') }}</p>
        </a>
        @endif
        @if($user->employeeCan('accounting'))
        <a href="{{ route('employee.accounting.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-slate-400 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shadow-sm mb-3"><i class="fas fa-wallet text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('محاسبتي الشخصية') }}</h4>
            <p class="text-xs text-gray-600">{{ __('راتبك وخصوماتك وحسابك البنكي') }}</p>
        </a>
        @endif
        @if($user->employeeCan('agreements'))
        <a href="{{ route('employee.agreements.index') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-violet-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-700 shadow-sm mb-3"><i class="fas fa-file-contract text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('اتفاقيات العمل') }}</h4>
            <p class="text-xs text-gray-600">{{ __('عقودك مع المنصة') }}</p>
        </a>
        @endif
        @if($user->employeeCan('reports'))
        <a href="{{ route('employee.reports') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-purple-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700 shadow-sm mb-3"><i class="fas fa-chart-line text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('تقاريري') }}</h4>
            <p class="text-xs text-gray-600">{{ __('أداء المهام والإجازات') }}</p>
        </a>
        @endif
        @if($user->employeeCan('calendar'))
        <a href="{{ route('employee.calendar') }}" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-orange-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 shadow-sm mb-3"><i class="fas fa-calendar-alt text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('التقويم') }}</h4>
            <p class="text-xs text-gray-600">{{ __('المواعيد والمهام') }}</p>
        </a>
        @endif

        @if($user->employeeJob)
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm mb-3">
                <i class="fas fa-briefcase text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('وظيفتك') }}</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                <strong>{{ $user->employeeJob->name }}</strong>
                @if($user->employee_code)<br>{{ __('الرمز: ') }}{{ $user->employee_code }}@endif
            </p>
        </div>
        @endif

        @if($user->employeeCan('tasks'))
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm mb-3">
                <i class="fas fa-chart-bar text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ __('معدل إنجاز المهام') }}</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                <strong class="text-green-600">{{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0 }}%</strong>
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
