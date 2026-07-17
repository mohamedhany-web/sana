<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutorFormField;
use App\Models\TutorFormStep;
use App\Services\TutorFormSchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TutorFormBuilderController extends Controller
{
    public function index()
    {
        $steps = TutorFormStep::query()
            ->ordered()
            ->with(['fields' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->get();

        return view('admin.tutor-form-builder.index', [
            'steps' => $steps,
            'typeOptions' => TutorFormSchemaService::typeOptionsForAdmin(),
            'enabled' => TutorFormSchemaService::isEnabled(),
        ]);
    }

    public function storeStep(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'step_type' => ['required', Rule::in(['intro', 'fields', 'review'])],
        ]);

        $maxOrder = (int) TutorFormStep::query()->max('sort_order');
        $slug = Str::slug($data['title'], '_');
        if ($slug === '' || TutorFormStep::where('slug', $slug)->exists()) {
            $slug = 'step_'.Str::lower(Str::random(8));
        }

        TutorFormStep::create([
            'slug' => $slug,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'step_type' => $data['step_type'],
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
            'is_system' => false,
        ]);

        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تمت إضافة الخطوة.');
    }

    public function updateStep(Request $request, TutorFormStep $step)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
            'step_type' => ['required', Rule::in(['intro', 'fields', 'review'])],
        ]);

        $step->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'step_type' => $data['step_type'],
        ]);

        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم تحديث الخطوة.');
    }

    public function destroyStep(TutorFormStep $step)
    {
        if ($step->is_system) {
            return back()->with('error', 'لا يمكن حذف خطوة نظامية — يمكنك إيقافها فقط.');
        }

        $step->delete();
        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم حذف الخطوة.');
    }

    public function storeField(Request $request)
    {
        $data = $this->validateField($request);

        $key = Str::slug($data['field_key'] ?: $data['label'], '_');
        $key = preg_replace('/[^a-z0-9_]/', '', strtolower($key)) ?: 'field_'.Str::lower(Str::random(6));
        if (TutorFormField::where('field_key', $key)->exists()) {
            $key .= '_'.Str::lower(Str::random(4));
        }

        $maxOrder = (int) TutorFormField::where('step_id', $data['step_id'])->max('sort_order');

        TutorFormField::create([
            'step_id' => $data['step_id'],
            'field_key' => $key,
            'label' => $data['label'],
            'help_text' => $data['help_text'] ?? null,
            'placeholder' => $data['placeholder'] ?? null,
            'field_type' => $data['field_type'],
            'is_required' => $request->boolean('is_required'),
            'is_active' => true,
            'is_system' => false,
            'sort_order' => $maxOrder + 1,
            'width' => $data['width'],
            'options' => $this->parseOptions($data),
            'settings' => $this->parseSettings($data),
        ]);

        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تمت إضافة الحقل.');
    }

    public function updateField(Request $request, TutorFormField $field)
    {
        $data = $this->validateField($request, $field);

        $payload = [
            'step_id' => $data['step_id'],
            'label' => $data['label'],
            'help_text' => $data['help_text'] ?? null,
            'placeholder' => $data['placeholder'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? $field->sort_order,
            'width' => $data['width'],
            'options' => $this->parseOptions($data, $field),
            'settings' => $this->parseSettings($data, $field),
        ];

        if (! $field->is_system) {
            $payload['field_type'] = $data['field_type'];
        }

        // حقول الحساب الأساسية لا تُجعل اختيارية
        if (in_array($field->field_key, ['name', 'email', 'password'], true)) {
            $payload['is_required'] = true;
            $payload['is_active'] = true;
        }

        $field->update($payload);
        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم تحديث الحقل.');
    }

    public function destroyField(TutorFormField $field)
    {
        if ($field->is_system) {
            return back()->with('error', 'لا يمكن حذف حقل نظامي — يمكنك إيقافه أو جعله اختيارياً.');
        }

        $field->delete();
        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم حذف الحقل.');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'steps' => ['nullable', 'array'],
            'steps.*' => ['integer', 'exists:tutor_form_steps,id'],
            'fields' => ['nullable', 'array'],
            'fields.*' => ['integer', 'exists:tutor_form_fields,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['steps'] ?? [] as $i => $id) {
                TutorFormStep::whereKey($id)->update(['sort_order' => $i + 1]);
            }
            foreach ($data['fields'] ?? [] as $i => $id) {
                TutorFormField::whereKey($id)->update(['sort_order' => $i + 1]);
            }
        });

        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم حفظ الترتيب.');
    }

    public function seedDefaults()
    {
        if (TutorFormStep::query()->exists()) {
            return back()->with('info', 'المخطط موجود بالفعل. احذف الخطوات أولاً إن أردت إعادة الزرع.');
        }

        (new \Database\Seeders\TutorFormBuilderSeeder)->run();
        TutorFormSchemaService::clearCache();

        return back()->with('success', 'تم زرع هيكل النموذج الافتراضي.');
    }

    private function validateField(Request $request, ?TutorFormField $field = null): array
    {
        return $request->validate([
            'step_id' => ['required', 'integer', 'exists:tutor_form_steps,id'],
            'label' => ['required', 'string', 'max:255'],
            'field_key' => ['nullable', 'string', 'max:80'],
            'help_text' => ['nullable', 'string', 'max:500'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'field_type' => ['required', Rule::in(TutorFormField::TYPES)],
            'width' => ['required', Rule::in(['full', 'half'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'options_text' => ['nullable', 'string', 'max:5000'],
            'options_source' => ['nullable', 'string', 'max:80'],
            'settings_max' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'settings_min' => ['nullable', 'integer'],
            'settings_rows' => ['nullable', 'integer', 'min:1', 'max:20'],
            'settings_accept' => ['nullable', 'string', 'max:200'],
            'settings_mimes' => ['nullable', 'string', 'max:200'],
            'is_required' => ['nullable'],
            'is_active' => ['nullable'],
        ]);
    }

    private function parseOptions(array $data, ?TutorFormField $field = null): ?array
    {
        if (! empty($data['options_source'])) {
            return ['source' => $data['options_source']];
        }

        $text = trim((string) ($data['options_text'] ?? ''));
        if ($text === '') {
            return $field?->options;
        }

        $items = [];
        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, '|')) {
                [$value, $label] = array_map('trim', explode('|', $line, 2));
            } else {
                $value = Str::slug($line, '_');
                $label = $line;
            }
            if ($value === '') {
                $value = 'opt_'.count($items);
            }
            $items[] = ['value' => $value, 'label' => $label];
        }

        return $items !== [] ? ['items' => $items] : ($field?->options);
    }

    private function parseSettings(array $data, ?TutorFormField $field = null): ?array
    {
        $settings = $field?->settings ?? [];
        foreach (['max' => 'settings_max', 'min' => 'settings_min', 'rows' => 'settings_rows', 'accept' => 'settings_accept', 'mimes' => 'settings_mimes'] as $k => $input) {
            if (array_key_exists($input, $data) && $data[$input] !== null && $data[$input] !== '') {
                $settings[$k] = $data[$input];
            }
        }

        return $settings !== [] ? $settings : null;
    }
}
