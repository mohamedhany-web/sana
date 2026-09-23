@extends('layouts.admin')

@section('title', __('تعديل المهمة'))
@section('header', __('تعديل المهمة'))

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-edit"></i>
                        {{ __('تعديل المهمة') }}
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ __('تعديل مهمة:') }} {{ $employeeTask->title }}</h1>
                </div>
                <a href="{{ route('admin.employee-tasks.show', $employeeTask) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-arrow-right"></i>
                    {{ __('العودة للتفاصيل') }}
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <form action="{{ route('admin.employee-tasks.update', $employeeTask) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">{{ __('معلومات المهمة') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الموظف') }} <span class="text-red-500">*</span></label>
                        <select name="employee_id" id="employee_id" required class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        data-job-name="{{ $employee->employeeJob?->name ?? '' }}"
                                        data-job-code="{{ $employee->employeeJob?->code ?? '' }}"
                                        {{ old('employee_id', $employeeTask->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                    @if($employee->employee_code)({{ $employee->employee_code }})@endif
                                    @if($employee->employeeJob) - {{ $employee->employeeJob->name }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="task_type" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('نوع المهمة') }} <span class="text-red-500">*</span></label>
                        <select name="task_type" id="task_type" required class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            @foreach($taskTypeDefinitions as $code => $meta)
                                <option value="{{ $code }}"
                                        @if(($meta['job_codes'] ?? null) === null) data-all-jobs="1"
                                        @else data-job-codes="{{ e(json_encode($meta['job_codes'])) }}"
                                        @endif
                                        {{ old('task_type', $employeeTask->task_type ?? 'general') === $code ? 'selected' : '' }}>
                                    {{ $meta['label'] ?? $code }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('عنوان المهمة') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $employeeTask->title) }}" required class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                        @error('title')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('وصف المهمة') }}</label>
                        <textarea name="description" id="description" rows="4" class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">{{ old('description', $employeeTask->description) }}</textarea>
                        @error('description')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الأولوية') }} <span class="text-red-500">*</span></label>
                        <select name="priority" id="priority" required class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="low" {{ old('priority', $employeeTask->priority) == 'low' ? 'selected' : '' }}>{{ __('منخفضة') }}</option>
                            <option value="medium" {{ old('priority', $employeeTask->priority) == 'medium' ? 'selected' : '' }}>{{ __('متوسطة') }}</option>
                            <option value="high" {{ old('priority', $employeeTask->priority) == 'high' ? 'selected' : '' }}>{{ __('عالية') }}</option>
                            <option value="urgent" {{ old('priority', $employeeTask->priority) == 'urgent' ? 'selected' : '' }}>{{ __('عاجلة') }}</option>
                        </select>
                        @error('priority')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الحالة') }} <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="pending" {{ old('status', $employeeTask->status) == 'pending' ? 'selected' : '' }}>{{ __('معلقة') }}</option>
                            <option value="in_progress" {{ old('status', $employeeTask->status) == 'in_progress' ? 'selected' : '' }}>{{ __('قيد التنفيذ') }}</option>
                            <option value="completed" {{ old('status', $employeeTask->status) == 'completed' ? 'selected' : '' }}>{{ __('مكتملة') }}</option>
                            <option value="cancelled" {{ old('status', $employeeTask->status) == 'cancelled' ? 'selected' : '' }}>{{ __('ملغاة') }}</option>
                            <option value="on_hold" {{ old('status', $employeeTask->status) == 'on_hold' ? 'selected' : '' }}>{{ __('معلقة مؤقتاً') }}</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الموعد النهائي') }}</label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $employeeTask->deadline?->format('Y-m-d')) }}" class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                        @error('deadline')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="progress" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('التقدم (%)') }}</label>
                        <input type="number" name="progress" id="progress" value="{{ old('progress', $employeeTask->progress) }}" min="0" max="100" class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                        @error('progress')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ملاحظات إضافية') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">{{ old('notes', $employeeTask->notes) }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.employee-tasks.show', $employeeTask) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-times"></i>
                    {{ __('إلغاء') }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 hover:from-blue-700 hover:via-blue-600 hover:to-blue-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-save"></i>
                    {{ __('حفظ التعديلات') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var employeeSelect = document.getElementById('employee_id');
    var taskTypeSelect = document.getElementById('task_type');
    if (!employeeSelect || !taskTypeSelect) return;

    function jobCodeFromEmployee() {
        var opt = employeeSelect.options[employeeSelect.selectedIndex];
        return opt ? (opt.getAttribute('data-job-code') || '') : '';
    }

    function filterTaskTypes() {
        var code = jobCodeFromEmployee();
        var noEmployee = !employeeSelect.value;
        var options = taskTypeSelect.querySelectorAll('option');
        var firstVisible = null;
        options.forEach(function(o) {
            var all = o.getAttribute('data-all-jobs') === '1';
            var raw = o.getAttribute('data-job-codes');
            var allowed = all;
            if (!allowed && raw) {
                try {
                    var arr = JSON.parse(raw);
                    allowed = !noEmployee && code && arr.indexOf(code) !== -1;
                } catch (e) { allowed = false; }
            }
            if (noEmployee && !all) allowed = false;
            o.hidden = !allowed;
            o.disabled = !allowed;
            if (allowed && !firstVisible) firstVisible = o;
        });
        var sel = taskTypeSelect.selectedOptions[0];
        if (sel && sel.disabled && firstVisible) taskTypeSelect.value = firstVisible.value;
    }

    employeeSelect.addEventListener('change', filterTaskTypes);
    filterTaskTypes();
});
</script>
@endpush
@endsection
