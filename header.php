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
