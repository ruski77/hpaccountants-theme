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
 * Recommend CF7 plugin.
 */
function hp_require_cf7() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
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
