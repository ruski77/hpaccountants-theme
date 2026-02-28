<?php
/**
 * HP Accountants Content Migration Script
 *
 * Usage: wp eval-file wp-content/themes/hpaccountants-theme/migration/import-content.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    $wp_load = dirname( __FILE__ ) . '/../../../wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    } else {
        die( 'Run via WP-CLI: wp eval-file import-content.php' );
    }
}

if ( ! current_user_can( 'manage_options' ) && ! defined( 'WP_CLI' ) ) {
    die( 'Unauthorized' );
}

function hp_log( $msg ) {
    if ( defined( 'WP_CLI' ) ) { WP_CLI::log( $msg ); } else { echo esc_html( $msg ) . "<br>\n"; }
}

function hp_ok( $msg ) {
    if ( defined( 'WP_CLI' ) ) { WP_CLI::success( $msg ); } else { echo "<strong style='color:green'>" . esc_html( $msg ) . "</strong><br>\n"; }
}

// ============================================================
// 1. CREATE PAGES
// ============================================================
hp_log( '--- Creating Pages ---' );

$home_page = get_page_by_path( 'home' );
if ( ! $home_page ) {
    $home_id = wp_insert_post( array(
        'post_title'   => 'Home',
        'post_name'    => 'home',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ) );
    hp_ok( "Created Home page (ID: $home_id)" );
} else {
    $home_id = $home_page->ID;
    hp_log( "Home page exists (ID: $home_id)" );
}

$about_content = '<p>Holland Price &amp; Associates is a husband and wife team with over 30 years of combined experience in public practice and commercial accounting. Servicing small to medium-sized businesses is their focus.</p>';
$about_page = get_page_by_path( 'about' );
if ( ! $about_page ) {
    $about_id = wp_insert_post( array(
        'post_title'    => 'About Us',
        'post_name'     => 'about',
        'post_content'  => $about_content,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-about.php',
    ) );
    hp_ok( "Created About page (ID: $about_id)" );
} else {
    hp_log( 'About page exists' );
}

$contact_page = get_page_by_path( 'contact' );
if ( ! $contact_page ) {
    $contact_id = wp_insert_post( array(
        'post_title'    => 'Contact Us',
        'post_name'     => 'contact',
        'post_content'  => '[contact-form-7 id="REPLACE_WITH_CF7_FORM_ID" title="Contact Form"]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-contact.php',
    ) );
    hp_ok( "Created Contact page (ID: $contact_id) - Update CF7 shortcode after creating form" );
} else {
    hp_log( 'Contact page exists' );
}

// ============================================================
// 2. IMPORT SERVICES
// ============================================================
hp_log( '' );
hp_log( '--- Importing Services ---' );

$services = array(
    array( 'title' => 'Self Managed Super Funds', 'desc' => 'Accounting for SMSF\'s.', 'pos' => 1 ),
    array( 'title' => 'ASIC compliance', 'desc' => 'Our office can look after all of your corporate compliance matters from the initial company establishment to annual returns.', 'pos' => 2 ),
    array( 'title' => 'Business services', 'desc' => 'Business health checks for clients including a review of financial performance, business strategy, structure, accounting systems and more. Liability limited by a scheme approved under Professional Standards Legislation.', 'pos' => 3 ),
    array( 'title' => 'Business structuring & restructuring', 'desc' => 'We can advise you on the most appropriate business structure for tax purposes and protection of your assets.', 'pos' => 4 ),
    array( 'title' => 'Business Activity Statement preparation', 'desc' => 'Activity statements can be prepared or reviewed by our office.', 'pos' => 5 ),
    array( 'title' => 'Due diligence', 'desc' => 'We undertake due diligence assignments to ascertain the value of prospective business\'s.', 'pos' => 6 ),
    array( 'title' => 'Preparation of budgets and cash flows', 'desc' => 'If you require assistance with your business cash flow we can help by creating forecasts.', 'pos' => 7 ),
    array( 'title' => 'Part-time CFO', 'desc' => 'With our wealth of in-house accounting experience we offer an affordable consulting service to review and analyse your accounts on a regular basis. We add value to your business by setting up proper procedures and process\'s and monitor your accounts through rolling monthly forecasts so you can make informed strategic decisions.', 'pos' => 8 ),
    array( 'title' => 'Tax return preparation', 'desc' => 'We prepare business returns only including partnership, company, and trust income tax returns along with Self Managed Super Funds.', 'pos' => 9 ),
    array( 'title' => 'Xero Partners', 'desc' => 'We can assist you in setting up with Xero Accounting Software.', 'pos' => 10 ),
);

foreach ( $services as $s ) {
    $exists = get_posts( array( 'post_type' => 'hp_service', 'title' => $s['title'], 'posts_per_page' => 1 ) );
    if ( $exists ) { hp_log( "Service '{$s['title']}' exists, skipping" ); continue; }
    $id = wp_insert_post( array( 'post_title' => $s['title'], 'post_content' => $s['desc'], 'post_status' => 'publish', 'post_type' => 'hp_service' ) );
    update_post_meta( $id, '_hp_position', $s['pos'] );
    update_post_meta( $id, '_hp_active', 'Y' );
    hp_ok( "Service: {$s['title']} (ID: $id)" );
}

// ============================================================
// 3. IMPORT TESTIMONIALS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Testimonials ---' );

$testimonials = array(
    array( 'quote' => 'Thank-you for helping us change from our long term accountant to your services so smoothly. Your help has been much appreciated.', 'name' => 'Donna Bell', 'title' => 'Partner', 'biz' => 'C & D Bell Constructions', 'pos' => 1 ),
    array( 'quote' => 'Holland Price are extremely helpful and efficient each year when they do our tax and also throughout the year with updates and any questions we have. We love that they are a local business and would recommend their services to any small business in the area looking for a professional company to look after their tax.', 'name' => 'Jason & Nicki Andrews', 'title' => '', 'biz' => 'Dayboro Mobile Pool Supplies & Service', 'pos' => 2 ),
    array( 'quote' => 'I would recommend Holland Price & Associates to anyone that wants to have honest service. Scott is there for taxation and business management advice and always happy to help.', 'name' => 'David Joseph', 'title' => 'Director', 'biz' => 'Joseph Group', 'pos' => 3 ),
    array( 'quote' => 'We have worked with Holland Price Associates since its inception, and would never consider working with anyone else. HPA strikes the perfect balance between professional accounting while giving the personal touches that let you know that they have your business and family needs as their utmost priority. We have found them to be exceptional in their service, promptness, knowledge and trustworthiness. We have referred numerous family members and friends to HPA, and have heard nothing but positive experiences. We would give HPA our highest endorsement and can only see them going from strength to strength.', 'name' => 'Dr Simon Lucey', 'title' => 'Associate Research Professor', 'biz' => 'The Robotics Institute at CMU Pittsburgh USA', 'pos' => 4 ),
);

foreach ( $testimonials as $t ) {
    $slug = sanitize_title( $t['name'] . '-testimonial' );
    $exists = get_posts( array( 'post_type' => 'hp_testimonial', 'name' => $slug, 'posts_per_page' => 1 ) );
    if ( $exists ) { hp_log( "Testimonial from '{$t['name']}' exists, skipping" ); continue; }
    $id = wp_insert_post( array( 'post_title' => $t['name'] . ' - Testimonial', 'post_name' => $slug, 'post_content' => $t['quote'], 'post_status' => 'publish', 'post_type' => 'hp_testimonial' ) );
    update_post_meta( $id, '_hp_client_name', $t['name'] );
    update_post_meta( $id, '_hp_client_title', $t['title'] );
    update_post_meta( $id, '_hp_business_name', $t['biz'] );
    update_post_meta( $id, '_hp_position', $t['pos'] );
    update_post_meta( $id, '_hp_active', 'Y' );
    hp_ok( "Testimonial: {$t['name']} (ID: $id)" );
}

// ============================================================
// 4. IMPORT DOWNLOAD CATEGORIES
// ============================================================
hp_log( '' );
hp_log( '--- Creating Download Categories ---' );

$cat_names = array( 'Our Fees', 'Our Articles', 'Our Templates' );
$cat_ids = array();
foreach ( $cat_names as $cn ) {
    $ex = term_exists( $cn, 'download_category' );
    if ( $ex ) { $cat_ids[ $cn ] = $ex['term_id']; hp_log( "Category '$cn' exists" ); }
    else {
        $r = wp_insert_term( $cn, 'download_category' );
        if ( ! is_wp_error( $r ) ) { $cat_ids[ $cn ] = $r['term_id']; hp_ok( "Category: $cn" ); }
    }
}

// ============================================================
// 5. IMPORT DOWNLOADS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Downloads ---' );

$rails_cat_map = array( 1 => 'Our Fees', 2 => 'Our Articles', 3 => 'Our Templates' );
$s3_base = 'https://hpaccountants.s3.amazonaws.com/downloads/attachments/000/000/';

$downloads = array(
    array( 'id' => 5,  'title' => 'To employ or not to employ?', 'file' => 'To_employ_or_not_to_employ_-_Article.pdf', 'type' => 'pdf', 'views' => 1603, 'cat' => 3 ),
    array( 'id' => 6,  'title' => 'Cloud Accounting, Is it right for you?', 'file' => 'Cloud_Accounting_-_Holland_Price_Feb_19_normal.pdf', 'type' => 'pdf', 'views' => 1499, 'cat' => 3 ),
    array( 'id' => 7,  'title' => 'Top 5 small business mistakes', 'file' => 'Sept_15_Edition_HP_A_-_Grapevine_normal.pdf', 'type' => 'pdf', 'views' => 1413, 'cat' => 3 ),
    array( 'id' => 8,  'title' => 'New Client Information Form', 'file' => 'New_Client_Information_Form.doc', 'type' => 'doc', 'views' => 1513, 'cat' => 2 ),
    array( 'id' => 24, 'title' => 'Newsletter Quarter 3 2017', 'file' => 'HP_BM_Q3_2017.pdf', 'type' => 'pdf', 'views' => 551, 'cat' => 3 ),
    array( 'id' => 25, 'title' => 'Newsletter Quarter 4 2017', 'file' => 'HP_BM_Q4_2017.pdf', 'type' => 'pdf', 'views' => 748, 'cat' => 3 ),
    array( 'id' => 26, 'title' => 'Newsletter Quarter 1 2018', 'file' => 'HP_BM_Q1_2018.pdf', 'type' => 'pdf', 'views' => 669, 'cat' => 3 ),
    array( 'id' => 27, 'title' => 'Newsletter Quarter 2 2018', 'file' => 'HP_BM_Q2_2018.pdf', 'type' => 'pdf', 'views' => 632, 'cat' => 3 ),
    array( 'id' => 28, 'title' => 'Year End Strategies 2018', 'file' => 'Year_End_Strategies_2018.pdf', 'type' => 'pdf', 'views' => 626, 'cat' => 3 ),
    array( 'id' => 29, 'title' => 'Newsletter Quarter 3 2018', 'file' => 'HP_BM_Q3_2018.pdf', 'type' => 'pdf', 'views' => 750, 'cat' => 3 ),
    array( 'id' => 30, 'title' => 'Newsletter Quarter 4 2018', 'file' => 'HP_BM_Q4_2018.pdf', 'type' => 'pdf', 'views' => 734, 'cat' => 3 ),
    array( 'id' => 31, 'title' => 'Newsletter Quarter 1 2019', 'file' => 'HP_BM_Q1_2019.pdf', 'type' => 'pdf', 'views' => 632, 'cat' => 3 ),
    array( 'id' => 32, 'title' => 'Newsletter Quarter 2 2019', 'file' => 'HP_BM_Q2_2019.pdf', 'type' => 'pdf', 'views' => 649, 'cat' => 3 ),
    array( 'id' => 33, 'title' => 'End of Year Update 2019', 'file' => 'EOYU2019.pdf', 'type' => 'pdf', 'views' => 726, 'cat' => 3 ),
    array( 'id' => 34, 'title' => 'Newsletter Quarter 3 2019', 'file' => 'HP_BM_Q3_2019.pdf', 'type' => 'pdf', 'views' => 723, 'cat' => 3 ),
    array( 'id' => 35, 'title' => 'Newsletter Quarter 4 2019', 'file' => 'HP_BM_Q4_2019.pdf', 'type' => 'pdf', 'views' => 734, 'cat' => 3 ),
    array( 'id' => 36, 'title' => 'Newsletter Quarter 1 2020', 'file' => 'HP_BM_Q1_2020.pdf', 'type' => 'pdf', 'views' => 674, 'cat' => 3 ),
    array( 'id' => 37, 'title' => 'Seven things to know about COVID-19', 'file' => 'Seven_things_you_need_to_know_about_COVID-19_and_how_it_affects_you_and_your_business.pdf', 'type' => 'pdf', 'views' => 742, 'cat' => 3 ),
    array( 'id' => 38, 'title' => 'Newsletter Quarter 2 2020', 'file' => 'HP_BM_Q2_2020.pdf', 'type' => 'pdf', 'views' => 755, 'cat' => 3 ),
    array( 'id' => 39, 'title' => 'Newsletter Quarter 3 2020', 'file' => 'Newsletter_Quarter_2_2020.pdf', 'type' => 'pdf', 'views' => 932, 'cat' => 3 ),
    array( 'id' => 48, 'title' => 'Newsletter Quarter 4 2020', 'file' => 'Newsletter_Quarter_4_2020.pdf', 'type' => 'pdf', 'views' => 672, 'cat' => 3 ),
    array( 'id' => 52, 'title' => 'Our Fee Structure from 1 July 2022', 'file' => 'Our_Fee_Structure_from_1_July_2022_-_General.pdf', 'type' => 'pdf', 'views' => 703, 'cat' => 1 ),
    array( 'id' => 53, 'title' => 'Client Checklist for 2022', 'file' => 'CLIENT_CHECKLIST_2022.pdf', 'type' => 'pdf', 'views' => 643, 'cat' => 2 ),
);

foreach ( $downloads as $dl ) {
    $exists = get_posts( array( 'post_type' => 'hp_download', 'title' => $dl['title'], 'posts_per_page' => 1 ) );
    if ( $exists ) { hp_log( "Download '{$dl['title']}' exists, skipping" ); continue; }
    $id_pad = str_pad( $dl['id'], 3, '0', STR_PAD_LEFT );
    $s3_url = $s3_base . $id_pad . '/original/' . $dl['file'];
    $id = wp_insert_post( array( 'post_title' => $dl['title'], 'post_content' => '', 'post_status' => 'publish', 'post_type' => 'hp_download' ) );
    update_post_meta( $id, '_hp_s3_url', $s3_url );
    update_post_meta( $id, '_hp_file_type', $dl['type'] );
    update_post_meta( $id, '_hp_view_count', $dl['views'] );
    $cn = isset( $rails_cat_map[ $dl['cat'] ] ) ? $rails_cat_map[ $dl['cat'] ] : 'Our Templates';
    if ( isset( $cat_ids[ $cn ] ) ) { wp_set_object_terms( $id, (int) $cat_ids[ $cn ], 'download_category' ); }
    hp_ok( "Download: {$dl['title']} (ID: $id)" );
}

// ============================================================
// 6. IMPORT LINKS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Links ---' );

$links = array(
    array( 'title' => 'XERO accounting software', 'desc' => 'Partners in Xero. Log in online anytime, anywhere on your Mac, PC, tablet or phone and see up-to-date financials.', 'url' => 'https://www.xero.com/au/', 'pos' => 1, 'active' => 'Y', 'logo' => 'logo-xero.png' ),
    array( 'title' => 'Lightspeed point of sale software', 'desc' => 'We are partners for Lightspeed point-of-sale, inventory, and customer loyalty software.', 'url' => 'https://www.lightspeedhq.com/au/', 'pos' => 2, 'active' => 'Y', 'logo' => 'logo-lightspeed.png' ),
    array( 'title' => 'CLASS Super software', 'desc' => 'Online SMSF accounting solution. Access up to date reports for your SMSF anytime online.', 'url' => 'http://www.classsuper.com.au/', 'pos' => 3, 'active' => 'Y', 'logo' => 'logo-class.png' ),
    array( 'title' => 'Australian Taxation Office', 'desc' => 'The Australian Tax Office website', 'url' => 'http://www.ato.gov.au', 'pos' => 4, 'active' => 'Y', 'logo' => 'logo-ato.png' ),
    array( 'title' => 'Superannuation', 'desc' => 'A place for all things super.', 'url' => 'http://www.super.com.au', 'pos' => 5, 'active' => 'N' ),
    array( 'title' => 'ABR', 'desc' => 'Registration of your business with the Australian Government.', 'url' => 'http://www.abr.gov.au', 'pos' => 6, 'active' => 'Y', 'logo' => 'logo-abr.png' ),
    array( 'title' => 'ASIC', 'desc' => 'Australian Securities & Investment Commission', 'url' => 'http://www.asic.gov.au/', 'pos' => 7, 'active' => 'Y', 'logo' => 'logo-asic.png' ),
);

foreach ( $links as $l ) {
    $exists = get_posts( array( 'post_type' => 'hp_link', 'title' => $l['title'], 'posts_per_page' => 1 ) );
    if ( $exists ) { hp_log( "Link '{$l['title']}' exists, skipping" ); continue; }
    $id = wp_insert_post( array( 'post_title' => $l['title'], 'post_content' => $l['desc'], 'post_status' => 'publish', 'post_type' => 'hp_link' ) );
    update_post_meta( $id, '_hp_url', $l['url'] );
    update_post_meta( $id, '_hp_position', $l['pos'] );
    update_post_meta( $id, '_hp_active', $l['active'] );
    if ( ! empty( $l['logo'] ) ) {
        update_post_meta( $id, '_hp_logo', $l['logo'] );
    }
    hp_ok( "Link: {$l['title']} (ID: $id)" );
}

// ============================================================
// 7. CONFIGURE WORDPRESS
// ============================================================
hp_log( '' );
hp_log( '--- Configuring WordPress ---' );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
hp_ok( "Static front page set to Home (ID: $home_id)" );

update_option( 'permalink_structure', '/%postname%/' );
hp_ok( 'Permalinks: /%postname%/' );

update_option( 'blogname', 'Holland Price & Associates' );
update_option( 'blogdescription', 'Professional Accounting Services' );
hp_ok( 'Site title and tagline updated' );

update_option( 'timezone_string', 'Australia/Brisbane' );
hp_ok( 'Timezone: Australia/Brisbane' );

// ============================================================
// 8. CREATE NAV MENUS
// ============================================================
hp_log( '' );
hp_log( '--- Creating Navigation Menus ---' );

// Get page IDs for menu items.
$about_page_obj   = get_page_by_path( 'about' );
$contact_page_obj = get_page_by_path( 'contact' );

// -- Primary Menu: Home, About, Services, Contact --
$primary_menu_name = 'Primary Menu';
$primary_menu = wp_get_nav_menu_object( $primary_menu_name );
if ( ! $primary_menu ) {
    $primary_menu_id = wp_create_nav_menu( $primary_menu_name );

    wp_update_nav_menu_item( $primary_menu_id, 0, array(
        'menu-item-title'   => 'Home',
        'menu-item-url'     => home_url( '/' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 1,
    ) );

    if ( $about_page_obj ) {
        wp_update_nav_menu_item( $primary_menu_id, 0, array(
            'menu-item-title'     => 'About',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $about_page_obj->ID,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => 2,
        ) );
    }

    wp_update_nav_menu_item( $primary_menu_id, 0, array(
        'menu-item-title'   => 'Services',
        'menu-item-url'     => get_post_type_archive_link( 'hp_service' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 3,
    ) );

    if ( $contact_page_obj ) {
        wp_update_nav_menu_item( $primary_menu_id, 0, array(
            'menu-item-title'     => 'Contact',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $contact_page_obj->ID,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => 4,
        ) );
    }

    // Assign to primary location.
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary'] = $primary_menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    hp_ok( "Primary Menu created (Home, About, Services, Contact)" );
} else {
    hp_log( 'Primary Menu already exists' );
}

// -- Footer Menu: Home, About, Services, Testimonials, Downloads, Contact --
$footer_menu_name = 'Footer Menu';
$footer_menu = wp_get_nav_menu_object( $footer_menu_name );
if ( ! $footer_menu ) {
    $footer_menu_id = wp_create_nav_menu( $footer_menu_name );

    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'   => 'Home',
        'menu-item-url'     => home_url( '/' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 1,
    ) );

    if ( $about_page_obj ) {
        wp_update_nav_menu_item( $footer_menu_id, 0, array(
            'menu-item-title'     => 'About',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $about_page_obj->ID,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => 2,
        ) );
    }

    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'   => 'Services',
        'menu-item-url'     => get_post_type_archive_link( 'hp_service' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 3,
    ) );

    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'   => 'Testimonials',
        'menu-item-url'     => get_post_type_archive_link( 'hp_testimonial' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 4,
    ) );

    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'   => 'Downloads',
        'menu-item-url'     => get_post_type_archive_link( 'hp_download' ),
        'menu-item-status'  => 'publish',
        'menu-item-type'    => 'custom',
        'menu-item-position' => 5,
    ) );

    if ( $contact_page_obj ) {
        wp_update_nav_menu_item( $footer_menu_id, 0, array(
            'menu-item-title'     => 'Contact',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $contact_page_obj->ID,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-position'  => 6,
        ) );
    }

    // Assign to footer location.
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['footer'] = $footer_menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    hp_ok( "Footer Menu created (Home, About, Services, Testimonials, Downloads, Contact)" );
} else {
    hp_log( 'Footer Menu already exists' );
}

// ============================================================
// 9. MAILING LIST TABLE
// ============================================================
hp_log( '' );
hp_log( '--- Mailing List ---' );

hp_newsletter_create_table();

global $wpdb;
$table = $wpdb->prefix . 'hp_mailinglist';
$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE email = %s", 'russell.adcock1@gmail.com' ) );
if ( ! $exists ) {
    $wpdb->insert( $table, array( 'name' => 'Russell', 'email' => 'russell.adcock1@gmail.com' ), array( '%s', '%s' ) );
    hp_ok( 'Added subscriber: russell.adcock1@gmail.com' );
}

// ============================================================
hp_log( '' );
hp_ok( '=== Migration Complete ===' );
hp_log( '' );
hp_log( 'Next steps:' );
hp_log( '1. Install Contact Form 7 plugin' );
hp_log( '2. Create contact form (Name, Email, Message)' );
hp_log( '3. Set mail to: price@hpaccountants.com.au' );
hp_log( '4. Update Contact page with CF7 shortcode' );
hp_log( '5. Flush permalinks: Settings > Permalinks > Save' );
