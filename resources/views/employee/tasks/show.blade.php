@extends('layouts.employee')

@section('title', 'تفاصيل المهمة')
@section('header', 'تفاصيل المهمة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">
                        <i class="fas fa-tag"></i> {{ $task->taskTypeLabel() }}
                    </span>
                </div>
                <p class="text-gray-600">عرض تفاصيل المهمة والتسليمات</p>
            </div>
            <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors shrink-0">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">المكلف</p>
                <p class="font-semibold text-gray-900">{{ $task->assigner->name }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الأولوية</p>
                <span class="inline-block px-2 py-1 rounded-lg text-sm font-semibold
                    @if($task->priority === 'urgent') bg-red-100 text-red-800
                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    @if($task->priority === 'urgent') عاجل
                    @elseif($task->priority === 'high') عالي
                    @elseif($task->priority === 'medium') متوسط
                    @else منخفض
                    @endif
                </span>
            </div>
            @if($task->deadline)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الموعد النهائي</p>
                <p class="font-semibold {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $task->deadline->format('Y-m-d') }}
                </p>
            </div>
            @endif
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">التقدم</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[60px]">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $task->progress }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $task->progress }}%</span>
                </div>
            </div>
        </div>

        @if($task->description)
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $task->description }}</p>
        </div>
        @endif

        @php $ttDef = $task->taskTypeDefinition(); @endphp
        @if(!empty($ttDef['employee_hint'] ?? null))
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50/80 p-4">
            <p class="text-xs font-bold text-blue-900 mb-1">توجيه حسب نوع المهمة</p>
            <p class="text-sm text-blue-950 leading-relaxed">{{ $ttDef['employee_hint'] }}</p>
            <p class="text-xs text-blue-800/80 mt-2">كل ما ترفعه هنا يظهر لإدارة المنصة في صفحة المهمة.</p>
        </div>
        @endif

        <!-- تحديث الحالة -->
        <div class="mb-6 p-5 bg-slate-50 rounded-xl border border-slate-200">
            <h3 class="text-base font-semibold text-gray-900 mb-3">تحديث حالة المهمة</h3>
            <form action="{{ route('employee.tasks.update-status', $task) }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                @method('PUT')
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="on_hold" {{ $task->status == 'on_hold' ? 'selected' : '' }}>معلقة مؤقتاً</option>
                    </select>
                </div>
                <div class="w-24">
                    <label class="block text-sm font-medium text-gray-700 mb-1">التقدم %</label>
                    <input type="number" name="progress" value="{{ $task->progress }}" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث
                </button>
            </form>
        </div>

        <!-- زر التسليمات: عند النقر يفتح ويظهر كامل التسليمات -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <details class="group rounded-2xl border-2 border-slate-200 bg-white overflow-hidden" id="deliverables-section" {{ request()->has('open') ? 'open' : '' }}>
                <summary class="flex items-center justify-between gap-4 w-full cursor-pointer list-none px-6 py-4 bg-gradient-to-l from-slate-50 to-white hover:from-blue-50/50 hover:to-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-2xl">
                    <span class="flex items-center gap-3 font-bold text-gray-900 text-lg">
                        <span class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-inbox text-xl"></i>
                        </span>
                        <span>التسليمات</span>
                        @if($task->deliverables->count() > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                                {{ $task->deliverables->count() }}
                            </span>
                        @endif
                    </span>
                    <span class="flex items-center gap-2 text-gray-500 group-open:rotate-180 transition-transform">
                        <i class="fas fa-chevron-down"></i>
                        <span class="text-sm font-medium">عرض الكل</span>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 bg-slate-50/50 border-t border-slate-100">
            <!-- نموذج التسليم -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">إضافة تسليم جديد</h4>
                    <form action="{{ route('employee.tasks.submit-deliverable', $task) }}" method="POST" enctype="multipart/form-data" id="deliverableForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان التسليم *</label>
                                <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            @if($task->isVideoEditing())
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-violet-50 border border-violet-200">
                                <div>
                                    <label class="block text-sm font-medium text-violet-900 mb-2">ممن استُلم المصدر (فيديو)</label>
                                    <input type="text" name="received_from" class="w-full px-4 py-2.5 border border-violet-200 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="اسم أو جهة">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-violet-900 mb-2">المدة قبل المونتاج</label>
                                    <input type="text" name="duration_before" class="w-full px-4 py-2.5 border border-violet-200 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 12:30">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-violet-900 mb-2">المدة بعد المونتاج</label>
                                    <input type="text" name="duration_after" class="w-full px-4 py-2.5 border border-violet-200 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 10:00">
                                </div>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التسليم *</label>
                                <select name="delivery_type" id="delivery_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="file">ملف</option>
                                    <option value="image">صورة</option>
                                    <option value="link">رابط</option>
                                </select>
                            </div>
                            <div id="file_field">
                                <label class="block text-sm font-medium text-gray-700 mb-2" id="file_label">الملف *</label>
                                <input type="file" name="file" id="file_input" accept="" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1" id="file_hint">حدد ملف للتسليم</p>
                            </div>
                            <div id="link_field" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الرابط *</label>
                                <input type="url" name="link_url" id="link_input" placeholder="https://example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-upload mr-2"></i>تسليم المهمة
                            </button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('delivery_type').addEventListener('change', function() {
                            var type = this.value;
                            var fileField = document.getElementById('file_field');
                            var linkField = document.getElementById('link_field');
                            var fileInput = document.getElementById('file_input');
                            var linkInput = document.getElementById('link_input');
                            var fileLabel = document.getElementById('file_label');
                            var fileHint = document.getElementById('file_hint');
                            if (type === 'link') {
                                fileField.style.display = 'none';
                                linkField.style.display = 'block';
                                fileInput.removeAttribute('required');
                                linkInput.setAttribute('required', 'required');
                            } else {
                                fileField.style.display = 'block';
                                linkField.style.display = 'none';
                                fileInput.setAttribute('required', 'required');
                                linkInput.removeAttribute('required');
                                fileLabel.textContent = type === 'image' ? 'الصورة *' : 'الملف *';
                                fileInput.setAttribute('accept', type === 'image' ? 'image/*' : '');
                                fileHint.textContent = type === 'image' ? 'حدد صورة للتسليم' : 'حدد ملف للتسليم';
                            }
                        });
                    </script>
                </div>

            <!-- جميع التسليمات -->
            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-list text-slate-500"></i>
                جميع التسليمات
            </h4>
            @if($task->deliverables->count() > 0)
                <div class="space-y-4" id="task-deliverables-list">
                    @foreach($task->deliverables as $index => $deliverable)
                        <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition-colors bg-white shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1 space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">
                                            {{ $deliverable->title ?: ('تسليم ' . ($index + 1)) }}
                                        </h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($deliverable->status === 'approved') bg-green-100 text-green-800
                                            @elseif($deliverable->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($deliverable->status === 'submitted') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($deliverable->status === 'approved') معتمد
                                            @elseif($deliverable->status === 'rejected') مرفوض
                                            @elseif($deliverable->status === 'submitted') مقدم
                                            @else معلق
                                            @endif
                                        </span>
                                    </div>

                                    @if($deliverable->description)
                                        <p class="text-sm text-gray-600">{{ $deliverable->description }}</p>
                                    @endif

                                    @if($deliverable->received_from || $deliverable->duration_before || $deliverable->duration_after)
                                        <div class="text-sm text-violet-900 space-y-1 bg-violet-50/80 rounded-lg p-3 border border-violet-100">
                                            @if($deliverable->received_from)<p><span class="font-semibold">المصدر:</span> {{ $deliverable->received_from }}</p>@endif
                                            @if($deliverable->duration_before)<p><span class="font-semibold">مدة قبل:</span> {{ $deliverable->duration_before }}</p>@endif
                                            @if($deliverable->duration_after)<p><span class="font-semibold">مدة بعد:</span> {{ $deliverable->duration_after }}</p>@endif
                                        </div>
                                    @endif

                                    @if($deliverable->delivery_type === 'link' && $deliverable->link_url)
                                        <div class="flex flex-wrap items-center gap-2 text-sm">
                                            <i class="fas fa-link text-gray-500"></i>
                                            <a href="{{ $deliverable->link_url }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 font-medium break-all">
                                                {{ Str::limit($deliverable->link_url, 60) }}
                                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                                            </a>
                                        </div>
                                    @elseif($deliverable->file_name)
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <i class="fas fa-file text-gray-500"></i>
                                            <span>{{ $deliverable->file_name }}</span>
                                            @if($deliverable->file_path)
                                                <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download"></i> تحميل
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if($deliverable->feedback)
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                            <p class="text-xs font-semibold text-amber-800 mb-1">ملاحظات المراجع</p>
                                            <p class="text-sm text-gray-900">{{ $deliverable->feedback }}</p>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-400">{{ $deliverable->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium">لا توجد تسليمات حتى الآن</p>
                    <p class="text-sm text-gray-500 mt-1">التسليمات التي تقدمها من النموذج أعلاه ستظهر هنا</p>
                </div>
            @endif
                </div>
            </details>
        </div>
    </div>
</div>
@endsection
