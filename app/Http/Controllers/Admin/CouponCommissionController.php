<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponCommissionAccrual;
use App\Services\CouponCommissionService;
use Illuminate\Http\Request;

class CouponCommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = CouponCommissionAccrual::with(['coupon', 'beneficiary', 'order', 'expense'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('beneficiary_id')) {
            $query->where('beneficiary_user_id', (int) $request->beneficiary_id);
        }

        $accruals = $query->paginate(25)->withQueryString();

        $stats = [
            'pending' => CouponCommissionAccrual::where('status', CouponCommissionAccrual::STATUS_PENDING)->count(),
            'expense_pending' => CouponCommissionAccrual::where('status', CouponCommissionAccrual::STATUS_EXPENSE_PENDING)->count(),
            'settled' => CouponCommissionAccrual::where('status', CouponCommissionAccrual::STATUS_SETTLED)->count(),
            'amount_pending' => (float) CouponCommissionAccrual::whereIn('status', [
                CouponCommissionAccrual::STATUS_PENDING,
                CouponCommissionAccrual::STATUS_EXPENSE_PENDING,
            ])->sum('commission_amount_egp'),
        ];

        return view('admin.coupon-commissions.index', compact('accruals', 'stats'));
    }

    public function storeExpense(CouponCommissionAccrual $accrual, CouponCommissionService $service)
    {
        try {
            $expense = $service->createPendingExpense($accrual);

            return redirect()->route('admin.expenses.show', $expense)
                ->with('success', __('تم إنشاء مصروف تسويق معلق. وافق عليه من شاشة المصروفات لإتمام التسوية المحاسبية.'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
