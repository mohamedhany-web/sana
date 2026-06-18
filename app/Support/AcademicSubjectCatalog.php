<?php

namespace App\Support;

use App\Models\AcademicSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AcademicSubjectCatalog
{
    /** @return Builder<AcademicSubject> */
    public static function publicQuery(): Builder
    {
        return AcademicSubject::query()->forCatalog();
    }

    /** @return Collection<int, AcademicSubject> */
    public static function allActive(): Collection
    {
        return self::publicQuery()
            ->with('academicYear:id,name')
            ->get();
    }

    /** @return Collection<int, AcademicSubject> */
    public static function forYear(int $academicYearId): Collection
    {
        return self::publicQuery()
            ->forYear($academicYearId)
            ->get();
    }

    /**
     * @param  array<int|string>  $subjectIds
     * @return array<int, int>
     */
    public static function assertActiveSubjectIds(array $subjectIds, ?int $academicYearId = null): array
    {
        $ids = collect($subjectIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            throw ValidationException::withMessages([
                'subject_ids' => 'اختر مادة واحدة على الأقل من قائمة المواد النشطة.',
            ]);
        }

        $query = AcademicSubject::query()->active()->whereIn('id', $ids);
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $valid = $query->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (count($valid) !== count($ids)) {
            throw ValidationException::withMessages([
                'subject_ids' => 'بعض المواد المختارة غير نشطة أو لا تتبع المرحلة المحددة — راجع قائمة المواد في لوحة الإدارة.',
            ]);
        }

        return $valid;
    }
}
