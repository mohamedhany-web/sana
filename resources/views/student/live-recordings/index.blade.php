@extends('layouts.app')
@section('title', 'تسجيلات جلسات البث')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <i class="fas fa-play-circle text-emerald-500 ml-2"></i>تسجيلات جلسات البث
        </h1>
        <p class="text-sm text-slate-500 mt-1">مشاهدة تسجيلات الجلسات المنتهية (مخزنة على R2)</p>
    </div>

    @if($recordings->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <i class="fas fa-film text-5xl text-slate-300 mb-4"></i>
        <p class="text-lg font-semibold text-slate-600">لا توجد تسجيلات متاحة حالياً</p>
        <p class="text-sm text-slate-500 mt-1">ستظهر هنا تسجيلات الجلسات بعد انتهائها ونشرها من الإدارة</p>
        <a href="{{ route('student.live-sessions.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium">عودة لجلسات البث</a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($recordings as $rec)
        <a href="{{ route('student.live-recordings.show', $rec) }}" class="block bg-white rounded-xl border border-slate-200 p-5 hover:border-emerald-300 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fas fa-play text-lg"></i>
            </div>
            <h3 class="font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $rec->title ?? 'تسجيل #' . $rec->id }}</h3>
            <p class="text-sm text-slate-500 mt-1">{{ $rec->session?->title }}</p>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
                <span><i class="fas fa-clock ml-1"></i> {{ $rec->duration_for_humans }}</span>
                <span><i class="fas fa-hdd ml-1"></i> {{ $rec->file_size_for_humans }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @if($recordings->hasPages())
    <div class="mt-6">{{ $recordings->links() }}</div>
    @endif
    @endif
</div>
@endsection
