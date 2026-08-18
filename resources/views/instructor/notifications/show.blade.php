@extends('layouts.app')

@section('title', $notification->title)
@section('header', __('instructor.notifications'))

@section('content')
<div class="space-y-6 w-full max-w-3xl">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('instructor.notifications') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#283593] no-underline hover:underline">
            <i class="fas fa-arrow-right text-xs"></i>
            كل الإشعارات
        </a>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-start gap-3">
            <span class="w-11 h-11 rounded-xl bg-[#eef2ff] text-[#283593] flex items-center justify-center shrink-0">
                <i class="{{ $notification->type_icon }}"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-black text-slate-800 m-0">{{ $notification->title }}</h1>
                <p class="text-xs text-slate-500 mt-1 m-0">
                    {{ optional($notification->created_at)->diffForHumans() }}
                    · {{ $notification->sender->name ?? 'النظام' }}
                </p>
            </div>
        </div>
        <div class="px-5 py-5">
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap m-0">{{ $notification->message }}</p>
            @if($notification->action_url)
                <a href="{{ route('instructor.notifications.go', $notification) }}" class="inline-flex items-center gap-2 mt-5 px-4 py-2.5 rounded-xl bg-[#283593] hover:bg-[#1F2A7A] text-white text-sm font-bold no-underline">
                    {{ $notification->action_text ?: 'فتح الرابط' }}
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
