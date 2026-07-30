@include('emails.partials.layout-start', [
    'preheader' => 'تم تفعيل حسابك كمعلم في '.config('app.name', 'Sana'),
    'title' => 'تم تفعيل حسابك',
])

<tr>
    <td style="padding:28px 28px 8px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#0f172a;font-size:22px;font-weight:800;line-height:1.4;text-align:right;">
        تم تفعيل حسابك كمعلم
    </td>
</tr>
<tr>
    <td style="padding:0 28px 16px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#475569;font-size:15px;line-height:1.8;text-align:right;">
        مرحباً {{ $user->name }}،
        <br>
        نهنئك! تم تفعيل حسابك في
        <strong style="color:#0f172a;">{{ config('app.name', 'أكاديمية سنا') }}</strong>
        ويمكنك الآن الدخول إلى بوابة المعلمين والبدء بالعمل.
    </td>
</tr>
<tr>
    <td style="padding:0 28px 20px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ecfdf5;border-radius:12px;border-right:4px solid #10b981;">
            <tr>
                <td style="padding:16px 18px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;text-align:right;">
                    <div style="font-size:12px;color:#047857;font-weight:700;margin-bottom:6px;">البريد الإلكتروني لتسجيل الدخول</div>
                    <div style="font-size:16px;color:#064e3b;font-weight:800;direction:ltr;text-align:right;">{{ $user->email }}</div>
                </td>
            </tr>
        </table>
    </td>
</tr>
@if(!empty($adminNote))
<tr>
    <td style="padding:0 28px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
            <tr>
                <td style="padding:14px 16px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;text-align:right;color:#475569;font-size:14px;line-height:1.7;">
                    <div style="font-size:12px;color:#64748b;font-weight:700;margin-bottom:6px;">ملاحظة من الإدارة</div>
                    {{ $adminNote }}
                </td>
            </tr>
        </table>
    </td>
</tr>
@endif
<tr>
    <td style="padding:0 28px 12px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#475569;font-size:14px;line-height:1.8;text-align:right;">
        استخدم نفس البريد الإلكتروني وكلمة المرور التي أنشأتها عند التسجيل.
    </td>
</tr>
<tr>
    <td style="padding:8px 28px 28px;text-align:center;">
        <a href="{{ url(route('staff.login')) }}"
           style="display:inline-block;padding:14px 28px;background:#1D4EDB;color:#ffffff !important;text-decoration:none;border-radius:10px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:15px;font-weight:700;">
            تسجيل الدخول الآن
        </a>
    </td>
</tr>

@include('emails.partials.layout-end')
