@extends('layouts.admin')

@section('title', '????? ??????? - Sana')
@section('header', '????? ???????')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('????? ???????') }}</h1>
                <p class="text-gray-600">{{ __('????? ????? ??????? ????????? ???? ???? Sana') }}</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="showCreateTemplateModal()" 
                        class="text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors"
                        style="background-color:#16a34a;">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('???? ????') }}
                </button>
                <a href="{{ route('admin.messages.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('??????') }}
                </a>
            </div>
        </div>
    </div>

    <!-- ??????? ??????? -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ __('??????? ???????') }}
            </h3>
        </div>

        @if($templates->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($templates as $template)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        {{ $template->title }}
                                    </h4>
                                    <span class="mr-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $template->is_active ? __('???') : __('????') }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-3">
                                    {{ Str::limit($template->content, 150) }}
                                </p>
                                
                                <div class="flex items-center text-xs text-gray-500">
                                    <span>{{ __('?????') }}: {{ $template->type }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ __('??????') }}: {{ $template->creator->name }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $template->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex space-x-1 space-x-reverse">
                                <button onclick="editTemplate({{ $template->id }})" 
                                        class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="useTemplate('{{ addslashes($template->content) }}', '{{ $template->type }}', '{{ $template->title }}')"
                                        class="text-green-600 hover:text-green-800 p-2">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <form action="{{ route('admin.messages.templates.destroy', $template) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2"
                                            onclick="return confirm('{{ __('?? ???? ??? ??? ???????') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <div class="text-gray-400 text-4xl mb-4">
                    <i class="fas fa-file-alt"></i>
                </div>
                <p class="text-gray-600">
                    {{ __('?? ???? ????? ?????') }}
                </p>
            </div>
        @endif
    </div>
</div>

<!-- ????? ????? ???? -->
<div id="createTemplateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ __('????? ???? ????') }}
                    </h3>
                    <button onclick="hideCreateTemplateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.messages.templates.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('??? ??????') }}
                        </label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900"
                               placeholder="{{ __('????: ?????_????_????') }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('????? ??????') }}
                        </label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900"
                               placeholder="{{ __('????: ????? ???? ????') }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('??? ??????') }}
                        </label>
                        <select name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                            <option value="">{{ __('???? ?????...') }}</option>
                            <option value="student_report">{{ __('????? ????') }}</option>
                            <option value="exam_result">{{ __('????? ??????') }}</option>
                            <option value="general_announcement">{{ __('????? ???') }}</option>
                            <option value="parent_report">{{ __('????? ???? ?????') }}</option>
                            <option value="course_reminder">{{ __('????? ???????') }}</option>
                            <option value="welcome_message">{{ __('????? ?????') }}</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('????? ??????') }}
                        </label>
                        <textarea name="content" rows="8" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="{{ __('???? ????? ??????... ????? ??????? ????????? ??? {student_name}') }}"></textarea>
                        <div class="mt-2 text-xs text-gray-500">
                            {{ __('????? ??? ????????? ??????: {student_name}, {courses_count}, {avg_score}, {month_name}, {date}') }}
                        </div>
                    </div>

                    <div class="flex space-x-2 space-x-reverse">
                        <button type="submit"
                                class="flex-1 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors"
                                style="background-color:#16a34a;">
                            {{ __('????? ??????') }}
                        </button>
                        <button type="button" onclick="hideCreateTemplateModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('?????') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showCreateTemplateModal() {
    document.getElementById('createTemplateModal').classList.remove('hidden');
}

function hideCreateTemplateModal() {
    document.getElementById('createTemplateModal').classList.add('hidden');
}

function useTemplate(content, type, title) {
    // ????? ????? ????? ????? ????? ?? ??????
    const params = new URLSearchParams({
        template_content: content,
        template_type: type,
        template_title: title
    });
    
    window.location.href = '{{ route("admin.messages.create") }}?' + params.toString();
}

function editTemplate(templateId) {
    // ???? ????? ????? ????? ??????
    console.log('Edit template:', templateId);
}
</script>
@endpush
@endsection
