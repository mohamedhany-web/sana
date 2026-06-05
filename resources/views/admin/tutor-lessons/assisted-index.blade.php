@extends('layouts.admin')
@section('title', 'طلبات المساعدة')
@section('header', 'طلبات المساعدة في إيجاد معلم')
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')
<div class="space-y-3">
    @foreach($requests as $r)
        <a href="{{ route('admin.tutor-lessons.assisted.show', $r) }}" class="block bg-white border rounded-xl p-4 hover:border-blue-300">
            <strong>{{ $r->code }}</strong> — {{ $r->student?->name }} <span class="text-slate-500 text-sm">{{ $r->statusLabel() }}</span>
        </a>
    @endforeach
    {{ $requests->links() }}
</div>
</div>
@endsection
