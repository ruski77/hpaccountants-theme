# HP Accountants: Rails to WordPress Migration Design

**Date:** 2026-02-24
**Status:** Approved

## Overview

Convert the Holland Price & Associates accounting firm website from Ruby on Rails 3.1.12 (PostgreSQL) to WordPress, using a forked version of the Ruki theme (v1.4.8). The output is a single theme folder deployable to A2 Hosting VPS.

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Theme approach | Fork Ruki directly | Single folder deployment, simpler |
| Downloads storage | Keep on S3 | Existing S3 URLs, no migration needed |
| Contact form | Contact Form 7 | Ruki has built-in CF7 styling |
| Newsletter signup | Custom DB table + theme form | No external service dependency |
| Content management | Custom Post Types | WordPress-native, familiar admin UX |
| URL structure | WordPress slug-based | Clean slate, old site is down |
| Image assets | Placeholders | User will replace later |

## Content Mapping

| Rails Model | WordPress Equivalent |
|---|---|
| Services (10 records) | Custom Post Type `hp_service` with position meta |
| Testimonials (4 records) | Custom Post Type `hp_testimonial` with position meta |
| Downloads (24 records) | Custom Post Type `hp_download` with S3 URL, view count meta |
| Links (8 records) | Custom Post Type `hp_link` with URL, position meta |
| About (1 record) | WordPress Page |
| Contact (form) | WordPress Page + Contact Form 7 shortcode |
| Home | WordPress Static Front Page (custom template) |
| Categories (3 records) | Custom taxonomy `download_category` on `hp_download` |
| Mailing List (24 records) | Custom DB table `wp_hp_mailinglist` + AJAX form |

## Custom Post Types

### hp_service
- **Title**: Service name
- **Content**: Service description
- **Meta**: `_hp_position` (int), `_hp_active` (Y/N)
- **Archive**: `/services/` ordered by position
- **Single**: `/services/{slug}/`

### hp_testimonial
- **Title**: Client name (for admin reference)
- **Content**: Testimonial quote
- **Meta**: `_hp_client_name`, `_hp_client_title`, `_hp_business_name`, `_hp_position` (int), `_hp_active` (Y/N)
- **Archive**: `/testimonials/` ordered by position

### hp_download
- **Title**: Download title
- **Content**: Description (optional)
- **Meta**: `_hp_s3_url` (full S3 URL), `_hp_file_type` (pdf/doc/etc), `_hp_view_count` (int)
- **Taxonomy**: `download_category` (Our Fees, Our Articles, Our Templates)
- **Archive**: `/downloads/` grouped by category, sorted by view count

### hp_link
- **Title**: Link title
- **Content**: Link description
- **Meta**: `_hp_url`, `_hp_position` (int), `_hp_active` (Y/N)
- **Archive**: `/links/`

## Page Templates

1. **`front-page.php`** - Static homepage: about summary, latest services, partner links
2. **`page-about.php`** - Team bios, photo placeholders, company philosophy
3. **`page-contact.php`** - Google Maps embed + CF7 form + business details
4. **`archive-hp_service.php`** - Services listing ordered by position
5. **`single-hp_service.php`** - Individual service detail
6. **`archive-hp_testimonial.php`** - Testimonials listing ordered by position
7. **`archive-hp_download.php`** - Downloads grouped by category
8. **`archive-hp_link.php`** - Partner/resource links

## Newsletter System

- Custom table: `{prefix}_hp_mailinglist` (id, name, email, created_at)
- Created on theme activation via `dbDelta()`
- AJAX form rendered in footer on all pages
- Handler: nonce verification, email validation, duplicate check
- Success/error messages via JSON response

## Contact Form

- Contact Form 7 plugin (required)
- Fields: Name (required), Email (required), Message (required)
- Recipient: configurable in CF7 admin
- Subject: "Email submitted via Holland Price & Associates website"

## Migration Script

PHP script (`migration/import-content.php`) that uses WP-CLI or direct WP functions to:
1. Create About, Contact, Home pages with correct page templates
2. Insert all 10 services as `hp_service` posts with meta
3. Insert all 4 testimonials as `hp_testimonial` posts with meta
4. Create 3 download categories in `download_category` taxonomy
5. Insert all 24 downloads as `hp_download` posts with S3 URLs and meta
6. Insert all 8 links as `hp_link` posts with meta
7. Create `wp_hp_mailinglist` table and import legitimate subscribers
8. Set static front page in WordPress options

## Deployment Steps

1. Install WordPress on A2 Hosting VPS with MySQL
2. Upload `hpaccountants-theme/` to `wp-content/themes/`
3. Activate theme in WordPress admin
4. Install and activate Contact Form 7 plugin
5. Run migration script to import all content
6. Set static front page in Settings > Reading
7. Configure CF7 form with correct email recipient
8. Replace placeholder images with actual team photos
9. Configure menus (Primary nav: Home, About, Services, Testimonials, Downloads, Contact)

## Out of Scope

- Drag-and-drop reordering (position field edited manually in admin)
- Custom user roles (WordPress admin is sufficient)
- File serving proxy for S3 downloads (direct links)
- WooCommerce
- Old URL redirects (site is no longer accessible)
