@extends('layouts.employee')

@section('title', __('الإشراف الأكاديمي'))
@section('header', __('الإشراف الأكاديمي'))

@section('content')
<div class="space-y-6">
    <p class="text-sm text-gray-600">{{ __('متابعة الطلاب المعيّنين لك: آخر ظهور، الاشتراك، الميتينج النشط، والكورسات.') }}</p>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">{{ __('طلابي') }} ({{ $students->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">{{ __('الطالب') }}</th>
                        <th class="text-right px-4 py-3">{{ __('آخر ظهور') }}</th>
                        <th class="text-right px-4 py-3">{{ __('ميتينج الآن') }}</th>
                        <th class="text-right px-4 py-3 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $row)
                        @php
                            $live = $liveMeetings->get($row->id);
                        @endphp
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $row->name }}</p>
                                <p class="text-xs text-gray-500">{{ $row->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                {{ $row->last_login_at ? $row->last_login_at->diffForHumans() : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($live)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-2 py-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
 {{ __('لايف —') }} {{ $live->participants_count }} في الغرفة
                                    </span>
                                @else
                                    <span class="text-gray-400">{{ __('لا يوجد') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('employee.academic-supervision.show', $row) }}" class="text-teal-700 font-semibold hover:underline">{{ __('التفاصيل') }}</a>
                                @if($live)
                                    <a href="{{ route('employee.academic-supervision.meeting.observe', $live) }}" class="block text-xs text-cyan-600 font-semibold mt-1 hover:underline">{{ __('دخول المراقبة') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">{{ __('لم يُعيَّن لك طلاب بعد. تواصل مع الإدارة لربط الطلاب بحسابك.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
