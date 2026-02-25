<?php
/**
 * One-time script to update partner links.
 *
 * - Removes Point & Claim (no longer exists)
 * - Updates Vend → Lightspeed
 * - Assigns logos to all partners
 *
 * Usage: Visit in browser while logged in as admin, or run via WP-CLI:
 *   wp eval-file wp-content/themes/hpaccountants-theme/migration/update-partner-logos.php
 *
 * DELETE THIS FILE AFTER RUNNING.
 */

if ( ! defined( 'ABSPATH' ) ) {
    $wp_load = dirname( __FILE__ ) . '/../../../../wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    } else {
        die( 'Cannot find wp-load.php' );
    }
}

if ( ! current_user_can( 'manage_options' ) && ! defined( 'WP_CLI' ) ) {
    die( 'Unauthorized' );
}

echo '<pre>';

// 1. Remove Point & Claim.
$point_claim = get_posts( array(
    'post_type'      => 'hp_link',
    'title'          => 'Point & Claim software',
    'posts_per_page' => 1,
) );
if ( ! empty( $point_claim ) ) {
    wp_trash_post( $point_claim[0]->ID );
    echo "TRASHED: Point & Claim software (ID: {$point_claim[0]->ID})\n";
} else {
    echo "NOT FOUND: Point & Claim software (already removed?)\n";
}

// 2. Update Vend → Lightspeed.
$vend = get_posts( array(
    'post_type'      => 'hp_link',
    'title'          => 'VEND point of sale software',
    'posts_per_page' => 1,
) );
if ( ! empty( $vend ) ) {
    $vend_id = $vend[0]->ID;
    wp_update_post( array(
        'ID'           => $vend_id,
        'post_title'   => 'Lightspeed point of sale software',
        'post_content' => 'We are partners for Lightspeed point-of-sale, inventory, and customer loyalty software.',
    ) );
    update_post_meta( $vend_id, '_hp_url', 'https://www.lightspeedhq.com/au/' );
    update_post_meta( $vend_id, '_hp_logo', 'logo-lightspeed.png' );
    echo "UPDATED: Vend → Lightspeed (ID: {$vend_id})\n";
} else {
    echo "NOT FOUND: VEND point of sale software\n";
}

// 3. Update Xero logo.
$xero = get_posts( array(
    'post_type'      => 'hp_link',
    'title'          => 'XERO accounting software',
    'posts_per_page' => 1,
) );
if ( ! empty( $xero ) ) {
    update_post_meta( $xero[0]->ID, '_hp_logo', 'logo-xero.png' );
    echo "UPDATED: Xero logo (ID: {$xero[0]->ID})\n";
} else {
    echo "NOT FOUND: XERO accounting software\n";
}

// 4. Assign logos to remaining partners.
$logo_map = array(
    'CLASS Super software'       => 'logo-class.png',
    'Australian Taxation Office' => 'logo-ato.png',
    'ABR'                        => 'logo-abr.png',
    'ASIC'                       => 'logo-asic.png',
);

foreach ( $logo_map as $title => $logo_file ) {
    $posts = get_posts( array(
        'post_type'      => 'hp_link',
        'title'          => $title,
        'posts_per_page' => 1,
    ) );

    if ( empty( $posts ) ) {
        echo "NOT FOUND: {$title}\n";
        continue;
    }

    $post_id = $posts[0]->ID;
    update_post_meta( $post_id, '_hp_logo', $logo_file );
    echo "UPDATED: {$title} — logo set to {$logo_file} (ID: {$post_id})\n";
}

echo "\nDone. DELETE THIS FILE NOW.\n";
echo '</pre>';
