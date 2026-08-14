<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subjectLine }}</title>
    <style>
        body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        table { border-collapse: collapse; }
        .wrapper { width: 100%; background-color: #f8fafc; padding: 28px 14px; }
        .container { max-width: 560px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: #0f172a; padding: 26px 24px; text-align: center; }
        .brand { color: #ffffff; font-size: 22px; font-weight: 800; margin: 0; letter-spacing: 0.04em; }
        .sub { color: #94a3b8; font-size: 13px; margin: 8px 0 0; }
        .body-cell { padding: 26px 24px 28px; color: #334155; font-size: 15px; line-height: 1.75; }
        .greeting { color: #0f172a; font-weight: 700; font-size: 16px; margin: 0 0 14px; }
        .footer { background: #f1f5f9; padding: 16px 24px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer-text { color: #64748b; font-size: 12px; margin: 0; }
        .footer-brand { color: #0f172a; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="container">
                        <tr>
                            <td>
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="card">
                                    <tr>
                                        <td class="header">
                                            <p class="brand">{{ config('app.name', 'Sana') }}</p>
                                            <p class="sub">إشعار من المنصة</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="body-cell">
                                            @if($recipientName)
                                                <p class="greeting">مرحباً {{ $recipientName }}،</p>
                                            @else
                                                <p class="greeting">مرحباً،</p>
                                            @endif
                                            <div style="color:#334155;font-size:15px;line-height:1.75;">
                                                {!! nl2br(e($body)) !!}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="footer">
                                            <p class="footer-text">هذه الرسالة من <a href="{{ url('/') }}" class="footer-brand">{{ config('app.name', 'Sana') }}</a>.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
