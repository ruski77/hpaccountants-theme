# Downloads Migration Design: S3 to WordPress Media Library

**Date:** 2026-02-28
**Status:** Approved

## Problem

Download files are hosted on AWS S3 (`hpaccountants.s3.amazonaws.com`). The S3 download links are broken for visitors. Files need to move to WordPress Media Library so they're managed alongside the site on A2 Hosting.

## Requirements

- Migrate 23 existing download files from S3 to WordPress Media Library
- Preserve all existing data: titles, categories, view counts
- Keep the same visitor experience: click link, file downloads
- Admin should use the WordPress Media Library picker to attach files
- Auto-migration script to pull files from S3, with manual upload as fallback

## Data Model Changes

- **New meta field:** `_hp_file_id` (int) — WordPress attachment post ID
- **Keep existing:** `_hp_s3_url` as read-only fallback for un-migrated posts
- **Auto-detect:** `_hp_file_type` from attachment MIME type when using Media Library
- **Preserve:** `_hp_view_count` values unchanged

## Admin Meta Box Changes (hp-meta-boxes.php)

Replace the S3 URL text input with a Media Library picker:

- "Select File" button using `wp.media` JavaScript API
- Displays selected filename and file type badge after selection
- "Remove" button to clear the selection
- Falls back to showing `_hp_s3_url` (read-only) if no `_hp_file_id` set
- New admin JS file: `js/hp-admin-downloads.js`

## Template Changes (archive-hp_download.php)

Download link resolution order:

1. `_hp_file_id` exists → `wp_get_attachment_url($file_id)`
2. `_hp_s3_url` exists → use directly (legacy fallback)
3. Neither → no link displayed

## Migration Script (migration/migrate-s3-to-media.php)

One-time admin-only script:

1. Query all `hp_download` posts with `_hp_s3_url` meta
2. For each post: `download_url()` + `media_handle_sideload()` to pull from S3
3. Store resulting attachment ID in `_hp_file_id`
4. Log success/failure per file
5. Output summary with list of failures for manual upload

Access: admin-only page or WP-CLI, runs once.

## Bug Fix

Fix AJAX tracking selector mismatch:
- `js/hp-downloads.js` uses `.hp-download-link`
- `archive-hp_download.php` uses class `download-link`
- Align both to `.download-link`

## What Stays the Same

- `hp_download` CPT registration and `download_category` taxonomy
- Archive page layout (category groups, view counts, file type badges)
- View count AJAX tracking system
- All existing view counts preserved
- Download tracking backend (hp-download-tracking.php)

## Files to Modify

- `inc/hp-meta-boxes.php` — replace S3 URL field with Media Library picker
- `archive-hp_download.php` — update link resolution logic
- `js/hp-downloads.js` — fix selector bug
- `functions.php` — enqueue new admin JS

## Files to Create

- `js/hp-admin-downloads.js` — Media Library picker logic
- `migration/migrate-s3-to-media.php` — one-time S3 migration script
