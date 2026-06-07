<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\UserProfileImageStorage;
use App\Support\CloudStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'must_change_password',
        'google_id',
        'role',
        'is_community_contributor',
        'community_contributor_type',
        'parent_id',
        'is_active',
        'profile_image',
        'birth_date',
        'address',
        'bio',
        'onboarding_preferences',
        'academic_year_id',
        'last_login_at',
        'referral_code',
        'referred_by',
        'referred_at',
        'total_referrals',
        'completed_referrals',
        'employee_job_id',
        'employee_code',
        'hire_date',
        'termination_date',
        'salary',
        'employee_notes',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_holder_name',
        'bank_iban',
        'is_employee',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_community_contributor' => 'boolean',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'referred_at' => 'datetime',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'salary' => 'decimal:2',
            'is_employee' => 'boolean',
            'must_change_password' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'onboarding_preferences' => 'array',
        ];
    }

    /** مساهم في مجتمع البيانات فقط */
    public const COMMUNITY_CONTRIBUTOR_TYPE_DATA = 'data';

    /** مساهم في الذكاء الاصطناعي (Model Zoo، نماذج، إلخ) */
    public const COMMUNITY_CONTRIBUTOR_TYPE_AI = 'ai';

    public function isCommunityDataContributor(): bool
    {
        return $this->community_contributor_type === self::COMMUNITY_CONTRIBUTOR_TYPE_DATA;
    }

    public function isCommunityAiContributor(): bool
    {
        return $this->community_contributor_type === self::COMMUNITY_CONTRIBUTOR_TYPE_AI;
    }

    public function contributorProfile()
    {
        return $this->hasOne(ContributorProfile::class);
    }

    /**
     * رابط صورة الملف الشخصي.
     * الصور في storage/app/public تُعرض عبر Storage::disk('public')->url() لضمان الرابط الصحيح.
     * تطبيع المسار (backslash على Windows) وضمان URL كامل.
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (empty($this->profile_image)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($this->profile_image));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $base = $path;
        } else {
            $base = UserProfileImageStorage::publicUrl($path);
            if ($base === null) {
                $base = CloudStorage::localPublicStorageUrl($path);
            }
        }
        $ts = $this->updated_at ? $this->updated_at->timestamp : '';

        return $base.(str_contains($base, '?') ? '&' : '?').'v='.$ts;
    }

    /**
     * هل هذا المستخدم مشمول بإلزام المصادقة الثنائية عند تفعيل الخيار من إعدادات النظام (.env أو لوحة التحكم).
     * يقتصر على المدير العام والأدمن فقط — لا يشمل المدربين ولا بقية الأدوار.
     */
    public function requiresTwoFactor(): bool
    {
        if (! \App\Services\PlatformSecuritySettings::isAdminTwoFactorRequired()) {
            return false;
        }

        return in_array((string) $this->role, ['super_admin', 'admin'], true);
    }

    /**
     * هل يستخدم هذا المستخدم 2FA عبر البريد (بدون تطبيق TOTP)
     */
    public function usesEmailTwoFactor(): bool
    {
        return $this->requiresTwoFactor() && ! $this->hasTwoFactorEnabled();
    }

    /**
     * هل المصادقة الثنائية مفعّلة للمستخدم
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    /**
     * علاقة مع ولي الأمر
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * علاقة مع الأطفال (للوالدين) — عبر parent_id
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * الطلاب المرتبطون بولي الأمر (جدول parent_students)
     */
    public function linkedStudents()
    {
        return $this->belongsToMany(User::class, 'parent_students', 'parent_id', 'student_id')
            ->withPivot('relation', 'is_primary')
            ->withTimestamps();
    }

    /**
     * أولياء الأمور المرتبطون بالطالب
     */
    public function guardians()
    {
        return $this->belongsToMany(User::class, 'parent_students', 'student_id', 'parent_id')
            ->withPivot('relation', 'is_primary')
            ->withTimestamps();
    }

    /**
     * علاقة مع السنة الدراسية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * علاقة مع تسجيلات الكورسات
     */
    public function courseEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'user_id');
    }

    /**
     * طلاب يشرف عليهم هذا الموظف كمشرف أكاديمي.
     */
    public function supervisedStudentsAsAcademic()
    {
        return $this->belongsToMany(User::class, 'academic_supervisor_students', 'supervisor_id', 'student_id')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    /**
     * المشرفون الأكاديميون المعيّنون لهذا الطالب.
     */
    public function academicSupervisors()
    {
        return $this->belongsToMany(User::class, 'academic_supervisor_students', 'student_id', 'supervisor_id')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    /**
     * اشتراكات المستخدم (باقات المعلمين وغيرها)
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * الاشتراك النشط الحالي (باقة معلم مفعلة ولم تنتهِ)
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderByDesc('end_date')
            ->first();
    }

    /**
     * مفاتيح ميزات الاشتراك النشط بعد التطبيع، مع استنتاج الباقة من teacher_plan_key إذا كانت features فارغة (بيانات قديمة).
     *
     * @return list<string>
     */
    public function subscriptionResolvedFeatureKeys(): array
    {
        $sub = $this->activeSubscription();
        if (! $sub) {
            return [];
        }

        $fromDb = $this->normalizeSubscriptionFeaturesRaw($sub->features);

        if ($fromDb !== []) {
            return Subscription::normalizeFeatureKeys($fromDb);
        }

        if (is_string($sub->teacher_plan_key) && in_array($sub->teacher_plan_key, ['teacher_starter', 'teacher_pro'], true)) {
            $defaults = SubscriptionRequest::planDefaults($sub->teacher_plan_key);
            $feat = $defaults['features'] ?? [];

            return Subscription::normalizeFeatureKeys(is_array($feat) ? $feat : []);
        }

        return [];
    }

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    private function normalizeSubscriptionFeaturesRaw($raw): array
    {
        if (! is_array($raw) || $raw === []) {
            return [];
        }

        if (! array_is_list($raw)) {
            $raw = array_keys(array_filter($raw, static fn ($v) => (bool) $v));
        }

        $out = [];
        foreach ($raw as $f) {
            if (! is_string($f)) {
                continue;
            }
            $t = trim($f);
            if ($t === '' || $t === 'zoom_access') {
                continue;
            }
            $out[] = $t;
        }

        return $out;
    }

    /**
     * هل لدى المستخدم ميزة معينة من اشتراكه النشط (مثل teacher_profile, library_access)
     */
    public function hasSubscriptionFeature(string $featureKey): bool
    {
        return in_array($featureKey, $this->subscriptionResolvedFeatureKeys(), true);
    }

    /**
     * صفحة «استخدامات AI»: اشتراك يتضمن أدوات AI، وصلاحية RBAC عند وجود ربط user_roles.
     *
     * حسابات قديمة بدون صفوف user_roles تعتمد على عمود role فقط — يُسمَح لها بالوصول إن وُجدت الميزة في الباقة.
     */
    public function canAccessStudentAiUsages(): bool
    {
        if (! $this->isStudent()) {
            return false;
        }

        $keys = $this->subscriptionResolvedFeatureKeys();
        $hasAiPackage = in_array('full_ai_suite', $keys, true) || in_array('ai_tools', $keys, true);
        if (! $hasAiPackage) {
            return false;
        }

        if ($this->hasPermission('student.view.ai-usages')) {
            return true;
        }

        if (! $this->roles()->exists()) {
            return true;
        }

        return false;
    }

    /** ألعاب HTML المحفوظة من المساعد الذكي */
    public function savedAiGames()
    {
        return $this->hasMany(StudentSavedAiGame::class, 'user_id');
    }

    /**
     * علاقة مع اتفاقيات المدرب
     */
    public function instructorAgreements()
    {
        return $this->hasMany(InstructorAgreement::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function studentLearningProfile()
    {
        return $this->hasOne(StudentLearningProfile::class);
    }

    public function tutorAvailabilities()
    {
        return $this->hasMany(TutorAvailability::class, 'instructor_id');
    }

    public function lessonBookingsAsStudent()
    {
        return $this->hasMany(LessonBooking::class, 'student_id');
    }

    public function lessonBookingsAsInstructor()
    {
        return $this->hasMany(LessonBooking::class, 'instructor_id');
    }

    public function tutorWorkLogs()
    {
        return $this->hasMany(TutorWorkLog::class, 'instructor_id');
    }

    public function agreementPayments()
    {
        return $this->hasMany(AgreementPayment::class, 'instructor_id');
    }

    public function payoutDetail()
    {
        return $this->hasOne(InstructorPayoutDetail::class);
    }

    /**
     * علاقة مع محاولات الامتحان
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * علاقة مع التقارير كطالب
     */
    public function studentReports()
    {
        return $this->hasMany(StudentReport::class, 'student_id');
    }

    /**
     * علاقة مع التقارير كولي أمر
     */
    public function parentReports()
    {
        return $this->hasMany(StudentReport::class, 'parent_id');
    }

    /**
     * علاقة مع رسائل الواتساب
     */
    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * علاقة مع الإشعارات المخصصة (تجاوز Laravel's built-in)
     */
    public function customNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * تجاوز علاقة notifications الافتراضية
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * علاقة مع محفظة المستخدم المالية
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * التحقق من كون المستخدم طالب
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * التحقق من كون المستخدم مدرب
     */
    public function isInstructor(): bool
    {
        return in_array((string) $this->role, ['instructor', 'teacher'], true);
    }

    /**
     * التحقق من كون المستخدم مدير عام
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * التحقق من كون المستخدم إداري (للتوافق مع الكود القديم)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * التحقق من كون المستخدم معلّم/مدرب (للتوافق مع الكود القديم + دور teacher)
     */
    public function isTeacher(): bool
    {
        return in_array((string) $this->role, ['instructor', 'teacher'], true);
    }

    public function isParent(): bool
    {
        return (string) $this->role === 'parent';
    }

    /** دخول الصفحة العامة (/login) — طالب أو ولي أمر فقط */
    public function canUsePublicLoginPortal(): bool
    {
        return $this->isStudent() || $this->isParent();
    }

    /** دخول فريق العمل (/staff/login) — إدارة، مدربون، موظفون */
    public function canUseStaffLoginPortal(): bool
    {
        if ($this->isStudent() || $this->isParent()) {
            return false;
        }

        if ($this->isEmployee()) {
            return true;
        }

        return in_array((string) $this->role, ['super_admin', 'admin', 'instructor', 'teacher'], true);
    }

    /**
     * scope للطلاب
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeParents($query)
    {
        return $query->where('role', 'parent');
    }

    /**
     * scope للمدربين
     */
    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * scope للمدربين (للتوافق مع الكود القديم)
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * المستخدمون الذين يمكن تعيينهم كمعلم/مقدم لجلسة بث مباشر:
     * أدمن/مدرب داخلي، أو طالب مشترك لديه اشتراك نشط (المعلم = المشترك عندنا).
     */
    public function scopeCanHostLiveSession($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('role', ['instructor', 'teacher'])
                ->orWhere(function ($q2) {
                    $q2->where('role', 'student')
                        ->whereHas('subscriptions', function ($sub) {
                            $sub->where('status', 'active')
                                ->where(function ($d) {
                                    $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                                });
                        });
                });
        });
    }

    /**
     * scope للمستخدمين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على الكورسات النشطة للطالب
     */
    public function activeCourses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'student_course_enrollments', 'user_id', 'advanced_course_id')
            ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at'])
            ->where('student_course_enrollments.status', 'active')
            ->orderByDesc('student_course_enrollments.activated_at')
            ->orderByDesc('student_course_enrollments.created_at');
    }

    /**
     * التحقق من التسجيل في كورس أونلاين
     */
    public function isEnrolledIn($courseId): bool
    {
        return $this->courseEnrollments()
            ->where('advanced_course_id', $courseId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * الحصول على تسجيل الكورس
     */
    public function getCourseEnrollment($courseId)
    {
        return $this->courseEnrollments()
            ->where('advanced_course_id', $courseId)
            ->first();
    }

    /**
     * الحصول على آخر تقرير شهري
     */
    public function getLastMonthlyReport()
    {
        return $this->studentReports()
            ->where('report_type', 'monthly')
            ->latest()
            ->first();
    }

    /**
     * الحصول على متوسط الدرجات
     */
    public function getAverageScore()
    {
        return $this->examAttempts()
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;
    }

    /**
     * الحصول على عدد الامتحانات المكتملة
     */
    public function getCompletedExamsCount()
    {
        return $this->examAttempts()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * تحديث آخر دخول بدون تفعيل Observers
     */
    public function updateLastLogin()
    {
        // استخدام DB مباشرة لتجنب أي مشاكل
        \DB::table('users')
            ->where('id', $this->id)
            ->update(['last_login_at' => now(), 'updated_at' => now()]);
    }

    /**
     * العلاقة مع الأدوار (نظام الصلاحيات المخصص)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * الحصول على جميع الصلاحيات للمستخدم (من الأدوار)
     */
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    /**
     * كل أسماء الصلاحيات في الجدول التي تُعدّ مطابقة للاسم المطلوب (حديث + قديم).
     *
     * @return list<string>
     */
    public static function permissionNamesToCheck(string $permissionName): array
    {
        $legacy = config('permission_aliases.legacy_names_for_canonical.'.$permissionName, []);

        return array_values(array_unique(array_merge([$permissionName], $legacy)));
    }

    /** @var list<string>|null */
    protected ?array $resolvedPermissionNamesCache = null;

    /**
     * هل للمستخدم أدوار RBAC مخصّصة (بعد تحميل العلاقة إن وُجدت).
     */
    public function hasAssignedRbacRoles(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->isNotEmpty();
    }

    /**
     * كل أسماء الصلاحيات الممنوحة (مباشرة + من الأدوار) — مرة واحدة لكل طلب HTTP.
     *
     * @return list<string>
     */
    public function resolvedPermissionNames(): array
    {
        if ($this->resolvedPermissionNamesCache !== null) {
            return $this->resolvedPermissionNamesCache;
        }

        if ($this->isAdmin() && ! $this->hasAssignedRbacRoles()) {
            return $this->resolvedPermissionNamesCache = ['__all__'];
        }

        $this->loadMissing(['roles.permissions', 'directPermissions']);

        $names = $this->directPermissions
            ->pluck('name')
            ->merge($this->roles->flatMap(fn ($role) => $role->permissions->pluck('name')))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $this->resolvedPermissionNamesCache = $names;
    }

    protected function grantsPermission(string $permissionName): bool
    {
        $granted = $this->resolvedPermissionNames();
        if (in_array('__all__', $granted, true)) {
            return true;
        }

        $check = self::permissionNamesToCheck($permissionName);

        return (bool) array_intersect($check, $granted);
    }

    /**
     * هل يمكن للمستخدم الدخول إلى لوحة الإدارة (صلاحية admin.access أو سوبر أدمن بدون أدوار).
     */
    public function userHasAdminAccessCapability(): bool
    {
        if ($this->isAdmin() && ! $this->hasAssignedRbacRoles()) {
            return true;
        }

        // يتماشى مع EnsurePermission لـ permission:admin.access: موظف له دور RBAC يُسمح له بمجموعة admin ثم يُقيَّد لاحقاً
        if ($this->is_employee && $this->hasAssignedRbacRoles()) {
            return true;
        }

        return $this->grantsPermission('admin.access');
    }

    /**
     * Gate:: و can('manage.orders') يعتمدان على الصلاحيات المخزّنة للأدوار/المباشرة (مثل hasPermission).
     * بدون هذا، الموظف ذو الدور يمرّ بوسيط RBAC ثم يُرفض داخل المتحكم.
     *
     * @param  string|iterable  $abilities
     * @param  mixed  $arguments
     */
    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities) && $arguments === [] && str_contains($abilities, '.')) {
            if ($this->hasPermission($abilities)) {
                return true;
            }
        }

        return parent::can($abilities, $arguments);
    }

    /**
     * التحقق من وجود صلاحية معينة (من الأدوار أو المباشرة)
     */
    public function hasPermission($permissionName)
    {
        if ($this->isAdmin() && ! $this->hasAssignedRbacRoles()) {
            return true;
        }

        if ($permissionName === 'view.dashboard' && $this->userHasAdminAccessCapability()) {
            return true;
        }

        return $this->grantsPermission((string) $permissionName);
    }

    /**
     * التحقق من وجود أي صلاحية من القائمة (للـ Blade والـ sidebar)
     */
    public function hasAnyPermission(...$permissionNames): bool
    {
        foreach ($permissionNames as $name) {
            if ($this->hasPermission($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من وجود دور معين
     */
    public function hasRole($roleName)
    {
        if (strtolower((string) $this->role) === strtolower((string) $roleName)) {
            return true;
        }

        $this->loadMissing('roles');

        return $this->roles->contains(fn ($role) => strtolower((string) $role->name) === strtolower((string) $roleName));
    }

    /**
     * إضافة دور للمستخدم
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role && ! $this->hasRole($role->name)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * إزالة دور من المستخدم
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * العلاقة المباشرة مع الصلاحيات (بدون أدوار)
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * الحصول على جميع الصلاحيات (من الأدوار + المباشرة)
     */
    public function getAllPermissions()
    {
        $rolePermissions = $this->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->unique('id');

        $directPermissions = $this->directPermissions;

        return $rolePermissions->merge($directPermissions)->unique('id');
    }

    /**
     * علاقة مع وظيفة الموظف
     */
    public function employeeJob()
    {
        return $this->belongsTo(EmployeeJob::class, 'employee_job_id');
    }

    /**
     * علاقة مع مهام الموظف
     */
    public function employeeTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'employee_id');
    }

    /**
     * علاقة مع اتفاقيات الموظف
     */
    public function employeeAgreements()
    {
        return $this->hasMany(EmployeeAgreement::class, 'employee_id');
    }

    /**
     * علاقة مع خصومات الراتب
     */
    public function salaryDeductions()
    {
        return $this->hasMany(EmployeeSalaryDeduction::class, 'employee_id');
    }

    /**
     * علاقة مع مدفوعات الراتب
     */
    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class, 'employee_id');
    }

    /**
     * علاقة مع المهام المكلف بها
     */
    public function assignedTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'assigned_by');
    }

    /**
     * علاقة مع طلبات الإجازة
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    /**
     * سجل أحداث الموارد البشرية المرتبطة بهذا الموظف
     */
    public function hrEmployeeEvents()
    {
        return $this->hasMany(HrEmployeeEvent::class, 'employee_id')->latest('event_date')->latest('id');
    }

    /**
     * طلبات الكورسات المسندة للموظف كمندوب مبيعات
     */
    public function salesOwnedOrders()
    {
        return $this->hasMany(Order::class, 'sales_owner_id');
    }

    /**
     * التحقق من كون المستخدم موظف
     */
    public function isEmployee(): bool
    {
        return $this->is_employee === true;
    }

    /**
     * Scope للموظفين
     */
    public function scopeEmployees($query)
    {
        return $query->where('is_employee', true);
    }

    /**
     * رمز وظيفة الموظف (accountant, hr, sales, supervisor, academic_supervisor, general_supervision, …)
     */
    public function employeeJobCode(): ?string
    {
        if (! $this->is_employee || ! $this->relationLoaded('employeeJob')) {
            $this->load('employeeJob');
        }

        return $this->employeeJob?->code;
    }

    /**
     * تحويل مفتاح عنصر قائمة الموظف (مثل desk_accountant) إلى اسم صلاحية RBAC في جدول permissions
     * (مثل manage.invoices)، بنفس منطق config/employee_sidebar.php.
     */
    public static function rbacPermissionForEmployeeMenuKey(string $key): string
    {
        $items = config('employee_sidebar.items', []);
        $meta = $items[$key] ?? null;
        if (is_array($meta) && array_key_exists('permission', $meta) && $meta['permission'] !== null) {
            return $meta['permission'];
        }

        return $key;
    }

    /**
     * هل الموظف يملك صلاحية عرض خانة في السايدبار.
     *
     * الأولوية:
     * 1. عناصر أساسية (dashboard, profile, notifications, settings) → دائماً مسموح.
     * 2. إذا كان للمستخدم أدوار RBAC مخصصة → نعتمد على hasPermission() فقط.
     * 3. إذا لم يكن للمستخدم أدوار RBAC → نعود للصلاحيات المحددة في EmployeeJob.
     */
    public function employeeCan(string $permission): bool
    {
        if (! $this->is_employee) {
            return false;
        }

        // عناصر أساسية متاحة لكل موظف بغض النظر عن صلاحياته
        $alwaysAllowed = ['dashboard', 'profile', 'notifications', 'settings'];
        if (in_array($permission, $alwaysAllowed, true)) {
            return true;
        }

        // إذا كان للمستخدم أدوار RBAC مخصصة → اعتمد عليها فقط
        if ($this->roles()->exists()) {
            if ($permission === 'tasks') {
                return $this->hasPermission('manage.tasks') || $this->hasPermission('view.tasks');
            }
            if ($permission === 'reports') {
                return $this->hasPermission('view.statistics')
                    || $this->hasPermission('view.reports')
                    || $this->hasPermission('view.financial-reports')
                    || $this->hasPermission('view.academic-reports');
            }

            $rbacName = self::rbacPermissionForEmployeeMenuKey($permission);

            return $this->hasPermission($rbacName);
        }

        // لا يوجد دور RBAC → اعتمد على صلاحيات وظيفة الموظف (النظام القديم)
        if (! $this->relationLoaded('employeeJob')) {
            $this->load('employeeJob');
        }
        $job = $this->employeeJob;
        if (! $job) {
            // بدون وظيفة ولا RBAC: اعرض كل شيء (مدير يدوي)
            return true;
        }
        $jobPermissions = $job->permissions;
        if (! is_array($jobPermissions) || empty($jobPermissions)) {
            // وظيفة بدون صلاحيات محددة → لا تُعرَض الأقسام الإضافية
            return false;
        }

        return in_array($permission, $jobPermissions, true);
    }

    /**
     * التحقق من وجود صلاحية معينة (من الأدوار أو المباشرة)
     */
    public function hasPermissionDirect($permissionName)
    {
        return $this->hasPermission($permissionName);
    }

    /**
     * علاقة مع الإحالات (كمحيل)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * علاقة مع الإحالة (كمحال)
     */
    public function referral()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * علاقة مع المستخدم الذي أحاله
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * علاقة مع المستخدمين المحالين
     */
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * علاقة مع تسجيلات المسارات التعليمية
     */
    public function learningPathEnrollments()
    {
        return $this->hasMany(LearningPathEnrollment::class, 'user_id');
    }

    /**
     * علاقة مع المسارات التعليمية التي يدرب فيها
     */
    public function teachingLearningPaths()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_instructors', 'instructor_id', 'academic_year_id')
            ->withPivot('assigned_courses', 'notes')
            ->withTimestamps();
    }
}
