<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Wallet;

class OrderWalletAndCouponFinalizer
{
    /**
     * بعد إتمام الطلب (موافقة أدمن أو دفع أونلاين): تسجيل الكوبون ثم خصم رصيد المحفظة.
     *
     * @throws \Throwable
     */
    public static function run(Order $order): void
    {
        self::finalizeCoupon($order);
        self::finalizeWallet($order);
    }

    private static function finalizeCoupon(Order $order): void
    {
        if (! $order->coupon_id) {
            return;
        }

        $discount = (float) ($order->discount_amount ?? 0);
        if ($discount <= 0) {
            return;
        }

        $coupon = Coupon::whereKey($order->coupon_id)->lockForUpdate()->first();
        if (! $coupon) {
            return;
        }

        $coupon->recordUsage(
            (int) $order->user_id,
            $discount,
            (float) ($order->original_amount ?? $order->amount),
            (float) $order->amount,
            (int) $order->id
        );
    }

    /**
     * @throws \Throwable
     */
    private static function finalizeWallet(Order $order): void
    {
        $credit = (float) ($order->wallet_credit_amount ?? 0);
        if ($credit <= 0) {
            return;
        }

        $wallet = Wallet::where('user_id', $order->user_id)->lockForUpdate()->first();
        if (! $wallet) {
            throw new \RuntimeException('لا توجد محفظة للطالب لخصم الرصيد.');
        }
        if ((float) $wallet->balance + 0.00001 < $credit) {
            throw new \RuntimeException('رصيد محفظة الطالب غير كافٍ لإتمام الطلب (مطلوب خصم '.number_format($credit, 2).currency_suffix().').');
        }
        $wallet->withdraw(
            $credit,
            'خصم لشراء كورس — طلب #'.$order->id
        );
    }
}
