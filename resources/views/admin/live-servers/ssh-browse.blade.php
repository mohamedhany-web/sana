@extends('layouts.admin')
@section('title', 'تصفح الملفات — ' . $server->name)

@section('content')
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.live-servers.index') }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-xl font-bold text-slate-800"><i class="fas fa-folder-open text-amber-500 ml-2"></i>تصفح الملفات — {{ $server->name }}</h1>
                <nav class="text-sm text-slate-500 mt-0.5 font-mono break-all">
                    @php $parts = array_filter(explode('/', $path)); $current = ''; @endphp
                    <a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => '/']) }}" class="text-cyan-500 hover:underline">/</a>
                    @foreach($parts as $part)
                        @php $current .= '/' . $part; @endphp
                        <span class="text-slate-400">/</span><a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => $current]) }}" class="text-cyan-500 hover:underline">{{ $part }}</a>
                    @endforeach
                </nav>
            </div>
        </div>
        <a href="{{ route('admin.live-servers.edit', $server) }}" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-medium hover:bg-slate-300"><i class="fas fa-cog ml-1"></i> إعدادات السيرفر</a>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> {{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600 w-24">الحجم</th>
                </tr>
            </thead>
            <tbody>
                @if($path !== '/')
                @php $parent = dirname($path); if ($parent === '.') $parent = '/'; @endphp
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-4" colspan="2">
                        <a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => $parent]) }}" class="flex items-center gap-2 text-cyan-500 hover:underline">
                            <i class="fas fa-level-up-alt"></i> المجلد الأعلى
                        </a>
                    </td>
                </tr>
                @endif
                @foreach($dirs as $dir)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-4">
                        <a href="{{ route('admin.live-servers.ssh-browse', [$server, 'path' => $dir['path']]) }}" class="flex items-center gap-2 text-slate-800 hover:text-cyan-500">
                            <i class="fas fa-folder text-amber-500"></i> {{ $dir['name'] }}
                        </a>
                    </td>
                    <td class="py-2 px-4 text-slate-500">—</td>
                </tr>
                @endforeach
                @foreach($files as $file)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-4">
                        <a href="{{ route('admin.live-servers.ssh-file', [$server, 'path' => $file['path']]) }}" class="flex items-center gap-2 text-slate-800 hover:text-cyan-500">
                            <i class="fas fa-file text-slate-400"></i> {{ $file['name'] }}
                        </a>
                    </td>
                    <td class="py-2 px-4 text-slate-500 font-mono text-xs">{{ number_format($file['size']) }} بايت</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(empty($dirs) && empty($files) && $path === '/')
        <p class="py-8 text-center text-slate-500">المجلد فارغ أو لا يمكن قراءته.</p>
        @endif
    </div>
</div>
@endsection
