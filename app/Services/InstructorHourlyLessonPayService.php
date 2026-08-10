<?php

namespace App\Services;

use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\LessonBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * عند اكتمال حصة مع طالب: إنشاء مستحق للمعلم من اتفاقية «سعر بالساعة»
 * حسب دقائق الميتينج الفعلية (billable_minutes).
 */
class InstructorHourlyLessonPayService
{
    public static function processLessonCompletion(LessonBooking $booking): ?AgreementPayment
    {
        if ($booking->status !== LessonBooking::STATUS_COMPLETED) {
            return null;
        }

        if ($booking->is_trial) {
            return null;
        }

        $minutes = max(0, (int) $booking->billable_minutes);
        if ($minutes <= 0) {
            return null;
        }

        $agreement = self::activeHourlyAgreementFor((int) $booking->instructor_id, $booking);
        if (! $agreement) {
            return null;
        }

        if (Schema::hasColumn('agreement_payments', 'lesson_booking_id')) {
            $exists = AgreementPayment::where('lesson_booking_id', $booking->id)
                ->where('type', AgreementPayment::TYPE_HOURLY_TEACHING)
                ->exists();
            if ($exists) {
                return null;
            }
        } else {
            $exists = AgreementPayment::where('agreement_id', $agreement->id)
                ->where('type', AgreementPayment::TYPE_HOURLY_TEACHING)
                ->where('description', 'like', '%#'.$booking->code.'%')
                ->exists();
            if ($exists) {
                return null;
            }
        }

        $hourlyRate = (float) $agreement->rate;
        if ($hourlyRate <= 0) {
            return null;
        }

        $hoursExact = round($minutes / 60, 4);
        $amount = round($hourlyRate * ($minutes / 60), 2);
        if ($amount <= 0) {
            return null;
        }

        $studentName = $booking->student?->name ?? 'طالب';
        $hoursLabel = rtrim(rtrim(number_format($hoursExact, 2, '.', ''), '0'), '.');

        try {
            return DB::transaction(function () use ($agreement, $booking, $amount, $minutes, $hoursExact, $hoursLabel, $studentName, $hourlyRate) {
                $payload = [
                    'agreement_id' => $agreement->id,
                    'instructor_id' => $agreement->instructor_id,
                    'type' => AgreementPayment::TYPE_HOURLY_TEACHING,
                    'amount' => $amount,
                    'status' => AgreementPayment::STATUS_APPROVED,
                    'description' => sprintf(
                        'حصة #%s مع %s — %s د (%s س) × %s %s/س',
                        $booking->code,
                        $studentName,
                        $minutes,
                        $hoursLabel,
                        number_format($hourlyRate, 2),
                        __('public.currency')
                    ),
                    'hours_count' => (int) max(1, (int) ceil($minutes / 60)),
                    'payment_date' => now(),
                    'created_by' => null,
                    'notes' => json_encode([
                        'minutes' => $minutes,
                        'hours_exact' => $hoursExact,
                        'hourly_rate' => $hourlyRate,
                        'booking_code' => $booking->code,
                    ], JSON_UNESCAPED_UNICODE),
                ];

                if (Schema::hasColumn('agreement_payments', 'lesson_booking_id')) {
                    $payload['lesson_booking_id'] = $booking->id;
                }
                if (Schema::hasColumn('agreement_payments', 'minutes_count')) {
                    $payload['minutes_count'] = $minutes;
                }

                $payment = AgreementPayment::create($payload);

                Log::info('Instructor hourly lesson payment created', [
                    'agreement_id' => $agreement->id,
                    'booking_id' => $booking->id,
                    'minutes' => $minutes,
                    'amount' => $amount,
                ]);

                return $payment;
            });
        } catch (\Throwable $e) {
            Log::error('InstructorHourlyLessonPayService failed', [
                'booking_id' => $booking->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function activeHourlyAgreementFor(int $instructorId, ?LessonBooking $booking = null): ?InstructorAgreement
    {
        $query = InstructorAgreement::query()
            ->where('instructor_id', $instructorId)
            ->where('status', InstructorAgreement::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->where('type', 'hourly_rate')
                    ->orWhere('billing_type', InstructorAgreement::BILLING_HOURLY_LESSON);
            })
            ->where('rate', '>', 0)
            ->orderByDesc('id');

        if ($booking?->scheduled_at) {
            $date = $booking->scheduled_at->toDateString();
            $query->whereDate('start_date', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
                });
        }

        return $query->first();
    }
}
