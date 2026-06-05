<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SearchInput;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\AdminSubscriptionFormService;
use App\Services\StudentSubscriptionPlansService;
use App\Services\SubscriptionLimitService;
use App\Services\TeacherSubscriptionActivationService;
use App\Services\TutorLessonQuotaService;
use App\Models\LessonBooking;
use App\Models\StudentLearningProfile;
use App\Models\ClassroomMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * عرض قائمة الاشتراكات
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            $query = Subscription::with('user')
                ->orderBy('created_at', 'desc');

            // فلترة حسب الحالة - حماية من SQL Injection
            if ($request->filled('status')) {
                $status = strip_tags(trim($request->status));
                $status = preg_replace('/[^a-z_]/', '', $status);
                if (in_array($status, ['active', 'expired', 'cancelled'])) {
                    $query->where('status', $status);
                }
            }

            if ($request->filled('subscriber_role')) {
                $role = strip_tags(trim((string) $request->subscriber_role));
                if ($role === 'student') {
                    $query->whereHas('user', fn ($q) => $q->where('role', 'student'));
                } elseif ($role === 'instructor') {
                    $query->whereHas('user', fn ($q) => $q->whereIn('role', ['instructor', 'teacher']));
                }
            }

            // البحث - حماية من XSS و SQL Injection
            if ($request->filled('search')) {
                $search = SearchInput::sanitizeForLike((string) $request->search);
                if ($search !== '') {
                    $query->whereHas('user', function($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            }

            $subscriptions = $query->paginate(18);

            $stats = [
                'total' => Subscription::count(),
                'active' => Subscription::where('status', 'active')->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'auto_renew' => Subscription::where('auto_renew', true)->count(),
                'active_revenue' => (float) Subscription::where('status', 'active')->sum('price'),
                'active_students' => Subscription::where('status', 'active')
                    ->whereHas('user', fn ($q) => $q->where('role', 'student'))->count(),
                'active_instructors' => Subscription::where('status', 'active')
                    ->whereHas('user', fn ($q) => $q->whereIn('role', ['instructor', 'teacher']))->count(),
            ];

            $currentMonthRange = [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ];

            $monthlyNew = Subscription::whereBetween('start_date', $currentMonthRange)->count();
            $monthlyRevenue = (float) Subscription::whereBetween('start_date', $currentMonthRange)->sum('price');

            $planDistribution = Subscription::selectRaw("COALESCE(NULLIF(subscription_type, ''), 'other') as subscription_type, COUNT(*) as subscriptions_count, SUM(price) as total_price")
                ->groupBy('subscription_type')
                ->get()
                ->map(function ($row) {
                    $type = $row->subscription_type;
                    return [
                        'type' => $type,
                        'label' => Subscription::typeLabel($type),
                        'subscriptions_count' => (int) $row->subscriptions_count,
                        'total_price' => (float) $row->total_price,
                    ];
                });

            $expiringSoon = Subscription::with('user')
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
                ->orderBy('end_date')
                ->take(6)
                ->get();

            $recentSubscriptions = Subscription::with('user')
                ->latest()
                ->take(6)
                ->get();

            $pendingRequests = SubscriptionRequest::pending()->with(['user', 'wallet'])->latest()->get();

            return view('admin.subscriptions.index', compact(
                'subscriptions',
                'stats',
                'monthlyNew',
                'monthlyRevenue',
                'planDistribution',
                'expiringSoon',
                'recentSubscriptions',
                'pendingRequests'
            ));
        } catch (\Exception $e) {
            Log::error('Error in SubscriptionController@index: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'invoice.payments', 'transactions']);

        $learningProfile = null;
        $tutorBookingsCount = 0;
        if ($subscription->subscriberIsStudent() && $subscription->user) {
            $learningProfile = StudentLearningProfile::where('user_id', $subscription->user_id)->first();
            $tutorBookingsCount = LessonBooking::where('student_id', $subscription->user_id)->count();
        }

        $classroomMeetingsMonth = 0;
        if ($subscription->subscriberIsInstructor() && $subscription->user) {
            $classroomMeetingsMonth = ClassroomMeeting::where('user_id', $subscription->user_id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
        }

        return view('admin.subscriptions.show', compact(
            'subscription',
            'learningProfile',
            'tutorBookingsCount',
            'classroomMeetingsMonth'
        ));
    }

    /**
     * رقابة استهلاك المشترك (المعلم/الطالب) — كل ما يستهلكه في النظام والخطة
     */
    public function consumption(Subscription $subscription)
    {
        $subscription->load(['user', 'invoice']);
        $user = $subscription->user;
        if (!$user) {
            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('error', 'لا يوجد مستخدم مرتبط بهذا الاشتراك.');
        }

        $enrollments = \App\Models\StudentCourseEnrollment::where('user_id', $user->id)
            ->with(['course:id,title,academic_subject_id', 'course.academicSubject:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $examAttempts = \App\Models\ExamAttempt::where('user_id', $user->id)
            ->with(['exam:id,title'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with(['course:id,title'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(80)
            ->get();

        $featuresConfig = config('student_subscription_features', []);
        $subscriptionFeatures = $subscription->features ?? [];

        $learningProfile = null;
        $tutorBookings = collect();
        $tutorBookingsCount = 0;
        $classroomMeetingsMonth = 0;
        if ($subscription->subscriberIsStudent()) {
            $learningProfile = StudentLearningProfile::where('user_id', $user->id)->first();
            $tutorBookingsCount = LessonBooking::where('student_id', $user->id)->count();
            $tutorBookings = LessonBooking::where('student_id', $user->id)
                ->with('instructor')
                ->orderByDesc('scheduled_at')
                ->limit(20)
                ->get();
        } elseif ($subscription->subscriberIsInstructor()) {
            $classroomMeetingsMonth = ClassroomMeeting::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
        }

        return view('admin.subscriptions.consumption', compact(
            'subscription',
            'user',
            'enrollments',
            'examAttempts',
            'orders',
            'activityLogs',
            'featuresConfig',
            'subscriptionFeatures',
            'learningProfile',
            'tutorBookings',
            'tutorBookingsCount',
            'classroomMeetingsMonth'
        ));
    }

    public function create()
    {
        $users = AdminSubscriptionFormService::subscribersForSelect();
        $form = AdminSubscriptionFormService::formContext();

        return view('admin.subscriptions.create', array_merge(compact('users'), $form));
    }

    public function edit(Subscription $subscription)
    {
        $users = AdminSubscriptionFormService::subscribersForSelect();
        $form = AdminSubscriptionFormService::formContext($subscription);

        return view('admin.subscriptions.edit', array_merge(compact('subscription', 'users'), $form));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_type' => 'required|string',
            'teacher_plan_key' => 'nullable|string|max:50',
            'plan_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'boolean',
            'billing_cycle' => 'required|string',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'limits.classroom_meetings_per_month' => 'nullable|integer|min:0|max:10000',
            'limits.classroom_max_participants' => 'nullable|integer|min:1|max:1000',
            'limits.classroom_default_duration_minutes' => 'nullable|integer|min:15|max:1440',
            'limits.classroom_max_duration_minutes' => 'nullable|integer|min:30|max:1440',
            'limits.personal_marketing_profile_sections' => 'nullable|integer|min:1|max:20',
            'limits.personal_marketing_priority_score' => 'nullable|integer|min:0|max:100',
            'limits.personal_marketing_monthly_featured_days' => 'nullable|integer|min:0|max:31',
            'limits.tutor_lesson_hours' => 'nullable|integer|min:0|max:10000',
        ]);

        // تحويل شكل المزايا من [key => 1] إلى [key, key, ...] لتوافق hasSubscriptionFeature()
        $featureKeys = [];
        if (!empty($validated['features']) && is_array($validated['features'])) {
            $featureKeys = array_keys(array_filter($validated['features'], fn ($v) => (bool) $v));
        }
        $featureKeys = Subscription::normalizeFeatureKeys($featureKeys);

        $featureLimits = SubscriptionLimitService::sanitizeFeatureLimits($validated['limits'] ?? null);

        try {
            DB::beginTransaction();

            $this->expirePreviousActiveSubscription((int) $validated['user_id']);

            // إنشاء Invoice تلقائياً للاشتراك
            $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $validated['user_id'],
                'type' => 'subscription',
                'description' => 'فاتورة اشتراك: ' . $validated['plan_name'],
                'subtotal' => $validated['price'],
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $validated['price'],
                'status' => 'pending',
                'due_date' => Carbon::parse($validated['start_date']),
                'notes' => 'فاتورة اشتراك - نوع: ' . Subscription::typeLabel($validated['subscription_type']),
                'items' => [
                    [
                        'description' => 'اشتراك: ' . $validated['plan_name'],
                        'quantity' => 1,
                        'price' => $validated['price'],
                        'total' => $validated['price'],
                    ]
                ],
            ]);

            // إنشاء Subscription
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'],
                'subscription_type' => $validated['subscription_type'],
                'teacher_plan_key' => $validated['teacher_plan_key'] ?? null,
                'plan_name' => $validated['plan_name'],
                'price' => $validated['price'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'active',
                'auto_renew' => $validated['auto_renew'] ?? false,
                'billing_cycle' => $validated['billing_cycle'],
                'invoice_id' => $invoice->id,
                'features' => $featureKeys,
                'feature_limits' => $featureLimits,
            ]);

            DB::commit();

            $this->syncSubscriberAfterSave((int) $validated['user_id']);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'تم إنشاء الاشتراك والفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الاشتراك: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_type' => 'required|string',
            'teacher_plan_key' => 'nullable|string|max:50',
            'plan_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'auto_renew' => 'boolean',
            'billing_cycle' => 'required|string',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'limits.classroom_meetings_per_month' => 'nullable|integer|min:0|max:10000',
            'limits.classroom_max_participants' => 'nullable|integer|min:1|max:1000',
            'limits.classroom_default_duration_minutes' => 'nullable|integer|min:15|max:1440',
            'limits.classroom_max_duration_minutes' => 'nullable|integer|min:30|max:1440',
            'limits.personal_marketing_profile_sections' => 'nullable|integer|min:1|max:20',
            'limits.personal_marketing_priority_score' => 'nullable|integer|min:0|max:100',
            'limits.personal_marketing_monthly_featured_days' => 'nullable|integer|min:0|max:31',
            'limits.tutor_lesson_hours' => 'nullable|integer|min:0|max:10000',
        ]);

        // تحويل شكل المزايا من [key => 1] إلى [key, key, ...]
        $featureKeys = [];
        if (!empty($validated['features']) && is_array($validated['features'])) {
            $featureKeys = array_keys(array_filter($validated['features'], fn ($v) => (bool) $v));
        }

        $data = $validated;
        $data['features'] = Subscription::normalizeFeatureKeys($featureKeys);
        $data['feature_limits'] = SubscriptionLimitService::sanitizeFeatureLimits($validated['limits'] ?? null);
        unset($data['limits']);

        $subscription->update($data);

        $this->syncSubscriberAfterSave((int) $validated['user_id']);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }

    /**
     * تفعيل طلب اشتراك: إنشاء اشتراك نشط للطالب وربطه بالطلب
     */
    public function approveRequest(Request $request, SubscriptionRequest $subscriptionRequest)
    {
        if ($subscriptionRequest->status !== SubscriptionRequest::STATUS_PENDING) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'هذا الطلب تمت معالجته مسبقاً.');
        }

        // دفع إلكتروني (فواتيرك): عادة يُفعَّل عند العودة من البوابة؛ إن تعذّر ذلك نُكمّل يدوياً بنفس مسار الفاتورة/المدفوعات/المعاملات
        if ($subscriptionRequest->payment_method === 'online' && empty($subscriptionRequest->payment_proof)) {
            try {
                TeacherSubscriptionActivationService::activateAfterGatewayPayment(
                    $subscriptionRequest,
                    'other',
                    trim((string) $request->input('gateway_transaction_id', '')) ?: null,
                    [
                        'admin_manual_approve' => true,
                        'admin_user_id' => Auth::id(),
                    ],
                    'تأكيد يدوي — لوحة الإدارة (عودة فواتيرك أو مراجعة)'
                );
                SubscriptionRequest::whereKey($subscriptionRequest->id)->update([
                    'approved_by' => Auth::id(),
                ]);

                return redirect()->route('admin.subscriptions.index')
                    ->with('success', 'تم تفعيل اشتراك الدفع الإلكتروني وتسجيل الفاتورة والمدفوعات والمعاملات.');
            } catch (\Throwable $e) {
                Log::error('Subscription online manual approve failed', [
                    'subscription_request_id' => $subscriptionRequest->id,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return redirect()->route('admin.subscriptions.index')
                    ->with('error', 'تعذّر التفعيل الإلكتروني: '.$e->getMessage());
            }
        }

        $planKey = (string) ($subscriptionRequest->teacher_plan_key ?? '');
        $planConfig = null;
        if (StudentSubscriptionPlansService::isStudentPlanKey($planKey)) {
            $planConfig = StudentSubscriptionPlansService::getPlans()[$planKey] ?? null;
        } else {
            $settings = \App\Services\InstructorSubscriptionPlansService::getPlans();
            $planConfig = $settings[$planKey] ?? null;
        }
        $features = $planConfig['features'] ?? SubscriptionRequest::planDefaults($subscriptionRequest->teacher_plan_key)['features'] ?? [];
        $features = Subscription::normalizeFeatureKeys($features);
        $approvedFeatureLimits = is_array($planConfig['limits'] ?? null)
            ? SubscriptionLimitService::sanitizeFeatureLimits($planConfig['limits'])
            : null;

        $startDate = Carbon::now();
        $endDate = match ($subscriptionRequest->billing_cycle) {
            'monthly' => $startDate->copy()->addMonth(),
            'quarterly' => $startDate->copy()->addMonths(3),
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };

        try {
            DB::beginTransaction();

            // إنهاء أي اشتراك نشط سابق للمستخدم قبل تفعيل الجديد (خصوصاً في حالة الترقية)
            $activeOld = Subscription::where('user_id', $subscriptionRequest->user_id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->orderByDesc('end_date')
                ->first();

            if ($activeOld) {
                $activeOld->update([
                    'status' => 'expired',
                    'end_date' => now()->toDateString(),
                ]);
            }

            $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $subscriptionRequest->user_id,
                'type' => 'subscription',
                'description' => ($subscriptionRequest->request_type ?? null) === 'upgrade'
                    ? 'فاتورة ترقية اشتراك: ' . $subscriptionRequest->plan_name
                    : 'فاتورة اشتراك: ' . $subscriptionRequest->plan_name,
                'subtotal' => $subscriptionRequest->original_price ?? $subscriptionRequest->price,
                'tax_amount' => 0,
                'discount_amount' => $subscriptionRequest->discount_amount ?? 0,
                'total_amount' => $subscriptionRequest->price,
                'status' => 'pending',
                'due_date' => $startDate,
                'notes' => (($subscriptionRequest->request_type ?? null) === 'upgrade' ? 'ترقية' : 'اشتراك') . ' من طلب المستخدم - ' . $subscriptionRequest->plan_name,
                'items' => [
                    [
                        'description' => 'اشتراك: ' . $subscriptionRequest->plan_name,
                        'quantity' => 1,
                        'price' => $subscriptionRequest->original_price ?? $subscriptionRequest->price,
                        'total' => $subscriptionRequest->price,
                    ],
                ],
            ]);

            $subscriptionType = match ($subscriptionRequest->billing_cycle) {
                'monthly' => 'monthly',
                'quarterly' => 'quarterly',
                'yearly' => 'yearly',
                default => 'monthly',
            };

            $subscription = Subscription::create([
                'user_id' => $subscriptionRequest->user_id,
                'subscription_type' => $subscriptionType,
                'teacher_plan_key' => $subscriptionRequest->teacher_plan_key,
                'plan_name' => $subscriptionRequest->plan_name,
                'price' => $subscriptionRequest->price,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'auto_renew' => false,
                'billing_cycle' => $subscriptionRequest->billing_cycle,
                'invoice_id' => $invoice->id,
                'features' => $features,
                'feature_limits' => $approvedFeatureLimits,
            ]);

            $subscriptionRequest->update([
                'status' => SubscriptionRequest::STATUS_APPROVED,
                'subscription_id' => $subscription->id,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            if ($subscriptionRequest->coupon_id && (float) ($subscriptionRequest->discount_amount ?? 0) > 0) {
                $subscriptionRequest->coupon?->recordUsage(
                    (int) $subscriptionRequest->user_id,
                    (float) $subscriptionRequest->discount_amount,
                    (float) ($subscriptionRequest->original_price ?? $subscriptionRequest->price),
                    (float) $subscriptionRequest->price,
                    null,
                    (int) $invoice->id
                );
            }

            DB::commit();

            $this->syncSubscriberAfterSave((int) $subscriptionRequest->user_id);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'تم تفعيل الاشتراك بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SubscriptionRequest approve error: ' . $e->getMessage());
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'حدث خطأ أثناء التفعيل: ' . $e->getMessage());
        }
    }

    /**
     * رفض طلب اشتراك
     */
    public function rejectRequest(SubscriptionRequest $subscriptionRequest)
    {
        if ($subscriptionRequest->status !== SubscriptionRequest::STATUS_PENDING) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'هذا الطلب تمت معالجته مسبقاً.');
        }
        $subscriptionRequest->update([
            'status' => SubscriptionRequest::STATUS_REJECTED,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم رفض طلب الاشتراك.');
    }

    private function syncSubscriberAfterSave(int $userId): void
    {
        $user = User::find($userId);
        if ($user?->isStudent()) {
            TutorLessonQuotaService::syncProfileForUser($user);
        }
    }

    private function expirePreviousActiveSubscription(int $userId): void
    {
        $activeOld = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderByDesc('end_date')
            ->first();

        if ($activeOld) {
            $activeOld->update([
                'status' => 'expired',
                'end_date' => now()->toDateString(),
            ]);
        }
    }
}
