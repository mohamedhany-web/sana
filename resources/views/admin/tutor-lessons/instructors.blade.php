@extends('layouts.admin')
@section('title', 'معلمو الحصص')
@section('header', 'معلمو الحصص')
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3 text-right">المعلم</th><th class="p-3">الحالة</th><th class="p-3">تفعيل</th></tr></thead>
            <tbody>
            @foreach($profiles as $p)
                <tr class="border-t">
                    <td class="p-3 font-semibold">{{ $p->user?->name }}</td>
                    <td class="p-3">{{ $p->isTutorActivated() ? 'مفعّل' : 'قيد التفعيل' }}</td>
                    <td class="p-3">
                        @if(!$p->isTutorActivated())
                        <form method="post" action="{{ route('admin.tutor-lessons.instructors.activate', $p) }}">@csrf<button class="text-blue-700 font-bold">تفعيل</button></form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $profiles->links() }}
    </div>
</div>
@endsection

