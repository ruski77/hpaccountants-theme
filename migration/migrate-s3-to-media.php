<?php
/**
 * HP Accountants - Migrate S3 Downloads to WordPress Media Library
 *
 * One-time migration script. Pulls files from S3 URLs and imports them
 * into the WordPress Media Library, updating each download post's
 * _hp_file_id meta field.
 *
 * Usage: Visit /wp-admin/ and go to Tools > Migrate S3 Downloads
 *
 * @package HP_Accountants
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the migration admin page under Tools.
 */
function hp_register_migration_page() {
	add_management_page(
		__( 'Migrate S3 Downloads', 'hpaccountants' ),
		__( 'Migrate S3 Downloads', 'hpaccountants' ),
		'manage_options',
		'hp-migrate-s3',
		'hp_migrate_s3_page'
	);
}
add_action( 'admin_menu', 'hp_register_migration_page' );

/**
 * Render the migration admin page and handle form submission.
 */
function hp_migrate_s3_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'Unauthorized.', 'hpaccountants' ) );
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Migrate S3 Downloads to Media Library', 'hpaccountants' ) . '</h1>';

	// Run migration on form submit.
	if ( isset( $_POST['hp_run_migration'] ) && check_admin_referer( 'hp_migrate_s3' ) ) {
		hp_run_s3_migration();
	} else {
		// Show status and form.
		$downloads = get_posts( array(
			'post_type'      => 'hp_download',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_hp_s3_url',
					'value'   => '',
					'compare' => '!=',
				),
			),
		) );

		$total     = count( $downloads );
		$migrated  = 0;
		$pending   = 0;

		foreach ( $downloads as $dl ) {
			if ( get_post_meta( $dl->ID, '_hp_file_id', true ) ) {
				$migrated++;
			} else {
				$pending++;
			}
		}

		echo '<p>' . sprintf(
			esc_html__( 'Found %d downloads with S3 URLs. %d already migrated, %d pending.', 'hpaccountants' ),
			$total, $migrated, $pending
		) . '</p>';

		if ( $pending > 0 ) {
			echo '<form method="post">';
			wp_nonce_field( 'hp_migrate_s3' );
			echo '<p><button type="submit" name="hp_run_migration" class="button button-primary">';
			esc_html_e( 'Run Migration', 'hpaccountants' );
			echo '</button></p>';
			echo '</form>';
		} else {
			echo '<p><strong>' . esc_html__( 'All downloads have been migrated.', 'hpaccountants' ) . '</strong></p>';
		}
	}

	echo '</div>';
}

/**
 * Execute the S3 to Media Library migration.
 */
function hp_run_s3_migration() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$downloads = get_posts( array(
		'post_type'      => 'hp_download',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => '_hp_s3_url',
				'value'   => '',
				'compare' => '!=',
			),
			array(
				'key'     => '_hp_file_id',
				'compare' => 'NOT EXISTS',
			),
		),
	) );

	if ( empty( $downloads ) ) {
		echo '<p>' . esc_html__( 'No pending downloads to migrate.', 'hpaccountants' ) . '</p>';
		return;
	}

	$success = 0;
	$failed  = array();

	echo '<h2>' . esc_html__( 'Migration Results', 'hpaccountants' ) . '</h2>';
	echo '<table class="widefat"><thead><tr><th>Title</th><th>S3 URL</th><th>Status</th></tr></thead><tbody>';

	foreach ( $downloads as $dl ) {
		$s3_url = get_post_meta( $dl->ID, '_hp_s3_url', true );

		echo '<tr>';
		echo '<td>' . esc_html( $dl->post_title ) . '</td>';
		echo '<td><small>' . esc_html( $s3_url ) . '</small></td>';

		// Download the file from S3.
		$tmp_file = download_url( $s3_url, 30 );

		if ( is_wp_error( $tmp_file ) ) {
			$failed[] = $dl->post_title;
			echo '<td style="color:red;">FAILED: ' . esc_html( $tmp_file->get_error_message() ) . '</td>';
			echo '</tr>';
			continue;
		}

		// Prepare file array for sideload.
		$file_array = array(
			'name'     => basename( wp_parse_url( $s3_url, PHP_URL_PATH ) ),
			'tmp_name' => $tmp_file,
		);

		// Sideload into Media Library, attached to the download post.
		$attachment_id = media_handle_sideload( $file_array, $dl->ID );

		if ( is_wp_error( $attachment_id ) ) {
			$failed[] = $dl->post_title;
			echo '<td style="color:red;">FAILED: ' . esc_html( $attachment_id->get_error_message() ) . '</td>';
			echo '</tr>';
			// Clean up temp file if sideload failed.
			if ( file_exists( $tmp_file ) ) {
				unlink( $tmp_file );
			}
			continue;
		}

		// Save attachment ID to download post.
		update_post_meta( $dl->ID, '_hp_file_id', $attachment_id );

		// Auto-detect file type from MIME.
		$mime = get_post_mime_type( $attachment_id );
		$type_map = array(
			'application/pdf'                                                          => 'pdf',
			'application/msword'                                                       => 'doc',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
			'application/vnd.ms-excel'                                                 => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
		);
		if ( isset( $type_map[ $mime ] ) ) {
			update_post_meta( $dl->ID, '_hp_file_type', $type_map[ $mime ] );
		}

		$success++;
		echo '<td style="color:green;">OK (attachment #' . esc_html( $attachment_id ) . ')</td>';
		echo '</tr>';
	}

	echo '</tbody></table>';

	echo '<h3>' . esc_html__( 'Summary', 'hpaccountants' ) . '</h3>';
	echo '<p>' . sprintf( esc_html__( 'Success: %d | Failed: %d', 'hpaccountants' ), $success, count( $failed ) ) . '</p>';

	if ( ! empty( $failed ) ) {
		echo '<p><strong>' . esc_html__( 'Failed downloads (upload manually via Media Library):', 'hpaccountants' ) . '</strong></p>';
		echo '<ul>';
		foreach ( $failed as $title ) {
			echo '<li>' . esc_html( $title ) . '</li>';
		}
		echo '</ul>';
	}
}
