<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wrapper { width: 100%; background: #f8fafc; padding: 28px 14px; }
        .card { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: #0f172a; padding: 26px 24px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 22px; font-weight: 800; letter-spacing: 0.04em; }
        .header p { margin: 8px 0 0; color: #94a3b8; font-size: 13px; }
        .body { padding: 26px 24px; color: #334155; font-size: 15px; line-height: 1.75; }
        .greeting { margin: 0 0 14px; color: #0f172a; font-weight: 700; font-size: 16px; }
        .title { margin: 0 0 12px; color: #0f172a; font-size: 17px; font-weight: 800; }
        .btn { display: inline-block; margin-top: 18px; background: #0f172a; color: #fff !important; text-decoration: none; padding: 11px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; }
        .footer { background: #f1f5f9; border-top: 1px solid #e2e8f0; padding: 16px 24px; text-align: center; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>{{ config('app.name', 'Sana') }}</h1>
            <p>إشعار من مركز الإشعارات</p>
        </div>
        <div class="body">
            <p class="greeting">مرحباً{{ $recipientName ? ' '.$recipientName : '' }}،</p>
            <p class="title">{{ $subjectLine }}</p>
            <div>{!! nl2br(e($body)) !!}</div>
            @if(!empty($actionUrl))
                <a class="btn" href="{{ $actionUrl }}" target="_blank" rel="noopener noreferrer">{{ $actionText ?: 'فتح الرابط' }}</a>
            @endif
        </div>
        <div class="footer">
            وصلك هذا البريد لأن حسابك مسجّل في {{ config('app.name', 'Sana') }}. يمكنك أيضاً مراجعة الإشعار من جرس الإشعارات داخل المنصة.
        </div>
    </div>
</div>
</body>
</html>
