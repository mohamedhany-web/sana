@extends('layouts.admin')
@section('title', 'إعدادات نظام البث المباشر')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800"><i class="fas fa-sliders-h text-violet-500 ml-2"></i>إعدادات نظام البث المباشر</h1>
        <p class="text-sm text-slate-500 mt-1">تكوين LiveKit والبث العامة على live.sanaedu.com</p>
    </div>

    <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-video text-cyan-600 text-xl mt-0.5"></i>
        <div class="text-sm text-cyan-900 leading-relaxed">
            <p class="font-bold mb-1">المزوّد الحالي: LiveKit</p>
            <p>عنوان WebSocket: <code class="bg-cyan-100 px-1.5 py-0.5 rounded text-xs" dir="ltr">{{ config('livekit.url') }}</code></p>
            <p class="mt-1">النطاق العام: <code class="bg-cyan-100 px-1.5 py-0.5 rounded text-xs" dir="ltr">{{ \App\Models\LiveSetting::getLiveKitHost() }}</code></p>
            <p class="mt-2 text-cyan-800">مفاتيح API تُضبط من ملف <code class="text-xs bg-cyan-100 px-1 rounded">.env</code> (<code class="text-xs">LIVEKIT_API_KEY</code> / <code class="text-xs">LIVEKIT_API_SECRET</code>) وليست من هذه الصفحة.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
        <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.live-settings.update') }}" class="space-y-6">
        @csrf

        @php $index = 0; @endphp
        @foreach($settings as $group => $items)
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                @if($group === 'general')
                    <i class="fas fa-cog text-slate-400"></i> إعدادات عامة
                @elseif($group === 'jitsi')
                    <i class="fas fa-video text-cyan-500"></i> إعدادات LiveKit (مفاتيح قديمة متوافقة)
                @elseif($group === 'access')
                    <i class="fas fa-lock text-amber-400"></i> صلاحيات الدخول
                @elseif($group === 'room')
                    <i class="fas fa-door-open text-emerald-400"></i> إعدادات الغرفة
                @else
                    <i class="fas fa-cog text-slate-400"></i> {{ $group }}
                @endif
            </h2>
            @if($group === 'jitsi')
            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 leading-relaxed">
                <p class="font-semibold text-slate-800 mb-2"><i class="fas fa-info-circle text-cyan-500 ml-1"></i> ملاحظة التوافق</p>
                <p>حقل <strong>jitsi_domain</strong> إن وُجد يُستخدم كنطاق احتياطي لـ LiveKit عند غياب <code class="text-xs bg-slate-200 px-1 rounded">LIVEKIT_PUBLIC_URL</code>. القيمة الموصى بها: <code class="text-xs bg-slate-200 px-1 rounded" dir="ltr">live.sanaedu.com</code>.</p>
            </div>
            @endif
            <div class="space-y-4">
                @foreach($items as $setting)
                <div class="flex items-center justify-between gap-4">
                    <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting->key }}">
                    <label class="text-sm font-medium text-slate-700 flex-1">
                        @if($setting->key === 'jitsi_domain')
                            نطاق LiveKit (أو jitsi_domain للتوافق)
                        @else
                            {{ $setting->label ?? $setting->key }}
                        @endif
                    </label>
                    @if($setting->type === 'boolean')
                        <select name="settings[{{ $index }}][value]" class="w-28 rounded-lg border-slate-300 text-sm">
                            <option value="1" {{ $setting->value ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ !$setting->value ? 'selected' : '' }}>لا</option>
                        </select>
                    @elseif($setting->type === 'integer')
                        <input type="number" name="settings[{{ $index }}][value]" value="{{ $setting->value }}" class="w-32 rounded-lg border-slate-300 text-sm">
                    @else
                        <input type="text" name="settings[{{ $index }}][value]" value="{{ $setting->value }}" class="w-64 rounded-lg border-slate-300 text-sm" placeholder="live.sanaedu.com" dir="ltr">
                    @endif
                </div>
                @php $index++; @endphp
                @endforeach
            </div>
        </div>
        @endforeach

        <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
            <i class="fas fa-save ml-1"></i> حفظ الإعدادات
        </button>
    </form>
</div>
@endsection
