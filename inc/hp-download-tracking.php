<?php
/**
 * HP Accountants Download Tracking
 *
 * Handles AJAX view count tracking for hp_download posts
 * and enqueues the download tracking script.
 *
 * @package WordPress
 * @subpackage Ruki
 * @since 1.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ========================================================
// 1. AJAX handler: track download views
// ========================================================
function hp_track_download() {
	// Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hp_download_tracking_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed.' ) );
	}

	// Validate post_id.
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID.' ) );
	}

	// Ensure the post is an hp_download.
	if ( get_post_type( $post_id ) !== 'hp_download' ) {
		wp_send_json_error( array( 'message' => 'Invalid post type.' ) );
	}

	// Increment view count.
	$current_count = (int) get_post_meta( $post_id, '_hp_view_count', true );
	$new_count     = $current_count + 1;

	update_post_meta( $post_id, '_hp_view_count', $new_count );

	wp_send_json_success( array( 'count' => $new_count ) );
}
add_action( 'wp_ajax_hp_track_download', 'hp_track_download' );
add_action( 'wp_ajax_nopriv_hp_track_download', 'hp_track_download' );

// ========================================================
// 2. Enqueue download tracking script on archive pages
// ========================================================
function hp_download_tracking_enqueue_scripts() {
	if ( ! is_post_type_archive( 'hp_download' ) ) {
		return;
	}

	wp_enqueue_script(
		'hp-downloads',
		get_template_directory_uri() . '/js/hp-downloads.js',
		array( 'jquery' ),
		filemtime( get_template_directory() . '/js/hp-downloads.js' ),
		true
	);

	wp_localize_script( 'hp-downloads', 'hp_downloads', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'hp_download_tracking_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'hp_download_tracking_enqueue_scripts' );
