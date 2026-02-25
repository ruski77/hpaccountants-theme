# HP Accountants Theme Redesign - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace the Ruki-based theme with a standalone custom WordPress theme using a warm & earthy professional design with colors #436600, #e9e5da, #86794d.

**Architecture:** Build a standalone WordPress theme from scratch. The theme keeps the existing `inc/hp-*.php` backend files (CPTs, meta boxes, newsletter, download tracking) and `js/hp-*.js` AJAX scripts unchanged. All template files (`header.php`, `footer.php`, `front-page.php`, etc.), the stylesheet, `functions.php`, and mobile nav JS are written fresh with custom markup. No Ruki dependencies.

**Tech Stack:** WordPress 6.9.1, PHP 8.x, vanilla CSS (custom properties), vanilla JS (mobile nav), Google Fonts (Lora + Inter), jQuery (existing AJAX scripts only)

**Design doc:** `docs/plans/2026-02-25-theme-redesign-design.md`

---

### Task 1: New functions.php

**Files:**
- Create: `hpaccountants-theme/functions-new.php` (we'll rename at the end)

**Context:** This is the theme's bootstrap file. It registers menus, enqueues fonts/styles/scripts, registers widget areas, and includes the existing `inc/hp-*.php` files. It replaces Ruki's 1400+ line functions.php with a lean ~100 line file.

**Step 1: Write functions.php**

```php
<?php
/**
 * HP Accountants Theme - Functions
 *
 * @package HP_Accountants
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme version for cache busting.
define( 'HP_THEME_VERSION', '2.0.0' );

/**
 * Theme setup: menus, support, image sizes.
 */
function hp_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'hpaccountants' ),
        'footer'  => __( 'Footer Menu', 'hpaccountants' ),
    ) );
}
add_action( 'after_setup_theme', 'hp_theme_setup' );

/**
 * Enqueue styles and scripts.
 */
function hp_enqueue_assets() {
    // Google Fonts: Lora + Inter.
    wp_enqueue_style(
        'hp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Lora:ital,wght@0,700;1,400&display=swap',
        array(),
        null
    );

    // Main stylesheet.
    wp_enqueue_style(
        'hp-style',
        get_stylesheet_uri(),
        array( 'hp-google-fonts' ),
        HP_THEME_VERSION
    );

    // Mobile nav + back-to-top.
    wp_enqueue_script(
        'hp-nav',
        get_template_directory_uri() . '/js/hp-nav.js',
        array(),
        HP_THEME_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'hp_enqueue_assets' );

/**
 * Register widget areas.
 */
function hp_register_widget_areas() {
    register_sidebar( array(
        'name'          => __( 'Footer Column 1', 'hpaccountants' ),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer Column 2', 'hpaccountants' ),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer Column 3', 'hpaccountants' ),
        'id'            => 'footer-3',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'hp_register_widget_areas' );

/**
 * Require CF7 plugin.
 */
function hp_require_cf7() {
    if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) && current_user_can( 'activate_plugins' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-warning"><p><strong>HP Accountants:</strong> Contact Form 7 plugin is recommended for the contact page.</p></div>';
        } );
    }
}
add_action( 'admin_init', 'hp_require_cf7' );

// Include HP custom functionality (unchanged from previous theme).
require_once get_template_directory() . '/inc/hp-custom-post-types.php';
require_once get_template_directory() . '/inc/hp-meta-boxes.php';
require_once get_template_directory() . '/inc/hp-newsletter.php';
require_once get_template_directory() . '/inc/hp-download-tracking.php';
```

**Step 2: Verify it's syntactically valid**

Run: `php -l hpaccountants-theme/functions-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/functions-new.php
git commit -m "feat: add new standalone functions.php for custom theme"
```

---

### Task 2: style.css - CSS Custom Properties, Reset, Typography, Layout Utilities

**Files:**
- Create: `hpaccountants-theme/style-new.css`

**Context:** This is the complete theme stylesheet. WordPress requires `style.css` at the theme root with a theme header comment. We build it in sections. This first task covers: theme header, CSS custom properties, reset, typography, layout utilities, and the container system.

**Step 1: Write the foundation CSS**

The file should start with the WordPress theme header:

```css
/*
Theme Name: HP Accountants
Theme URI: https://hpaccountants.com.au
Author: Holland Price & Associates
Description: Custom theme for HP Accountants - Professional Accounting Services
Version: 2.0.0
Requires at least: 6.0
Tested up to: 6.9.1
Requires PHP: 8.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: hpaccountants
*/
```

Then CSS custom properties on `:root`:

```css
:root {
    --color-green: #436600;
    --color-green-dark: #345200;
    --color-cream: #e9e5da;
    --color-gold: #86794d;
    --color-gold-light: #a0946a;
    --color-white: #ffffff;
    --color-dark: #2a2a2a;
    --color-muted: #6b6b6b;
    --color-border: #d4d0c7;
    --font-heading: 'Lora', Georgia, serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --max-width: 1100px;
    --radius: 12px;
    --radius-sm: 8px;
    --shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    --shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
    --transition: 0.3s ease;
}
```

Then a minimal reset, box-sizing, smooth scroll, html/body base styles, heading styles (h1-h3 using Lora), body text (Inter), link styles, `.container` class (max-width 1100px, centered, 20px padding), `.btn-primary` and `.btn-secondary` classes, and responsive section padding.

Include a grid system:
- `.grid` - CSS grid with gap
- `.grid-2` through `.grid-4` for column counts
- Responsive: `.grid-3` becomes 2-col at tablet, 1-col at mobile; `.grid-4` becomes 2-col at tablet/mobile

**Step 2: Verify no syntax issues**

Visual review of CSS is sufficient since there's no CSS linter set up. Check the file is well-formed.

**Step 3: Commit**

```bash
git add hpaccountants-theme/style-new.css
git commit -m "feat: add base stylesheet with design tokens, reset, and layout system"
```

---

### Task 3: style.css - Component Styles (Header, Nav, Hero, Cards, Testimonials, Partners, Footer)

**Files:**
- Modify: `hpaccountants-theme/style-new.css` (append)

**Context:** Add all component-specific CSS after the foundation. This covers every visual component on the site.

**Step 1: Append component styles**

Add styles for each component, following the design doc:

**Header:**
- `.site-header` - centered logo area, 40px vertical padding
- `.site-header .logo img` - max-width 200px
- `.site-header .tagline` - Inter 0.9rem muted
- `.nav-bar` - full-width `var(--color-green)` bg
- `.nav-bar ul` - centered flex, gap
- `.nav-bar a` - white, uppercase, Inter 600, letter-spacing 1px, gold underline on hover/active (use `::after` pseudo-element with `transform: scaleX(0)` to `scaleX(1)`)

**Mobile nav:**
- `.mobile-header` - hidden on desktop, flex on mobile with logo + hamburger
- `.nav-overlay` - fixed full-screen, cream bg, centered stacked links, 48px touch targets, `opacity: 0; visibility: hidden` transitioning to visible when `.nav-overlay.is-open`
- `.hamburger` - simple 3-line icon using spans + transforms (CSS only, animated to X when open)

**Hero:**
- `.hero` - full-width, `var(--color-green)` bg, 80px vertical padding, white text centered
- `.hero h1` - Lora 2.5rem
- `.hero p` - Inter 1.1rem white/90%
- `.hero .btn-secondary` - white border variant
- `.hero::after` - 3px gold bottom line

**Cards:**
- `.card` - white bg, var(--radius), 30px padding, var(--shadow), hover lift + shadow-hover
- `.card-title` - Lora, `var(--color-green)`

**Testimonials:**
- `.testimonial` - cream bg, var(--radius), 30px padding, 4px left border gold
- `.testimonial::before` - decorative open-quote (Lora 4rem gold 10% opacity)
- `.testimonial-content` - Inter italic
- `.testimonial-author strong` - Lora bold green
- `.testimonial-author span` - muted

**Partners:**
- `.partner-card` - white bg, radius, centered, hover lift
- `.partner-card img` - grayscale filter default, full color on hover
- `.partner-card:hover img` - `filter: grayscale(0)`

**Sections:**
- `.section` - 70px vertical padding
- `.section-alt` - cream background
- `.section-title` - Lora centered, margin-bottom 40px

**Footer:**
- `.footer-newsletter` - green bg, centered
- `.footer-newsletter input` - white bg, rounded
- `.footer-newsletter button` - gold bg, white text
- `.footer-info` - `#2a2a2a` bg, 3-col grid
- `.footer-info h4` - white, gold bottom border (2px, 40px wide via `::after`)
- `.footer-info a` - white, gold on hover
- `.footer-copyright` - `#222` bg, centered muted small text

**About page:**
- `.about-intro` - centered text, family photo styling
- `.team-member` - white card, gold top-border, grid layout (1/3 photo, 2/3 bio)

**Downloads:**
- `.download-row` - flex row, hover cream bg
- `.file-badge` - small pill with file type color

**Contact:**
- `.contact-grid` - 2-col grid
- Form inputs: cream bg, gold focus border

**Back-to-top:**
- `.back-to-top` - fixed bottom-right, gold circle, white arrow, hidden by default, `.is-visible` shows

**Responsive at bottom:**
- `@media (max-width: 1023px)` - tablet adjustments
- `@media (max-width: 767px)` - mobile: stacked grids, smaller headings, reduced padding

**Step 2: Verify completeness**

Check that every class referenced in the design doc has a corresponding CSS rule.

**Step 3: Commit**

```bash
git add hpaccountants-theme/style-new.css
git commit -m "feat: add all component styles for header, nav, hero, cards, footer"
```

---

### Task 4: header.php

**Files:**
- Create: `hpaccountants-theme/header-new.php`

**Context:** Custom header with centered logo, green nav bar, mobile hamburger + overlay. References the `primary` nav menu registered in functions.php.

**Step 1: Write header.php**

```php
<?php
/**
 * HP Accountants - Header
 *
 * @package HP_Accountants
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container header-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>"
                 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                 class="logo">
        </a>
        <p class="tagline"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
    </div>

    <nav class="nav-bar" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'hpaccountants' ); ?>">
        <div class="container">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'depth'          => 1,
                'fallback_cb'    => false,
            ) );
            ?>
        </div>
    </nav>

    <!-- Mobile header -->
    <div class="mobile-header">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-logo-link">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>"
                 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                 class="mobile-logo">
        </a>
        <button class="hamburger" aria-label="<?php esc_attr_e( 'Toggle Menu', 'hpaccountants' ); ?>" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Mobile overlay -->
    <div class="nav-overlay" aria-hidden="true">
        <button class="nav-overlay-close" aria-label="<?php esc_attr_e( 'Close Menu', 'hpaccountants' ); ?>">&times;</button>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-overlay-menu',
            'depth'          => 1,
            'fallback_cb'    => false,
        ) );
        ?>
    </div>
</header>
```

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/header-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/header-new.php
git commit -m "feat: add custom header with centered logo and green nav bar"
```

---

### Task 5: footer.php

**Files:**
- Create: `hpaccountants-theme/footer-new.php`

**Context:** Three-part footer: newsletter band (green), info columns (dark), copyright bar (darker). Uses the newsletter shortcode from `inc/hp-newsletter.php` and the `footer` nav menu.

**Step 1: Write footer.php**

```php
<?php
/**
 * HP Accountants - Footer
 *
 * @package HP_Accountants
 */
?>

<footer class="site-footer">

    <!-- Newsletter band -->
    <div class="footer-newsletter">
        <div class="container footer-newsletter-inner">
            <h3>Stay Updated</h3>
            <p>Subscribe to our newsletter for the latest accounting news and updates.</p>
            <?php echo hp_newsletter_shortcode( array() ); ?>
        </div>
    </div>

    <!-- Info columns -->
    <div class="footer-info">
        <div class="container grid grid-3">
            <div class="footer-col">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo-footer.png' ); ?>"
                         alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                         class="footer-logo-img">
                </a>
                <p>Holland Price & Associates is a husband and wife team providing professional accounting services to small and medium-sized businesses in Dayboro and surrounds.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer-nav',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ) );
                ?>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>15 Roderick Street<br>Dayboro, QLD 4521</p>
                <p><a href="tel:0447384179">0447 384 179</a></p>
                <p><a href="mailto:price@hpaccountants.com.au">price@hpaccountants.com.au</a></p>
            </div>
        </div>
    </div>

    <!-- Copyright bar -->
    <div class="footer-copyright">
        <div class="container">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Holland Price & Associates. All rights reserved.</p>
        </div>
    </div>

</footer>

<!-- Back to top -->
<button class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'hpaccountants' ); ?>">&#8593;</button>

<?php wp_footer(); ?>
</body>
</html>
```

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/footer-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/footer-new.php
git commit -m "feat: add custom footer with newsletter, info columns, and copyright"
```

---

### Task 6: front-page.php (Homepage)

**Files:**
- Create: `hpaccountants-theme/front-page-new.php`

**Context:** Homepage with 5 sections: hero, about summary, services grid, testimonials, partner links. Uses same WP_Query patterns as the existing front-page.php but with new markup/classes matching the design.

**Step 1: Write front-page.php**

Structure:
1. `get_header()`
2. Hero section: `<section class="hero">` with tagline, subline, CTA button
3. About section: `<section class="section">` query about page, show excerpt + Read More button
4. Services section: `<section class="section section-alt">` with `.grid.grid-3` of `.card` elements. WP_Query for `hp_service` (same query as current: 6 posts, ordered by `_hp_position`, active only)
5. Testimonials section: `<section class="section">` with `.grid.grid-2` of `.testimonial` blockquotes. WP_Query for `hp_testimonial` (4 posts, same query pattern)
6. Partners section: `<section class="section section-alt">` with `.grid.grid-4` of `.partner-card` links. WP_Query for `hp_link` (8 posts, same pattern)
7. `get_footer()`

Keep all the same WP_Query parameters and meta_query logic from the existing file. Only change the HTML markup and CSS classes.

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/front-page-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/front-page-new.php
git commit -m "feat: add homepage template with hero, services, testimonials, partners"
```

---

### Task 7: page-about.php (About Page)

**Files:**
- Create: `hpaccountants-theme/page-about-new.php`

**Context:** About page with family photo, tagline, intro text, expertise list, and two team member cards. Keep the same content as existing but with new markup.

**Step 1: Write page-about.php**

Structure:
1. `get_header()`
2. Intro section: `<section class="section section-alt">` with family photo (`.about-photo`), tagline in Lora, intro paragraphs centered max-width 750px, expertise list with gold checkmarks
3. Team members: Two `<div class="team-member">` cards each with `.grid.grid-3` layout (1/3 photo, 2/3 bio via `grid-column: span 2`). Gold top-border. Scott first, then Christy.
4. `get_footer()`

Uses `Template Name: About Page` in the header comment for WordPress template assignment.

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/page-about-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/page-about-new.php
git commit -m "feat: add about page template with team member cards"
```

---

### Task 8: page-contact.php (Contact Page)

**Files:**
- Create: `hpaccountants-theme/page-contact-new.php`

**Context:** Contact page with 2-column layout: map + details on the left, CF7 form on the right. Uses `Template Name: Contact Page`.

**Step 1: Write page-contact.php**

Structure:
1. `get_header()`
2. Page header: `<section class="section">` with "Contact Us" h1
3. Contact grid: `<div class="grid grid-2 contact-grid">` with map column (Google Maps iframe + address/phone/email details) and form column (`the_content()` which renders the CF7 shortcode)
4. `get_footer()`

Keep the same Google Maps embed URL and contact details as the existing page.

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/page-contact-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/page-contact-new.php
git commit -m "feat: add contact page template with map and form grid"
```

---

### Task 9: Archive Templates (Services, Testimonials, Downloads, Links)

**Files:**
- Create: `hpaccountants-theme/archive-hp_service-new.php`
- Create: `hpaccountants-theme/archive-hp_testimonial-new.php`
- Create: `hpaccountants-theme/archive-hp_download-new.php`
- Create: `hpaccountants-theme/archive-hp_link-new.php`

**Context:** Archive pages for each CPT. The main query is already modified by `hp_custom_archive_queries()` in `inc/hp-custom-post-types.php` so templates just need to loop and render.

**Step 1: Write archive templates**

**archive-hp_service.php:**
- Page title "Our Services" centered
- `.grid.grid-2` of `.card` elements with full content (not excerpt)
- Mobile: single column

**archive-hp_testimonial.php:**
- Page title "What Our Clients Say" centered
- `.grid.grid-2` of `.testimonial` blockquotes (same style as homepage but shows all)

**archive-hp_download.php:**
- Page title "Downloads" centered
- Grouped by `download_category` taxonomy (same logic as current: `get_terms()` then WP_Query per category)
- Each category: heading with gold border, list of `.download-row` items with file badge, title link, view count

**archive-hp_link.php:**
- Page title "Our Partners" centered
- `.grid.grid-4` of `.partner-card` elements (same as homepage partners section)

**Step 2: Verify syntax for all four files**

Run: `php -l hpaccountants-theme/archive-hp_service-new.php && php -l hpaccountants-theme/archive-hp_testimonial-new.php && php -l hpaccountants-theme/archive-hp_download-new.php && php -l hpaccountants-theme/archive-hp_link-new.php`
Expected: `No syntax errors detected` for each

**Step 3: Commit**

```bash
git add hpaccountants-theme/archive-*-new.php
git commit -m "feat: add archive templates for services, testimonials, downloads, links"
```

---

### Task 10: single-hp_service.php, index.php, 404.php

**Files:**
- Create: `hpaccountants-theme/single-hp_service-new.php`
- Create: `hpaccountants-theme/index-new.php`
- Create: `hpaccountants-theme/404-new.php`

**Context:** Single service view + fallback templates.

**Step 1: Write the templates**

**single-hp_service.php:**
- Content centered at max-width 750px
- Title in Lora, content in Inter
- Footer: two buttons "All Services" (secondary) + "Contact Us" (primary)

**index.php:**
- Generic fallback. Shows page title + standard loop with `.card` layout
- Simple and functional, not heavily styled

**404.php:**
- "Page Not Found" heading
- Brief message + link back to homepage
- Centered, simple

**Step 2: Verify syntax**

Run: `php -l hpaccountants-theme/single-hp_service-new.php && php -l hpaccountants-theme/index-new.php && php -l hpaccountants-theme/404-new.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add hpaccountants-theme/single-hp_service-new.php hpaccountants-theme/index-new.php hpaccountants-theme/404-new.php
git commit -m "feat: add single service, index fallback, and 404 templates"
```

---

### Task 11: js/hp-nav.js (Mobile Nav + Back-to-Top)

**Files:**
- Create: `hpaccountants-theme/js/hp-nav.js`

**Context:** Vanilla JS (no jQuery) for mobile menu toggle and back-to-top button. Small and focused.

**Step 1: Write hp-nav.js**

```javascript
(function () {
    'use strict';

    // Mobile nav toggle
    var hamburger = document.querySelector('.hamburger');
    var overlay = document.querySelector('.nav-overlay');
    var closeBtn = document.querySelector('.nav-overlay-close');
    var body = document.body;

    function openMenu() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        hamburger.setAttribute('aria-expanded', 'true');
        hamburger.classList.add('is-active');
        body.style.overflow = 'hidden';
    }

    function closeMenu() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.classList.remove('is-active');
        body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (overlay.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeMenu);
    }

    // Close on overlay link click
    if (overlay) {
        var links = overlay.querySelectorAll('a');
        for (var i = 0; i < links.length; i++) {
            links[i].addEventListener('click', closeMenu);
        }
    }

    // Back to top
    var backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTop.classList.add('is-visible');
            } else {
                backToTop.classList.remove('is-visible');
            }
        });

        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/js/hp-nav.js
git commit -m "feat: add mobile nav toggle and back-to-top button JS"
```

---

### Task 12: Swap Files - Replace Old with New

**Files:**
- Rename/replace all `-new` files to their final names
- Remove old Ruki-specific files that are no longer needed

**Context:** Now that all new files are written and verified, swap them in. This is the cutover point.

**Step 1: Back up and swap files**

```bash
# Back up old files
mkdir -p hpaccountants-theme/_ruki-backup

# Move old core files to backup
mv hpaccountants-theme/functions.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/style.css hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/header.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/footer.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/front-page.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/page-about.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/page-contact.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/archive-hp_service.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/archive-hp_testimonial.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/archive-hp_download.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/archive-hp_link.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/single-hp_service.php hpaccountants-theme/_ruki-backup/
mv hpaccountants-theme/index.php hpaccountants-theme/_ruki-backup/

# Swap new files into place
mv hpaccountants-theme/functions-new.php hpaccountants-theme/functions.php
mv hpaccountants-theme/style-new.css hpaccountants-theme/style.css
mv hpaccountants-theme/header-new.php hpaccountants-theme/header.php
mv hpaccountants-theme/footer-new.php hpaccountants-theme/footer.php
mv hpaccountants-theme/front-page-new.php hpaccountants-theme/front-page.php
mv hpaccountants-theme/page-about-new.php hpaccountants-theme/page-about.php
mv hpaccountants-theme/page-contact-new.php hpaccountants-theme/page-contact.php
mv hpaccountants-theme/archive-hp_service-new.php hpaccountants-theme/archive-hp_service.php
mv hpaccountants-theme/archive-hp_testimonial-new.php hpaccountants-theme/archive-hp_testimonial.php
mv hpaccountants-theme/archive-hp_download-new.php hpaccountants-theme/archive-hp_download.php
mv hpaccountants-theme/archive-hp_link-new.php hpaccountants-theme/archive-hp_link.php
mv hpaccountants-theme/single-hp_service-new.php hpaccountants-theme/single-hp_service.php
mv hpaccountants-theme/index-new.php hpaccountants-theme/index.php
mv hpaccountants-theme/404-new.php hpaccountants-theme/404.php
```

**Step 2: Remove Ruki-only files that are no longer needed**

Remove old Ruki template-parts, sidebar, search, comments, page templates that the new theme doesn't use. Keep `inc/hp-*.php`, `js/hp-*.js`, `images/`, `migration/`. After swapping, the `_ruki-backup/` folder can be removed once confirmed working.

**Step 3: Verify all PHP files pass lint**

```bash
find hpaccountants-theme -name "*.php" -not -path "*/_ruki-backup/*" -not -path "*/migration/*" | xargs -I {} php -l {}
```
Expected: All files pass syntax check.

**Step 4: Commit**

```bash
git add -A hpaccountants-theme/
git commit -m "feat: swap to standalone custom theme, back up Ruki files"
```

---

### Task 13: Clean Up - Remove Ruki Backup and Unused Files

**Files:**
- Remove: `hpaccountants-theme/_ruki-backup/` directory
- Remove: Ruki-only directories that are no longer used (e.g., `template-parts/`, `customizer/`, etc.)

**Context:** After verifying the swap works (all PHP lints pass), clean out the Ruki remnants.

**Step 1: Identify Ruki-only directories to remove**

Check which directories exist and are purely Ruki infrastructure:
- `template-parts/` - Ruki's partial templates (our theme doesn't use them)
- `customizer/` - Ruki's Customizer code (not included in new functions.php)
- `languages/` - Ruki translations (we use our own text domain)
- Any other Ruki-specific directories

Keep: `inc/`, `js/`, `images/`, `css/` (if still referenced), `migration/`

**Step 2: Remove backup and Ruki directories**

```bash
rm -rf hpaccountants-theme/_ruki-backup
# Remove Ruki-only directories identified in step 1
```

**Step 3: Also remove `css/hp-custom.css`**

The old custom CSS file is fully replaced by the new `style.css`. Remove it.

```bash
rm hpaccountants-theme/css/hp-custom.css
```

**Step 4: Verify the theme directory is clean**

List the final directory structure and verify it matches expectations:
- `style.css`, `functions.php`, `header.php`, `footer.php`
- `front-page.php`, `page-about.php`, `page-contact.php`
- `archive-hp_*.php`, `single-hp_service.php`
- `index.php`, `404.php`
- `screenshot.png` (if exists)
- `inc/hp-*.php` (4 files)
- `js/hp-nav.js`, `js/hp-newsletter.js`, `js/hp-downloads.js`
- `images/*`
- `migration/*`

**Step 5: Commit**

```bash
git add -A hpaccountants-theme/
git commit -m "chore: remove Ruki backup and unused theme files"
```

---

### Task 14: Final Review and Polish

**Files:**
- Possibly modify: `hpaccountants-theme/style.css` (minor tweaks)
- Possibly modify: any template file (minor fixes)

**Context:** Do a final review pass checking for:
1. All PHP files lint clean
2. CSS class names in templates match CSS class names in style.css
3. All image paths reference files that exist in `images/`
4. The newsletter shortcode function `hp_newsletter_shortcode` is still called correctly in footer.php
5. All WP_Query parameters match the original working queries
6. Mobile nav overlay works (JS selectors match HTML classes)
7. `wp_head()` and `wp_footer()` are present
8. No hardcoded colors remaining (all should use CSS custom properties)

**Step 1: Run full PHP lint**

```bash
find hpaccountants-theme -name "*.php" -not -path "*/migration/*" | xargs -I {} php -l {}
```

**Step 2: Cross-reference CSS classes**

Grep all class names used in PHP templates and verify each has a corresponding CSS rule in style.css.

**Step 3: Fix any issues found**

Address any mismatches, typos, or missing styles.

**Step 4: Commit**

```bash
git add -A hpaccountants-theme/
git commit -m "fix: final review polish and class name consistency"
```
