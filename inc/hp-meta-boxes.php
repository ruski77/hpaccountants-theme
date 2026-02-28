<?php
/**
 * HP Accountants - Meta Boxes and Admin Columns
 *
 * Registers meta boxes for custom post types, handles saving,
 * and adds custom admin columns.
 *
 * @package HP_Accountants
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// Meta Box Registration
// =============================================================================

/**
 * Register meta boxes for all HP custom post types.
 */
function hp_register_meta_boxes() {

	// Service meta box (side).
	add_meta_box(
		'hp_service_meta',
		__( 'Service Options', 'hpaccountants' ),
		'hp_service_meta_box_callback',
		'hp_service',
		'side',
		'default'
	);

	// Testimonial meta box (normal).
	add_meta_box(
		'hp_testimonial_meta',
		__( 'Testimonial Details', 'hpaccountants' ),
		'hp_testimonial_meta_box_callback',
		'hp_testimonial',
		'normal',
		'default'
	);

	// Download meta box (normal).
	add_meta_box(
		'hp_download_meta',
		__( 'Download Details', 'hpaccountants' ),
		'hp_download_meta_box_callback',
		'hp_download',
		'normal',
		'default'
	);

	// Link meta box (normal).
	add_meta_box(
		'hp_link_meta',
		__( 'Partner Link Details', 'hpaccountants' ),
		'hp_link_meta_box_callback',
		'hp_link',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'hp_register_meta_boxes' );

// =============================================================================
// Meta Box Callbacks
// =============================================================================

/**
 * Render the Service meta box.
 *
 * @param WP_Post $post Current post object.
 */
function hp_service_meta_box_callback( $post ) {
	wp_nonce_field( 'hp_save_meta', 'hp_meta_nonce' );

	$position = get_post_meta( $post->ID, '_hp_position', true );
	$active   = get_post_meta( $post->ID, '_hp_active', true );

	if ( '' === $active ) {
		$active = 'Y';
	}
	?>
	<p>
		<label for="hp_position"><?php esc_html_e( 'Position', 'hpaccountants' ); ?></label><br>
		<input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0" style="width:100%;">
	</p>
	<p>
		<label for="hp_active"><?php esc_html_e( 'Active', 'hpaccountants' ); ?></label><br>
		<select id="hp_active" name="hp_active" style="width:100%;">
			<option value="Y" <?php selected( $active, 'Y' ); ?>><?php esc_html_e( 'Yes', 'hpaccountants' ); ?></option>
			<option value="N" <?php selected( $active, 'N' ); ?>><?php esc_html_e( 'No', 'hpaccountants' ); ?></option>
		</select>
	</p>
	<?php
}

/**
 * Render the Testimonial meta box.
 *
 * @param WP_Post $post Current post object.
 */
function hp_testimonial_meta_box_callback( $post ) {
	wp_nonce_field( 'hp_save_meta', 'hp_meta_nonce' );

	$client_name  = get_post_meta( $post->ID, '_hp_client_name', true );
	$client_title = get_post_meta( $post->ID, '_hp_client_title', true );
	$business     = get_post_meta( $post->ID, '_hp_business_name', true );
	$position     = get_post_meta( $post->ID, '_hp_position', true );
	$active       = get_post_meta( $post->ID, '_hp_active', true );

	if ( '' === $active ) {
		$active = 'Y';
	}
	?>
	<table class="form-table">
		<tr>
			<th><label for="hp_client_name"><?php esc_html_e( 'Client Name', 'hpaccountants' ); ?></label></th>
			<td><input type="text" id="hp_client_name" name="hp_client_name" value="<?php echo esc_attr( $client_name ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="hp_client_title"><?php esc_html_e( 'Client Title', 'hpaccountants' ); ?></label></th>
			<td><input type="text" id="hp_client_title" name="hp_client_title" value="<?php echo esc_attr( $client_title ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="hp_business_name"><?php esc_html_e( 'Business Name', 'hpaccountants' ); ?></label></th>
			<td><input type="text" id="hp_business_name" name="hp_business_name" value="<?php echo esc_attr( $business ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="hp_position"><?php esc_html_e( 'Position', 'hpaccountants' ); ?></label></th>
			<td><input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="hp_active"><?php esc_html_e( 'Active', 'hpaccountants' ); ?></label></th>
			<td>
				<select id="hp_active" name="hp_active">
					<option value="Y" <?php selected( $active, 'Y' ); ?>><?php esc_html_e( 'Yes', 'hpaccountants' ); ?></option>
					<option value="N" <?php selected( $active, 'N' ); ?>><?php esc_html_e( 'No', 'hpaccountants' ); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Render the Download meta box.
 *
 * @param WP_Post $post Current post object.
 */
function hp_download_meta_box_callback( $post ) {
	wp_nonce_field( 'hp_save_meta', 'hp_meta_nonce' );

	$file_id    = get_post_meta( $post->ID, '_hp_file_id', true );
	$s3_url     = get_post_meta( $post->ID, '_hp_s3_url', true );
	$file_type  = get_post_meta( $post->ID, '_hp_file_type', true );
	$view_count = get_post_meta( $post->ID, '_hp_view_count', true );

	$file_name = '';
	if ( $file_id ) {
		$file_name = basename( get_attached_file( $file_id ) );
	}
	?>
	<table class="form-table">
		<tr>
			<th><?php esc_html_e( 'File', 'hpaccountants' ); ?></th>
			<td>
				<input type="hidden" id="hp_file_id" name="hp_file_id" value="<?php echo esc_attr( $file_id ); ?>">
				<button type="button" id="hp-select-file" class="button"><?php esc_html_e( 'Select File', 'hpaccountants' ); ?></button>
				<button type="button" id="hp-remove-file" class="button" style="<?php echo $file_id ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'hpaccountants' ); ?></button>
				<span id="hp-file-name" style="<?php echo $file_id ? '' : 'display:none;'; ?> margin-left:10px;"><?php echo esc_html( $file_name ); ?></span>
			</td>
		</tr>
		<?php if ( $s3_url && ! $file_id ) : ?>
		<tr>
			<th><label for="hp_s3_url"><?php esc_html_e( 'S3 URL (legacy)', 'hpaccountants' ); ?></label></th>
			<td>
				<input type="url" id="hp_s3_url" name="hp_s3_url" value="<?php echo esc_attr( $s3_url ); ?>" class="regular-text" readonly>
				<p class="description"><?php esc_html_e( 'Legacy S3 link. Select a file above to replace it.', 'hpaccountants' ); ?></p>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th><label for="hp_view_count"><?php esc_html_e( 'View Count', 'hpaccountants' ); ?></label></th>
			<td><input type="number" id="hp_view_count" name="hp_view_count" value="<?php echo esc_attr( $view_count ); ?>" min="0" class="small-text"></td>
		</tr>
	</table>
	<?php
}

/**
 * Render the Partner Link meta box.
 *
 * @param WP_Post $post Current post object.
 */
function hp_link_meta_box_callback( $post ) {
	wp_nonce_field( 'hp_save_meta', 'hp_meta_nonce' );

	$url      = get_post_meta( $post->ID, '_hp_url', true );
	$position = get_post_meta( $post->ID, '_hp_position', true );
	$active   = get_post_meta( $post->ID, '_hp_active', true );

	if ( '' === $active ) {
		$active = 'Y';
	}
	?>
	<table class="form-table">
		<tr>
			<th><label for="hp_url"><?php esc_html_e( 'Link URL', 'hpaccountants' ); ?></label></th>
			<td><input type="url" id="hp_url" name="hp_url" value="<?php echo esc_attr( $url ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="hp_position"><?php esc_html_e( 'Position', 'hpaccountants' ); ?></label></th>
			<td><input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="hp_active"><?php esc_html_e( 'Active', 'hpaccountants' ); ?></label></th>
			<td>
				<select id="hp_active" name="hp_active">
					<option value="Y" <?php selected( $active, 'Y' ); ?>><?php esc_html_e( 'Yes', 'hpaccountants' ); ?></option>
					<option value="N" <?php selected( $active, 'N' ); ?>><?php esc_html_e( 'No', 'hpaccountants' ); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

// =============================================================================
// Save Meta Fields
// =============================================================================

/**
 * Save meta box data for all HP custom post types.
 *
 * @param int $post_id The ID of the post being saved.
 */
function hp_save_meta_boxes( $post_id ) {

	// Skip autosaves.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Skip revisions.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Verify nonce.
	if ( ! isset( $_POST['hp_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hp_meta_nonce'], 'hp_save_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// ---- Service ----
	if ( 'hp_service' === $post_type ) {
		if ( isset( $_POST['hp_position'] ) ) {
			update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
		}
		if ( isset( $_POST['hp_active'] ) ) {
			$active = sanitize_text_field( $_POST['hp_active'] );
			update_post_meta( $post_id, '_hp_active', in_array( $active, array( 'Y', 'N' ), true ) ? $active : 'Y' );
		}
	}

	// ---- Testimonial ----
	if ( 'hp_testimonial' === $post_type ) {
		if ( isset( $_POST['hp_client_name'] ) ) {
			update_post_meta( $post_id, '_hp_client_name', sanitize_text_field( $_POST['hp_client_name'] ) );
		}
		if ( isset( $_POST['hp_client_title'] ) ) {
			update_post_meta( $post_id, '_hp_client_title', sanitize_text_field( $_POST['hp_client_title'] ) );
		}
		if ( isset( $_POST['hp_business_name'] ) ) {
			update_post_meta( $post_id, '_hp_business_name', sanitize_text_field( $_POST['hp_business_name'] ) );
		}
		if ( isset( $_POST['hp_position'] ) ) {
			update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
		}
		if ( isset( $_POST['hp_active'] ) ) {
			$active = sanitize_text_field( $_POST['hp_active'] );
			update_post_meta( $post_id, '_hp_active', in_array( $active, array( 'Y', 'N' ), true ) ? $active : 'Y' );
		}
	}

	// ---- Download ----
	if ( 'hp_download' === $post_type ) {
		if ( isset( $_POST['hp_file_id'] ) ) {
			$file_id = absint( $_POST['hp_file_id'] );
			update_post_meta( $post_id, '_hp_file_id', $file_id );

			// Auto-detect file type from attachment.
			if ( $file_id ) {
				$mime = get_post_mime_type( $file_id );
				$type_map = array(
					'application/pdf'                                                          => 'pdf',
					'application/msword'                                                       => 'doc',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
					'application/vnd.ms-excel'                                                 => 'xls',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
				);
				$detected = isset( $type_map[ $mime ] ) ? $type_map[ $mime ] : '';
				if ( $detected ) {
					update_post_meta( $post_id, '_hp_file_type', $detected );
				}
			}
		}
		if ( isset( $_POST['hp_s3_url'] ) ) {
			update_post_meta( $post_id, '_hp_s3_url', esc_url_raw( $_POST['hp_s3_url'] ) );
		}
		if ( isset( $_POST['hp_view_count'] ) ) {
			update_post_meta( $post_id, '_hp_view_count', absint( $_POST['hp_view_count'] ) );
		}
	}

	// ---- Link ----
	if ( 'hp_link' === $post_type ) {
		if ( isset( $_POST['hp_url'] ) ) {
			update_post_meta( $post_id, '_hp_url', esc_url_raw( $_POST['hp_url'] ) );
		}
		if ( isset( $_POST['hp_position'] ) ) {
			update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
		}
		if ( isset( $_POST['hp_active'] ) ) {
			$active = sanitize_text_field( $_POST['hp_active'] );
			update_post_meta( $post_id, '_hp_active', in_array( $active, array( 'Y', 'N' ), true ) ? $active : 'Y' );
		}
	}
}
add_action( 'save_post', 'hp_save_meta_boxes' );

// =============================================================================
// Custom Admin Columns
// =============================================================================

// ---- Service Columns ----

/**
 * Add custom columns to the Services admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function hp_service_admin_columns( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['hp_position'] = __( 'Position', 'hpaccountants' );
			$new_columns['hp_active']   = __( 'Active', 'hpaccountants' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_hp_service_posts_columns', 'hp_service_admin_columns' );

/**
 * Render custom column content for Services.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function hp_service_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'hp_position':
			echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
			break;
		case 'hp_active':
			echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
			break;
	}
}
add_action( 'manage_hp_service_posts_custom_column', 'hp_service_admin_column_content', 10, 2 );

// ---- Testimonial Columns ----

/**
 * Add custom columns to the Testimonials admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function hp_testimonial_admin_columns( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['hp_client']   = __( 'Client', 'hpaccountants' );
			$new_columns['hp_business'] = __( 'Business', 'hpaccountants' );
			$new_columns['hp_position'] = __( 'Position', 'hpaccountants' );
			$new_columns['hp_active']   = __( 'Active', 'hpaccountants' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_hp_testimonial_posts_columns', 'hp_testimonial_admin_columns' );

/**
 * Render custom column content for Testimonials.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function hp_testimonial_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'hp_client':
			$name  = get_post_meta( $post_id, '_hp_client_name', true );
			$title = get_post_meta( $post_id, '_hp_client_title', true );
			echo esc_html( $name );
			if ( $title ) {
				echo '<br><em>' . esc_html( $title ) . '</em>';
			}
			break;
		case 'hp_business':
			echo esc_html( get_post_meta( $post_id, '_hp_business_name', true ) );
			break;
		case 'hp_position':
			echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
			break;
		case 'hp_active':
			echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
			break;
	}
}
add_action( 'manage_hp_testimonial_posts_custom_column', 'hp_testimonial_admin_column_content', 10, 2 );

// ---- Download Columns ----

/**
 * Add custom columns to the Downloads admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function hp_download_admin_columns( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['hp_file_type']  = __( 'Type', 'hpaccountants' );
			$new_columns['hp_view_count'] = __( 'Views', 'hpaccountants' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_hp_download_posts_columns', 'hp_download_admin_columns' );

/**
 * Render custom column content for Downloads.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function hp_download_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'hp_file_type':
			$type = get_post_meta( $post_id, '_hp_file_type', true );
			echo $type ? esc_html( strtoupper( $type ) ) : '&mdash;';
			break;
		case 'hp_view_count':
			echo esc_html( get_post_meta( $post_id, '_hp_view_count', true ) );
			break;
	}
}
add_action( 'manage_hp_download_posts_custom_column', 'hp_download_admin_column_content', 10, 2 );

// ---- Link Columns ----

/**
 * Add custom columns to the Partner Links admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function hp_link_admin_columns( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['hp_url']      = __( 'URL', 'hpaccountants' );
			$new_columns['hp_position'] = __( 'Position', 'hpaccountants' );
			$new_columns['hp_active']   = __( 'Active', 'hpaccountants' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_hp_link_posts_columns', 'hp_link_admin_columns' );

/**
 * Render custom column content for Partner Links.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function hp_link_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'hp_url':
			$url = get_post_meta( $post_id, '_hp_url', true );
			if ( $url ) {
				echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a>';
			} else {
				echo '&mdash;';
			}
			break;
		case 'hp_position':
			echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
			break;
		case 'hp_active':
			echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
			break;
	}
}
add_action( 'manage_hp_link_posts_custom_column', 'hp_link_admin_column_content', 10, 2 );
