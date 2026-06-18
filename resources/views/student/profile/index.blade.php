@extends('layouts.app')

@section('title', __('student.profile_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $roleLabels = [
        'student' => __('student.student_role'),
        'parent' => 'ولي أمر',
        'teacher' => __('student.teacher_role'),
        'instructor' => __('student.instructor_role'),
        'admin' => __('student.admin_role_label'),
        'super_admin' => __('student.super_admin_role'),
    ];
    $roleLabel = $roleLabels[$user->role] ?? __('student.user_role');

    $memberSince = ($user->created_at instanceof \Carbon\CarbonInterface)
        ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y')
        : null;

    $coursesCount = method_exists($user, 'courseEnrollments') ? $user->courseEnrollments()->count() : 0;
    $notificationsCount = method_exists($user, 'customNotifications') ? $user->customNotifications()->count() : 0;

    $lastLogin = ($user->last_login_at instanceof \Carbon\CarbonInterface)
        ? $user->last_login_at->copy()->locale('ar')->diffForHumans()
        : null;
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.profile_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.profile_subtitle') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('dashboard') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-arrow-right"></i>
                لوحة التحكم
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="sanua-alert sanua-alert--info">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="sanua-profile-hero">
        <div class="sanua-profile-avatar">
            @if($user->profile_image)
                <img src="{{ $user->profile_image_url }}" alt="{{ __('student.profile_image_alt') }}">
            @else
                {{ mb_substr($user->name, 0, 1) }}
            @endif
        </div>
        <div class="sanua-profile-hero__main">
            <span class="sanua-badge sanua-badge--submitted" style="background:rgba(255,255,255,0.2);color:#fff;border:none;">
                <i class="fas fa-user-graduate"></i>
                {{ $roleLabel }}
            </span>
            <h2 class="sanua-profile-hero__name">{{ $user->name }}</h2>
            <p class="sanua-profile-hero__sub">{{ __('student.profile_subtitle') }}</p>
            <div class="sanua-profile-hero__chips">
                <span class="sanua-profile-chip"><i class="fas fa-phone"></i> {{ $user->phone ?? '—' }}</span>
                @if($user->email)
                    <span class="sanua-profile-chip"><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                @endif
                <span class="sanua-profile-chip"><i class="fas fa-id-badge"></i> #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-calendar-week"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $memberSince ?: '—' }}</strong>
                <span>{{ __('student.join_date_label') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-layer-group"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $coursesCount }}</strong>
                <span>{{ __('student.active_courses_count') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-bell"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $notificationsCount }}</strong>
                <span>{{ __('student.notifications') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $lastLogin ?: '—' }}</strong>
                <span>{{ __('student.last_login_label') }}</span>
            </div>
        </div>
    </div>

    <div class="sanua-profile-layout">
        <aside class="space-y-4">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>معلومات الاتصال</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-id-badge"></i> رقم العضوية</span>
                        <span class="sanua-profile-info-row__value">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-user-shield"></i> نوع الحساب</span>
                        <span class="sanua-profile-info-row__value">
                            <span class="sanua-badge sanua-badge--submitted">{{ $roleLabel }}</span>
                        </span>
                    </div>
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-signal"></i> الحالة</span>
                        <span class="sanua-profile-info-row__value">
                            <span class="sanua-badge {{ $user->is_active ? 'sanua-badge--approved' : 'sanua-badge--rejected' }}">
                                {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </span>
                    </div>
                    <div class="sanua-alert sanua-alert--info" style="margin:12px 0 0;">
                        <i class="fas fa-shield-halved ml-1"></i>
                        يمكنك تحسين أمان حسابك بتفعيل التحقق بخطوتين (قريباً).
                    </div>
                </div>
            </div>

            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>نصائح سريعة</h3></div>
                <div class="sanua-panel__body">
                    <ul class="sanua-tip-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>حدّث معلومات التواصل</strong>
                                احرص على أن يكون بريدك ورقم هاتفك محدثين لاستقبال الإشعارات.
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-lock"></i>
                            <div>
                                <strong>أنشئ كلمة مرور قوية</strong>
                                استخدم مزيجاً من الأحرف والأرقام وغيّرها بشكل دوري.
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-bell"></i>
                            <div>
                                <strong>تابع الإشعارات</strong>
                                ابقَ على اطلاع بالكورسات والتنبيهات المهمة.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <div class="space-y-4">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3>تحديث البيانات الأساسية</h3>
                </div>
                <div class="sanua-panel__body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="sanua-form-grid">
                            <div class="sanua-field">
                                <label for="name">الاسم الكامل</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')<p class="sanua-field__error">{{ $message }}</p>@enderror
                            </div>
                            <div class="sanua-field">
                                <label for="phone">رقم الهاتف</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')<p class="sanua-field__error">{{ $message }}</p>@enderror
                            </div>
                            <div class="sanua-field sanua-field--full">
                                <label for="email">البريد الإلكتروني (اختياري)</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
                                @error('email')<p class="sanua-field__error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="sanua-field sanua-field--full" style="margin-top:16px;">
                            <label>صورة الملف الشخصي</label>
                            <div class="sanua-upload-box">
                                <div class="sanua-upload-preview">
                                    @if($user->profile_image)
                                        <img src="{{ $user->profile_image_url }}" alt="{{ __('student.profile_image_alt') }}">
                                    @else
                                        <i class="fas fa-camera"></i>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:180px;">
                                    <input type="file" name="profile_image" accept="image/*">
                                    <p style="margin:6px 0 0;font-size:0.72rem;font-weight:600;color:#94a3b8;">PNG أو JPG — الحد الأقصى حسب إعدادات المنصة.</p>
                                    @error('profile_image')<p class="sanua-field__error">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="sanua-password-box" style="margin-top:16px;">
                            <h4 style="margin:0 0 4px;font-size:0.88rem;font-weight:900;color:#1e1b4b;">تغيير كلمة المرور</h4>
                            <p style="margin:0 0 12px;font-size:0.72rem;font-weight:600;color:#94a3b8;">اترك الحقول فارغة إذا لم ترغب في التغيير.</p>
                            <div class="sanua-form-grid">
                                <div class="sanua-field">
                                    <label for="current_password">كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" id="current_password">
                                    @error('current_password')<p class="sanua-field__error">{{ $message }}</p>@enderror
                                </div>
                                <div class="sanua-field">
                                    <label for="password">كلمة المرور الجديدة</label>
                                    <input type="password" name="password" id="password">
                                    @error('password')<p class="sanua-field__error">{{ $message }}</p>@enderror
                                </div>
                                <div class="sanua-field">
                                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="sanua-form-actions">
                            <p style="margin:0;font-size:0.72rem;font-weight:600;color:#94a3b8;">
                                <i class="fas fa-info-circle" style="color:#8B5CF6;"></i>
                                سيتم إرسال إشعار عند تغيير كلمة المرور.
                            </p>
                            <button type="submit" class="sanua-btn sanua-btn--purple">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>نشاط الحساب الأخير</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-activity-row">
                        <div class="sanua-activity-row__main">
                            <span class="sanua-activity-row__icon"><i class="fas fa-desktop"></i></span>
                            <div>
                                <p class="sanua-activity-row__title">آخر نشاط للنظام</p>
                                <p class="sanua-activity-row__sub">تم تسجيل الدخول بنجاح</p>
                            </div>
                        </div>
                        <span style="font-size:0.72rem;font-weight:800;color:#64748b;">{{ $lastLogin ?: 'قبل قليل' }}</span>
                    </div>
                    <div class="sanua-activity-row">
                        <div class="sanua-activity-row__main">
                            <span class="sanua-activity-row__icon" style="background:linear-gradient(135deg,#059669,#10B981);"><i class="fas fa-shield-heart"></i></span>
                            <div>
                                <p class="sanua-activity-row__title">أمان الحساب</p>
                                <p class="sanua-activity-row__sub">ننصح بتحديث كلمة المرور كل 90 يوماً.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
