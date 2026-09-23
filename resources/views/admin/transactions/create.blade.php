@extends('layouts.admin')

@section('title', __('إضافة معاملة جديدة'))
@section('header', __('إضافة معاملة جديدة'))

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة معاملة جديدة</h1>
        
        <form action="{{ route('admin.transactions.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <label for="transaction-client-search" class="sr-only">بحث عن عميل بالاسم أو البريد</label>
                    <input type="search" id="transaction-client-search" autocomplete="off" placeholder="{{ __('بحث بالاسم أو البريد أو الجوال…') }}"
                           class="w-full mb-2 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <select id="transaction-user-id" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر العميل</option>
                        @foreach($users as $user)
                        @php
                            $searchHaystack = mb_strtolower(
                                trim($user->name.' '.($user->email ?? '').' '.($user->phone ?? '')),
                                'UTF-8'
                            );
                        @endphp
                        <option value="{{ $user->id }}" data-search="{{ e($searchHaystack) }}">{{ $user->name }} — {{ $user->email }} @if($user->phone) · {{ $user->phone }} @endif</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النوع *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="deposit">إيداع</option>
                        <option value="withdrawal">سحب</option>
                        <option value="payment">دفع</option>
                        <option value="refund">استرداد</option>
                        <option value="commission">عمولة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending">معلقة</option>
                        <option value="completed" selected>مكتملة</option>
                        <option value="failed">فاشلة</option>
                        <option value="cancelled">ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إنشاء المعاملة
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
(function () {
    var searchInput = document.getElementById('transaction-client-search');
    var select = document.getElementById('transaction-user-id');
    if (!searchInput || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
        var q = (searchInput.value || '').trim().toLowerCase();
        options.forEach(function (opt) {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }
            if (opt.selected) {
                opt.hidden = false;
                return;
            }
            var hay = (opt.getAttribute('data-search') || '').toLowerCase();
            opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
        });
    }
    searchInput.addEventListener('input', applyFilter);
    searchInput.addEventListener('search', applyFilter);
    select.addEventListener('change', applyFilter);
})();
</script>
@endpush
@endsection

