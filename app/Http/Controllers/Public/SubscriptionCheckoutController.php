<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\TeacherFeaturesController;
use App\Models\Coupon;
use App\Models\SubscriptionRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionCheckoutController extends Controller
{
    protected const VALID_PLANS = ['teacher_starter', 'teacher_pro'];
    protected const PLAN_RANK = ['teacher_starter' => 1, 'teacher_pro' => 2];

    /**
     * عرض صفحة دفع اشتراك الباقة (تحويل المبلغ + رفع إيصال الدفع)
     */
    public function show(string $plan)
    {
        if (!in_array($plan, self::VALID_PLANS, true)) {
            return redirect()->route('public.pricing')->with('error', 'الباقة غير صحيحة.');
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login', ['intended' => url()->current()]))
                ->with('info', 'يرجى تسجيل الدخول أولاً لدفع اشتراك الباقة.');
        }

        $featuresController = new TeacherFeaturesController();
        $settings = $featuresController->getSettings();
        $planConfig = $settings[$plan] ?? null;

        if (!$planConfig) {
            $planConfig = [
                'label' => SubscriptionRequest::planDefaults($plan)['plan_name'] ?? $plan,
                'price' => SubscriptionRequest::planDefaults($plan)['price'] ?? 0,
                'billing_cycle' => SubscriptionRequest::planDefaults($plan)['billing_cycle'] ?? 'monthly',
                'features' => SubscriptionRequest::planDefaults($plan)['features'] ?? [],
            ];
        } else {
            $planConfig['label'] = $planConfig['label'] ?? ($plan === 'teacher_starter' ? 'الباقة الأساسية' : 'الباقة الشاملة');
        }

        $billingLabel = [
            'monthly' => 'شهري',
            'quarterly' => 'كل 3 أشهر',
            'yearly' => 'سنوي',
        ][$planConfig['billing_cycle'] ?? 'monthly'] ?? $planConfig['billing_cycle'];

        $wallets = Wallet::where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($q) {
                $q->whereNotNull('account_number')->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('public.subscription-checkout', [
            'planKey' => $plan,
            'plan' => $planConfig,
            'billingLabel' => $billingLabel,
            'wallets' => $wallets,
            'upgrade' => (bool) request()->boolean('upgrade'),
            'fromSubscriptionId' => request()->input('from'),
        ]);
    }

    /**
     * استلام دفع الاشتراك: رفع إيصال الدفع وإنشاء طلب اشتراك يظهر في لوحة الأدمن للمراجعة
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        $plan = $request->input('plan');
        if (!in_array($plan, self::VALID_PLANS, true)) {
            return redirect()->route('public.pricing')->with('error', 'الباقة غير صحيحة.');
        }

        $upgrade = (bool) $request->boolean('upgrade');
        $fromSubscriptionId = $request->input('from');

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,wallet',
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:'.config('upload_limits.max_upload_kb'),
            'notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:64',
            'upgrade' => 'nullable|boolean',
            'from' => 'nullable',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_proof.required' => 'صورة إيصال الدفع مطلوبة بعد تحويل المبلغ',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'صيغة الصورة: jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'wallet_id.required_if' => 'يجب اختيار المحفظة التي تم التحويل إليها',
        ]);

        $featuresController = new TeacherFeaturesController();
        $settings = $featuresController->getSettings();
        $planConfig = $settings[$plan] ?? SubscriptionRequest::planDefaults($plan);

        $planName = $planConfig['label'] ?? $planConfig['plan_name'] ?? 'باقة المعلم';
        $basePrice = (float) ($planConfig['price'] ?? 0);
        $billingCycle = $planConfig['billing_cycle'] ?? 'monthly';
        $couponResolved = $this->resolveSubscriptionCoupon(Auth::user(), $basePrice, $validated['coupon_code'] ?? null);
        if (! $couponResolved['ok']) {
            return back()->withErrors(['coupon_code' => $couponResolved['message']])->withInput();
        }
        $price = $couponResolved['final_amount'];

        // ترقية: تحقق من الاشتراك الحالي + منع الداونجريد
        $fromSubscription = null;
        if ($upgrade) {
            $fromSubscription = Auth::user()->activeSubscription();
            if (!$fromSubscription) {
                return redirect()->route('public.pricing')->with('error', 'لا يوجد اشتراك نشط للترقية منه.');
            }

            if (!empty($fromSubscriptionId) && (int) $fromSubscriptionId !== (int) $fromSubscription->id) {
                return redirect()->route('student.my-subscription')->with('error', 'بيانات الترقية غير صحيحة. حاول مرة أخرى.');
            }

            $fromKey = (string) ($fromSubscription->teacher_plan_key ?? '');
            $fromRank = self::PLAN_RANK[$fromKey] ?? 0;
            $toRank = self::PLAN_RANK[$plan] ?? 0;
            if ($fromRank <= 0 || $toRank <= 0) {
                return redirect()->route('student.my-subscription')->with('error', 'لا يمكن ترقية هذه الباقة حالياً.');
            }
            if ($toRank <= $fromRank) {
                return redirect()->route('student.my-subscription')->with('error', 'يسمح بالترقية إلى باقة أعلى فقط.');
            }
        }

        $existing = SubscriptionRequest::where('user_id', Auth::id())
            ->where('teacher_plan_key', $plan)
            ->where('status', SubscriptionRequest::STATUS_PENDING)
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')
                ->with('info', 'لديك بالفعل طلب اشتراك قيد المراجعة لهذه الباقة.');
        }

        $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        SubscriptionRequest::create([
            'user_id' => Auth::id(),
            'teacher_plan_key' => $plan,
            'from_teacher_plan_key' => $upgrade ? ($fromSubscription?->teacher_plan_key) : null,
            'plan_name' => $planName,
            'price' => $price,
            'original_price' => $couponResolved['original_amount'],
            'discount_amount' => $couponResolved['discount_amount'],
            'coupon_id' => $couponResolved['coupon_id'],
            'coupon_code' => $couponResolved['coupon_code'],
            'billing_cycle' => $billingCycle,
            'request_type' => $upgrade ? 'upgrade' : 'new',
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $paymentProofPath,
            'wallet_id' => $validated['wallet_id'] ?? null,
            'notes' => $this->buildSubscriptionRequestNotes(
                $validated['notes'] ?? null,
                $couponResolved['coupon_code'],
                $couponResolved['discount_amount']
            ),
            'status' => SubscriptionRequest::STATUS_PENDING,
            'from_subscription_id' => $upgrade ? ($fromSubscription?->id) : null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم استلام إيصال الدفع بنجاح. سيتم مراجعة التحويل وتفعيل اشتراكك خلال أقرب وقت.');
    }

    /**
     * @return array{
     *   ok: bool,
     *   message?: string,
     *   coupon_id: ?int,
     *   coupon_code: ?string,
     *   original_amount: float,
     *   discount_amount: float,
     *   final_amount: float
     * }
     */
    private function resolveSubscriptionCoupon($user, float $basePrice, ?string $couponCode): array
    {
        $original = round(max(0, $basePrice), 2);
        $code = strtoupper(trim((string) $couponCode));
        if ($code === '') {
            return [
                'ok' => true,
                'coupon_id' => null,
                'coupon_code' => null,
                'original_amount' => $original,
                'discount_amount' => 0.0,
                'final_amount' => $original,
            ];
        }

        $coupon = Coupon::query()->where('code', $code)->first();
        if (! $coupon) {
            return [
                'ok' => false,
                'message' => 'كود الكوبون غير صحيح.',
                'coupon_id' => null,
                'coupon_code' => null,
                'original_amount' => $original,
                'discount_amount' => 0.0,
                'final_amount' => $original,
            ];
        }

        if (! in_array((string) $coupon->applicable_to, ['all', 'subscriptions'], true)) {
            return [
                'ok' => false,
                'message' => 'هذا الكوبون غير مخصص لدفع الباقات.',
                'coupon_id' => null,
                'coupon_code' => null,
                'original_amount' => $original,
                'discount_amount' => 0.0,
                'final_amount' => $original,
            ];
        }

        if (! $coupon->canBeUsedByUser((int) $user->id)) {
            return [
                'ok' => false,
                'message' => 'لا يمكن استخدام هذا الكوبون حالياً (منتهي/غير نشط/تجاوز الحد).',
                'coupon_id' => null,
                'coupon_code' => null,
                'original_amount' => $original,
                'discount_amount' => 0.0,
                'final_amount' => $original,
            ];
        }

        if ($coupon->minimum_amount && $original < (float) $coupon->minimum_amount) {
            return [
                'ok' => false,
                'message' => 'الحد الأدنى لاستخدام هذا الكوبون هو '.number_format((float) $coupon->minimum_amount, 2). currency_suffix(),
                'coupon_id' => null,
                'coupon_code' => null,
                'original_amount' => $original,
                'discount_amount' => 0.0,
                'final_amount' => $original,
            ];
        }

        $discount = (float) $coupon->calculateDiscount($original);

        return [
            'ok' => true,
            'coupon_id' => (int) $coupon->id,
            'coupon_code' => $coupon->code,
            'original_amount' => $original,
            'discount_amount' => $discount,
            'final_amount' => round(max(0, $original - $discount), 2),
        ];
    }

    private function buildSubscriptionRequestNotes(?string $baseNotes, ?string $couponCode, float $discountAmount): ?string
    {
        $notes = trim((string) ($baseNotes ?? ''));
        if ($couponCode === null || $couponCode === '' || $discountAmount <= 0) {
            return $notes !== '' ? $notes : null;
        }
        $couponLine = "كوبون مطبق: {$couponCode} (خصم ".number_format($discountAmount, 2).currency_suffix().')';
        if ($notes === '') {
            return $couponLine;
        }

        return $notes."\n".$couponLine;
    }
}
