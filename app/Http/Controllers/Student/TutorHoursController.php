<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TutorHourPurchase;
use App\Models\Wallet;
use App\Services\TutorLessonQuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TutorHoursController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = TutorLessonQuotaService::syncProfileForUser($user);
        $plans = TutorLessonQuotaService::purchasablePlans();
        $purchases = TutorHourPurchase::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $base = TutorLessonQuotaService::baseQuotaHoursForUser($user);
        $bonus = max(0, (int) ($profile->lesson_hours_bonus ?? 0));
        $remaining = $profile->remainingHours();
        if ($remaining === PHP_INT_MAX) {
            $remainingLabel = 'غير محدود';
        } else {
            $remainingLabel = (string) $remaining;
        }

        return view('student.tutor-lessons.hours', compact(
            'profile',
            'plans',
            'purchases',
            'base',
            'bonus',
            'remaining',
            'remainingLabel'
        ));
    }

    public function checkout(string $planKey)
    {
        $plans = TutorLessonQuotaService::purchasablePlans();
        $plan = $plans[$planKey] ?? null;
        if (! $plan) {
            return redirect()
                ->route('student.tutor-lessons.hours')
                ->with('error', 'هذه الباقة غير متاحة للشراء حالياً. راجع إعدادات الباقات أو تواصل مع الدعم.');
        }

        $billingLabel = [
            'monthly' => 'شهري',
            'quarterly' => 'كل 3 أشهر',
            'yearly' => 'سنوي',
        ][$plan['billing_cycle'] ?? 'monthly'] ?? ($plan['billing_cycle'] ?? '');

        $wallets = Wallet::where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($q) {
                $q->whereNotNull('account_number')->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());

        return view('student.tutor-lessons.hours-checkout', [
            'planKey' => $planKey,
            'plan' => $plan,
            'billingLabel' => $billingLabel,
            'wallets' => $wallets,
            'profile' => $profile,
            'hours' => (int) ($plan['limits']['tutor_lesson_hours'] ?? 0),
        ]);
    }

    public function purchase(Request $request, string $planKey)
    {
        $plans = TutorLessonQuotaService::purchasablePlans();
        $plan = $plans[$planKey] ?? null;
        if (! $plan) {
            return redirect()
                ->route('student.tutor-lessons.hours')
                ->with('error', 'هذه الباقة غير متاحة للشراء حالياً.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,wallet',
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:'.config('upload_limits.max_upload_kb'),
            'notes' => 'nullable|string|max:1000',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_proof.required' => 'صورة إيصال الدفع مطلوبة بعد تحويل المبلغ',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'صيغة الصورة: jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز الحد المسموح',
            'wallet_id.required_if' => 'يجب اختيار المحفظة التي تم التحويل إليها',
        ]);

        $pending = TutorHourPurchase::where('user_id', Auth::id())
            ->where('plan_key', $planKey)
            ->where('status', TutorHourPurchase::STATUS_PENDING)
            ->exists();

        if ($pending) {
            return redirect()
                ->route('student.tutor-lessons.hours')
                ->with('info', 'لديك بالفعل طلب شراء قيد المراجعة لهذه الباقة.');
        }

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        TutorHourPurchase::create([
            'user_id' => Auth::id(),
            'plan_key' => $planKey,
            'plan_name' => (string) ($plan['label'] ?? $planKey),
            'hours' => (int) ($plan['limits']['tutor_lesson_hours'] ?? 0),
            'price' => (float) ($plan['price'] ?? 0),
            'billing_cycle' => $plan['billing_cycle'] ?? 'monthly',
            'payment_method' => $validated['payment_method'],
            'wallet_id' => $validated['wallet_id'] ?? null,
            'payment_proof' => $proofPath,
            'notes' => $validated['notes'] ?? null,
            'status' => TutorHourPurchase::STATUS_PENDING,
        ]);

        return redirect()
            ->route('student.tutor-lessons.hours')
            ->with('success', 'تم استلام إيصال الدفع. بعد مراجعة الأدمن ستُضاف الساعات إلى رصيدك.');
    }
}
