<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', now());
                    });
            } elseif ($request->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                        ->orWhere('expires_at', '<', now());
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $coupons = $query->paginate(20);

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })->count(),
            'expired' => Coupon::where(function ($q) {
                $q->where('is_active', false)->orWhere('expires_at', '<', now());
            })->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $courses = AdvancedCourse::orderBy('title')->get(['id', 'title']);

        return view('admin.coupons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'applicable_to' => 'required|in:all,courses,subscriptions,specific',
            'applicable_course_ids' => 'nullable|array',
            'applicable_course_ids.*' => 'integer|exists:advanced_courses,id',
            'applicable_user_ids_text' => 'nullable|string|max:10000',
            'beneficiary_user_id' => 'nullable|integer|exists:users,id',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'commission_on' => 'nullable|in:final_paid,original_price',
        ]);

        $courseIds = array_values(array_unique(array_filter(array_map('intval', $request->input('applicable_course_ids', [])))));
        $userIds = $this->parseUserIdsFromText($request->input('applicable_user_ids_text'));

        if (in_array($validated['applicable_to'], ['courses', 'specific'], true) && count($courseIds) === 0) {
            return back()->withErrors(['applicable_course_ids' => 'اختر كورساً واحداً على الأقل لهذا النطاق.'])->withInput();
        }

        foreach ($userIds as $uid) {
            if (! User::whereKey($uid)->exists()) {
                return back()->withErrors(['applicable_user_ids_text' => "المستخدم رقم {$uid} غير موجود."])->withInput();
            }
        }

        $beneficiaryId = $validated['beneficiary_user_id'] ?? null;
        $commissionPercent = $validated['commission_percent'] ?? null;
        if ($commissionPercent === null || $commissionPercent === '') {
            $commissionPercent = null;
        } else {
            $commissionPercent = round((float) $commissionPercent, 2);
        }
        if (! $beneficiaryId || $commissionPercent === null || $commissionPercent <= 0) {
            $beneficiaryId = null;
            $commissionPercent = null;
        }

        Coupon::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['title'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_amount' => $validated['minimum_amount'] ?? null,
            'maximum_discount' => $validated['maximum_discount'] ?? null,
            'usage_limit' => $validated['max_uses'] ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? 1,
            'starts_at' => $validated['valid_from'] ?? null,
            'expires_at' => $validated['valid_until'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_public' => $request->boolean('is_public', true),
            'applicable_to' => $validated['applicable_to'],
            'applicable_course_ids' => in_array($validated['applicable_to'], ['courses', 'specific'], true) ? $courseIds : null,
            'applicable_user_ids' => count($userIds) > 0 ? $userIds : null,
            'beneficiary_user_id' => $beneficiaryId,
            'commission_percent' => $commissionPercent,
            'commission_on' => $beneficiaryId ? ($validated['commission_on'] ?? 'final_paid') : 'final_paid',
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    public function show(Coupon $coupon)
    {
        $coupon->syncUsedCountFromUsages();
        $coupon->load(['usages.user', 'usages.order', 'beneficiary']);
        $scopedCourses = collect();
        $ids = $coupon->applicable_course_ids ?? [];
        if (is_array($ids) && count($ids) > 0) {
            $scopedCourses = AdvancedCourse::whereIn('id', $ids)->orderBy('title')->get(['id', 'title']);
        }

        return view('admin.coupons.show', compact('coupon', 'scopedCourses'));
    }

    public function edit(Coupon $coupon)
    {
        $courses = AdvancedCourse::orderBy('title')->get(['id', 'title']);

        return view('admin.coupons.edit', compact('coupon', 'courses'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'title' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'applicable_to' => 'required|in:all,courses,subscriptions,specific',
            'applicable_course_ids' => 'nullable|array',
            'applicable_course_ids.*' => 'integer|exists:advanced_courses,id',
            'applicable_user_ids_text' => 'nullable|string|max:10000',
            'beneficiary_user_id' => 'nullable|integer|exists:users,id',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'commission_on' => 'nullable|in:final_paid,original_price',
        ]);

        $courseIds = array_values(array_unique(array_filter(array_map('intval', $request->input('applicable_course_ids', [])))));
        $userIds = $this->parseUserIdsFromText($request->input('applicable_user_ids_text'));

        if (in_array($validated['applicable_to'], ['courses', 'specific'], true) && count($courseIds) === 0) {
            return back()->withErrors(['applicable_course_ids' => 'اختر كورساً واحداً على الأقل لهذا النطاق.'])->withInput();
        }

        foreach ($userIds as $uid) {
            if (! User::whereKey($uid)->exists()) {
                return back()->withErrors(['applicable_user_ids_text' => "المستخدم رقم {$uid} غير موجود."])->withInput();
            }
        }

        $beneficiaryId = $validated['beneficiary_user_id'] ?? null;
        $commissionPercent = $validated['commission_percent'] ?? null;
        if ($commissionPercent === null || $commissionPercent === '') {
            $commissionPercent = null;
        } else {
            $commissionPercent = round((float) $commissionPercent, 2);
        }
        if (! $beneficiaryId || $commissionPercent === null || $commissionPercent <= 0) {
            $beneficiaryId = null;
            $commissionPercent = null;
        }

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['title'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_amount' => $validated['minimum_amount'] ?? null,
            'maximum_discount' => $validated['maximum_discount'] ?? null,
            'usage_limit' => $validated['max_uses'] ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? 1,
            'starts_at' => $validated['valid_from'] ?? null,
            'expires_at' => $validated['valid_until'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_public' => $request->boolean('is_public', true),
            'applicable_to' => $validated['applicable_to'],
            'applicable_course_ids' => in_array($validated['applicable_to'], ['courses', 'specific'], true) ? $courseIds : null,
            'applicable_user_ids' => count($userIds) > 0 ? $userIds : null,
            'beneficiary_user_id' => $beneficiaryId,
            'commission_percent' => $commissionPercent,
            'commission_on' => $beneficiaryId ? ($validated['commission_on'] ?? 'final_paid') : 'final_paid',
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }

    /**
     * @return int[]
     */
    private function parseUserIdsFromText(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }
        $parts = preg_split('/[\s,;،]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $ids = [];
        foreach ($parts as $p) {
            $n = (int) trim($p);
            if ($n > 0) {
                $ids[$n] = $n;
            }
        }

        return array_values($ids);
    }
}
