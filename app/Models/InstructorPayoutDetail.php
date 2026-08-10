<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorPayoutDetail extends Model
{
    public const METHOD_INSTAPAY = 'instapay';

    public const METHOD_IBAN = 'iban';

    public const METHOD_STC_PAY = 'stc_pay';

    protected $fillable = [
        'user_id',
        'payout_method',
        'bank_name',
        'account_holder_name',
        'account_number',
        'iban',
        'branch_name',
        'swift_code',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function methodLabels(): array
    {
        return [
            self::METHOD_INSTAPAY => 'InstaPay',
            self::METHOD_IBAN => 'IBAN',
            self::METHOD_STC_PAY => 'STC Pay',
        ];
    }

    public function methodLabel(): string
    {
        return self::methodLabels()[$this->payout_method] ?? ($this->payout_method ?: '—');
    }

    public function hasAnyDetails(): bool
    {
        return match ($this->payout_method) {
            self::METHOD_INSTAPAY => filled($this->account_number),
            self::METHOD_STC_PAY => filled($this->account_number),
            self::METHOD_IBAN => filled($this->iban),
            default => filled($this->iban) || filled($this->account_number),
        };
    }

    /** القيمة الأساسية للتحويل (رقم/آيبان) حسب الطريقة */
    public function primaryValue(): ?string
    {
        return match ($this->payout_method) {
            self::METHOD_IBAN => $this->iban,
            self::METHOD_INSTAPAY, self::METHOD_STC_PAY => $this->account_number,
            default => $this->iban ?: $this->account_number,
        };
    }

    public function primaryValueLabel(): string
    {
        return match ($this->payout_method) {
            self::METHOD_INSTAPAY => 'رقم / معرّف InstaPay',
            self::METHOD_STC_PAY => 'رقم STC Pay',
            self::METHOD_IBAN => 'IBAN',
            default => 'بيانات التحويل',
        };
    }
}
