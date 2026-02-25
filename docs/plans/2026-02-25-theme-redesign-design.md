# HP Accountants Theme Redesign - Design Document

**Date:** 2026-02-25
**Approach:** Standalone custom WordPress theme from scratch
**Design direction:** Warm & Earthy Professional

## Color System

| Token | Hex | Usage |
|-------|-----|-------|
| `--color-green` | `#436600` | Primary buttons, nav bg, headings accent, links |
| `--color-cream` | `#e9e5da` | Page backgrounds, alternating section bg |
| `--color-gold` | `#86794d` | Secondary buttons, borders, icon accents |
| `--color-white` | `#ffffff` | Card surfaces, form inputs, alternating section bg |
| `--color-dark` | `#2a2a2a` | Body text, footer info bg |
| `--color-muted` | `#6b6b6b` | Secondary text, captions |

## Typography

- **Headings:** Lora (Google Fonts), 700 weight, serif
- **Body:** Inter (Google Fonts), 400/600 weight, sans-serif
- **Scale:** h1: 2.5rem, h2: 2rem, h3: 1.4rem, body: 1rem (16px base)
- **Line height:** 1.7 body, 1.2 headings

## Buttons

- **Primary:** `#436600` bg, white text, 8px radius, darken on hover
- **Secondary:** transparent, `#86794d` border, gold text, fills on hover
- **Sizing:** 600 weight Inter, 14px 32px padding

## Header & Navigation

### Desktop
1. **Logo area:** Centered, 40px vertical padding. `logo.png` at ~200px width. Tagline below in Inter 0.9rem muted.
2. **Nav bar:** Full-width `#436600` bg. Items centered, white text, Inter 600, uppercase, letter-spacing 1px. Hover/active: gold underline sliding in from left.

### Menu items
Home | About | Services | Downloads | Contact

### Mobile (below 768px)
- Logo centered, smaller (150px)
- Hamburger icon right-aligned
- Full-screen overlay: cream bg, stacked items centered, large touch targets (48px+)
- Close X top-right, fade-in transition

### Tablet (768px-1024px)
- Same as desktop, nav font slightly smaller, wraps if needed

## Homepage Sections

### 1. Hero Banner
- Full-width `#436600` bg
- "Approachable . Passionate . Accurate" in Lora white 2.5rem centered
- Subline: "Holland Price & Associates - trusted accounting for small to medium businesses in Dayboro and beyond" in Inter white/90% 1.1rem
- CTA: "Get In Touch" secondary style (white border/text, fills white with green text on hover)
- 80px vertical padding
- 3px gold bottom border

### 2. About Summary
- White bg, 70px padding
- 80-word excerpt centered, max-width 750px
- Inter 1.1rem, relaxed line-height
- "Read More" secondary button (gold border)

### 3. Services
- Cream bg (`#e9e5da`)
- "Our Services" heading centered, Lora
- 3-col grid (desktop), 2-col (tablet), 1-col (mobile)
- Cards: white bg, 12px radius, 30px padding, subtle shadow
- Title: Lora `#436600`, excerpt: Inter muted
- Hover: deeper shadow, `translateY(-2px)`
- "View All Services" button centered below

### 4. Testimonials
- White bg
- 2-col grid (desktop), 1-col (mobile)
- Cards: cream bg, 12px radius, 30px padding
- Left border 4px `#86794d`
- Decorative open-quote (Lora 4rem gold 10% opacity) top-left
- Quote: Inter italic. Author: Lora bold `#436600`. Business: muted Inter.

### 5. Partners
- Cream bg
- 4-col grid (desktop), 2-col (tablet/mobile)
- Cards: white bg, 12px radius, centered content
- Logos: grayscale default, full color on hover (CSS filter)
- Same subtle lift hover as service cards

## Interior Pages

### About Page
- Cream bg intro section
- Family photo: max-width 800px, 12px radius, subtle shadow
- Tagline centered in Lora below photo
- Intro paragraphs centered, max-width 750px
- Expert advice list: gold checkmark icons

### Team Members
- White card on cream bg
- 2-col: photo left (1/3), bio right (2/3)
- Photo: 12px radius, fills column
- Name: Lora `#436600`, title: gold italic
- 3px gold top-border on each card
- Mobile: stacks, photo full-width above bio

### Services Archive
- Cream bg, 2-col grid of white cards (full content)
- Mobile: single column

### Single Service
- White bg, max-width 750px centered
- Bottom: "All Services" (secondary) + "Contact Us" (primary) buttons

### Downloads Archive
- White bg, grouped by category
- Category heading: Lora, 2px gold bottom-border
- Each download: row with file-type badge, title link, view count
- Hover: cream bg on row
- File badges: PDF red, DOC blue, XLS green

### Contact Page
- 2-col: map + details left, form right
- Map: 12px radius
- Contact details: gold bold labels
- Form: cream input bg, gold focus-border, green submit
- Mobile: stacks, map top, form below

## Footer

### Newsletter Band
- `#436600` bg
- "Stay Updated" Lora white, subline white/80%
- Email input (white bg) + gold submit button inline, max-width 500px centered
- Mobile: stacks vertically

### Info Columns
- `#2a2a2a` bg, 3-col grid (stacks on mobile)
- Col 1: Footer logo + company description (muted)
- Col 2: Quick links (white, gold on hover)
- Col 3: Contact details (muted labels, white values)
- Column headings: Lora white, 2px gold bottom-border (40px wide)

### Copyright Bar
- `#222222` bg
- Centered: "(c) 2026 Holland Price & Associates. All rights reserved."
- 0.85rem muted text

## Responsive Breakpoints

- Desktop: 1024px+, container max-width 1100px
- Tablet: 768px-1023px
- Mobile: below 768px

## Global Details

- Container: 1100px max-width, centered, 20px horizontal padding
- Section padding: 70px (desktop), 50px (tablet), 40px (mobile)
- Transitions: 0.3s ease throughout
- Focus states: 2px gold outline ring (accessibility)
- Links: `#436600`, darken on hover, underline on hover only
- Smooth scroll behavior
- Back-to-top button: gold circle, white arrow, appears after 300px scroll

## Files Retained (unchanged)

- `inc/hp-custom-post-types.php`
- `inc/hp-meta-boxes.php`
- `inc/hp-newsletter.php`
- `inc/hp-download-tracking.php`
- `js/hp-newsletter.js`
- `js/hp-downloads.js`
- `migration/import-content.php`
- `migration/DEPLOYMENT.md`
- All `images/*` assets

## Files Built from Scratch

- `style.css` - Theme declaration + complete stylesheet
- `functions.php` - Lean: enqueue fonts/styles/scripts, register menus/widget areas, include hp-* files
- `header.php` - Custom markup
- `footer.php` - Custom markup
- `front-page.php` - Homepage
- `page-about.php` - About page template
- `page-contact.php` - Contact page template
- `archive-hp_service.php` - Services listing
- `archive-hp_testimonial.php` - Testimonials listing
- `archive-hp_download.php` - Downloads listing
- `archive-hp_link.php` - Partners listing
- `single-hp_service.php` - Individual service
- `index.php` - Fallback
- `404.php` - Not found page
- `js/hp-nav.js` - Mobile menu toggle + back-to-top
