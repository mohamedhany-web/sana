@include('emails.partials.layout-start', [
    'preheader' => 'تم استلام ملف تقديمك وهو قيد مراجعة أكاديمية '.config('app.name', 'Sana'),
    'title' => 'تم استلام بياناتك',
])

<tr>
    <td style="padding:28px 28px 8px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#0f172a;font-size:22px;font-weight:800;line-height:1.4;text-align:right;">
        تم استلام بياناتك بنجاح
    </td>
</tr>
<tr>
    <td style="padding:0 28px 16px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#475569;font-size:15px;line-height:1.8;text-align:right;">
        مرحباً {{ $user->name }}،
        <br>
        شكراً لإكمال ملف التقديم. استلمنا بياناتك في
        <strong style="color:#0f172a;">{{ config('app.name', 'Sana') }}</strong>
        وسيقوم فريق الإدارة بمراجعتها قريباً.
    </td>
</tr>
<tr>
    <td style="padding:0 28px 20px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0ebff;border-radius:12px;border-right:4px solid #6A2CFF;">
            <tr>
                <td style="padding:16px 18px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;text-align:right;">
                    <div style="font-size:12px;color:#6A2CFF;font-weight:700;margin-bottom:6px;">البريد المسجّل</div>
                    <div style="font-size:16px;color:#0f172a;font-weight:800;direction:ltr;text-align:right;">{{ $user->email }}</div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:0 28px 12px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#475569;font-size:14px;line-height:1.8;text-align:right;">
        يمكنك متابعة حالة طلبك من لوحة تحكم المعلمين بعد تسجيل الدخول بنفس البريد وكلمة المرور.
    </td>
</tr>
<tr>
    <td style="padding:8px 28px 28px;text-align:center;">
        <a href="{{ url(route('staff.login')) }}"
           style="display:inline-block;padding:14px 28px;background:#1D4EDB;color:#ffffff !important;text-decoration:none;border-radius:10px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:15px;font-weight:700;">
            الدخول إلى لوحة المعلمين
        </a>
    </td>
</tr>

@include('emails.partials.layout-end')
