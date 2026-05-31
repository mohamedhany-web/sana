<?php

namespace App\Services\Parent;

use App\Models\ParentStudent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ParentGuardianService
{
    /**
     * إنشاء/ربط ولي أمر تلقائياً عند تسجيل الطالب (بدون بريد منفصل).
     * ولي الأمر يسجّل الدخول بنفس بريد الطالب + اختيار «ولي أمر» في صفحة الدخول.
     */
    public function ensureGuardianForStudent(User $student, string $relation = 'guardian'): User
    {
        $relation = in_array($relation, ['father', 'mother', 'guardian'], true) ? $relation : 'guardian';

        $existing = $student->guardians()->first();
        if ($existing) {
            return $existing;
        }

        if ($student->parent_id && ($legacy = User::find($student->parent_id)) && $legacy->isParent()) {
            $this->attachPivotIfMissing($legacy, $student, $relation);

            return $legacy;
        }

        $parent = User::create([
            'name' => $this->defaultParentName($student, $relation),
            'email' => $this->internalParentEmail($student),
            'phone' => $this->uniquePlaceholderPhone('student_'.$student->id),
            'password' => Hash::make(config('parent.default_password')),
            'role' => 'parent',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $this->attachPivotIfMissing($parent, $student, $relation, true);

        return $parent;
    }

    /**
     * عند الدخول كولي أمر: البحث بالبريد المُدخل (بريد الطالب).
     */
    public function resolveParentByStudentEmail(string $email): ?User
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $student = User::query()
            ->where('role', 'student')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $student) {
            return null;
        }

        $parent = $student->guardians()->first();
        if ($parent) {
            return $parent;
        }

        if ($student->parent_id && ($legacy = User::find($student->parent_id)) && $legacy->isParent()) {
            return $legacy;
        }

        return $this->ensureGuardianForStudent($student);
    }

    public function usesDefaultPassword(User $parent): bool
    {
        if (! $parent->isParent()) {
            return false;
        }

        return Hash::check(config('parent.default_password'), (string) $parent->password);
    }

    private function attachPivotIfMissing(User $parent, User $student, string $relation, bool $forcePrimary = false): void
    {
        $exists = ParentStudent::query()
            ->where('parent_id', $parent->id)
            ->where('student_id', $student->id)
            ->exists();

        if (! $exists) {
            $isPrimary = $forcePrimary || ! ParentStudent::query()
                ->where('student_id', $student->id)
                ->where('is_primary', true)
                ->exists();

            ParentStudent::create([
                'parent_id' => $parent->id,
                'student_id' => $student->id,
                'relation' => $relation,
                'is_primary' => $isPrimary,
            ]);
        }

        if (! $student->parent_id) {
            $student->update(['parent_id' => $parent->id]);
        }
    }

    private function internalParentEmail(User $student): string
    {
        $host = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'sana.local';
        $token = substr(md5(strtolower((string) $student->email).$student->id), 0, 10);

        return 'parent.'.$student->id.'.'.$token.'@login.'.$host;
    }

    private function defaultParentName(User $student, string $relation): string
    {
        $label = match ($relation) {
            'father' => 'ولي الأمر (الأب)',
            'mother' => 'ولي الأمر (الأم)',
            default => 'ولي أمر',
        };

        return $label.' — '.$student->name;
    }

    private function uniquePlaceholderPhone(string $seed): string
    {
        $base = 'PARENT_'.substr(md5($seed), 0, 14);
        $phone = $base;
        $i = 0;

        while (User::where('phone', $phone)->exists()) {
            $phone = $base.'_'.(++ $i);
        }

        return $phone;
    }
}
