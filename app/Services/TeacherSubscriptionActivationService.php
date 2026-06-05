<?php

namespace App\Services;

use App\Services\InstructorSubscriptionPlansService;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeacherSubscriptionActivationService
{
    /**
     * تفعيل اشتراك المعلم بعد نجاح الدفع عبر بوابة (فواتيرك وغيرها): فاتورة مدفوعة، دفع، معاملات، اشتراك نشط.
     *
     * @param  'kashier'|'other'  $paymentGateway
     */
    public static function activateAfterGatewayPayment(
        SubscriptionRequest $subscriptionRequest,
        string $paymentGateway,
        ?string $transactionId,
        array $gatewayResponse,
        string $gatewayDisplayName
    ): Invoice {
        return DB::transaction(function () use ($subscriptionRequest, $paymentGateway, $transactionId, $gatewayResponse, $gatewayDisplayName) {
            $locked = SubscriptionRequest::whereKey($subscriptionRequest->id)
                ->lockForUpdate()
                ->first();

            if (! $locked || $locked->status !== SubscriptionRequest::STATUS_PENDING) {
                throw new \RuntimeException('طلب الاشتراك غير قابل للتفعيل.');
            }

            $settings = InstructorSubscriptionPlansService::getPlans();
            $planConfig = $settings[$locked->teacher_plan_key] ?? null;
            $features = $planConfig['features'] ?? SubscriptionRequest::planDefaults($locked->teacher_plan_key)['features'] ?? [];
            $features = Subscription::normalizeFeatureKeys($features);
            $featureLimitsSnapshot = is_array($planConfig['limits'] ?? null)
                ? SubscriptionLimitService::sanitizeFeatureLimits($planConfig['limits'])
                : null;

            $startDate = Carbon::now();
            $endDate = match ($locked->billing_cycle) {
                'monthly' => $startDate->copy()->addMonth(),
                'quarterly' => $startDate->copy()->addMonths(3),
                'yearly' => $startDate->copy()->addYear(),
                default => $startDate->copy()->addMonth(),
            };

            $gross = (float) $locked->price;
            $split = PaymentGatewaySettings::computeFeeSplit($gross);

            $activeOld = Subscription::where('user_id', $locked->user_id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', now());
                })
                ->orderByDesc('end_date')
                ->lockForUpdate()
                ->first();

            if ($activeOld) {
                $activeOld->update([
                    'status' => 'expired',
                    'end_date' => now()->toDateString(),
                ]);
            }

            $invoice = Invoice::create([
                'user_id' => $locked->user_id,
                'type' => 'subscription',
                'description' => ($locked->request_type ?? null) === 'upgrade'
                    ? 'فاتورة ترقية اشتراك: '.$locked->plan_name
                    : 'فاتورة اشتراك: '.$locked->plan_name,
                'subtotal' => $locked->original_price ?? $gross,
                'tax_amount' => 0,
                'discount_amount' => $locked->discount_amount ?? 0,
                'total_amount' => $gross,
                'status' => 'paid',
                'due_date' => $startDate,
                'paid_at' => now(),
                'notes' => 'دفع عبر '.$gatewayDisplayName.' — طلب اشتراك #'.$locked->id,
                'items' => [
                    [
                        'description' => 'اشتراك: '.$locked->plan_name,
                        'quantity' => 1,
                        'price' => $locked->original_price ?? $gross,
                        'total' => $gross,
                    ],
                ],
            ]);

            $paymentNumber = 'PAY-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));
            $currency = currency_code();

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'user_id' => $locked->user_id,
                'payment_method' => 'online',
                'payment_gateway' => $paymentGateway,
                'amount' => $gross,
                'gateway_fee_amount' => $split['fee'],
                'net_after_gateway_fee' => $split['net'],
                'currency' => $currency,
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'gateway_response' => $gatewayResponse,
                'paid_at' => now(),
                'notes' => 'دفع اشتراك عبر '.$gatewayDisplayName.' — طلب #'.$locked->id,
            ]);

            $txnCreditNo = 'TXN-'.now()->format('YmdHisu').'-'.strtoupper(Str::random(6));
            Transaction::create([
                'transaction_number' => $txnCreditNo,
                'user_id' => $locked->user_id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'expense_id' => null,
                'subscription_id' => null,
                'type' => 'credit',
                'category' => 'subscription',
                'amount' => $gross,
                'currency' => $currency,
                'description' => 'اشتراك باقة معلم: '.$locked->plan_name.' — طلب #'.$locked->id,
                'status' => 'completed',
                'metadata' => [
                    'subscription_request_id' => $locked->id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'teacher_plan_key' => $locked->teacher_plan_key,
                ],
            ]);

            if ($split['fee'] > 0.0001) {
                $txnFeeNo = 'TXN-'.now()->format('YmdHisu').'-'.strtoupper(Str::random(6));
                Transaction::create([
                    'transaction_number' => $txnFeeNo,
                    'user_id' => $locked->user_id,
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'expense_id' => null,
                    'subscription_id' => null,
                    'type' => 'debit',
                    'category' => 'fee',
                    'amount' => $split['fee'],
                    'currency' => $currency,
                    'description' => 'عمولة بوابة الدفع — دفع #'.$payment->payment_number,
                    'status' => 'completed',
                    'metadata' => [
                        'subscription_request_id' => $locked->id,
                        'payment_id' => $payment->id,
                        'gateway' => $paymentGateway,
                    ],
                ]);
            }

            $subscriptionType = match ($locked->billing_cycle) {
                'monthly' => 'monthly',
                'quarterly' => 'quarterly',
                'yearly' => 'yearly',
                default => 'monthly',
            };

            $subscription = Subscription::create([
                'user_id' => $locked->user_id,
                'subscription_type' => $subscriptionType,
                'teacher_plan_key' => $locked->teacher_plan_key,
                'plan_name' => $locked->plan_name,
                'price' => $gross,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'auto_renew' => false,
                'billing_cycle' => $locked->billing_cycle,
                'invoice_id' => $invoice->id,
                'features' => $features,
                'feature_limits' => $featureLimitsSnapshot,
            ]);

            $locked->update([
                'status' => SubscriptionRequest::STATUS_APPROVED,
                'subscription_id' => $subscription->id,
                'approved_at' => now(),
                'approved_by' => null,
            ]);

            if ($locked->coupon_id && (float) ($locked->discount_amount ?? 0) > 0) {
                $locked->coupon?->recordUsage(
                    (int) $locked->user_id,
                    (float) $locked->discount_amount,
                    (float) ($locked->original_price ?? $gross),
                    $gross,
                    null,
                    (int) $invoice->id
                );
            }

            return $invoice;
        });
    }
}
