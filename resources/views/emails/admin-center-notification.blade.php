<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wrapper { width: 100%; background: #f1f5f9; padding: 28px 14px; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(15,23,42,.08); }
        .header { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); padding: 28px 24px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 20px; font-weight: 800; }
        .header p { margin: 8px 0 0; color: rgba(255,255,255,.9); font-size: 13px; }
        .body { padding: 26px 24px; color: #334155; font-size: 15px; line-height: 1.75; }
        .greeting { margin: 0 0 14px; color: #0f172a; font-weight: 700; font-size: 16px; }
        .title { margin: 0 0 12px; color: #0f172a; font-size: 17px; font-weight: 800; }
        .btn { display: inline-block; margin-top: 18px; background: #2563eb; color: #fff !important; text-decoration: none; padding: 11px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; text-align: center; color: #64748b; font-size: 12px; }
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
