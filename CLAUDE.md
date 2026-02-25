# HP Accountants WordPress Theme

## Project Overview

Custom WordPress theme for **Holland Price & Associates** (hpaccountants.com.au) — a small accounting firm in Dayboro, QLD serving small to medium businesses. This is a classic PHP WordPress theme (no block editor / FSE). Local development via **Local by Flywheel**.

**Live site:** https://hpaccountants.com.au
**Local dev:** https://hpaccountants.local (Local by Flywheel)

## Tech Stack

- **WordPress** 6.0+ (tested up to 6.9.1)
- **PHP** 8.0+ — classic theme, no Composer
- **CSS** — single `style.css`, CSS custom properties, no preprocessor
- **JavaScript** — vanilla JS + jQuery (for AJAX), no build tools
- **Fonts** — Google Fonts: Lora (headings) + Inter (body)
- **Plugin dependency** — Contact Form 7 (recommended, not required)

## Theme Architecture

```
hpaccountants-theme/
├── style.css              # All CSS (custom properties, components, responsive)
├── functions.php          # Theme setup, enqueues, widget areas, includes
├── header.php / footer.php
├── front-page.php         # Homepage (hero, about excerpt, services, testimonials, partners)
├── page-about.php         # About page template
├── page-contact.php       # Contact page template (CF7 form)
├── page.php               # Generic page template
├── single-hp_service.php  # Single service view
├── archive-hp_service.php # Services listing
├── archive-hp_testimonial.php
├── archive-hp_download.php
├── archive-hp_link.php
├── 404.php
├── index.php              # Fallback
├── inc/
│   ├── hp-custom-post-types.php  # CPT + taxonomy registration + archive queries
│   ├── hp-meta-boxes.php         # Meta boxes, save handlers, admin columns
│   ├── hp-newsletter.php         # Newsletter: DB table, AJAX, shortcode, admin page
│   └── hp-download-tracking.php  # Download view count AJAX tracking
├── js/
│   ├── hp-nav.js           # Mobile nav toggle + back-to-top button
│   ├── hp-newsletter.js    # Newsletter form AJAX submission
│   └── hp-downloads.js     # Download click tracking AJAX
├── images/                 # Theme images (logos, team photos, partner logos)
├── migration/              # One-time content import script
└── docs/                   # Documentation / plans
```

## Custom Post Types

All CPTs use `_hp_position` (int) for ordering and `_hp_active` (Y/N) for visibility toggling. Archive queries are modified in `hp_custom_archive_queries()`.

| CPT | Slug | Meta Fields | Taxonomy |
|-----|------|-------------|----------|
| `hp_service` | services | `_hp_position`, `_hp_active` | — |
| `hp_testimonial` | testimonials | `_hp_client_name`, `_hp_client_title`, `_hp_business_name`, `_hp_position`, `_hp_active` | — |
| `hp_download` | downloads | `_hp_s3_url`, `_hp_file_type`, `_hp_view_count` | `download_category` |
| `hp_link` | links | `_hp_url`, `_hp_position`, `_hp_active` | — |

## Design System

### Colors (CSS custom properties in `:root`)
- `--color-green: #436600` — Primary (buttons, nav bar, hero, links)
- `--color-green-dark: #345200` — Hover states
- `--color-cream: #e9e5da` — Alt section backgrounds
- `--color-gold: #86794d` — Accents, borders, secondary buttons
- `--color-gold-light: #a0946a` — Gold hover
- `--color-dark: #2a2a2a` — Text
- `--color-muted: #6b6b6b` — Secondary text
- `--color-border: #d4d0c7` — Borders

### Typography
- Headings: `Lora` (serif, 700)
- Body: `Inter` (sans-serif, 400/600)

### Layout
- Max width: `1100px` (`--max-width`)
- Grid system: `.grid`, `.grid-2`, `.grid-3`, `.grid-4`
- Sections: `.section` (70px padding), `.section-alt` (cream bg)
- Responsive breakpoints: 1023px (tablet), 767px (mobile)

### Components
- `.btn-primary` — Green solid button
- `.btn-secondary` — Gold outline button
- `.card` — White card with shadow + hover lift
- `.testimonial` — Cream card with gold left border + quote mark
- `.partner-card` — Partner logo card with grayscale-to-color hover
- `.download-row` — Flex row with file type badge

## Conventions

- **Function prefix:** `hp_` for all theme functions
- **Meta key prefix:** `_hp_` (underscore prefix hides from Custom Fields UI)
- **Text domain:** `hpaccountants`
- **Theme constant:** `HP_THEME_VERSION` for cache busting
- **Security:** All meta saves verify nonce + capability; all output escaped with `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`
- **Code style:** WordPress Coding Standards (tabs for indentation in PHP, spaces in CSS/JS)
- **Menus:** `primary` (nav bar) and `footer` (footer nav)
- **Widget areas:** `footer-1`, `footer-2`, `footer-3`, `hp-newsletter`

## Custom Database

- `{prefix}_hp_mailinglist` table — newsletter subscribers (id, name, email, created_at)
- Created on theme activation via `dbDelta()`

## Important Notes

- **No build step** — edit CSS/JS/PHP directly, no compilation needed
- **No block theme** — classic theme with template files, not FSE/block patterns
- Downloads link to **S3 URLs** (external file hosting)
- The `migration/` directory contains a one-time import script — do not modify unless re-migrating
- Partner logos in `images/` are referenced by `_hp_logo` meta field filename
- Newsletter system uses a custom DB table, NOT a plugin
- Some include files still have `@subpackage Ruki` and text domain `ruki` from the previous theme — legacy references, functionally fine
