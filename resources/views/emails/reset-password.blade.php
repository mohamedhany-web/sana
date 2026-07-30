@include('emails.partials.layout-start', [
    'preheader' => 'رابط آمن لإعادة تعيين كلمة المرور — صالح لمدة محدودة',
    'title' => 'إعادة تعيين كلمة المرور',
])

<tr>
    <td style="padding:28px 28px 8px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#0f172a;font-size:22px;font-weight:800;line-height:1.4;text-align:right;">
        إعادة تعيين كلمة المرور
    </td>
</tr>
<tr>
    <td style="padding:0 28px 16px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#475569;font-size:15px;line-height:1.8;text-align:right;">
        مرحباً {{ $user->name ?? '' }}،
        <br>
        تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك في
        <strong style="color:#0f172a;">{{ config('app.name', 'Sana') }}</strong>.
        اضغط الزر أدناه لاختيار كلمة مرور جديدة.
    </td>
</tr>
<tr>
    <td style="padding:8px 28px 20px;text-align:center;">
        <a href="{{ $url }}"
           style="display:inline-block;padding:14px 28px;background:#1D4EDB;color:#ffffff !important;text-decoration:none;border-radius:10px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:15px;font-weight:700;">
            تعيين كلمة مرور جديدة
        </a>
    </td>
</tr>
<tr>
    <td style="padding:0 28px 12px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#64748b;font-size:13px;line-height:1.7;text-align:right;">
        الرابط صالح لمدة <strong>{{ $expire }}</strong> دقيقة تقريباً. إذا لم تطلب إعادة التعيين فتجاهل هذه الرسالة — حسابك آمن.
    </td>
</tr>
<tr>
    <td style="padding:0 28px 28px;font-family:Tahoma,'Segoe UI',Arial,sans-serif;color:#94a3b8;font-size:12px;line-height:1.7;text-align:right;word-break:break-all;">
        أو انسخ الرابط:<br>
        <a href="{{ $url }}" style="color:#1D4EDB;">{{ $url }}</a>
    </td>
</tr>

@include('emails.partials.layout-end')
