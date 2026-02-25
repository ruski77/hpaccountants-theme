# HP Accountants WordPress - Deployment Guide

## Prerequisites

- A2 Hosting VPS with WordPress installed
- MySQL database created
- PHP 7.0+ with required WordPress extensions
- SSH or FTP access to the server

## Step 1: Upload Theme

Upload the entire `hpaccountants-theme/` folder to:

```
/path/to/wordpress/wp-content/themes/hpaccountants-theme/
```

## Step 2: Activate Theme

1. Log in to WordPress admin
2. Go to Appearance > Themes
3. Activate "HP Accountants"

## Step 3: Install Required Plugins

1. Go to Appearance > Install Plugins
2. Install and activate **Contact Form 7**

## Step 4: Run Migration

Via WP-CLI (recommended):

```bash
cd /path/to/wordpress
wp eval-file wp-content/themes/hpaccountants-theme/migration/import-content.php
```

## Step 5: Configure Contact Form 7

1. Go to Contact > Add New
2. Create form with fields:
   - `[text* your-name placeholder "Your Name"]`
   - `[email* your-email placeholder "Your Email"]`
   - `[textarea* your-message placeholder "Your Message"]`
   - `[submit "Send Message"]`
3. Set Mail tab recipient to: `price@hpaccountants.com.au`
4. Set subject to: `Email submitted via Holland Price & Associates website`
5. Copy the shortcode and paste it into the Contact page content

## Step 6: Set Up Menus

1. Go to Appearance > Menus
2. Create "Primary Menu":
   - Home (page)
   - About Us (page)
   - Services (custom link: /services/)
   - Testimonials (custom link: /testimonials/)
   - Downloads (custom link: /downloads/)
   - Contact Us (page)
3. Assign to "Primary Menu" location

## Step 7: Flush Permalinks

Go to Settings > Permalinks and click "Save Changes".

## Step 8: Final Checks

- [ ] Homepage displays services, testimonials, and links
- [ ] About page shows team bios
- [ ] Services archive lists all services
- [ ] Individual service pages work
- [ ] Testimonials page displays quotes
- [ ] Downloads page shows files grouped by category
- [ ] Download links to S3 work
- [ ] Contact form sends emails
- [ ] Newsletter signup works
- [ ] Mobile responsive design works
- [ ] Replace placeholder images with actual team photos
