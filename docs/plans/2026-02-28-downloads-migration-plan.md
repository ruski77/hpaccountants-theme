# Downloads Migration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Migrate downloads from S3 external URLs to WordPress Media Library, with a Media Library picker in admin and an auto-migration script.

**Architecture:** Replace `_hp_s3_url` meta field with `_hp_file_id` (attachment ID). Admin gets a wp.media picker button. Archive template resolves download URL from `_hp_file_id` first, falls back to `_hp_s3_url`. One-time migration script pulls files from S3 into Media Library.

**Tech Stack:** PHP (WordPress APIs), vanilla JS (wp.media), jQuery (AJAX tracking)

---

### Task 1: Create Media Library picker admin JS

**Files:**
- Create: `js/hp-admin-downloads.js`

**Step 1: Create the admin JS file**

```javascript
(function ($) {
	'use strict';

	$(function () {
		var $fileId    = $('#hp_file_id');
		var $fileName  = $('#hp-file-name');
		var $selectBtn = $('#hp-select-file');
		var $removeBtn = $('#hp-remove-file');

		function toggleUI() {
			if ($fileId.val()) {
				$fileName.show();
				$removeBtn.show();
			} else {
				$fileName.hide();
				$removeBtn.hide();
			}
		}

		toggleUI();

		$selectBtn.on('click', function (e) {
			e.preventDefault();

			var frame = wp.media({
				title:    'Select Download File',
				button:   { text: 'Use this file' },
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				$fileId.val(attachment.id);
				$fileName.text(attachment.filename).show();
				$removeBtn.show();
			});

			frame.open();
		});

		$removeBtn.on('click', function (e) {
			e.preventDefault();
			$fileId.val('');
			$fileName.text('').hide();
			$removeBtn.hide();
		});
	});
})(jQuery);
```

**Step 2: Verify file exists**

Run: `ls -la js/hp-admin-downloads.js`
Expected: File exists with correct content.

---

### Task 2: Enqueue admin JS for download edit screens

**Files:**
- Modify: `functions.php` (add after line 83, before the includes)

**Step 1: Add admin enqueue function**

Add before the `// Include HP custom functionality` line in `functions.php`:

```php
/**
 * Enqueue admin scripts for download file picker.
 *
 * @param string $hook Current admin page hook.
 */
function hp_enqueue_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	if ( get_post_type() !== 'hp_download' ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'hp-admin-downloads',
		get_template_directory_uri() . '/js/hp-admin-downloads.js',
		array( 'jquery' ),
		HP_THEME_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'hp_enqueue_admin_assets' );
```

**Step 2: Verify in browser**

Visit: WP Admin > Downloads > Edit any download
Expected: No JS errors in console. The wp.media library is loaded.

---

### Task 3: Update Download meta box to show Media Library picker

**Files:**
- Modify: `inc/hp-meta-boxes.php` lines 150-181 (`hp_download_meta_box_callback`)

**Step 1: Replace the download meta box callback**

Replace the entire `hp_download_meta_box_callback` function (lines 150-181) with:

```php
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
```

**Step 2: Verify in browser**

Visit: WP Admin > Downloads > Edit any download
Expected: "Select File" button visible. If post has S3 URL but no file_id, legacy URL shown read-only.

---

### Task 4: Update Download save handler for `_hp_file_id`

**Files:**
- Modify: `inc/hp-meta-boxes.php` lines 284-297 (Download save section)

**Step 1: Replace the download save block**

Replace the `// ---- Download ----` section (lines 284-297) with:

```php
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
```

**Step 2: Verify in browser**

Visit: WP Admin > Downloads > Edit a download > Select a PDF > Save
Expected: `_hp_file_id` saved. File type auto-detected. Check with WP Admin custom fields or database.

**Step 3: Commit**

```bash
git add js/hp-admin-downloads.js functions.php inc/hp-meta-boxes.php
git commit -m "feat: add Media Library picker for downloads, replace S3 URL field"
```

---

### Task 5: Update archive template to use `_hp_file_id`

**Files:**
- Modify: `archive-hp_download.php` lines 42-50 (download link output)

**Step 1: Update the download link resolution**

Replace lines 42-50 (the section inside the `while` loop that gets meta and outputs the link) with:

```php
                <?php while ( $downloads->have_posts() ) : $downloads->the_post();
                    $file_id    = get_post_meta( get_the_ID(), '_hp_file_id', true );
                    $s3_url     = get_post_meta( get_the_ID(), '_hp_s3_url', true );
                    $file_type  = get_post_meta( get_the_ID(), '_hp_file_type', true );
                    $view_count = get_post_meta( get_the_ID(), '_hp_view_count', true );

                    $download_url = '';
                    if ( $file_id ) {
                        $download_url = wp_get_attachment_url( $file_id );
                    } elseif ( $s3_url ) {
                        $download_url = $s3_url;
                    }
                ?>
                <div class="download-row">
                    <div class="download-info">
                        <span class="file-badge file-badge-<?php echo esc_attr( $file_type ); ?>">
                            <?php echo esc_html( strtoupper( $file_type ) ); ?>
                        </span>
                        <?php if ( $download_url ) : ?>
                        <a href="<?php echo esc_url( $download_url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="download-link"
                           data-post-id="<?php the_ID(); ?>">
                            <?php the_title(); ?>
                        </a>
                        <?php else : ?>
                        <span class="download-link-disabled"><?php the_title(); ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="download-views"><?php echo esc_html( number_format( (int) $view_count ) ); ?> views</span>
                </div>
```

**Step 2: Verify in browser**

Visit: `/downloads/` on the front end
Expected: Downloads with `_hp_file_id` link to Media Library URL. Downloads with only S3 URL still use that. Downloads with neither show title without link.

**Step 3: Commit**

```bash
git add archive-hp_download.php
git commit -m "feat: resolve download URLs from Media Library with S3 fallback"
```

---

### Task 6: Fix download tracking JS selector bug

**Files:**
- Modify: `js/hp-downloads.js` line 4

**Step 1: Fix the selector**

Change `.hp-download-link` to `.download-link` on line 4. Also fix the missing closing brackets (the file has syntax errors — missing `});` closings):

Replace the entire file with:

```javascript
(function ($) {
	'use strict';

	$(document).on('click', '.download-link', function () {
		var postId = $(this).data('post-id');

		if (!postId) {
			return;
		}

		// Track the download view without preventing the default action.
		$.ajax({
			url:      hp_downloads.ajax_url,
			type:     'POST',
			dataType: 'json',
			data: {
				action:  'hp_track_download',
				nonce:   hp_downloads.nonce,
				post_id: postId
			}
		});
	});
})(jQuery);
```

**Step 2: Verify in browser**

Visit: `/downloads/` > open browser console > click a download link
Expected: AJAX POST to `admin-ajax.php` with `hp_track_download` action visible in Network tab.

**Step 3: Commit**

```bash
git add js/hp-downloads.js
git commit -m "fix: correct download tracking selector to match template class"
```

---

### Task 7: Create S3 to Media Library migration script

**Files:**
- Create: `migration/migrate-s3-to-media.php`

**Step 1: Create the migration script**

```php
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
```

**Step 2: Include the migration script in functions.php**

Add after the existing `require_once` lines at the bottom of `functions.php`:

```php
require_once get_template_directory() . '/migration/migrate-s3-to-media.php';
```

**Step 3: Verify in browser**

Visit: WP Admin > Tools > Migrate S3 Downloads
Expected: Page shows count of downloads with S3 URLs and a "Run Migration" button.

**Step 4: Commit**

```bash
git add migration/migrate-s3-to-media.php functions.php
git commit -m "feat: add S3 to Media Library migration tool under Tools menu"
```

---

### Task 8: Run migration and verify end-to-end

**Step 1: Run the migration**

Visit: WP Admin > Tools > Migrate S3 Downloads > Click "Run Migration"
Expected: Table showing each download with OK/FAILED status. Note any failures for manual upload.

**Step 2: Verify archive page**

Visit: `/downloads/` on front end
Expected: All migrated downloads link to local Media Library URLs (e.g., `/wp-content/uploads/2026/02/filename.pdf`). Failed ones still show S3 URL or no link.

**Step 3: Verify download tracking**

Open browser console on `/downloads/` page. Click a download link.
Expected: AJAX POST to `admin-ajax.php` visible in Network tab. View count increments.

**Step 4: Verify admin edit screen**

Visit: WP Admin > Downloads > Edit a migrated download
Expected: "Select File" button visible with filename shown. No S3 URL field (since file_id is set). View count preserved.

**Step 5: Final commit**

```bash
git add -A
git commit -m "feat: complete downloads migration from S3 to WordPress Media Library"
```
