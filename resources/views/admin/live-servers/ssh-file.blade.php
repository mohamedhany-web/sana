@extends('layouts.admin')
@section('title', 'عرض ملف — ' . basename($path))

@section('content')
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => dirname($path)]) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-xl font-bold text-slate-800"><i class="fas fa-file-code text-cyan-500 ml-2"></i>{{ basename($path) }}</h1>
                <p class="text-sm text-slate-500 font-mono break-all mt-0.5">{{ $path }}</p>
            </div>
        </div>
        <a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => dirname($path)]) }}" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-medium hover:bg-slate-300"><i class="fas fa-folder ml-1"></i> العودة للمجلد</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <pre class="p-4 text-sm text-slate-700 overflow-auto max-h-[70vh] font-mono whitespace-pre-wrap break-words">{{ e($content) }}</pre>
    </div>
</div>
@endsection
