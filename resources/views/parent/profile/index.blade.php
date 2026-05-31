@extends('layouts.app')

@section('title', __('parent.profile'))

@section('content')
<div class="par-page space-y-5 sm:space-y-6">
    @if(session('success'))
    <div class="par-flash par-flash--ok flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="par-hero par-card p-4 sm:p-6">
        <h1 class="par-page-title font-heading">
            <i class="fas fa-id-card text-teal-600 ml-2"></i>{{ __('parent.profile') }}
        </h1>
        <p class="par-page-lead">حدّث بياناتك وكلمة المرور لحماية حساب ولي الأمر</p>
    </div>

    <form action="{{ route('parent.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
        @csrf
        @method('PUT')

        <div class="par-form-layout">
            <div class="par-card p-4 sm:p-5 flex flex-col items-center text-center sm:flex-row sm:text-start sm:items-center gap-4 xl:flex-col xl:items-stretch xl:text-center">
                <div class="par-avatar mx-auto xl:mx-auto">
                    @if($user->profile_image)
                        <img src="{{ $profileImageUrl }}" alt="{{ $user->name }}">
                    @else
                        {{ mb_substr($user->name, 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1 xl:flex-none">
                    <p class="font-bold text-lg text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500 truncate mt-0.5" dir="ltr">{{ $user->email }}</p>
                    <span class="par-badge par-badge--teal mt-2">{{ __('parent.guardian_role') }}</span>
                </div>
            </div>

            <div class="par-form-columns">
                <div class="par-card p-4 sm:p-6 space-y-4">
                    <div class="par-section-head">
                        <i class="fas fa-user bg-teal-50 text-teal-600"></i>
                        البيانات الشخصية
                    </div>

                    <div class="par-field">
                        <label for="par-name">الاسم</label>
                        <input type="text" id="par-name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="par-field">
                        <label for="par-email">البريد الإلكتروني</label>
                        <input type="email" id="par-email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    @if(!str_starts_with((string) $user->phone, 'PARENT_'))
                    <div class="par-field">
                        <label for="par-phone">الهاتف</label>
                        <input type="text" id="par-phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    @endif

                    <div class="par-field">
                        <label for="par-avatar">صورة الملف (اختياري)</label>
                        <input type="file" id="par-avatar" name="profile_image" accept="image/*">
                    </div>
                </div>

                <div class="par-card p-4 sm:p-6 space-y-4">
                    <div class="par-section-head">
                        <i class="fas fa-key bg-amber-50 text-amber-600"></i>
                        {{ __('parent.change_password') }}
                    </div>
                    <p class="text-xs text-slate-500 -mt-2 mb-1">اترك الحقول فارغة إذا لا تريد تغيير كلمة المرور</p>

                    <div class="par-field">
                        <label for="par-current-pw">كلمة المرور الحالية</label>
                        <input type="password" id="par-current-pw" name="current_password" autocomplete="current-password">
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="par-field">
                        <label for="par-new-pw">كلمة المرور الجديدة</label>
                        <input type="password" id="par-new-pw" name="password" autocomplete="new-password">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="par-field">
                        <label for="par-confirm-pw">تأكيد كلمة المرور</label>
                        <input type="password" id="par-confirm-pw" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="par-btn par-btn--primary w-full sm:w-auto">
            <i class="fas fa-save"></i> حفظ التغييرات
        </button>
    </form>
</div>
@endsection
