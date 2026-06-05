<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * إعدادات عامة في جدول settings (مفاتيح نصية بسيطة).
 * مفتاح teacher_features = باقات اشتراك المدرب (JSON) — يُدار من InstructorSubscriptionPlansService.
 */
class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    public static function getValue(string $key): ?string
    {
        if ($key === 'teacher_features') {
            return null;
        }

        $v = static::query()->where('key', $key)->value('value');

        return $v !== null ? (string) $v : null;
    }

    public static function setValue(string $key, ?string $value): void
    {
        if ($key === 'teacher_features') {
            return;
        }

        $trimmed = $value !== null ? trim($value) : '';
        if ($trimmed === '') {
            static::query()->where('key', $key)->delete();

            return;
        }

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $trimmed]
        );
    }
}
