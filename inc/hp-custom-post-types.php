<?php
/**
 * HP Accountants - Custom Post Types and Taxonomy
 *
 * Registers custom post types (Services, Testimonials, Downloads, Partner Links)
 * and the Download Category taxonomy. Also modifies archive queries for each CPT.
 *
 * @package HP_Accountants
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post types.
 */
function hp_register_custom_post_types() {

	// ---- Service ----
	register_post_type( 'hp_service', array(
		'labels' => array(
			'name'                  => __( 'Services', 'hpaccountants' ),
			'singular_name'         => __( 'Service', 'hpaccountants' ),
			'add_new'               => __( 'Add New', 'hpaccountants' ),
			'add_new_item'          => __( 'Add New Service', 'hpaccountants' ),
			'edit_item'             => __( 'Edit Service', 'hpaccountants' ),
			'new_item'              => __( 'New Service', 'hpaccountants' ),
			'view_item'             => __( 'View Service', 'hpaccountants' ),
			'search_items'          => __( 'Search Services', 'hpaccountants' ),
			'not_found'             => __( 'No services found', 'hpaccountants' ),
			'not_found_in_trash'    => __( 'No services found in Trash', 'hpaccountants' ),
			'all_items'             => __( 'All Services', 'hpaccountants' ),
			'archives'              => __( 'Service Archives', 'hpaccountants' ),
			'insert_into_item'      => __( 'Insert into service', 'hpaccountants' ),
			'uploaded_to_this_item' => __( 'Uploaded to this service', 'hpaccountants' ),
			'menu_name'             => __( 'Services', 'hpaccountants' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-clipboard',
		'supports'     => array( 'title', 'editor' ),
		'rewrite'      => array(
			'slug'       => 'services',
			'with_front' => false,
		),
	) );

	// ---- Testimonial ----
	register_post_type( 'hp_testimonial', array(
		'labels' => array(
			'name'                  => __( 'Testimonials', 'hpaccountants' ),
			'singular_name'         => __( 'Testimonial', 'hpaccountants' ),
			'add_new'               => __( 'Add New', 'hpaccountants' ),
			'add_new_item'          => __( 'Add New Testimonial', 'hpaccountants' ),
			'edit_item'             => __( 'Edit Testimonial', 'hpaccountants' ),
			'new_item'              => __( 'New Testimonial', 'hpaccountants' ),
			'view_item'             => __( 'View Testimonial', 'hpaccountants' ),
			'search_items'          => __( 'Search Testimonials', 'hpaccountants' ),
			'not_found'             => __( 'No testimonials found', 'hpaccountants' ),
			'not_found_in_trash'    => __( 'No testimonials found in Trash', 'hpaccountants' ),
			'all_items'             => __( 'All Testimonials', 'hpaccountants' ),
			'archives'              => __( 'Testimonial Archives', 'hpaccountants' ),
			'insert_into_item'      => __( 'Insert into testimonial', 'hpaccountants' ),
			'uploaded_to_this_item' => __( 'Uploaded to this testimonial', 'hpaccountants' ),
			'menu_name'             => __( 'Testimonials', 'hpaccountants' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-format-quote',
		'supports'     => array( 'title', 'editor' ),
		'rewrite'      => array(
			'slug'       => 'testimonials',
			'with_front' => false,
		),
	) );

	// ---- Download ----
	register_post_type( 'hp_download', array(
		'labels' => array(
			'name'                  => __( 'Downloads', 'hpaccountants' ),
			'singular_name'         => __( 'Download', 'hpaccountants' ),
			'add_new'               => __( 'Add New', 'hpaccountants' ),
			'add_new_item'          => __( 'Add New Download', 'hpaccountants' ),
			'edit_item'             => __( 'Edit Download', 'hpaccountants' ),
			'new_item'              => __( 'New Download', 'hpaccountants' ),
			'view_item'             => __( 'View Download', 'hpaccountants' ),
			'search_items'          => __( 'Search Downloads', 'hpaccountants' ),
			'not_found'             => __( 'No downloads found', 'hpaccountants' ),
			'not_found_in_trash'    => __( 'No downloads found in Trash', 'hpaccountants' ),
			'all_items'             => __( 'All Downloads', 'hpaccountants' ),
			'archives'              => __( 'Download Archives', 'hpaccountants' ),
			'insert_into_item'      => __( 'Insert into download', 'hpaccountants' ),
			'uploaded_to_this_item' => __( 'Uploaded to this download', 'hpaccountants' ),
			'menu_name'             => __( 'Downloads', 'hpaccountants' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-download',
		'supports'     => array( 'title', 'editor' ),
		'rewrite'      => array(
			'slug'       => 'downloads',
			'with_front' => false,
		),
	) );

	// ---- Partner Link ----
	register_post_type( 'hp_link', array(
		'labels' => array(
			'name'                  => __( 'Partner Links', 'hpaccountants' ),
			'singular_name'         => __( 'Partner Link', 'hpaccountants' ),
			'add_new'               => __( 'Add New', 'hpaccountants' ),
			'add_new_item'          => __( 'Add New Partner Link', 'hpaccountants' ),
			'edit_item'             => __( 'Edit Partner Link', 'hpaccountants' ),
			'new_item'              => __( 'New Partner Link', 'hpaccountants' ),
			'view_item'             => __( 'View Partner Link', 'hpaccountants' ),
			'search_items'          => __( 'Search Partner Links', 'hpaccountants' ),
			'not_found'             => __( 'No partner links found', 'hpaccountants' ),
			'not_found_in_trash'    => __( 'No partner links found in Trash', 'hpaccountants' ),
			'all_items'             => __( 'All Partner Links', 'hpaccountants' ),
			'archives'              => __( 'Partner Link Archives', 'hpaccountants' ),
			'insert_into_item'      => __( 'Insert into partner link', 'hpaccountants' ),
			'uploaded_to_this_item' => __( 'Uploaded to this partner link', 'hpaccountants' ),
			'menu_name'             => __( 'Partner Links', 'hpaccountants' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-links',
		'supports'     => array( 'title', 'editor' ),
		'rewrite'      => array(
			'slug'       => 'links',
			'with_front' => false,
		),
	) );
}
add_action( 'init', 'hp_register_custom_post_types' );

/**
 * Register custom taxonomies.
 */
function hp_register_custom_taxonomies() {

	register_taxonomy( 'download_category', 'hp_download', array(
		'labels' => array(
			'name'              => __( 'Download Categories', 'hpaccountants' ),
			'singular_name'     => __( 'Download Category', 'hpaccountants' ),
			'search_items'      => __( 'Search Download Categories', 'hpaccountants' ),
			'all_items'         => __( 'All Download Categories', 'hpaccountants' ),
			'parent_item'       => __( 'Parent Download Category', 'hpaccountants' ),
			'parent_item_colon' => __( 'Parent Download Category:', 'hpaccountants' ),
			'edit_item'         => __( 'Edit Download Category', 'hpaccountants' ),
			'update_item'       => __( 'Update Download Category', 'hpaccountants' ),
			'add_new_item'      => __( 'Add New Download Category', 'hpaccountants' ),
			'new_item_name'     => __( 'New Download Category Name', 'hpaccountants' ),
			'menu_name'         => __( 'Categories', 'hpaccountants' ),
		),
		'hierarchical'      => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => array(
			'slug'       => 'download-category',
			'with_front' => false,
		),
	) );
}
add_action( 'init', 'hp_register_custom_taxonomies' );

/**
 * Modify archive queries for custom post types.
 *
 * - Services:     ordered by _hp_position ASC, only active (Y), all posts.
 * - Testimonials: ordered by _hp_position ASC, only active (Y), all posts.
 * - Downloads:    ordered by _hp_view_count DESC, all posts.
 * - Links:        ordered by _hp_position ASC, only active (Y), all posts.
 *
 * Only applies on the frontend main query (not admin).
 */
function hp_custom_archive_queries( $query ) {

	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Services archive.
	if ( $query->is_post_type_archive( 'hp_service' ) ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_hp_position' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
		$query->set( 'meta_query', array(
			array(
				'key'     => '_hp_active',
				'value'   => 'Y',
				'compare' => '=',
			),
		) );
		return;
	}

	// Testimonials archive.
	if ( $query->is_post_type_archive( 'hp_testimonial' ) ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_hp_position' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
		$query->set( 'meta_query', array(
			array(
				'key'     => '_hp_active',
				'value'   => 'Y',
				'compare' => '=',
			),
		) );
		return;
	}

	// Downloads archive.
	if ( $query->is_post_type_archive( 'hp_download' ) ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_hp_view_count' );
		$query->set( 'order', 'DESC' );
		$query->set( 'posts_per_page', -1 );
		return;
	}

	// Links archive.
	if ( $query->is_post_type_archive( 'hp_link' ) ) {
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_hp_position' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
		$query->set( 'meta_query', array(
			array(
				'key'     => '_hp_active',
				'value'   => 'Y',
				'compare' => '=',
			),
		) );
		return;
	}
}
add_action( 'pre_get_posts', 'hp_custom_archive_queries' );
