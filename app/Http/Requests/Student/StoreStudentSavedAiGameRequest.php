<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;

class StoreStudentSavedAiGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->canAccessStudentAiUsages();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'storage_path' => ['required', 'string', 'max:512'],
            'title' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $path = (string) $this->input('storage_path');
            $uid = (int) $this->user()->id;
            $prefix = 'ai-games/student-'.$uid.'/';

            if (! str_starts_with($path, $prefix)) {
                $validator->errors()->add('storage_path', __('student.ai_usages.invalid_path'));

                return;
            }
            if (str_contains($path, '..') || str_contains($path, "\0")) {
                $validator->errors()->add('storage_path', __('student.ai_usages.invalid_path'));

                return;
            }
            if (! Storage::disk('public')->exists($path)) {
                $validator->errors()->add('storage_path', __('student.ai_usages.file_missing'));
            }
        });
    }
}
