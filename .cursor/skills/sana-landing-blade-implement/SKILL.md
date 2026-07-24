---
name: sana-landing-blade-implement
description: >-
  Implements Sana public marketing pages from designs/public-pages comps into
  Laravel Blade under resources/views/landing/sana and auth/tutor views. Use when
  converting static designs to production, updating landing Blade, or syncing
  public pages with design comps.
---

# Implement Sana landing Blade from design comps

## When to use

- User approved comps in `designs/public-pages/` and wants them in the live site
- Updating public landing / about / pricing / contact / auth chrome to match comps
- Bridging design tokens into `landing/sana/theme.blade.php`

## Principles

1. **Comps mirror live copy** — if a comp string differs from Blade/lang, prefer Blade/lang unless the user asked to change product copy
2. **Comps are layout reference** — adapt to existing Blade structure; do not paste full HTML into routes
3. **Preserve brand** — keep `config/brand.php` + current landing CSS variables
4. **Reuse partials** — `navbar.blade.php`, `footer.blade.php`, section partials
5. **No new public routes** unless product asks; map to existing `routes/web.php` names
6. **RTL + Cairo/Tajawal** already expected in `theme.blade.php`

## Mapping comps → Blade

| Comp | Implement into |
|------|----------------|
| `01-home.html` | `LandingController@index` + `landing/sana/sections/*` |
| `02-about.html` | `landing/sana/about-theme.blade.php` |
| `03-courses.html` | `landing/sana/courses-catalog-theme.blade.php` |
| `04-pricing.html` | `landing/sana/pricing-theme.blade.php` |
| `05-contact.html` | `landing/sana/contact-theme.blade.php` |
| `06-auth-login.html` | `resources/views/auth/*` login |
| `07-auth-register.html` | `resources/views/auth/register.blade.php` |
| `08-tutor-apply.html` | `resources/views/tutor/apply*.blade.php` |
| `09-instructors.html` | `landing/sana/instructors-catalog-theme.blade.php` |

## Workflow

1. Open matching comp + current Blade side by side
2. Port **structure and hierarchy** first (hero → sections), then spacing/type
3. Move new shared CSS into `theme.blade.php` or extracted CSS — avoid duplicating `shell.css` as a second system
4. Keep dynamic data (courses, instructors, plans) from controllers/models
5. Verify desktop + mobile; keep one composition in the first viewport

## Do not

- Replace entire `eduvalt` theme unless explicitly requested (prefer active `sana` theme)
- Add dashboard-like widgets to marketing heroes
- Commit secrets or `.env` while touching landing assets

## Related skill

For design-only HTML comps: `sana-public-page-design`
