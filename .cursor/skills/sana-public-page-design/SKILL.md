---
name: sana-public-page-design
description: >-
  Designs Sana public marketing pages as static HTML comps in designs/public-pages.
  Use when creating or updating external landing designs, marketing comps, public-page
  mockups, or when the user asks for تصميمات الصفحات الرئيسية / خارجية / لاندنج.
---

# Sana public page design (static comps)

## When to use

- User wants **design files only** for public/external pages (not Laravel Blade yet)
- Refreshing home, about, courses, pricing, contact, auth, tutor-apply, instructors comps
- Aligning visuals with Sana brand before implementation
- User says designs don’t match the live site — **fix by copying live copy**, not inventing marketing text

## Hard rule: mirror production design + copy

Comps must be **CSS+HTML twins** of the live Sana landing — not a parallel design system.

1. **Extract CSS** from `resources/views/landing/sana/*-theme.blade.php` / `theme.blade.php` into `designs/public-pages/assets/sana-*.css`
2. **Reuse production class names** (`sana-nav`, `sana-hero`, `sana-ab-hero`, …) — never invent `site-header` / `shell.css` layouts as the primary mirror
3. Open matching Blade + `lang/ar/*`; paste strings **verbatim**
4. Images: `../../public/img/sanua/...`
5. Scripts: `assets/sana-scripts.js` from `landing/sana/scripts.blade.php`

**Never** invent hero slogans, nav items, or a second stylesheet system that replaces `sana-*`.

## Output location

```
designs/public-pages/
  README.md
  CONTENT.md
  assets/sana-theme.css          ← from theme.blade.php
  assets/sana-*-theme.css        ← page themes
  assets/sana-scripts.js
  01-home.html … 09-instructors.html
```

Open `01-home.html` first. Badge: مرآة تصميم الإنتاج · …

## Brand tokens (required)

Read and reuse:

- `config/brand.php` — purple `#6A2CFF`, blue `#1D4EDB`, yellow `#F4B000`
- `resources/views/landing/sana/theme.blade.php` — `--p`, `--bg`, Cairo/Tajawal

Put shared values in `designs/public-pages/assets/tokens.css`. Keep comps on existing Sana purple system (established brand exception to generic “avoid purple” AI defaults).

## Composition rules

1. **First viewport = one composition** — not a dashboard
2. **Brand first** — `Sana` is hero-level, not only nav text
3. **Hero budget** — brand, one headline, one supporting sentence, one CTA group, one full-bleed visual plane
4. **No hero cards / floating badges / stat strips** on the hero
5. **RTL** `dir="rtl"`, Arabic copy
6. **Fonts** — Cairo (display) + Tajawal (body); never Inter/Roboto/Arial as primary
7. **Atmosphere** — gradients / soft orbs / full-bleed hero; avoid flat single-color only
8. **One job per section** — one headline + short support
9. **Cards** only for interaction containers (forms, pricing choose, course tiles)
10. **2–3 intentional motions** max (e.g. fade-up, soft float) — not decorative noise

## Page map

| Comp file | App route (reference) |
|-----------|------------------------|
| `01-home.html` | `/` |
| `02-about.html` | `/about` |
| `03-courses.html` | public courses catalog |
| `04-pricing.html` | `/pricing` |
| `05-contact.html` | `/contact` |
| `06-auth-login.html` | `/login` |
| `07-auth-register.html` | `/register` |
| `08-tutor-apply.html` | `/tutor/apply` |
| `09-instructors.html` | public instructors |

## Workflow

1. Update `tokens.css` / `shell.css` if brand tokens change
2. Edit or add numbered HTML comps; link between them for review
3. Update `designs/public-pages/README.md` index
4. If implementing in the app next, load skill `sana-landing-blade-implement`

## Anti-patterns

- Inventing Arabic copy that is not in Blade/lang (most common failure)
- Inset hero image cards, collage heroes, purple glow spam, emoji clusters
- Shipping comps as production Blade without adapting to `landing/sana/*`
- Inventing a second brand palette that conflicts with `config/brand.php`
