#!/usr/bin/env python3
"""One-off: replace EGP / ج.م with SAR / translation helpers."""
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SKIP = {"storage", "vendor", "node_modules", ".git", "bootstrap/cache", "scripts"}


def should_skip(path: Path) -> bool:
    return bool(SKIP & set(path.parts))


def process_file(path: Path) -> bool:
    try:
        text = path.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        text = path.read_text(encoding="utf-8", errors="replace")
    orig = text

    text = text.replace("__('public.currency_egp')", "__('public.currency')")
    text = text.replace('__("public.currency_egp")', '__("public.currency")')

    text = re.sub(r"'currency'\s*=>\s*'EGP'", "'currency' => currency_code()", text)
    text = re.sub(r'"currency"\s*=>\s*"EGP"', '"currency" => currency_code()', text)
    text = text.replace("config('kashier.currency', 'EGP')", "config('kashier.currency', currency_code())")
    text = text.replace('$currency = \'EGP\'', '$currency = currency_code()')
    text = text.replace("$currency = 'EGP'", '$currency = currency_code()')

    # PHP string suffixes
    text = text.replace(" + ' ج.م'", " . currency_suffix()")
    text = text.replace(".' ج.م'", ". currency_suffix()")
    text = text.replace("' ج.م'", "currency_suffix()")
    text = text.replace('" ج.م"', 'currency_suffix()')
    text = text.replace(" . ' ج.م'", " . currency_suffix()")
    text = text.replace(". ' ج.م'", ". currency_suffix()")
    text = text.replace("' ج.م/شهر'", "currency_suffix().'/شهر'")
    text = text.replace("?? 'EGP'", "?? currency_code()")
    text = text.replace('?? "EGP"', "?? currency_code()")
    text = text.replace("?? 'ج.م'", "?? currency_label()")
    text = text.replace(
        "'يوجد عليك قسط مستحق بقيمة %s ج.م منذ %s'",
        "'يوجد عليك قسط مستحق بقيمة %s%s منذ %s'",
    )
    text = text.replace(
        "'اليوم هو موعد سداد قسط بقيمة %s ج.م. يرجى السداد لتجنب التأخير.'",
        "'اليوم هو موعد سداد قسط بقيمة %s%s. يرجى السداد لتجنب التأخير.'",
    )
    text = text.replace(
        "'تبقى %d يوم/أيام على موعد سداد قسط بقيمة %s ج.م (%s).'",
        "'تبقى %d يوم/أيام على موعد سداد قسط بقيمة %s%s (%s).'",
    )

    # Blade / HTML
    text = text.replace(">ج.م<", ">{{ __('public.currency') }}<")
    text = text.replace("> ج.م<", "> {{ __('public.currency') }}<")
    text = text.replace(" }} ج.م", " }} {{ __('public.currency') }}")
    text = text.replace("(ج.م)", "({{ __('public.currency') }})")
    text = text.replace("— جنيه)", "— {{ __('public.currency_name') }})")
    text = text.replace("جنيه مصري", "{{ __('public.currency_name') }}")
    text = text.replace("الجنيه المصري (ج.م)", "{{ __('public.currency_name') }} ({{ __('public.currency') }})")
    text = text.replace("الجنيه المصري", "{{ __('public.currency_name') }}")
    text = text.replace("(جنيه)", "({{ __('public.currency') }})")
    text = text.replace("بالجنيه المصري", "بالريال السعودي")
    text = text.replace("مبلغ الشراء (ج.م)", "مبلغ الشراء ({{ __('public.currency') }})")
    text = text.replace("حصتي (ج.م)", "حصتي ({{ __('public.currency') }})")
    text = text.replace("— جنيه", "— {{ __('public.currency') }}")

    if text != orig:
        path.write_text(text, encoding="utf-8")
        return True
    return False


def main():
    count = 0
    for base in ("app", "resources", "lang", "config"):
        p = ROOT / base
        if not p.exists():
            continue
        for f in p.rglob("*"):
            if should_skip(f) or not f.is_file():
                continue
            if f.suffix not in {".php", ".blade.php"}:
                continue
            if process_file(f):
                count += 1
                print(f.relative_to(ROOT))
    print("updated", count, "files")


if __name__ == "__main__":
    main()
