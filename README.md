# HP Accountants WordPress Theme

Custom WordPress theme for **Holland Price & Associates** — professional accounting services for small to medium businesses in Dayboro, QLD.

## Requirements

- WordPress 6.0+
- PHP 8.0+
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) (recommended for contact page)

## Installation

1. Clone this repo into your WordPress themes directory:
   ```bash
   cd wp-content/themes/
   git clone git@github.com:ruski77/hpaccountants-theme.git hpaccountants-theme
   ```
2. Activate the theme in **Appearance → Themes**
3. Set up menus under **Appearance → Menus**: `Primary Menu` and `Footer Menu`
4. Create pages: Home (set as static front page), About (slug: `about`), Contact (slug: `contact`)

## Custom Post Types

| Post Type | Admin Menu | Description |
|-----------|-----------|-------------|
| Services | Services | Accounting services offered |
| Testimonials | Testimonials | Client testimonials with author details |
| Downloads | Downloads | Downloadable files hosted on S3 |
| Partner Links | Partner Links | Partner/affiliate logo links |

All CPTs support **Position** ordering and **Active** (Y/N) visibility toggling via meta boxes in the editor.

## Newsletter

The theme includes a built-in newsletter subscription system with:
- Custom database table (`wp_hp_mailinglist`)
- AJAX signup form rendered via `[hp_newsletter]` shortcode or the footer
- Admin page at **Mailing List** to view subscribers

## Development

No build tools required — edit PHP, CSS, and JS files directly. The theme uses:
- Single `style.css` with CSS custom properties
- Vanilla JS + jQuery for AJAX functionality
- Google Fonts (Lora + Inter) loaded via CDN

### Local Development

This theme is developed using [Local by Flywheel](https://localwp.com/).
