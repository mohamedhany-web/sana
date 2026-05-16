<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\SubscriptionRequest;
use Illuminate\Console\Command;

class SyncCouponUsedCounts extends Command
{
    protected $signature = 'coupons:sync-used-counts {--backfill : إنشاء سجلات usage من الطلبات/الاشتراكات القديمة}';

    protected $description = 'مزامنة عداد used_count مع سجل coupon_usages وإصلاح البيانات القديمة';

    public function handle(): int
    {
        if ($this->option('backfill')) {
            $this->backfillFromOrders();
            $this->backfillFromSubscriptions();
        }

        $updated = 0;
        Coupon::query()->each(function (Coupon $coupon) use (&$updated) {
            $before = (int) $coupon->used_count;
            $coupon->syncUsedCountFromUsages();
            $coupon->refresh();
            if ((int) $coupon->used_count !== $before) {
                $updated++;
            }
        });

        $this->info("تمت مزامنة {$updated} كوبون.");

        return self::SUCCESS;
    }

    private function backfillFromOrders(): void
    {
        $created = 0;
        Order::query()
            ->whereNotNull('coupon_id')
            ->where('discount_amount', '>', 0)
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$created) {
                foreach ($orders as $order) {
                    if (CouponUsage::where('order_id', $order->id)->exists()) {
                        continue;
                    }
                    $coupon = Coupon::find($order->coupon_id);
                    if (! $coupon) {
                        continue;
                    }
                    $coupon->recordUsage(
                        (int) $order->user_id,
                        (float) $order->discount_amount,
                        (float) ($order->original_amount ?? $order->amount),
                        (float) $order->amount,
                        (int) $order->id
                    );
                    $created++;
                }
            });

        $this->info("أُنشئ {$created} سجل usage من الطلبات.");
    }

    private function backfillFromSubscriptions(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('subscription_requests', 'coupon_id')) {
            return;
        }

        $created = 0;
        $query = SubscriptionRequest::query()
            ->whereNotNull('coupon_id')
            ->where('discount_amount', '>', 0);

        if (\Illuminate\Support\Facades\Schema::hasColumn('subscription_requests', 'status')) {
            $query->where('status', 'approved');
        }

        $query->orderBy('id')->chunkById(100, function ($requests) use (&$created) {
            foreach ($requests as $req) {
                $invoiceId = null;
                if (\Illuminate\Support\Facades\Schema::hasColumn('subscription_requests', 'invoice_id')) {
                    $invoiceId = $req->invoice_id;
                } elseif ($req->subscription_id && \Illuminate\Support\Facades\Schema::hasTable('subscriptions')) {
                    $invoiceId = \Illuminate\Support\Facades\DB::table('subscriptions')
                        ->where('id', $req->subscription_id)
                        ->value('invoice_id');
                }

                if ($invoiceId && CouponUsage::where('coupon_id', $req->coupon_id)->where('invoice_id', $invoiceId)->exists()) {
                    continue;
                }

                $coupon = Coupon::find($req->coupon_id);
                if (! $coupon) {
                    continue;
                }

                $coupon->recordUsage(
                    (int) $req->user_id,
                    (float) $req->discount_amount,
                    (float) ($req->original_price ?? $req->price),
                    (float) $req->price,
                    null,
                    $invoiceId ? (int) $invoiceId : null
                );
                $created++;
            }
        });

        $this->info("أُنشئ {$created} سجل usage من طلبات الاشتراك.");
    }
}
