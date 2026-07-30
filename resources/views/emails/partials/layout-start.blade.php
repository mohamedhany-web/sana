{{-- بداية قالب بريد متوافق مع Gmail (جداول + أنماط مضمّنة) --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? config('app.name') }}</title>
    <!--[if mso]>
    <style type="text/css">
        table, td { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background:#f1f5f9;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
@if(!empty($preheader))
<div style="display:none;font-size:1px;color:#f1f5f9;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    {{ $preheader }}
</div>
@endif
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;margin:0;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                <tr>
                    <td style="background:linear-gradient(135deg,#6A2CFF 0%,#1D4EDB 100%);padding:22px 28px;text-align:right;">
                        <div style="font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:20px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">
                            {{ config('app.name', 'Sana') }}
                        </div>
                        <div style="font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:12px;color:rgba(255,255,255,.85);margin-top:4px;">
                            أكاديمية تعليمية
                        </div>
                    </td>
                </tr>
