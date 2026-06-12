@php
    $eval = $application->application_evaluation ?? [];
    $criteria = config('tutor_application.evaluation_criteria', []);
    $decisions = config('tutor_application.evaluation_decisions', []);
@endphp

<section class="rounded-3xl bg-white border border-indigo-200 shadow-lg p-6 sm:p-8">
    <h3 class="text-lg font-bold text-indigo-900 mb-1">١٣. تقييم فريق التوظيف</h3>
    <p class="text-xs text-slate-500 mb-4">للاستخدام الإداري فقط — لا يظهر للمتقدم</p>

    <form method="post" action="{{ route('admin.instructor-applications.evaluation', $application) }}" data-turbo="false" class="space-y-4">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-2 text-right">المعيار</th>
                        @foreach([1 => 'ضعيف', 2 => 'مقبول', 3 => 'جيد', 4 => 'ممتاز'] as $n => $lbl)
                            <th class="p-2 text-center text-xs">{{ $n }}<br>{{ $lbl }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($criteria as $key => $label)
                    <tr class="border-t border-slate-50">
                        <td class="p-2 font-medium">{{ $label }}</td>
                        @for($n = 1; $n <= 4; $n++)
                        <td class="p-2 text-center">
                            <input type="radio" name="scores[{{ $key }}]" value="{{ $n }}" @checked((int)($eval['scores'][$key] ?? 0) === $n)>
                        </td>
                        @endfor
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">قرار مبدئي</label>
                <select name="decision" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach($decisions as $val => $lbl)
                        <option value="{{ $val }}" @selected(($eval['decision'] ?? '') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">اسم المقيم</label>
                <input type="text" name="reviewer_name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       value="{{ old('reviewer_name', $eval['reviewer_name'] ?? auth()->user()?->name) }}">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('notes', $eval['notes'] ?? '') }}</textarea>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
            <i class="fas fa-save"></i> حفظ التقييم
        </button>
    </form>
</section>
