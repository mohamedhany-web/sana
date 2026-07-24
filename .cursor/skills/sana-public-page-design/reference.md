# Reference — Sana public design system

## Token mapping

| Token | Value | Source |
|-------|-------|--------|
| Brand purple | `#6A2CFF` | `config/brand.php` |
| Purple dark | `#5520CC` | brand |
| Blue | `#1D4EDB` | brand |
| Yellow | `#F4B000` | brand |
| Landing `--p` | `#6D28D9` | `landing/sana/theme.blade.php` |
| Background | `#F8F7FC` | landing theme |
| Text | `#1e1b4b` | landing theme |

## Blade counterparts (implementation)

| Area | Path |
|------|------|
| Home theme | `resources/views/landing/sana/theme.blade.php` |
| Navbar | `resources/views/landing/sana/navbar.blade.php` |
| Hero | `resources/views/landing/sana/sections/hero.blade.php` |
| Courses catalog | `resources/views/landing/sana/courses-catalog-theme.blade.php` |
| Pricing | `resources/views/landing/sana/pricing-theme.blade.php` |
| About | `resources/views/landing/sana/about-theme.blade.php` |
| Contact | `resources/views/landing/sana/contact-theme.blade.php` |
| Auth | `resources/views/auth/*` |
| Tutor apply | `resources/views/tutor/apply.blade.php` |

## Comp checklist before handoff

- [ ] Every visible string exists in Blade or `lang/ar/*` (see `designs/public-pages/CONTENT.md`)
- [ ] Opens offline in browser (relative `assets/`)
- [ ] RTL + Arabic
- [ ] Brand visible in first viewport without nav
- [ ] Mobile readable at 375px width
- [ ] README / CONTENT.md updated
