<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم تفعيل حسابك</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #334155; }
        .box { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
        h1 { font-size: 1.35rem; color: #0f172a; margin: 0 0 16px; }
        p { margin: 0 0 12px; font-size: 0.95rem; line-height: 1.7; }
        .card { background: #ecfdf5; border-radius: 12px; padding: 14px 16px; margin: 16px 0; border-right: 4px solid #10b981; }
        .label { font-size: 0.75rem; color: #047857; margin-bottom: 4px; font-weight: 600; }
        .value { font-weight: 700; color: #064e3b; direction: ltr; text-align: right; }
        .btn { display: inline-block; margin-top: 18px; padding: 12px 24px; background: #0ea5e9; color: #fff !important; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.95rem; }
        .note { font-size: 0.8125rem; color: #64748b; margin-top: 22px; }
        .admin-note { background: #f8fafc; border-radius: 12px; padding: 12px 14px; margin: 14px 0; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="box">
        <h1>تم تفعيل حسابك كمعلم</h1>
        <p>مرحباً {{ $user->name }}،</p>
        <p>
            نهنئك! تم تفعيل الحساب المخصص لك في
            <strong>{{ config('app.name', 'أكاديمية سنا') }}</strong>
            ويمكنك الآن تسجيل الدخول إلى بوابة المعلمين والبدء بالعمل.
        </p>

        <div class="card">
            <div class="label">البريد الإلكتروني لتسجيل الدخول</div>
            <div class="value">{{ $user->email }}</div>
        </div>

        @if(!empty($adminNote))
            <div class="admin-note">
                <div class="label" style="color:#64748b">ملاحظة من الإدارة</div>
                <p style="margin:0">{{ $adminNote }}</p>
            </div>
        @endif

        <p>استخدم نفس البريد الإلكتروني وكلمة المرور التي أنشأتها عند التقديم.</p>

        <a href="{{ url(route('staff.login')) }}" class="btn">تسجيل الدخول الآن</a>

        <p class="note">
            إذا لم تطلب هذا التفعيل أو واجهت مشكلة في الدخول، تواصل مع الدعم عبر
            {{ config('mail.from.address', 'info@sanaedu.com') }}.
        </p>
    </div>
</body>
</html>
