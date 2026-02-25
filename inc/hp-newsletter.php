<?php
/**
 * HP Accountants Newsletter System
 *
 * Handles mailing list table creation, AJAX subscription,
 * shortcode rendering, widget area, and admin page.
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
// 1. Create mailing list table on theme activation
// ========================================================
function hp_newsletter_create_table() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'hp_mailinglist';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL DEFAULT '',
		email varchar(255) NOT NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
add_action( 'after_switch_theme', 'hp_newsletter_create_table' );

// ========================================================
// 2. Safety net: ensure table exists on admin_init
// ========================================================
function hp_newsletter_maybe_create_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'hp_mailinglist';

	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
		hp_newsletter_create_table();
	}
}
add_action( 'admin_init', 'hp_newsletter_maybe_create_table' );

// ========================================================
// 3. Enqueue newsletter script
// ========================================================
function hp_newsletter_enqueue_scripts() {
	wp_enqueue_script(
		'hp-newsletter',
		get_template_directory_uri() . '/js/hp-newsletter.js',
		array( 'jquery' ),
		filemtime( get_template_directory() . '/js/hp-newsletter.js' ),
		true
	);

	wp_localize_script( 'hp-newsletter', 'hp_newsletter', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'hp_newsletter_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'hp_newsletter_enqueue_scripts' );

// ========================================================
// 4. AJAX handler for newsletter subscription
// ========================================================
function hp_newsletter_subscribe() {
	// Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hp_newsletter_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed.' ) );
	}

	// Sanitize inputs.
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$name  = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

	// Validate email.
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'hp_mailinglist';

	// Check for duplicates.
	$existing = $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM $table_name WHERE email = %s",
		$email
	) );

	if ( $existing ) {
		wp_send_json_error( array( 'message' => 'This email address is already subscribed.' ) );
	}

	// Insert subscriber.
	$result = $wpdb->insert(
		$table_name,
		array(
			'name'       => $name,
			'email'      => $email,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s' )
	);

	if ( false === $result ) {
		wp_send_json_error( array( 'message' => 'Something went wrong. Please try again later.' ) );
	}

	wp_send_json_success( array( 'message' => 'Thank you for subscribing!' ) );
}
add_action( 'wp_ajax_hp_newsletter_subscribe', 'hp_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_hp_newsletter_subscribe', 'hp_newsletter_subscribe' );

// ========================================================
// 5. Shortcode: [hp_newsletter]
// ========================================================
function hp_newsletter_shortcode( $atts ) {
	ob_start();
	?>
	<form id="hp-newsletter-form" class="hp-newsletter-form" method="post">
		<div class="hp-newsletter-fields">
			<input type="email" name="hp_newsletter_email" placeholder="Your email address" required />
			<button type="submit" class="hp-newsletter-submit">Subscribe</button>
		</div>
		<div class="hp-newsletter-response"></div>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hp_newsletter', 'hp_newsletter_shortcode' );

// ========================================================
// 6. Register widget area
// ========================================================
function hp_newsletter_register_widget_area() {
	register_sidebar( array(
		'name'          => __( 'Newsletter', 'ruki' ),
		'id'            => 'hp-newsletter',
		'description'   => __( 'Widget area for the newsletter signup form.', 'ruki' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'hp_newsletter_register_widget_area' );

// ========================================================
// 7. Admin menu page: Mailing List
// ========================================================
function hp_newsletter_admin_menu() {
	add_menu_page(
		__( 'Mailing List', 'ruki' ),
		__( 'Mailing List', 'ruki' ),
		'manage_options',
		'hp-mailinglist',
		'hp_newsletter_admin_page',
		'dashicons-email',
		30
	);
}
add_action( 'admin_menu', 'hp_newsletter_admin_menu' );

/**
 * Render the mailing list admin page.
 */
function hp_newsletter_admin_page() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'hp_mailinglist';
	$total      = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

	// Pagination.
	$per_page     = 20;
	$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$offset       = ( $current_page - 1 ) * $per_page;
	$total_pages  = ceil( $total / $per_page );

	$subscribers = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
		$per_page,
		$offset
	) );

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Mailing List', 'ruki' ); ?></h1>
		<p>
			<?php
			printf(
				/* translators: %d: total number of subscribers */
				esc_html__( 'Total subscribers: %d', 'ruki' ),
				$total
			);
			?>
		</p>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column"><?php esc_html_e( 'ID', 'ruki' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Name', 'ruki' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Email', 'ruki' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Date', 'ruki' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $subscribers ) : ?>
					<?php foreach ( $subscribers as $subscriber ) : ?>
						<tr>
							<td><?php echo esc_html( $subscriber->id ); ?></td>
							<td><?php echo esc_html( $subscriber->name ); ?></td>
							<td><?php echo esc_html( $subscriber->email ); ?></td>
							<td><?php echo esc_html( $subscriber->created_at ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No subscribers found.', 'ruki' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php
					$page_links = paginate_links( array(
						'base'      => add_query_arg( 'paged', '%#%' ),
						'format'    => '',
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
						'total'     => $total_pages,
						'current'   => $current_page,
					) );

					if ( $page_links ) {
						echo '<span class="pagination-links">' . $page_links . '</span>';
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
