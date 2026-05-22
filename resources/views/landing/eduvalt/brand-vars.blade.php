@php($c = config('brand.colors'))
    :root {
        /* أزرق — اللون الرئيسي (أزرار، روابط، شريط التقدّم) */
        --edu-primary: {{ $c['blue'] }};
        --edu-primary-dark: {{ $c['blue_dark'] }};
        --edu-primary-light: {{ $c['blue_light'] }};
        --edu-primary-rgb: {{ $c['blue_rgb'] }};

        /* بنفسجي — تمييز وثانوي */
        --edu-purple: {{ $c['purple'] }};
        --edu-purple-dark: {{ $c['purple_dark'] }};
        --edu-purple-light: {{ $c['purple_light'] }};
        --edu-purple-rgb: {{ $c['purple_rgb'] }};

        /* أصفر — تمييز وشارات */
        --edu-accent: {{ $c['yellow'] }};
        --edu-accent-dark: {{ $c['yellow_dark'] }};
        --edu-accent-light: {{ $c['yellow_light'] }};
        --edu-accent-rgb: {{ $c['yellow_rgb'] }};

        --edu-navy: #0f172a;
        --edu-text: #1e293b;
        --edu-muted: #64748b;
        --edu-bg: #f8fafc;
        --edu-radius: 24px;
        --edu-radius-sm: 16px;
        --edu-shadow: 0 12px 40px -12px rgba({{ $c['blue_rgb'] }}, 0.22);
        --edu-font: 'IBM Plex Sans Arabic', system-ui, sans-serif;
        --edu-gradient-cta: linear-gradient(135deg, {{ $c['blue'] }} 0%, {{ $c['purple'] }} 55%, #5b8ef5 100%);
        --edu-gradient-auth: linear-gradient(145deg, #0b1f3a 0%, {{ $c['blue'] }} 42%, {{ $c['purple'] }} 100%);
    }
