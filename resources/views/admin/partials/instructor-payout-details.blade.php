@php
    /** @var \App\Models\InstructorPayoutDetail|null $payoutDetail */
    $payoutDetail = $payoutDetail ?? null;
@endphp
@if($payoutDetail && $payoutDetail->hasAnyDetails())
    <dl class="space-y-2 text-sm">
        <div class="flex flex-wrap items-center gap-2">
            <dt class="text-slate-500">الطريقة</dt>
            <dd>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                    {{ $payoutDetail->methodLabel() }}
                </span>
            </dd>
        </div>
        @if($payoutDetail->account_holder_name)
            <div>
                <dt class="text-slate-500">صاحب الحساب</dt>
                <dd class="font-semibold text-slate-900">{{ $payoutDetail->account_holder_name }}</dd>
            </div>
        @endif
        <div>
            <dt class="text-slate-500">{{ $payoutDetail->primaryValueLabel() }}</dt>
            <dd class="font-mono font-bold text-slate-900 tracking-wide" dir="ltr">{{ $payoutDetail->primaryValue() ?: '—' }}</dd>
        </div>
        @if($payoutDetail->payout_method === 'iban' && $payoutDetail->bank_name)
            <div>
                <dt class="text-slate-500">البنك</dt>
                <dd class="font-medium">{{ $payoutDetail->bank_name }}</dd>
            </div>
        @endif
        @if($payoutDetail->notes)
            <div>
                <dt class="text-slate-500">ملاحظات</dt>
                <dd class="text-slate-700">{{ $payoutDetail->notes }}</dd>
            </div>
        @endif
    </dl>
@else
    <p class="text-amber-700 bg-amber-50 border border-amber-200 p-4 rounded-xl text-sm m-0">
        المعلم لم يُضف بعد بيانات التحويل (InstaPay / IBAN / STC Pay).
    </p>
@endif
