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
    <?php
    // Basic meta description fallback (overridden by SEO plugins like Rank Math).
    if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) ) :
        if ( is_front_page() ) {
            $hp_description = 'Holland Price & Associates — professional accounting services for small to medium businesses in Dayboro, QLD. Approachable. Passionate. Accurate.';
        } elseif ( is_singular() && has_excerpt() ) {
            $hp_description = wp_strip_all_tags( get_the_excerpt() );
        } elseif ( is_post_type_archive( 'hp_service' ) ) {
            $hp_description = 'Accounting services offered by Holland Price & Associates — tax, BAS, bookkeeping, business advisory and more.';
        } elseif ( is_post_type_archive( 'hp_download' ) ) {
            $hp_description = 'Download tax guides, checklists and business templates from Holland Price & Associates.';
        } else {
            $hp_description = get_bloginfo( 'description' );
        }
        if ( $hp_description ) :
    ?>
    <meta name="description" content="<?php echo esc_attr( $hp_description ); ?>">
    <?php
        endif;
    endif;
    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="nav-bar" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'hpaccountants' ); ?>">
    <div class="container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-bar-logo">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>"
                 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
        </a>
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

<header class="site-header">
    <div class="container header-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>"
                 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                 class="logo">
        </a>
        <p class="tagline">Approachable. Passionate. Accurate.</p>
    </div>

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
