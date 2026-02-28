<?php
/**
 * HP Accountants - Rank Math SEO Configuration
 *
 * One-time setup script. Configures Rank Math with HP Accountants
 * business details, local SEO schema, sitemap, and page-level meta.
 *
 * Usage: Visit /wp-admin/ and go to Tools > Setup Rank Math
 *
 * @package HP_Accountants
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the setup admin page under Tools (only when Rank Math is active).
 */
function hp_register_rank_math_setup_page() {
	if ( ! class_exists( 'RankMath' ) ) {
		return;
	}

	// Don't show menu item if setup already completed.
	if ( get_option( 'hp_rank_math_setup_done' ) ) {
		return;
	}

	add_management_page(
		__( 'Setup Rank Math', 'hpaccountants' ),
		__( 'Setup Rank Math', 'hpaccountants' ),
		'manage_options',
		'hp-setup-rank-math',
		'hp_rank_math_setup_page'
	);
}
add_action( 'admin_menu', 'hp_register_rank_math_setup_page' );

/**
 * Show admin notice prompting to run Rank Math setup.
 */
function hp_rank_math_setup_notice() {
	if ( ! class_exists( 'RankMath' ) ) {
		return;
	}
	if ( get_option( 'hp_rank_math_setup_done' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$url = admin_url( 'tools.php?page=hp-setup-rank-math' );
	echo '<div class="notice notice-info"><p>';
	echo '<strong>HP Accountants:</strong> Rank Math SEO is active but not configured. ';
	echo '<a href="' . esc_url( $url ) . '">Run the SEO setup</a> to configure it for HP Accountants.';
	echo '</p></div>';
}
add_action( 'admin_notices', 'hp_rank_math_setup_notice' );

/**
 * Render the setup page and handle form submission.
 */
function hp_rank_math_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'Unauthorized.', 'hpaccountants' ) );
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Setup Rank Math SEO for HP Accountants', 'hpaccountants' ) . '</h1>';

	if ( isset( $_POST['hp_run_rank_math_setup'] ) && check_admin_referer( 'hp_rank_math_setup' ) ) {
		hp_run_rank_math_setup();
	} elseif ( get_option( 'hp_rank_math_setup_done' ) ) {
		echo '<p><strong>' . esc_html__( 'Rank Math has already been configured.', 'hpaccountants' ) . '</strong></p>';
	} else {
		echo '<p>This will configure Rank Math SEO with:</p>';
		echo '<ul style="list-style: disc; padding-left: 20px;">';
		echo '<li>Local business schema (AccountingService) with address, phone, hours</li>';
		echo '<li>Homepage, About, and Contact page meta titles &amp; descriptions</li>';
		echo '<li>XML sitemap settings (services and downloads included)</li>';
		echo '<li>Open Graph / social sharing defaults</li>';
		echo '<li>Correct indexing rules (noindex testimonials and partner links)</li>';
		echo '</ul>';
		echo '<form method="post">';
		wp_nonce_field( 'hp_rank_math_setup' );
		echo '<p><button type="submit" name="hp_run_rank_math_setup" class="button button-primary">';
		esc_html_e( 'Configure Rank Math', 'hpaccountants' );
		echo '</button></p>';
		echo '</form>';
	}

	echo '</div>';
}

/**
 * Execute the Rank Math configuration.
 */
function hp_run_rank_math_setup() {
	$results = array();

	// ─── Titles & Meta ───────────────────────────────────────────────────────

	$titles = get_option( 'rank-math-options-titles', array() );

	// Knowledge Graph: Company.
	$titles['knowledgegraph_type']       = 'company';
	$titles['knowledgegraph_name']       = 'Holland Price & Associates';
	$titles['website_name']              = 'Holland Price & Associates';
	$titles['website_alternate_name']    = 'HP Accountants';

	// Local Business schema.
	$titles['local_business_type'] = 'AccountingService';
	$titles['local_address'] = array(
		'streetAddress'   => '15 Roderick Street',
		'addressLocality' => 'Dayboro',
		'addressRegion'   => 'QLD',
		'postalCode'      => '4521',
		'addressCountry'  => 'AU',
	);
	$titles['phone'] = '0447 384 179';
	$titles['email'] = 'price@hpaccountants.com.au';
	$titles['url']   = 'https://hpaccountants.com.au';

	// Opening hours (Mon-Fri 9-5).
	$titles['opening_hours'] = array(
		array( 'day' => 'Monday',    'time' => '09:00-17:00' ),
		array( 'day' => 'Tuesday',   'time' => '09:00-17:00' ),
		array( 'day' => 'Wednesday', 'time' => '09:00-17:00' ),
		array( 'day' => 'Thursday',  'time' => '09:00-17:00' ),
		array( 'day' => 'Friday',    'time' => '09:00-17:00' ),
	);

	// Title separator.
	$titles['title_separator'] = '|';

	// Homepage.
	$titles['homepage_title']       = 'HP Accountants | Professional Accounting Services Dayboro QLD';
	$titles['homepage_description'] = 'Holland Price & Associates provides professional accounting, tax, BAS and business advisory services for small to medium businesses in Dayboro and surrounds. Approachable. Passionate. Accurate.';

	// Services CPT schema.
	$titles['pt_hp_service_default_rich_snippet'] = 'service';
	$titles['pt_hp_service_default_snippet_name'] = '%seo_title%';
	$titles['pt_hp_service_default_snippet_desc'] = '%seo_description%';

	// Noindex testimonials and partner links.
	$titles['pt_hp_testimonial_robots']        = array( 'noindex' );
	$titles['pt_hp_testimonial_custom_robots'] = 'on';
	$titles['pt_hp_link_robots']               = array( 'noindex' );
	$titles['pt_hp_link_custom_robots']        = 'on';

	// Twitter card.
	$titles['twitter_card_type'] = 'summary_large_image';

	// Social sharing defaults.
	$titles['homepage_facebook_title']       = 'HP Accountants | Professional Accounting Services';
	$titles['homepage_facebook_description'] = 'Holland Price & Associates — trusted accounting for small to medium businesses in Dayboro, QLD.';
	$titles['homepage_twitter_title']        = 'HP Accountants | Professional Accounting Services';
	$titles['homepage_twitter_description']  = 'Holland Price & Associates — trusted accounting for small to medium businesses in Dayboro, QLD.';

	update_option( 'rank-math-options-titles', $titles );
	$results[] = 'Titles, schema &amp; social settings configured.';

	// ─── General Settings ────────────────────────────────────────────────────

	$general = get_option( 'rank-math-options-general', array() );

	$general['breadcrumbs']            = 'on';
	$general['breadcrumbs_home']       = 'on';
	$general['breadcrumbs_home_label'] = 'Home';
	$general['breadcrumbs_separator']  = '&raquo;';
	$general['new_window_external_links'] = 'on';
	$general['setup_mode'] = 'advanced';

	update_option( 'rank-math-options-general', $general );
	$results[] = 'General settings configured.';

	// ─── Sitemap Settings ────────────────────────────────────────────────────

	$sitemap = get_option( 'rank-math-options-sitemap', array() );

	$sitemap['include_images']                = 'on';
	$sitemap['pt_post_sitemap']               = 'on';
	$sitemap['pt_page_sitemap']               = 'on';
	$sitemap['pt_hp_service_sitemap']         = 'on';
	$sitemap['pt_hp_download_sitemap']        = 'on';
	$sitemap['pt_hp_testimonial_sitemap']     = 'off';
	$sitemap['pt_hp_link_sitemap']            = 'off';
	$sitemap['pt_attachment_sitemap']         = 'off';
	$sitemap['tax_category_sitemap']          = 'on';
	$sitemap['tax_download_category_sitemap'] = 'on';
	$sitemap['tax_post_tag_sitemap']          = 'off';
	$sitemap['ping_search_engines']           = 'on';

	update_option( 'rank-math-options-sitemap', $sitemap );
	$results[] = 'Sitemap settings configured.';

	// ─── Enable Modules ──────────────────────────────────────────────────────

	$modules = get_option( 'rank_math_modules', array() );
	$needed  = array( 'sitemap', 'rich-snippet', 'seo-analysis', 'link-counter', 'local-seo', 'redirections', '404-monitor' );
	foreach ( $needed as $mod ) {
		if ( ! in_array( $mod, $modules, true ) ) {
			$modules[] = $mod;
		}
	}
	update_option( 'rank_math_modules', $modules );
	$results[] = 'SEO modules enabled: ' . esc_html( implode( ', ', $needed ) ) . '.';

	// ─── Mark Wizard Complete ────────────────────────────────────────────────

	update_option( 'rank_math_wizard_completed', true );
	delete_transient( '_rank_math_activation_redirect' );

	// ─── Page-Level Meta ─────────────────────────────────────────────────────

	$front_page_id = get_option( 'page_on_front' );
	if ( $front_page_id ) {
		update_post_meta( $front_page_id, 'rank_math_title', 'HP Accountants | Professional Accounting Services Dayboro QLD' );
		update_post_meta( $front_page_id, 'rank_math_description', 'Holland Price & Associates provides professional accounting, tax, BAS and business advisory services for small to medium businesses in Dayboro and surrounds.' );
		update_post_meta( $front_page_id, 'rank_math_focus_keyword', 'accountant dayboro' );
		$results[] = 'Front page meta set.';
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		update_post_meta( $about->ID, 'rank_math_title', 'About Us | HP Accountants Dayboro' );
		update_post_meta( $about->ID, 'rank_math_description', 'Meet Scott and Christy Price — the husband and wife team behind Holland Price & Associates. Over 30 years of combined accounting experience serving Dayboro businesses.' );
		update_post_meta( $about->ID, 'rank_math_focus_keyword', 'accountant dayboro about' );
		$results[] = 'About page meta set.';
	}

	$contact = get_page_by_path( 'contact' );
	if ( $contact ) {
		update_post_meta( $contact->ID, 'rank_math_title', 'Contact Us | HP Accountants Dayboro' );
		update_post_meta( $contact->ID, 'rank_math_description', 'Contact Holland Price & Associates — 15 Roderick Street, Dayboro QLD 4521. Phone 0447 384 179 or email price@hpaccountants.com.au.' );
		update_post_meta( $contact->ID, 'rank_math_focus_keyword', 'contact accountant dayboro' );
		$results[] = 'Contact page meta set.';
	}

	// ─── Mark setup as done ──────────────────────────────────────────────────

	update_option( 'hp_rank_math_setup_done', true );

	// ─── Output Results ──────────────────────────────────────────────────────

	echo '<div class="notice notice-success"><p><strong>Rank Math configured successfully!</strong></p></div>';
	echo '<ul style="list-style: disc; padding-left: 20px;">';
	foreach ( $results as $r ) {
		echo '<li>' . $r . '</li>';
	}
	echo '</ul>';
	echo '<p>Your XML sitemap is available at: <a href="' . esc_url( home_url( '/sitemap_index.xml' ) ) . '" target="_blank">' . esc_html( home_url( '/sitemap_index.xml' ) ) . '</a></p>';
	echo '<p><strong>Next steps:</strong></p>';
	echo '<ol>';
	echo '<li>Submit your sitemap to <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>';
	echo '<li>Set up or verify your <a href="https://www.google.com/business/" target="_blank">Google Business Profile</a></li>';
	echo '</ol>';
}
