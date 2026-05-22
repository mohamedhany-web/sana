@extends('layouts.admin')

@section('title', '???????? ??????? - Sana')
@section('header', '???????? ???????')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('???????? ??????? ??????') }}</h1>
                <p class="text-gray-600">{{ __('?????? ???????? ??????? ??????? ?????? ??????? ?????? ??? ???? Sana') }}</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="showGenerateModal()" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('????? ?????? ?????') }}
                </button>
                <a href="{{ route('admin.messages.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    {{ __('??????') }}
                </a>
            </div>
        </div>
    </div>

    <!-- ???????? ???????? -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $stats['total_reports'] }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('?????? ????????') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $stats['this_month'] }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('?????? ??? ?????') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $stats['pending'] }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('?? ????????') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-check text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $stats['sent'] }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ __('?? ???????') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ????? ???????? -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ __('??? ???????? ???????') }}
            </h3>
        </div>

        @if($reports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('??????') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('??? ?????') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('?????') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('??????') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('????? ???????') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('?????????') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 font-medium">
                                                {{ substr($report->student->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $report->student->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $report->student->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->parent)
                                        <div class="text-sm text-gray-900">{{ $report->parent->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $report->parent->phone }}</div>
                                    @else
                                        <span class="text-sm text-gray-500">{{ __('??? ????') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $report->month_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($report->status === 'sent') bg-green-100 text-green-800
                                        @elseif($report->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $report->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $report->sent_at ? $report->sent_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1 space-x-reverse">
                                        <button onclick="viewReport({{ $report->id }})" 
                                                class="text-blue-600 hover:text-blue-800 p-1"
                                                title="{{ __('??? ?????? ???????') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4">
                {{ $reports->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ __('?? ???? ??????') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('???? ?????? ???????? ???????') }}
                </p>
                <button onclick="showGenerateModal()" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('????? ??????') }}
                </button>
            </div>
        @endif
    </div>
</div>

<!-- ????? ????? ???????? -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ __('????? ?????? ?????') }}
                    </h3>
                    <button onclick="hideGenerateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.messages.generate-monthly-reports') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('?????') }}
                        </label>
                        <input type="month" name="month" value="{{ now()->subMonth()->format('Y-m') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="send_to_parents" value="1" checked
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="mr-2 text-sm text-gray-700">
                                {{ __('????? ??????? ?????? ?????') }}
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('???? ????? ???????? ?????? ??????? ?????? ??? ????? ????????? ???????? ?? ??????') }}
                        </p>
                    </div>

                    <div class="flex space-x-2 space-x-reverse">
                        <button type="submit" 
                                class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            {{ __('????? ??????') }}
                        </button>
                        <button type="button" onclick="hideGenerateModal()" 
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
function showGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function hideGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function viewReport(reportId) {
    // ???? ????? ????? ???? ?????? ???????
    console.log('View report:', reportId);
}

// ???? ?????? ????? ????? ????? ??????? ??? ??????
</script>
@endpush
@endsection
