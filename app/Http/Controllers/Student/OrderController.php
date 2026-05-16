<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Wallet;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * عرض طلبات الطالب
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['course.academicSubject', 'course.academicYear', 'learningPath'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }

    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request, AdvancedCourse $advancedCourse)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'wallet_credit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'wallet_id.required_if' => 'يجب اختيار حساب التحويل على المنصة حتى يُسجَّل المبلغ على المحفظة عند الموافقة.',
            'wallet_id.exists' => 'المحفظة المختارة غير صالحة أو غير متاحة.',
        ]);

        // التحقق من عدم وجود طلب مقبول مسبق
        $existingApprovedOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_APPROVED)
            ->exists();

        if ($existingApprovedOrder) {
            return back()->with('error', 'أنت مسجل بالفعل في هذا الكورس');
        }

        // التحقق من وجود طلب في الانتظار
        $existingPendingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_PENDING)
            ->exists();

        if ($existingPendingOrder) {
            return back()->with('error', 'لديك طلب في الانتظار لهذا الكورس');
        }

        // حساب السعر (إحالة، كوبون، ثم رصيد محفظة الطالب على المنصة) — قبل رفع الإيصال حتى نعرف إن كان مطلوباً
        $originalAmount = $advancedCourse->effectivePurchasePrice();
        $finalAmount = $originalAmount;
        $discountAmount = 0;
        $referralCoupon = null;

        $referralService = app(ReferralService::class);
        $referralCoupon = $referralService->applyReferralDiscount(auth()->user(), $originalAmount);

        $referralDiscountAmount = 0.0;
        if ($referralCoupon) {
            $referralDiscountAmount = $referralCoupon->calculateDiscount($originalAmount);
            $discountAmount = $referralDiscountAmount;
            $finalAmount = $originalAmount - $referralDiscountAmount;
        }

        $couponId = null;
        $couponDiscountAmount = 0;
        $appliedCoupon = null;

        if ($request->filled('applied_coupon_id')) {
            $coupon = Coupon::find($request->applied_coupon_id);
            if ($coupon && $coupon->isValid() && $coupon->canBeUsedByUser(auth()->id()) && $coupon->appliesToAdvancedCourseId((int) $advancedCourse->id)) {
                $couponDiscountAmount = $coupon->calculateDiscount($finalAmount);
                if ($couponDiscountAmount > 0) {
                    $discountAmount += $couponDiscountAmount;
                    $finalAmount -= $couponDiscountAmount;
                    $couponId = $coupon->id;
                    $appliedCoupon = $coupon;
                }
            }
        }

        $walletCreditRequested = round(max(0, (float) $request->input('wallet_credit', 0)), 2);
        $walletApply = 0.0;
        if ($walletCreditRequested > 0.00001 && $finalAmount > 0) {
            $studentWallet = Wallet::where('user_id', auth()->id())->first();
            $balance = $studentWallet ? (float) $studentWallet->balance : 0.0;
            $walletApply = round(min($walletCreditRequested, $balance, $finalAmount), 2);
            $finalAmount = round(max(0, $finalAmount - $walletApply), 2);
        }

        $proofRequired = $originalAmount > 0 && $finalAmount > 0.009;

        $request->validate([
            'payment_proof' => ($proofRequired ? 'required|' : 'nullable|').'image|mimes:jpeg,png,jpg|max:'.config('upload_limits.max_upload_kb'),
        ], [
            'payment_proof.required' => 'صورة الإيصال مطلوبة',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز الحد المسموح',
        ]);

        if ($referralCoupon && $referralDiscountAmount > 0) {
            $referral = \App\Models\Referral::where('auto_coupon_id', $referralCoupon->id)->first();
            if ($referral) {
                $referral->incrementDiscountUsage();
                $referral->update(['discount_amount' => $referralDiscountAmount]);
            }
        }

        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        // إنشاء الطلب
        $orderData = [
            'user_id' => auth()->id(),
            'advanced_course_id' => $advancedCourse->id,
            'coupon_id' => $couponId,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'wallet_credit_amount' => $walletApply,
            'amount' => $finalAmount,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'notes' => $request->notes ?? '',
            'status' => Order::STATUS_PENDING,
        ];

        // إضافة ملاحظة عن الخصومات
        $discountNotes = [];
        if ($referralCoupon && $referralDiscountAmount > 0) {
            $discountNotes[] = 'خصم الإحالة: '.number_format($referralDiscountAmount, 2).' ج.م';
        }
        if ($couponDiscountAmount > 0) {
            $discountNotes[] = 'خصم الكوبون ('.($appliedCoupon->code ?? '').'): '.number_format($couponDiscountAmount, 2).' ج.م';
        }
        if ($walletApply > 0) {
            $discountNotes[] = 'خصم من رصيد المحفظة: '.number_format($walletApply, 2).' ج.م';
        }
        if (! empty($discountNotes)) {
            $orderData['notes'] .= (! empty($orderData['notes']) ? "\n" : '').implode("\n", $discountNotes);
        }

        $orderData['wallet_id'] = $request->payment_method === 'bank_transfer' ? $request->wallet_id : null;

        $order = Order::create($orderData);

        if ($appliedCoupon && $couponDiscountAmount > 0) {
            $appliedCoupon->recordUsage(
                (int) auth()->id(),
                $couponDiscountAmount,
                $originalAmount,
                (float) $order->amount,
                (int) $order->id
            );
        }

        return back()->with('success', 'تم إرسال طلبك بنجاح! سيتم مراجعته قريباً');
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        // التأكد من أن الطلب يخص الطالب الحالي
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'course.academicSubject',
            'course.academicYear',
            'learningPath',
            'approver',
            'invoice',
            'payment',
        ]);

        return view('student.orders.show', compact('order'));
    }
}
