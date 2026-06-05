@extends('layouts.admin')
@section('title', 'طلب '.$assisted->code)
@section('header', 'طلب مساعدة')
@section('content')
<div class="space-y-6 max-w-xl">
    @include('admin.tutor-lessons._nav')
<div class="bg-white rounded-xl border p-6 space-y-4">
    <p><strong>الطالب:</strong> {{ $assisted->student?->name }}</p>
    <p><strong>ولي الأمر:</strong> {{ $assisted->parent?->name ?? '—' }}</p>
    <p class="text-sm">{{ $assisted->message }}</p>
    <form method="post" action="{{ route('admin.tutor-lessons.assisted.assign', $assisted) }}" class="space-y-3">
        @csrf
        <div><label class="block text-sm font-bold mb-1">المعلم</label>
            <select name="assigned_instructor_id" class="w-full border rounded-lg p-2" required>
                @foreach($instructors as $ip)<option value="{{ $ip->user_id }}">{{ $ip->user?->name }}</option>@endforeach
            </select>
        </div>
        <div><label class="block text-sm font-bold mb-1">الموعد</label><input type="datetime-local" name="scheduled_at" class="w-full border rounded-lg p-2" required></div>
        <button class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">تعيين وإنشاء حجز</button>
    </form>
</div>
</div>
@endsection
