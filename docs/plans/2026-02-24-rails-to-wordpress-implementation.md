# HP Accountants WordPress Migration - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Convert the Holland Price & Associates Rails site to a WordPress theme based on Ruki, with all content migrated from the Postgres database.

**Architecture:** Fork the Ruki v1.4.8 theme, adding 4 custom post types (services, testimonials, downloads, links), custom page templates (home, about, contact), a newsletter subscription system with custom DB table, and a migration script to import all existing content. The output is a single theme folder deployable to A2 Hosting.

**Tech Stack:** WordPress 5.0+, PHP 7.0+, MySQL, Contact Form 7 plugin, Ruki theme (forked)

---

## Task 1: Extract and Fork Ruki Theme

**Files:**
- Source: `ruki-theme/ruki-1.4.8.zip`
- Create: `hpaccountants-theme/` (extracted and renamed)

**Step 1: Extract the Ruki theme**

```bash
cd ~/workspace/hpaccountants-web
unzip ruki-theme/ruki-1.4.8.zip -d .
mv ruki hpaccountants-theme
```

**Step 2: Update theme metadata in style.css**

Change the theme header at the top of `hpaccountants-theme/style.css`:

```css
/*
Theme Name: HP Accountants
Theme URI: http://www.hpaccountants.com.au
Author: Holland Price & Associates
Author URI: http://www.hpaccountants.com.au
Description: Holland Price & Associates - Professional Accounting Services
Version: 1.0.0
Requires at least: 5.0
Requires PHP: 7
Tested up to: 6.8
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ruki
Tags: one-column, two-columns, custom-logo, custom-menu, featured-images, footer-widgets, full-width-template, theme-options
*/
```

Note: Keep `Text Domain: ruki` to avoid breaking all existing translation strings.

**Step 3: Verify the theme structure**

```bash
ls hpaccountants-theme/functions.php
ls hpaccountants-theme/style.css
ls hpaccountants-theme/header.php
ls hpaccountants-theme/footer.php
ls hpaccountants-theme/front-page.php
ls hpaccountants-theme/page.php
ls hpaccountants-theme/inc/
```

Expected: All files exist.

**Step 4: Initialize git repo and commit**

```bash
cd ~/workspace/hpaccountants-web
git init
echo "dump.sql.gz" >> .gitignore
echo "dump2.sql.gz" >> .gitignore
echo "ruki-theme/" >> .gitignore
echo ".DS_Store" >> .gitignore
git add hpaccountants-theme/ docs/ .gitignore
git commit -m "feat: fork Ruki v1.4.8 as HP Accountants theme"
```

---

## Task 2: Register Custom Post Types

**Files:**
- Create: `hpaccountants-theme/inc/hp-custom-post-types.php`
- Modify: `hpaccountants-theme/functions.php` (add include)

**Step 1: Create the custom post types file**

Create `hpaccountants-theme/inc/hp-custom-post-types.php`:

```php
<?php
/**
 * HP Accountants Custom Post Types
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register all custom post types
 */
function hp_register_post_types() {

    // Services
    register_post_type( 'hp_service', array(
        'labels' => array(
            'name'               => 'Services',
            'singular_name'      => 'Service',
            'add_new'            => 'Add New Service',
            'add_new_item'       => 'Add New Service',
            'edit_item'          => 'Edit Service',
            'new_item'           => 'New Service',
            'view_item'          => 'View Service',
            'search_items'       => 'Search Services',
            'not_found'          => 'No services found',
            'not_found_in_trash' => 'No services found in Trash',
            'all_items'          => 'All Services',
            'menu_name'          => 'Services',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'services', 'with_front' => false ),
        'menu_icon'    => 'dashicons-clipboard',
        'supports'     => array( 'title', 'editor' ),
        'show_in_rest' => true,
    ) );

    // Testimonials
    register_post_type( 'hp_testimonial', array(
        'labels' => array(
            'name'               => 'Testimonials',
            'singular_name'      => 'Testimonial',
            'add_new'            => 'Add New Testimonial',
            'add_new_item'       => 'Add New Testimonial',
            'edit_item'          => 'Edit Testimonial',
            'new_item'           => 'New Testimonial',
            'view_item'          => 'View Testimonial',
            'search_items'       => 'Search Testimonials',
            'not_found'          => 'No testimonials found',
            'not_found_in_trash' => 'No testimonials found in Trash',
            'all_items'          => 'All Testimonials',
            'menu_name'          => 'Testimonials',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'testimonials', 'with_front' => false ),
        'menu_icon'    => 'dashicons-format-quote',
        'supports'     => array( 'title', 'editor' ),
        'show_in_rest' => true,
    ) );

    // Downloads
    register_post_type( 'hp_download', array(
        'labels' => array(
            'name'               => 'Downloads',
            'singular_name'      => 'Download',
            'add_new'            => 'Add New Download',
            'add_new_item'       => 'Add New Download',
            'edit_item'          => 'Edit Download',
            'new_item'           => 'New Download',
            'view_item'          => 'View Download',
            'search_items'       => 'Search Downloads',
            'not_found'          => 'No downloads found',
            'not_found_in_trash' => 'No downloads found in Trash',
            'all_items'          => 'All Downloads',
            'menu_name'          => 'Downloads',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'downloads', 'with_front' => false ),
        'menu_icon'    => 'dashicons-download',
        'supports'     => array( 'title', 'editor' ),
        'show_in_rest' => true,
    ) );

    // Links
    register_post_type( 'hp_link', array(
        'labels' => array(
            'name'               => 'Partner Links',
            'singular_name'      => 'Partner Link',
            'add_new'            => 'Add New Link',
            'add_new_item'       => 'Add New Partner Link',
            'edit_item'          => 'Edit Partner Link',
            'new_item'           => 'New Partner Link',
            'view_item'          => 'View Partner Link',
            'search_items'       => 'Search Partner Links',
            'not_found'          => 'No partner links found',
            'not_found_in_trash' => 'No partner links found in Trash',
            'all_items'          => 'All Partner Links',
            'menu_name'          => 'Partner Links',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'links', 'with_front' => false ),
        'menu_icon'    => 'dashicons-admin-links',
        'supports'     => array( 'title', 'editor' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'hp_register_post_types' );

/**
 * Register download_category taxonomy
 */
function hp_register_taxonomies() {
    register_taxonomy( 'download_category', 'hp_download', array(
        'labels' => array(
            'name'              => 'Download Categories',
            'singular_name'     => 'Download Category',
            'search_items'      => 'Search Categories',
            'all_items'         => 'All Categories',
            'edit_item'         => 'Edit Category',
            'update_item'       => 'Update Category',
            'add_new_item'      => 'Add New Category',
            'new_item_name'     => 'New Category Name',
            'menu_name'         => 'Categories',
        ),
        'hierarchical'  => true,
        'public'        => true,
        'rewrite'       => array( 'slug' => 'download-category', 'with_front' => false ),
        'show_in_rest'  => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'hp_register_taxonomies' );

/**
 * Customize archive queries for custom post types
 */
function hp_custom_archive_queries( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Services ordered by position
    if ( $query->is_post_type_archive( 'hp_service' ) ) {
        $query->set( 'meta_key', '_hp_position' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', -1 );
        $query->set( 'meta_query', array(
            array(
                'key'     => '_hp_active',
                'value'   => 'Y',
                'compare' => '=',
            ),
        ) );
    }

    // Testimonials ordered by position
    if ( $query->is_post_type_archive( 'hp_testimonial' ) ) {
        $query->set( 'meta_key', '_hp_position' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', -1 );
        $query->set( 'meta_query', array(
            array(
                'key'     => '_hp_active',
                'value'   => 'Y',
                'compare' => '=',
            ),
        ) );
    }

    // Downloads ordered by view count (most popular first)
    if ( $query->is_post_type_archive( 'hp_download' ) ) {
        $query->set( 'meta_key', '_hp_view_count' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'DESC' );
        $query->set( 'posts_per_page', -1 );
    }

    // Links ordered by position
    if ( $query->is_post_type_archive( 'hp_link' ) ) {
        $query->set( 'meta_key', '_hp_position' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', -1 );
        $query->set( 'meta_query', array(
            array(
                'key'     => '_hp_active',
                'value'   => 'Y',
                'compare' => '=',
            ),
        ) );
    }
}
add_action( 'pre_get_posts', 'hp_custom_archive_queries' );
```

**Step 2: Include from functions.php**

Add at the end of `hpaccountants-theme/functions.php`, just before the closing (or at the very end of the file), add:

```php
/**
 * HP Accountants Custom Functionality
 */
require get_template_directory() . '/inc/hp-custom-post-types.php';
```

**Step 3: Commit**

```bash
git add hpaccountants-theme/inc/hp-custom-post-types.php hpaccountants-theme/functions.php
git commit -m "feat: register custom post types and taxonomy"
```

---

## Task 3: Add Meta Boxes for Custom Fields

**Files:**
- Create: `hpaccountants-theme/inc/hp-meta-boxes.php`
- Modify: `hpaccountants-theme/functions.php` (add include)

**Step 1: Create the meta boxes file**

Create `hpaccountants-theme/inc/hp-meta-boxes.php`:

```php
<?php
/**
 * HP Accountants Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register meta boxes
 */
function hp_add_meta_boxes() {
    // Service meta
    add_meta_box( 'hp_service_meta', 'Service Settings', 'hp_service_meta_callback', 'hp_service', 'side' );

    // Testimonial meta
    add_meta_box( 'hp_testimonial_meta', 'Client Details', 'hp_testimonial_meta_callback', 'hp_testimonial', 'normal', 'high' );

    // Download meta
    add_meta_box( 'hp_download_meta', 'Download Settings', 'hp_download_meta_callback', 'hp_download', 'normal', 'high' );

    // Link meta
    add_meta_box( 'hp_link_meta', 'Link Settings', 'hp_link_meta_callback', 'hp_link', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'hp_add_meta_boxes' );

/**
 * Service meta box
 */
function hp_service_meta_callback( $post ) {
    wp_nonce_field( 'hp_service_meta', 'hp_service_meta_nonce' );
    $position = get_post_meta( $post->ID, '_hp_position', true );
    $active   = get_post_meta( $post->ID, '_hp_active', true );
    if ( '' === $active ) $active = 'Y';
    ?>
    <p>
        <label for="hp_position"><strong>Position (order):</strong></label><br>
        <input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0" style="width:100%">
    </p>
    <p>
        <label for="hp_active"><strong>Active:</strong></label><br>
        <select id="hp_active" name="hp_active" style="width:100%">
            <option value="Y" <?php selected( $active, 'Y' ); ?>>Yes</option>
            <option value="N" <?php selected( $active, 'N' ); ?>>No</option>
        </select>
    </p>
    <?php
}

/**
 * Testimonial meta box
 */
function hp_testimonial_meta_callback( $post ) {
    wp_nonce_field( 'hp_testimonial_meta', 'hp_testimonial_meta_nonce' );
    $client_name    = get_post_meta( $post->ID, '_hp_client_name', true );
    $client_title   = get_post_meta( $post->ID, '_hp_client_title', true );
    $business_name  = get_post_meta( $post->ID, '_hp_business_name', true );
    $position       = get_post_meta( $post->ID, '_hp_position', true );
    $active         = get_post_meta( $post->ID, '_hp_active', true );
    if ( '' === $active ) $active = 'Y';
    ?>
    <table class="form-table">
        <tr>
            <th><label for="hp_client_name">Client Name:</label></th>
            <td><input type="text" id="hp_client_name" name="hp_client_name" value="<?php echo esc_attr( $client_name ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="hp_client_title">Client Title:</label></th>
            <td><input type="text" id="hp_client_title" name="hp_client_title" value="<?php echo esc_attr( $client_title ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="hp_business_name">Business Name:</label></th>
            <td><input type="text" id="hp_business_name" name="hp_business_name" value="<?php echo esc_attr( $business_name ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="hp_position">Position (order):</label></th>
            <td><input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0"></td>
        </tr>
        <tr>
            <th><label for="hp_active">Active:</label></th>
            <td>
                <select id="hp_active" name="hp_active">
                    <option value="Y" <?php selected( $active, 'Y' ); ?>>Yes</option>
                    <option value="N" <?php selected( $active, 'N' ); ?>>No</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Download meta box
 */
function hp_download_meta_callback( $post ) {
    wp_nonce_field( 'hp_download_meta', 'hp_download_meta_nonce' );
    $s3_url     = get_post_meta( $post->ID, '_hp_s3_url', true );
    $file_type  = get_post_meta( $post->ID, '_hp_file_type', true );
    $view_count = get_post_meta( $post->ID, '_hp_view_count', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="hp_s3_url">File URL (S3):</label></th>
            <td><input type="url" id="hp_s3_url" name="hp_s3_url" value="<?php echo esc_attr( $s3_url ); ?>" class="large-text"></td>
        </tr>
        <tr>
            <th><label for="hp_file_type">File Type:</label></th>
            <td>
                <select id="hp_file_type" name="hp_file_type">
                    <option value="pdf" <?php selected( $file_type, 'pdf' ); ?>>PDF</option>
                    <option value="doc" <?php selected( $file_type, 'doc' ); ?>>DOC</option>
                    <option value="docx" <?php selected( $file_type, 'docx' ); ?>>DOCX</option>
                    <option value="xls" <?php selected( $file_type, 'xls' ); ?>>XLS</option>
                    <option value="xlsx" <?php selected( $file_type, 'xlsx' ); ?>>XLSX</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="hp_view_count">View Count:</label></th>
            <td><input type="number" id="hp_view_count" name="hp_view_count" value="<?php echo esc_attr( $view_count ); ?>" min="0"></td>
        </tr>
    </table>
    <?php
}

/**
 * Link meta box
 */
function hp_link_meta_callback( $post ) {
    wp_nonce_field( 'hp_link_meta', 'hp_link_meta_nonce' );
    $url      = get_post_meta( $post->ID, '_hp_url', true );
    $position = get_post_meta( $post->ID, '_hp_position', true );
    $active   = get_post_meta( $post->ID, '_hp_active', true );
    if ( '' === $active ) $active = 'Y';
    ?>
    <table class="form-table">
        <tr>
            <th><label for="hp_url">Link URL:</label></th>
            <td><input type="url" id="hp_url" name="hp_url" value="<?php echo esc_attr( $url ); ?>" class="large-text"></td>
        </tr>
        <tr>
            <th><label for="hp_position">Position (order):</label></th>
            <td><input type="number" id="hp_position" name="hp_position" value="<?php echo esc_attr( $position ); ?>" min="0"></td>
        </tr>
        <tr>
            <th><label for="hp_active">Active:</label></th>
            <td>
                <select id="hp_active" name="hp_active">
                    <option value="Y" <?php selected( $active, 'Y' ); ?>>Yes</option>
                    <option value="N" <?php selected( $active, 'N' ); ?>>No</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save meta boxes
 */
function hp_save_meta_boxes( $post_id ) {
    // Skip autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    // Skip revisions
    if ( wp_is_post_revision( $post_id ) ) return;

    $post_type = get_post_type( $post_id );

    // Service
    if ( 'hp_service' === $post_type ) {
        if ( ! isset( $_POST['hp_service_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hp_service_meta_nonce'], 'hp_service_meta' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['hp_position'] ) ) {
            update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
        }
        if ( isset( $_POST['hp_active'] ) ) {
            update_post_meta( $post_id, '_hp_active', sanitize_text_field( $_POST['hp_active'] ) );
        }
    }

    // Testimonial
    if ( 'hp_testimonial' === $post_type ) {
        if ( ! isset( $_POST['hp_testimonial_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hp_testimonial_meta_nonce'], 'hp_testimonial_meta' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $fields = array( 'hp_client_name', 'hp_client_title', 'hp_business_name' );
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
        if ( isset( $_POST['hp_position'] ) ) {
            update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
        }
        if ( isset( $_POST['hp_active'] ) ) {
            update_post_meta( $post_id, '_hp_active', sanitize_text_field( $_POST['hp_active'] ) );
        }
    }

    // Download
    if ( 'hp_download' === $post_type ) {
        if ( ! isset( $_POST['hp_download_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hp_download_meta_nonce'], 'hp_download_meta' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['hp_s3_url'] ) ) {
            update_post_meta( $post_id, '_hp_s3_url', esc_url_raw( $_POST['hp_s3_url'] ) );
        }
        if ( isset( $_POST['hp_file_type'] ) ) {
            update_post_meta( $post_id, '_hp_file_type', sanitize_text_field( $_POST['hp_file_type'] ) );
        }
        if ( isset( $_POST['hp_view_count'] ) ) {
            update_post_meta( $post_id, '_hp_view_count', absint( $_POST['hp_view_count'] ) );
        }
    }

    // Link
    if ( 'hp_link' === $post_type ) {
        if ( ! isset( $_POST['hp_link_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hp_link_meta_nonce'], 'hp_link_meta' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['hp_url'] ) ) {
            update_post_meta( $post_id, '_hp_url', esc_url_raw( $_POST['hp_url'] ) );
        }
        if ( isset( $_POST['hp_position'] ) ) {
            update_post_meta( $post_id, '_hp_position', absint( $_POST['hp_position'] ) );
        }
        if ( isset( $_POST['hp_active'] ) ) {
            update_post_meta( $post_id, '_hp_active', sanitize_text_field( $_POST['hp_active'] ) );
        }
    }
}
add_action( 'save_post', 'hp_save_meta_boxes' );

/**
 * Add custom admin columns for Services
 */
function hp_service_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( 'title' === $key ) {
            $new['hp_position'] = 'Position';
            $new['hp_active']   = 'Active';
        }
    }
    return $new;
}
add_filter( 'manage_hp_service_posts_columns', 'hp_service_admin_columns' );

function hp_service_admin_column_data( $column, $post_id ) {
    if ( 'hp_position' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
    }
    if ( 'hp_active' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
    }
}
add_action( 'manage_hp_service_posts_custom_column', 'hp_service_admin_column_data', 10, 2 );

/**
 * Add custom admin columns for Testimonials
 */
function hp_testimonial_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( 'title' === $key ) {
            $new['hp_client_name']  = 'Client';
            $new['hp_business']     = 'Business';
            $new['hp_position']     = 'Position';
            $new['hp_active']       = 'Active';
        }
    }
    return $new;
}
add_filter( 'manage_hp_testimonial_posts_columns', 'hp_testimonial_admin_columns' );

function hp_testimonial_admin_column_data( $column, $post_id ) {
    if ( 'hp_client_name' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_client_name', true ) );
    }
    if ( 'hp_business' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_business_name', true ) );
    }
    if ( 'hp_position' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
    }
    if ( 'hp_active' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
    }
}
add_action( 'manage_hp_testimonial_posts_custom_column', 'hp_testimonial_admin_column_data', 10, 2 );

/**
 * Add custom admin columns for Downloads
 */
function hp_download_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( 'title' === $key ) {
            $new['hp_file_type']  = 'Type';
            $new['hp_view_count'] = 'Views';
        }
    }
    return $new;
}
add_filter( 'manage_hp_download_posts_columns', 'hp_download_admin_columns' );

function hp_download_admin_column_data( $column, $post_id ) {
    if ( 'hp_file_type' === $column ) {
        echo esc_html( strtoupper( get_post_meta( $post_id, '_hp_file_type', true ) ) );
    }
    if ( 'hp_view_count' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_view_count', true ) );
    }
}
add_action( 'manage_hp_download_posts_custom_column', 'hp_download_admin_column_data', 10, 2 );

/**
 * Add custom admin columns for Links
 */
function hp_link_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[ $key ] = $value;
        if ( 'title' === $key ) {
            $new['hp_url']      = 'URL';
            $new['hp_position'] = 'Position';
            $new['hp_active']   = 'Active';
        }
    }
    return $new;
}
add_filter( 'manage_hp_link_posts_columns', 'hp_link_admin_columns' );

function hp_link_admin_column_data( $column, $post_id ) {
    if ( 'hp_url' === $column ) {
        $url = get_post_meta( $post_id, '_hp_url', true );
        echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>';
    }
    if ( 'hp_position' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_position', true ) );
    }
    if ( 'hp_active' === $column ) {
        echo esc_html( get_post_meta( $post_id, '_hp_active', true ) );
    }
}
add_action( 'manage_hp_link_posts_custom_column', 'hp_link_admin_column_data', 10, 2 );
```

**Step 2: Include from functions.php**

Add after the previous include in `hpaccountants-theme/functions.php`:

```php
require get_template_directory() . '/inc/hp-meta-boxes.php';
```

**Step 3: Commit**

```bash
git add hpaccountants-theme/inc/hp-meta-boxes.php hpaccountants-theme/functions.php
git commit -m "feat: add meta boxes and admin columns for custom post types"
```

---

## Task 4: Create Newsletter System

**Files:**
- Create: `hpaccountants-theme/inc/hp-newsletter.php`
- Create: `hpaccountants-theme/js/hp-newsletter.js`
- Modify: `hpaccountants-theme/functions.php` (add include)

**Step 1: Create the newsletter PHP file**

Create `hpaccountants-theme/inc/hp-newsletter.php`:

```php
<?php
/**
 * HP Accountants Newsletter Subscription System
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create mailing list table on theme activation
 */
function hp_create_mailinglist_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hp_mailinglist';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) DEFAULT '',
        email varchar(255) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'hp_create_mailinglist_table' );

/**
 * Also create table on init if it doesn't exist (safety net)
 */
function hp_ensure_mailinglist_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hp_mailinglist';
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
        hp_create_mailinglist_table();
    }
}
add_action( 'admin_init', 'hp_ensure_mailinglist_table' );

/**
 * Enqueue newsletter scripts
 */
function hp_newsletter_scripts() {
    wp_enqueue_script(
        'hp-newsletter',
        get_template_directory_uri() . '/js/hp-newsletter.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
    wp_localize_script( 'hp-newsletter', 'hpNewsletter', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'hp_newsletter_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'hp_newsletter_scripts' );

/**
 * AJAX handler for newsletter subscription
 */
function hp_newsletter_subscribe() {
    check_ajax_referer( 'hp_newsletter_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $name  = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'hp_mailinglist';

    // Check for duplicate
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE email = %s",
        $email
    ) );

    if ( $exists ) {
        wp_send_json_error( array( 'message' => 'This email is already subscribed.' ) );
    }

    $result = $wpdb->insert(
        $table_name,
        array(
            'name'  => $name,
            'email' => $email,
        ),
        array( '%s', '%s' )
    );

    if ( false === $result ) {
        wp_send_json_error( array( 'message' => 'An error occurred. Please try again.' ) );
    }

    wp_send_json_success( array( 'message' => 'Thank you for subscribing!' ) );
}
add_action( 'wp_ajax_hp_newsletter_subscribe', 'hp_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_hp_newsletter_subscribe', 'hp_newsletter_subscribe' );

/**
 * Newsletter form shortcode for use in widgets or templates
 */
function hp_newsletter_form_shortcode() {
    ob_start();
    ?>
    <div class="hp-newsletter-form">
        <h3 class="widget-title">Mailing List</h3>
        <p>Subscribe to our mailing list for updates.</p>
        <form id="hp-newsletter-form" class="newsletter-form">
            <input type="email" name="newsletter_email" placeholder="Email Address" required>
            <button type="submit" class="newsletter-submit">Subscribe</button>
        </form>
        <div class="hp-newsletter-response" style="display:none;"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'hp_newsletter', 'hp_newsletter_form_shortcode' );

/**
 * Register newsletter widget area
 */
function hp_register_newsletter_widget() {
    register_sidebar( array(
        'name'          => 'Newsletter Signup',
        'id'            => 'hp-newsletter',
        'description'   => 'Newsletter signup form area (appears in footer)',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'hp_register_newsletter_widget' );

/**
 * Admin page for viewing mailing list subscribers
 */
function hp_mailinglist_admin_menu() {
    add_menu_page(
        'Mailing List',
        'Mailing List',
        'manage_options',
        'hp-mailinglist',
        'hp_mailinglist_admin_page',
        'dashicons-email',
        30
    );
}
add_action( 'admin_menu', 'hp_mailinglist_admin_menu' );

function hp_mailinglist_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hp_mailinglist';
    $subscribers = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
    ?>
    <div class="wrap">
        <h1>Mailing List Subscribers</h1>
        <p>Total subscribers: <strong><?php echo count( $subscribers ); ?></strong></p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subscribed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $subscribers as $sub ) : ?>
                <tr>
                    <td><?php echo esc_html( $sub->id ); ?></td>
                    <td><?php echo esc_html( $sub->name ); ?></td>
                    <td><?php echo esc_html( $sub->email ); ?></td>
                    <td><?php echo esc_html( $sub->created_at ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
```

**Step 2: Create the newsletter JavaScript**

Create `hpaccountants-theme/js/hp-newsletter.js`:

```javascript
(function($) {
    'use strict';

    $(document).on('submit', '#hp-newsletter-form', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $response = $form.siblings('.hp-newsletter-response');
        var $button = $form.find('.newsletter-submit');
        var email = $form.find('input[name="newsletter_email"]').val();

        $button.prop('disabled', true).text('Subscribing...');
        $response.hide();

        $.ajax({
            url: hpNewsletter.ajax_url,
            type: 'POST',
            data: {
                action: 'hp_newsletter_subscribe',
                nonce: hpNewsletter.nonce,
                email: email
            },
            success: function(response) {
                if (response.success) {
                    $response.html('<p class="hp-success">' + response.data.message + '</p>').show();
                    $form.find('input[name="newsletter_email"]').val('');
                } else {
                    $response.html('<p class="hp-error">' + response.data.message + '</p>').show();
                }
            },
            error: function() {
                $response.html('<p class="hp-error">An error occurred. Please try again.</p>').show();
            },
            complete: function() {
                $button.prop('disabled', false).text('Subscribe');
            }
        });
    });
})(jQuery);
```

**Step 3: Include from functions.php**

Add after previous includes in `hpaccountants-theme/functions.php`:

```php
require get_template_directory() . '/inc/hp-newsletter.php';
```

**Step 4: Commit**

```bash
git add hpaccountants-theme/inc/hp-newsletter.php hpaccountants-theme/js/hp-newsletter.js hpaccountants-theme/functions.php
git commit -m "feat: add newsletter subscription system with custom DB table"
```

---

## Task 5: Create Download View Counter

**Files:**
- Create: `hpaccountants-theme/js/hp-downloads.js`
- Modify: `hpaccountants-theme/inc/hp-newsletter.php` (add AJAX handler, or create separate file)

**Step 1: Create download tracking file**

Add to the end of `hpaccountants-theme/inc/hp-custom-post-types.php`:

```php
/**
 * AJAX handler for download view tracking
 */
function hp_track_download_view() {
    check_ajax_referer( 'hp_download_nonce', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    if ( ! $post_id || 'hp_download' !== get_post_type( $post_id ) ) {
        wp_send_json_error();
    }

    $count = (int) get_post_meta( $post_id, '_hp_view_count', true );
    update_post_meta( $post_id, '_hp_view_count', $count + 1 );

    wp_send_json_success( array( 'count' => $count + 1 ) );
}
add_action( 'wp_ajax_hp_track_download', 'hp_track_download_view' );
add_action( 'wp_ajax_nopriv_hp_track_download', 'hp_track_download_view' );

/**
 * Enqueue download tracking script on download archives
 */
function hp_download_scripts() {
    if ( is_post_type_archive( 'hp_download' ) ) {
        wp_enqueue_script(
            'hp-downloads',
            get_template_directory_uri() . '/js/hp-downloads.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );
        wp_localize_script( 'hp-downloads', 'hpDownloads', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'hp_download_nonce' ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'hp_download_scripts' );
```

**Step 2: Create download tracking JavaScript**

Create `hpaccountants-theme/js/hp-downloads.js`:

```javascript
(function($) {
    'use strict';

    $(document).on('click', '.hp-download-link', function() {
        var postId = $(this).data('post-id');
        if (postId) {
            $.ajax({
                url: hpDownloads.ajax_url,
                type: 'POST',
                data: {
                    action: 'hp_track_download',
                    nonce: hpDownloads.nonce,
                    post_id: postId
                }
            });
        }
    });
})(jQuery);
```

**Step 3: Commit**

```bash
git add hpaccountants-theme/inc/hp-custom-post-types.php hpaccountants-theme/js/hp-downloads.js
git commit -m "feat: add download view counter with AJAX tracking"
```

---

## Task 6: Create Front Page Template

**Files:**
- Modify: `hpaccountants-theme/front-page.php` (replace entirely)

**Step 1: Override front-page.php**

Replace the entire content of `hpaccountants-theme/front-page.php` with:

```php
<?php
/**
 * HP Accountants - Static Front Page
 */
get_header(); ?>

<div class="wrap">
    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area">

            <?php // About Section ?>
            <section class="hp-section hp-about-summary">
                <div class="container">
                    <?php
                    $about_page = get_page_by_path( 'about' );
                    if ( $about_page ) :
                        $about_content = wp_strip_all_tags( $about_page->post_content );
                        $about_excerpt = wp_trim_words( $about_content, 80, '...' );
                    ?>
                    <h2 class="hp-section-title">Approachable . Passionate . Accurate</h2>
                    <div class="hp-about-text">
                        <p><?php echo esc_html( $about_excerpt ); ?></p>
                        <a href="<?php echo esc_url( get_permalink( $about_page ) ); ?>" class="hp-btn">Read More</a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php // Services Section ?>
            <section class="hp-section hp-services-section">
                <div class="container">
                    <h2 class="hp-section-title">Our Services</h2>
                    <div class="flex-grid cols-3">
                        <?php
                        $services = new WP_Query( array(
                            'post_type'      => 'hp_service',
                            'posts_per_page' => 6,
                            'meta_key'       => '_hp_position',
                            'orderby'        => 'meta_value_num',
                            'order'          => 'ASC',
                            'meta_query'     => array(
                                array(
                                    'key'   => '_hp_active',
                                    'value' => 'Y',
                                ),
                            ),
                        ) );
                        if ( $services->have_posts() ) :
                            while ( $services->have_posts() ) : $services->the_post();
                        ?>
                        <div class="flex-box hp-service-card">
                            <article class="hp-card">
                                <h3 class="hp-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="hp-card-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            </article>
                        </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                    <div class="hp-section-cta">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'hp_service' ) ); ?>" class="hp-btn">View All Services</a>
                    </div>
                </div>
            </section>

            <?php // Testimonials Section ?>
            <section class="hp-section hp-testimonials-section">
                <div class="container">
                    <h2 class="hp-section-title">What Our Clients Say</h2>
                    <div class="flex-grid cols-2">
                        <?php
                        $testimonials = new WP_Query( array(
                            'post_type'      => 'hp_testimonial',
                            'posts_per_page' => 4,
                            'meta_key'       => '_hp_position',
                            'orderby'        => 'meta_value_num',
                            'order'          => 'ASC',
                            'meta_query'     => array(
                                array(
                                    'key'   => '_hp_active',
                                    'value' => 'Y',
                                ),
                            ),
                        ) );
                        if ( $testimonials->have_posts() ) :
                            while ( $testimonials->have_posts() ) : $testimonials->the_post();
                                $client_name   = get_post_meta( get_the_ID(), '_hp_client_name', true );
                                $client_title  = get_post_meta( get_the_ID(), '_hp_client_title', true );
                                $business_name = get_post_meta( get_the_ID(), '_hp_business_name', true );
                        ?>
                        <div class="flex-box hp-testimonial-card">
                            <blockquote class="hp-testimonial">
                                <div class="hp-testimonial-content">
                                    <?php the_content(); ?>
                                </div>
                                <footer class="hp-testimonial-author">
                                    <strong><?php echo esc_html( $client_name ); ?></strong>
                                    <?php if ( $client_title || $business_name ) : ?>
                                    <span>
                                        <?php
                                        $parts = array_filter( array( $client_title, $business_name ) );
                                        echo esc_html( implode( ', ', $parts ) );
                                        ?>
                                    </span>
                                    <?php endif; ?>
                                </footer>
                            </blockquote>
                        </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </section>

            <?php // Partner Links Section ?>
            <section class="hp-section hp-links-section">
                <div class="container">
                    <h2 class="hp-section-title">Our Partners</h2>
                    <div class="flex-grid cols-4">
                        <?php
                        $links = new WP_Query( array(
                            'post_type'      => 'hp_link',
                            'posts_per_page' => 8,
                            'meta_key'       => '_hp_position',
                            'orderby'        => 'meta_value_num',
                            'order'          => 'ASC',
                            'meta_query'     => array(
                                array(
                                    'key'   => '_hp_active',
                                    'value' => 'Y',
                                ),
                            ),
                        ) );
                        if ( $links->have_posts() ) :
                            while ( $links->have_posts() ) : $links->the_post();
                                $url = get_post_meta( get_the_ID(), '_hp_url', true );
                        ?>
                        <div class="flex-box hp-link-card">
                            <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="hp-partner-link">
                                <h4><?php the_title(); ?></h4>
                                <p><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                            </a>
                        </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </section>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/front-page.php
git commit -m "feat: create custom front page template"
```

---

## Task 7: Create About Page Template

**Files:**
- Create: `hpaccountants-theme/page-about.php`

**Step 1: Create page-about.php**

```php
<?php
/**
 * Template Name: About Page
 * HP Accountants - About Us page
 */
get_header(); ?>

<div class="wrap">
    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area flex-grid the-post">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title">About Us</h1>
                </header>

                <div class="entry-content">
                    <div class="hp-about-intro">
                        <h2>Approachable . Passionate . Accurate</h2>
                        <p>Holland Price &amp; Associates is a husband and wife team with over 30 years of combined experience in public practice and commercial accounting. Servicing small to medium-sized businesses is their focus.</p>
                        <p>They provide genuine business advice to their clients and not just tax updates. Is your existing accountant providing you with value for money advice or just doing your books once a year?</p>
                    </div>

                    <div class="hp-expertise">
                        <h3>Areas of Expert Advice</h3>
                        <ul>
                            <li>Accounting software (Online or desktop) - are you on the most appropriate system</li>
                            <li>Employment - are you fulfilling all of your employer requirements</li>
                            <li>Business structuring/restructuring - are you in the most tax effective and asset protective structure</li>
                            <li>Pricing - is the pricing of your goods and services set correctly</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="hp-team-member">
                        <div class="flex-grid cols-3">
                            <div class="flex-box hp-team-photo">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/scott-placeholder.jpg' ); ?>" alt="Scott Price" class="hp-team-img">
                            </div>
                            <div class="flex-box hp-team-bio" style="grid-column: span 2;">
                                <h3>Scott Price</h3>
                                <p class="hp-team-title"><em>Principal Accountant</em></p>
                                <p>Scott graduated with a Bachelor of Commerce (Major in Accounting) from the University of Southern Queensland. He is a registered tax agent, a Fellow of the Institute of Public Accountants Australia and is Treasurer of the local Football (soccer) Club.</p>
                                <p>For 15 years Scott was employed as a manager of a number of Brisbane city accounting firms, providing complex taxation and business advice to small and medium-sized businesses. His industries of expertise include the construction, medical, legal and education industries.</p>
                                <p>Scott is passionate about advising his clients on how to grow and maintain their business by setting up appropriate strategies and systems. Further, Scott seeks to help his clients minimize tax and protect assets by advising of the most suitable structure.</p>
                                <p>Some of the specialist tax areas that Scott can assist with are capital gains tax, residency, research and development tax offsets and investment properties.</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="hp-team-member">
                        <div class="flex-grid cols-3">
                            <div class="flex-box hp-team-photo">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/christy-placeholder.jpg' ); ?>" alt="Christy Price" class="hp-team-img">
                            </div>
                            <div class="flex-box hp-team-bio" style="grid-column: span 2;">
                                <h3>Christy Price (nee Holland)</h3>
                                <p class="hp-team-title"><em>Chartered Accountant</em></p>
                                <p>Christy attained her Bachelor of Business (Accountancy) at Griffith University Gold Coast. She continued with postgraduate education by becoming a member of the Institute of Chartered Accountants in Australia.</p>
                                <p>Christy's work experience spans 15 years, including financial auditing of public and private sector organisations, along with financial and management accounting for small to medium sized businesses. She has been exposed to a wide number of industries, such as property development, real estate, agriculture, aged care, childcare and not-for-profit entities.</p>
                                <p>When she is not running around after her two small children, Christy likes to play netball and performs volunteer roles at local community groups.</p>
                            </div>
                        </div>
                    </div>

                </div><!-- .entry-content -->

            </article>
        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/page-about.php
git commit -m "feat: create about page template with team bios"
```

---

## Task 8: Create Contact Page Template

**Files:**
- Create: `hpaccountants-theme/page-contact.php`

**Step 1: Create page-contact.php**

```php
<?php
/**
 * Template Name: Contact Page
 * HP Accountants - Contact Us page
 */
get_header(); ?>

<div class="wrap">
    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area flex-grid the-post">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title">Contact Us</h1>
                </header>

                <div class="entry-content">
                    <div class="flex-grid cols-2 hp-contact-grid">

                        <div class="flex-box hp-contact-map">
                            <h3>Find Us</h3>
                            <div class="hp-map-embed">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3537.5!2d152.8209220!3d-27.1981510!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDExJzUzLjMiUyAxNTLCsDQ5JzE1LjMiRQ!5e0!3m2!1sen!2sau!4v1"
                                    width="100%"
                                    height="350"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                            <div class="hp-contact-details">
                                <h4>Office</h4>
                                <p>15 Roderick Street<br>Dayboro, QLD 4521</p>
                                <h4>Phone</h4>
                                <p><a href="tel:0447384179">0447 384 179</a></p>
                                <h4>Email</h4>
                                <p><a href="mailto:price@hpaccountants.com.au">price@hpaccountants.com.au</a></p>
                                <h4>Postal Address</h4>
                                <p>PO Box 141<br>Dayboro, QLD 4521</p>
                            </div>
                        </div>

                        <div class="flex-box hp-contact-form">
                            <h3>Get In Touch</h3>
                            <?php
                            // Display page content (should contain CF7 shortcode)
                            while ( have_posts() ) : the_post();
                                the_content();
                            endwhile;
                            ?>
                        </div>

                    </div>
                </div><!-- .entry-content -->

            </article>
        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/page-contact.php
git commit -m "feat: create contact page template with map and CF7"
```

---

## Task 9: Create Services Archive and Single Templates

**Files:**
- Create: `hpaccountants-theme/archive-hp_service.php`
- Create: `hpaccountants-theme/single-hp_service.php`

**Step 1: Create archive-hp_service.php**

```php
<?php
/**
 * HP Accountants - Services Archive
 */
get_header(); ?>

<div class="wrap">
    <header class="container page-header">
        <h1 class="page-title">Our Services</h1>
    </header>

    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area">

            <?php if ( have_posts() ) : ?>
            <div class="flex-grid cols-2 hp-services-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                <div class="flex-box hp-service-item">
                    <article class="hp-card">
                        <h3 class="hp-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="hp-card-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No services found.</p>
            <?php endif; ?>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Create single-hp_service.php**

```php
<?php
/**
 * HP Accountants - Single Service
 */
get_header(); ?>

<div class="wrap">
    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area flex-grid the-post">

            <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <footer class="hp-service-footer">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'hp_service' ) ); ?>" class="hp-btn">&larr; All Services</a>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="hp-btn">Contact Us</a>
                </footer>

            </article>
            <?php endwhile; ?>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 3: Commit**

```bash
git add hpaccountants-theme/archive-hp_service.php hpaccountants-theme/single-hp_service.php
git commit -m "feat: create services archive and single templates"
```

---

## Task 10: Create Testimonials Archive Template

**Files:**
- Create: `hpaccountants-theme/archive-hp_testimonial.php`

**Step 1: Create archive-hp_testimonial.php**

```php
<?php
/**
 * HP Accountants - Testimonials Archive
 */
get_header(); ?>

<div class="wrap">
    <header class="container page-header">
        <h1 class="page-title">What Our Clients Say</h1>
    </header>

    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area">

            <?php if ( have_posts() ) : ?>
            <div class="hp-testimonials-list">
                <?php while ( have_posts() ) : the_post();
                    $client_name   = get_post_meta( get_the_ID(), '_hp_client_name', true );
                    $client_title  = get_post_meta( get_the_ID(), '_hp_client_title', true );
                    $business_name = get_post_meta( get_the_ID(), '_hp_business_name', true );
                ?>
                <article class="hp-testimonial-item">
                    <blockquote class="hp-testimonial">
                        <div class="hp-testimonial-content">
                            <?php the_content(); ?>
                        </div>
                        <footer class="hp-testimonial-author">
                            <strong><?php echo esc_html( $client_name ); ?></strong>
                            <?php if ( $client_title || $business_name ) : ?>
                            <span>
                                <?php
                                $parts = array_filter( array( $client_title, $business_name ) );
                                echo esc_html( implode( ', ', $parts ) );
                                ?>
                            </span>
                            <?php endif; ?>
                        </footer>
                    </blockquote>
                </article>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No testimonials found.</p>
            <?php endif; ?>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/archive-hp_testimonial.php
git commit -m "feat: create testimonials archive template"
```

---

## Task 11: Create Downloads Archive Template

**Files:**
- Create: `hpaccountants-theme/archive-hp_download.php`

**Step 1: Create archive-hp_download.php**

```php
<?php
/**
 * HP Accountants - Downloads Archive (grouped by category)
 */
get_header(); ?>

<div class="wrap">
    <header class="container page-header">
        <h1 class="page-title">Downloads</h1>
    </header>

    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area">

            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'download_category',
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ) );

            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                foreach ( $categories as $category ) :
                    $downloads = new WP_Query( array(
                        'post_type'      => 'hp_download',
                        'posts_per_page' => -1,
                        'meta_key'       => '_hp_view_count',
                        'orderby'        => 'meta_value_num',
                        'order'          => 'DESC',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'download_category',
                                'field'    => 'term_id',
                                'terms'    => $category->term_id,
                            ),
                        ),
                    ) );

                    if ( $downloads->have_posts() ) :
            ?>
            <section class="hp-download-category">
                <h2><?php echo esc_html( $category->name ); ?> (<?php echo esc_html( $downloads->found_posts ); ?>)</h2>
                <div class="hp-downloads-list">
                    <?php while ( $downloads->have_posts() ) : $downloads->the_post();
                        $s3_url     = get_post_meta( get_the_ID(), '_hp_s3_url', true );
                        $file_type  = get_post_meta( get_the_ID(), '_hp_file_type', true );
                        $view_count = get_post_meta( get_the_ID(), '_hp_view_count', true );
                    ?>
                    <article class="hp-download-item">
                        <div class="hp-download-info">
                            <span class="hp-download-icon hp-icon-<?php echo esc_attr( $file_type ); ?>">
                                <?php echo esc_html( strtoupper( $file_type ) ); ?>
                            </span>
                            <a href="<?php echo esc_url( $s3_url ); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="hp-download-link"
                               data-post-id="<?php the_ID(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </div>
                        <span class="hp-download-views"><?php echo esc_html( number_format( (int) $view_count ) ); ?> views</span>
                    </article>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php
                    endif;
                    wp_reset_postdata();
                endforeach;
            else :
            ?>
            <p>No downloads found.</p>
            <?php endif; ?>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/archive-hp_download.php
git commit -m "feat: create downloads archive template grouped by category"
```

---

## Task 12: Create Links Archive Template

**Files:**
- Create: `hpaccountants-theme/archive-hp_link.php`

**Step 1: Create archive-hp_link.php**

```php
<?php
/**
 * HP Accountants - Partner Links Archive
 */
get_header(); ?>

<div class="wrap">
    <header class="container page-header">
        <h1 class="page-title">Useful Links</h1>
    </header>

    <main id="main" class="site-main" role="main">
        <div id="primary" class="content-area">

            <?php if ( have_posts() ) : ?>
            <div class="flex-grid cols-2 hp-links-grid">
                <?php while ( have_posts() ) : the_post();
                    $url = get_post_meta( get_the_ID(), '_hp_url', true );
                ?>
                <div class="flex-box hp-link-item">
                    <article class="hp-card">
                        <h3 class="hp-card-title">
                            <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <div class="hp-card-content">
                            <?php the_content(); ?>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="hp-btn hp-btn-small">Visit &rarr;</a>
                    </article>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No links found.</p>
            <?php endif; ?>

        </div><!-- #primary -->
    </main><!-- #main -->
</div>

<?php get_footer();
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/archive-hp_link.php
git commit -m "feat: create partner links archive template"
```

---

## Task 13: Add HP Accountants Custom CSS

**Files:**
- Create: `hpaccountants-theme/css/hp-custom.css`
- Modify: `hpaccountants-theme/functions.php` (enqueue the CSS)

**Step 1: Create custom CSS file**

Create `hpaccountants-theme/css/hp-custom.css`:

```css
/* ==========================================================================
   HP Accountants Custom Styles
   ========================================================================== */

/* --- Sections --- */
.hp-section {
    padding: 60px 0;
}

.hp-section:nth-child(even) {
    background-color: #f9f7f5;
}

.hp-section-title {
    text-align: center;
    margin-bottom: 40px;
    font-size: 1.8em;
    font-weight: 700;
}

.hp-section-cta {
    text-align: center;
    margin-top: 30px;
}

/* --- Buttons --- */
.hp-btn {
    display: inline-block;
    padding: 12px 28px;
    background-color: #2c3e50;
    color: #fff;
    text-decoration: none;
    border-radius: 3px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.hp-btn:hover {
    background-color: #1a252f;
    color: #fff;
}

.hp-btn-small {
    padding: 8px 18px;
    font-size: 0.9em;
}

/* --- About Summary (Homepage) --- */
.hp-about-summary {
    text-align: center;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.hp-about-text {
    font-size: 1.1em;
    line-height: 1.8;
}

/* --- Service Cards --- */
.hp-services-grid,
.hp-service-card {
    margin-bottom: 20px;
}

.hp-card {
    padding: 30px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 4px;
    height: 100%;
    transition: box-shadow 0.3s ease;
}

.hp-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.hp-card-title {
    font-size: 1.2em;
    margin-bottom: 10px;
}

.hp-card-title a {
    text-decoration: none;
    color: #2c3e50;
}

.hp-card-title a:hover {
    color: #e74c3c;
}

.hp-card-content,
.hp-card-excerpt {
    font-size: 0.95em;
    line-height: 1.6;
    color: #666;
}

/* --- Service Single --- */
.hp-service-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 15px;
}

/* --- Testimonials --- */
.hp-testimonials-section {
    background-color: #f9f7f5;
}

.hp-testimonial {
    padding: 30px;
    background: #fff;
    border-left: 4px solid #2c3e50;
    border-radius: 0 4px 4px 0;
    margin-bottom: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hp-testimonial-content {
    font-style: italic;
    font-size: 1em;
    line-height: 1.7;
    color: #555;
    margin-bottom: 15px;
}

.hp-testimonial-author {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.hp-testimonial-author strong {
    font-size: 1em;
    color: #2c3e50;
}

.hp-testimonial-author span {
    font-size: 0.85em;
    color: #888;
}

.hp-testimonial-item {
    margin-bottom: 30px;
}

/* --- Downloads --- */
.hp-download-category {
    margin-bottom: 40px;
}

.hp-download-category h2 {
    border-bottom: 2px solid #2c3e50;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.hp-download-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.hp-download-item:hover {
    background-color: #f9f7f5;
}

.hp-download-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.hp-download-icon {
    display: inline-block;
    padding: 4px 8px;
    background: #e74c3c;
    color: #fff;
    font-size: 0.7em;
    font-weight: 700;
    border-radius: 3px;
    min-width: 40px;
    text-align: center;
}

.hp-icon-doc,
.hp-icon-docx {
    background: #2b579a;
}

.hp-icon-xls,
.hp-icon-xlsx {
    background: #217346;
}

.hp-download-link {
    text-decoration: none;
    color: #2c3e50;
    font-weight: 500;
}

.hp-download-link:hover {
    color: #e74c3c;
}

.hp-download-views {
    font-size: 0.85em;
    color: #999;
    white-space: nowrap;
}

/* --- Partner Links --- */
.hp-links-section {
    background-color: #f9f7f5;
}

.hp-partner-link {
    display: block;
    padding: 25px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 4px;
    text-decoration: none;
    text-align: center;
    height: 100%;
    transition: box-shadow 0.3s ease;
}

.hp-partner-link:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.hp-partner-link h4 {
    color: #2c3e50;
    margin-bottom: 8px;
}

.hp-partner-link p {
    color: #666;
    font-size: 0.85em;
    line-height: 1.5;
}

/* --- Contact Page --- */
.hp-contact-grid {
    gap: 40px;
}

.hp-map-embed {
    margin-bottom: 20px;
}

.hp-map-embed iframe {
    border-radius: 4px;
}

.hp-contact-details h4 {
    margin-top: 20px;
    margin-bottom: 5px;
    color: #2c3e50;
}

.hp-contact-details p {
    margin: 0;
}

.hp-contact-details a {
    color: #2c3e50;
}

/* --- About Page --- */
.hp-about-intro h2 {
    text-align: center;
    margin-bottom: 25px;
}

.hp-expertise {
    margin: 30px 0;
}

.hp-expertise ul {
    list-style: none;
    padding: 0;
}

.hp-expertise ul li {
    padding: 8px 0 8px 25px;
    position: relative;
}

.hp-expertise ul li:before {
    content: "\2713";
    position: absolute;
    left: 0;
    color: #2c3e50;
    font-weight: 700;
}

.hp-team-member {
    margin: 40px 0;
}

.hp-team-img {
    width: 100%;
    border-radius: 4px;
    background: #eee;
    min-height: 200px;
}

.hp-team-title {
    color: #888;
    margin-bottom: 15px;
}

/* --- Newsletter Form --- */
.hp-newsletter-form {
    max-width: 400px;
}

.newsletter-form {
    display: flex;
    gap: 8px;
}

.newsletter-form input[type="email"] {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 0.95em;
}

.newsletter-submit {
    padding: 10px 20px;
    background: #2c3e50;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-weight: 600;
}

.newsletter-submit:hover {
    background: #1a252f;
}

.hp-success {
    color: #27ae60;
    font-size: 0.9em;
    margin-top: 8px;
}

.hp-error {
    color: #e74c3c;
    font-size: 0.9em;
    margin-top: 8px;
}

/* --- Page Header --- */
.page-header {
    padding: 40px 0 20px;
    text-align: center;
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .hp-section {
        padding: 40px 15px;
    }

    .hp-contact-grid {
        gap: 20px;
    }

    .hp-service-footer {
        flex-direction: column;
    }

    .hp-download-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .newsletter-form {
        flex-direction: column;
    }

    .hp-team-member .flex-grid {
        display: block;
    }

    .hp-team-photo {
        margin-bottom: 20px;
    }
}
```

**Step 2: Enqueue the custom CSS**

Add to `hpaccountants-theme/functions.php`, inside or after the existing `ruki_scripts` function area. The cleanest approach is to add a new function:

```php
/**
 * Enqueue HP Accountants custom styles
 */
function hp_custom_scripts() {
    wp_enqueue_style(
        'hp-custom',
        get_template_directory_uri() . '/css/hp-custom.css',
        array( 'ruki-style' ),
        '1.0.0'
    );
}
add_action( 'wp_enqueue_scripts', 'hp_custom_scripts', 20 );
```

Add this at the bottom of `functions.php` near the other HP includes.

**Step 3: Commit**

```bash
git add hpaccountants-theme/css/hp-custom.css hpaccountants-theme/functions.php
git commit -m "feat: add HP Accountants custom CSS styles"
```

---

## Task 14: Add Newsletter Form to Footer

**Files:**
- Modify: `hpaccountants-theme/footer.php`

**Step 1: Add newsletter form before footer columns**

In `hpaccountants-theme/footer.php`, add the newsletter form output inside the footer, just before the `get_template_part( 'template-parts/footer/footer', 'columns' )` line:

```php
<?php // HP Newsletter form in footer ?>
<div class="footer-widget-area hp-footer-newsletter container">
    <?php echo hp_newsletter_form_shortcode(); ?>
</div>
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/footer.php
git commit -m "feat: add newsletter signup form to footer"
```

---

## Task 15: Create Placeholder Images Directory

**Files:**
- Create: `hpaccountants-theme/images/` directory
- Create placeholder image files

**Step 1: Create images directory and placeholder files**

```bash
mkdir -p ~/workspace/hpaccountants-web/hpaccountants-theme/images
```

Create `hpaccountants-theme/images/.gitkeep` (empty file to track the directory).

Create simple SVG placeholder images:

Create `hpaccountants-theme/images/scott-placeholder.jpg` - this will be replaced by the user. For now, create a simple placeholder:

```bash
# Create a minimal 1x1 pixel placeholder (user will replace)
convert -size 400x400 xc:#2c3e50 -fill white -pointsize 40 -gravity center -annotate 0 "Scott\nPrice" ~/workspace/hpaccountants-web/hpaccountants-theme/images/scott-placeholder.jpg 2>/dev/null || touch ~/workspace/hpaccountants-web/hpaccountants-theme/images/scott-placeholder.jpg

convert -size 400x400 xc:#2c3e50 -fill white -pointsize 40 -gravity center -annotate 0 "Christy\nPrice" ~/workspace/hpaccountants-web/hpaccountants-theme/images/christy-placeholder.jpg 2>/dev/null || touch ~/workspace/hpaccountants-web/hpaccountants-theme/images/christy-placeholder.jpg
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/images/
git commit -m "feat: add placeholder images directory"
```

---

## Task 16: Create Migration Script

**Files:**
- Create: `hpaccountants-theme/migration/import-content.php`

**Step 1: Create the migration script**

Create `hpaccountants-theme/migration/import-content.php`:

```php
<?php
/**
 * HP Accountants Content Migration Script
 *
 * Usage: Place this file in your WordPress root directory and run:
 *   wp eval-file import-content.php
 *
 * Or access via browser (admin only) after placing in theme:
 *   /wp-admin/admin.php?page=hp-import (after adding admin menu hook)
 *
 * This script imports content from the old Rails/Postgres database
 * into WordPress custom post types.
 */

// If running via WP-CLI, WordPress is already loaded
if ( ! defined( 'ABSPATH' ) ) {
    // If running directly, try to load WordPress
    $wp_load = dirname( __FILE__ ) . '/../../../wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    } else {
        die( 'Could not find wp-load.php. Run this via WP-CLI: wp eval-file import-content.php' );
    }
}

// Safety check
if ( ! current_user_can( 'manage_options' ) && ! defined( 'WP_CLI' ) ) {
    die( 'Unauthorized' );
}

function hp_log( $message ) {
    if ( defined( 'WP_CLI' ) ) {
        WP_CLI::log( $message );
    } else {
        echo esc_html( $message ) . "<br>\n";
    }
}

function hp_success( $message ) {
    if ( defined( 'WP_CLI' ) ) {
        WP_CLI::success( $message );
    } else {
        echo "<strong style='color:green'>" . esc_html( $message ) . "</strong><br>\n";
    }
}

// ============================================================
// 1. CREATE PAGES
// ============================================================
hp_log( '--- Creating Pages ---' );

// Home page
$home_page = get_page_by_path( 'home' );
if ( ! $home_page ) {
    $home_id = wp_insert_post( array(
        'post_title'   => 'Home',
        'post_name'    => 'home',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ) );
    hp_success( "Created Home page (ID: $home_id)" );
} else {
    $home_id = $home_page->ID;
    hp_log( "Home page already exists (ID: $home_id)" );
}

// About page
$about_content = '<p>Holland Price &amp; Associates is a husband and wife team with over 30 years of combined experience in public practice and commercial accounting. Servicing small to medium-sized businesses is their focus.</p>';
$about_page = get_page_by_path( 'about' );
if ( ! $about_page ) {
    $about_id = wp_insert_post( array(
        'post_title'    => 'About Us',
        'post_name'     => 'about',
        'post_content'  => $about_content,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-about.php',
    ) );
    hp_success( "Created About page (ID: $about_id)" );
} else {
    hp_log( 'About page already exists' );
}

// Contact page (CF7 shortcode will be added manually)
$contact_page = get_page_by_path( 'contact' );
if ( ! $contact_page ) {
    $contact_id = wp_insert_post( array(
        'post_title'    => 'Contact Us',
        'post_name'     => 'contact',
        'post_content'  => '[contact-form-7 id="REPLACE_WITH_CF7_FORM_ID" title="Contact Form"]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-contact.php',
    ) );
    hp_success( "Created Contact page (ID: $contact_id) - Update CF7 shortcode after creating form" );
} else {
    hp_log( 'Contact page already exists' );
}

// ============================================================
// 2. IMPORT SERVICES
// ============================================================
hp_log( '' );
hp_log( '--- Importing Services ---' );

$services = array(
    array( 'title' => 'Self Managed Super Funds', 'description' => 'Accounting for SMSF\'s.', 'position' => 1 ),
    array( 'title' => 'ASIC compliance', 'description' => 'Our office can look after all of your corporate compliance matters from the initial company establishment to annual returns.', 'position' => 2 ),
    array( 'title' => 'Business services', 'description' => 'Business health checks for clients including a review of financial performance, business strategy, structure, accounting systems and more. Liability limited by a scheme approved under Professional Standards Legislation.', 'position' => 3 ),
    array( 'title' => 'Business structuring & restructuring', 'description' => 'We can advise you on the most appropriate business structure for tax purposes and protection of your assets.', 'position' => 4 ),
    array( 'title' => 'Business Activity Statement preparation', 'description' => 'Activity statements can be prepared or reviewed by our office.', 'position' => 5 ),
    array( 'title' => 'Due diligence', 'description' => 'We undertake due diligence assignments to ascertain the value of prospective business\'s.', 'position' => 6 ),
    array( 'title' => 'Preparation of budgets and cash flows', 'description' => 'If you require assistance with your business cash flow we can help by creating forecasts.', 'position' => 7 ),
    array( 'title' => 'Part-time CFO', 'description' => 'With our wealth of in-house accounting experience we offer an affordable consulting service to review and analyse your accounts on a regular basis. We add value to your business by setting up proper procedures and process\'s and monitor your accounts through rolling monthly forecasts so you can make informed strategic decisions.', 'position' => 8 ),
    array( 'title' => 'Tax return preparation', 'description' => 'We prepare business returns only including partnership, company, and trust income tax returns along with Self Managed Super Funds.', 'position' => 9 ),
    array( 'title' => 'Xero Partners', 'description' => 'We can assist you in setting up with Xero Accounting Software.', 'position' => 10 ),
);

foreach ( $services as $service ) {
    $existing = get_page_by_title( $service['title'], OBJECT, 'hp_service' );
    if ( $existing ) {
        hp_log( "Service '{$service['title']}' already exists, skipping" );
        continue;
    }
    $post_id = wp_insert_post( array(
        'post_title'   => $service['title'],
        'post_content' => $service['description'],
        'post_status'  => 'publish',
        'post_type'    => 'hp_service',
    ) );
    update_post_meta( $post_id, '_hp_position', $service['position'] );
    update_post_meta( $post_id, '_hp_active', 'Y' );
    hp_success( "Imported service: {$service['title']} (ID: $post_id)" );
}

// ============================================================
// 3. IMPORT TESTIMONIALS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Testimonials ---' );

$testimonials = array(
    array(
        'quote'    => 'Thank-you for helping us change from our long term accountant to your services so smoothly. Your help has been much appreciated.',
        'name'     => 'Donna Bell',
        'title'    => 'Partner',
        'business' => 'C & D Bell Constructions',
        'position' => 1,
    ),
    array(
        'quote'    => 'Holland Price are extremely helpful and efficient each year when they do our tax and also throughout the year with updates and any questions we have. We love that they are a local business and would recommend their services to any small business in the area looking for a professional company to look after their tax.',
        'name'     => 'Jason & Nicki Andrews',
        'title'    => '',
        'business' => 'Dayboro Mobile Pool Supplies & Service',
        'position' => 2,
    ),
    array(
        'quote'    => 'I would recommend Holland Price & Associates to anyone that wants to have honest service. Scott is there for taxation and business management advice and always happy to help.',
        'name'     => 'David Joseph',
        'title'    => 'Director',
        'business' => 'Joseph Group',
        'position' => 3,
    ),
    array(
        'quote'    => 'We have worked with Holland Price Associates since its inception, and would never consider working with anyone else. HPA strikes the perfect balance between professional accounting while giving the personal touches that let you know that they have your business and family needs as their utmost priority. We have found them to be exceptional in their service, promptness, knowledge and trustworthiness. We have referred numerous family members and friends to HPA, and have heard nothing but positive experiences. We would give HPA our highest endorsement and can only see them going from strength to strength.',
        'name'     => 'Dr Simon Lucey',
        'title'    => 'Associate Research Professor',
        'business' => 'The Robotics Institute at CMU Pittsburgh USA',
        'position' => 4,
    ),
);

foreach ( $testimonials as $t ) {
    $existing = get_page_by_title( $t['name'] . ' - Testimonial', OBJECT, 'hp_testimonial' );
    if ( $existing ) {
        hp_log( "Testimonial from '{$t['name']}' already exists, skipping" );
        continue;
    }
    $post_id = wp_insert_post( array(
        'post_title'   => $t['name'] . ' - Testimonial',
        'post_content' => $t['quote'],
        'post_status'  => 'publish',
        'post_type'    => 'hp_testimonial',
    ) );
    update_post_meta( $post_id, '_hp_client_name', $t['name'] );
    update_post_meta( $post_id, '_hp_client_title', $t['title'] );
    update_post_meta( $post_id, '_hp_business_name', $t['business'] );
    update_post_meta( $post_id, '_hp_position', $t['position'] );
    update_post_meta( $post_id, '_hp_active', 'Y' );
    hp_success( "Imported testimonial from: {$t['name']} (ID: $post_id)" );
}

// ============================================================
// 4. IMPORT DOWNLOAD CATEGORIES
// ============================================================
hp_log( '' );
hp_log( '--- Creating Download Categories ---' );

$categories = array( 'Our Fees', 'Our Articles', 'Our Templates' );
$cat_ids = array();

foreach ( $categories as $cat_name ) {
    $existing = term_exists( $cat_name, 'download_category' );
    if ( $existing ) {
        $cat_ids[ $cat_name ] = $existing['term_id'];
        hp_log( "Category '$cat_name' already exists" );
    } else {
        $result = wp_insert_term( $cat_name, 'download_category' );
        if ( ! is_wp_error( $result ) ) {
            $cat_ids[ $cat_name ] = $result['term_id'];
            hp_success( "Created category: $cat_name" );
        }
    }
}

// ============================================================
// 5. IMPORT DOWNLOADS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Downloads ---' );

// Map: category_id from Rails => category name
$rails_cat_map = array(
    1 => 'Our Fees',
    2 => 'Our Articles',
    3 => 'Our Templates',
);

// S3 bucket base URL (from Rails app config)
$s3_base = 'https://s3.amazonaws.com/hpaccountants/downloads/attachments/000/000/';

$downloads = array(
    array( 'id' => 5,  'title' => 'To employ or not to employ?', 'file' => 'To_employ_or_not_to_employ_-_Article.pdf', 'type' => 'pdf', 'views' => 1603, 'cat_id' => 3 ),
    array( 'id' => 6,  'title' => 'Cloud Accounting, Is it right for you?', 'file' => 'Cloud_Accounting_-_Holland_Price_Feb_19_normal.pdf', 'type' => 'pdf', 'views' => 1499, 'cat_id' => 3 ),
    array( 'id' => 7,  'title' => 'Top 5 small business mistakes', 'file' => 'Sept_15_Edition_HP_A_-_Grapevine_normal.pdf', 'type' => 'pdf', 'views' => 1413, 'cat_id' => 3 ),
    array( 'id' => 8,  'title' => 'New Client Information Form', 'file' => 'New_Client_Information_Form.doc', 'type' => 'doc', 'views' => 1513, 'cat_id' => 2 ),
    array( 'id' => 24, 'title' => 'Newsletter Quarter 3 2017', 'file' => 'HP_BM_Q3_2017.pdf', 'type' => 'pdf', 'views' => 551, 'cat_id' => 3 ),
    array( 'id' => 25, 'title' => 'Newsletter Quarter 4 2017', 'file' => 'HP_BM_Q4_2017.pdf', 'type' => 'pdf', 'views' => 748, 'cat_id' => 3 ),
    array( 'id' => 26, 'title' => 'Newsletter Quarter 1 2018', 'file' => 'HP_BM_Q1_2018.pdf', 'type' => 'pdf', 'views' => 669, 'cat_id' => 3 ),
    array( 'id' => 27, 'title' => 'Newsletter Quarter 2 2018', 'file' => 'HP_BM_Q2_2018.pdf', 'type' => 'pdf', 'views' => 632, 'cat_id' => 3 ),
    array( 'id' => 28, 'title' => 'Year End Strategies 2018', 'file' => 'Year_End_Strategies_2018.pdf', 'type' => 'pdf', 'views' => 626, 'cat_id' => 3 ),
    array( 'id' => 29, 'title' => 'Newsletter Quarter 3 2018', 'file' => 'HP_BM_Q3_2018.pdf', 'type' => 'pdf', 'views' => 750, 'cat_id' => 3 ),
    array( 'id' => 30, 'title' => 'Newsletter Quarter 4 2018', 'file' => 'HP_BM_Q4_2018.pdf', 'type' => 'pdf', 'views' => 734, 'cat_id' => 3 ),
    array( 'id' => 31, 'title' => 'Newsletter Quarter 1 2019', 'file' => 'HP_BM_Q1_2019.pdf', 'type' => 'pdf', 'views' => 632, 'cat_id' => 3 ),
    array( 'id' => 32, 'title' => 'Newsletter Quarter 2 2019', 'file' => 'HP_BM_Q2_2019.pdf', 'type' => 'pdf', 'views' => 649, 'cat_id' => 3 ),
    array( 'id' => 33, 'title' => 'End of Year Update 2019', 'file' => 'EOYU2019.pdf', 'type' => 'pdf', 'views' => 726, 'cat_id' => 3 ),
    array( 'id' => 34, 'title' => 'Newsletter Quarter 3 2019', 'file' => 'HP_BM_Q3_2019.pdf', 'type' => 'pdf', 'views' => 723, 'cat_id' => 3 ),
    array( 'id' => 35, 'title' => 'Newsletter Quarter 4 2019', 'file' => 'HP_BM_Q4_2019.pdf', 'type' => 'pdf', 'views' => 734, 'cat_id' => 3 ),
    array( 'id' => 36, 'title' => 'Newsletter Quarter 1 2020', 'file' => 'HP_BM_Q1_2020.pdf', 'type' => 'pdf', 'views' => 674, 'cat_id' => 3 ),
    array( 'id' => 37, 'title' => 'Seven things to know about COVID-19', 'file' => 'Seven_things_you_need_to_know_about_COVID-19_and_how_it_affects_you_and_your_business.pdf', 'type' => 'pdf', 'views' => 742, 'cat_id' => 3 ),
    array( 'id' => 38, 'title' => 'Newsletter Quarter 2 2020', 'file' => 'HP_BM_Q2_2020.pdf', 'type' => 'pdf', 'views' => 755, 'cat_id' => 3 ),
    array( 'id' => 39, 'title' => 'Newsletter Quarter 3 2020', 'file' => 'Newsletter_Quarter_2_2020.pdf', 'type' => 'pdf', 'views' => 932, 'cat_id' => 3 ),
    array( 'id' => 48, 'title' => 'Newsletter Quarter 4 2020', 'file' => 'Newsletter_Quarter_4_2020.pdf', 'type' => 'pdf', 'views' => 672, 'cat_id' => 3 ),
    array( 'id' => 52, 'title' => 'Our Fee Structure from 1 July 2022', 'file' => 'Our_Fee_Structure_from_1_July_2022_-_General.pdf', 'type' => 'pdf', 'views' => 703, 'cat_id' => 1 ),
    array( 'id' => 53, 'title' => 'Client Checklist for 2022', 'file' => 'CLIENT_CHECKLIST_2022.pdf', 'type' => 'pdf', 'views' => 643, 'cat_id' => 2 ),
);

foreach ( $downloads as $dl ) {
    $existing = get_page_by_title( $dl['title'], OBJECT, 'hp_download' );
    if ( $existing ) {
        hp_log( "Download '{$dl['title']}' already exists, skipping" );
        continue;
    }

    // Construct S3 URL (Paperclip format: /000/000/0XX/original/filename)
    $id_padded = str_pad( $dl['id'], 3, '0', STR_PAD_LEFT );
    $s3_url = $s3_base . $id_padded . '/original/' . $dl['file'];

    $post_id = wp_insert_post( array(
        'post_title'   => $dl['title'],
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'hp_download',
    ) );

    update_post_meta( $post_id, '_hp_s3_url', $s3_url );
    update_post_meta( $post_id, '_hp_file_type', $dl['type'] );
    update_post_meta( $post_id, '_hp_view_count', $dl['views'] );

    // Assign category
    $cat_name = isset( $rails_cat_map[ $dl['cat_id'] ] ) ? $rails_cat_map[ $dl['cat_id'] ] : 'Our Templates';
    if ( isset( $cat_ids[ $cat_name ] ) ) {
        wp_set_object_terms( $post_id, (int) $cat_ids[ $cat_name ], 'download_category' );
    }

    hp_success( "Imported download: {$dl['title']} (ID: $post_id)" );
}

// ============================================================
// 6. IMPORT LINKS
// ============================================================
hp_log( '' );
hp_log( '--- Importing Links ---' );

$links = array(
    array( 'title' => 'XERO accounting software', 'description' => 'Partners in Xero. Log in online anytime, anywhere on your Mac, PC, tablet or phone and see up-to-date financials.', 'url' => 'https://www.xero.com/au/', 'position' => 1, 'active' => 'Y' ),
    array( 'title' => 'VEND point of sale software', 'description' => 'We are partners for Vend point-of-sale, inventory, and customer loyalty software for iPad, Mac & PC.', 'url' => 'http://www.vendhq.com/', 'position' => 2, 'active' => 'Y' ),
    array( 'title' => 'CLASS Super software', 'description' => 'Online SMSF accounting solution. Access up to date reports for your SMSF anytime online.', 'url' => 'http://www.classsuper.com.au/', 'position' => 3, 'active' => 'Y' ),
    array( 'title' => 'Point & Claim software', 'description' => 'Save time sorting out your receipts at tax time, claim every dollar you\'re entitled to and never lose a receipt again.', 'url' => 'https://www.pointandclaim.com/', 'position' => 4, 'active' => 'Y' ),
    array( 'title' => 'Australian Taxation Office', 'description' => 'The Australian Tax Office website', 'url' => 'http://www.ato.gov.au', 'position' => 5, 'active' => 'Y' ),
    array( 'title' => 'Superannuation', 'description' => 'A place for all things super.', 'url' => 'http://www.super.com.au', 'position' => 6, 'active' => 'N' ),
    array( 'title' => 'ABR', 'description' => 'Registration of your business with the Australian Government.', 'url' => 'http://www.abr.gov.au', 'position' => 7, 'active' => 'Y' ),
    array( 'title' => 'ASIC', 'description' => 'Australian Securities & Investment Commission', 'url' => 'http://www.asic.gov.au/', 'position' => 8, 'active' => 'Y' ),
);

foreach ( $links as $link ) {
    $existing = get_page_by_title( $link['title'], OBJECT, 'hp_link' );
    if ( $existing ) {
        hp_log( "Link '{$link['title']}' already exists, skipping" );
        continue;
    }
    $post_id = wp_insert_post( array(
        'post_title'   => $link['title'],
        'post_content' => $link['description'],
        'post_status'  => 'publish',
        'post_type'    => 'hp_link',
    ) );
    update_post_meta( $post_id, '_hp_url', $link['url'] );
    update_post_meta( $post_id, '_hp_position', $link['position'] );
    update_post_meta( $post_id, '_hp_active', $link['active'] );
    hp_success( "Imported link: {$link['title']} (ID: $post_id)" );
}

// ============================================================
// 7. SET STATIC FRONT PAGE
// ============================================================
hp_log( '' );
hp_log( '--- Configuring WordPress Settings ---' );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
hp_success( "Set static front page to Home (ID: $home_id)" );

// Set permalink structure
update_option( 'permalink_structure', '/%postname%/' );
hp_success( 'Set permalink structure to /%postname%/' );

// Site title and tagline
update_option( 'blogname', 'Holland Price & Associates' );
update_option( 'blogdescription', 'Professional Accounting Services' );
hp_success( 'Updated site title and tagline' );

// Timezone
update_option( 'timezone_string', 'Australia/Brisbane' );
hp_success( 'Set timezone to Australia/Brisbane' );

// ============================================================
// 8. CREATE MAILING LIST TABLE AND IMPORT SUBSCRIBERS
// ============================================================
hp_log( '' );
hp_log( '--- Setting up Mailing List ---' );

// Ensure table exists
hp_create_mailinglist_table();

global $wpdb;
$table_name = $wpdb->prefix . 'hp_mailinglist';

// Import only the legitimate-looking subscribers
$subscribers = array(
    array( 'name' => 'Russell', 'email' => 'russell.adcock1@gmail.com' ),
);

foreach ( $subscribers as $sub ) {
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE email = %s",
        $sub['email']
    ) );
    if ( ! $exists ) {
        $wpdb->insert( $table_name, $sub, array( '%s', '%s' ) );
        hp_success( "Added subscriber: {$sub['email']}" );
    }
}

// ============================================================
// DONE
// ============================================================
hp_log( '' );
hp_success( '=== Migration Complete ===' );
hp_log( '' );
hp_log( 'Next steps:' );
hp_log( '1. Install and activate Contact Form 7 plugin' );
hp_log( '2. Create a contact form in CF7 with fields: Name, Email, Message' );
hp_log( '3. Set CF7 mail recipient to: price@hpaccountants.com.au' );
hp_log( '4. Update the Contact page with the correct CF7 shortcode' );
hp_log( '5. Set up menus: Primary (Home, About, Services, Testimonials, Downloads, Contact)' );
hp_log( '6. Add newsletter widget to Footer Column 3 (use [hp_newsletter] shortcode in Text widget)' );
hp_log( '7. Replace placeholder team photos in /images/' );
hp_log( '8. Verify S3 download URLs are accessible' );
hp_log( '9. Flush permalinks: Settings > Permalinks > Save' );
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/migration/import-content.php
git commit -m "feat: create content migration script from Rails/Postgres data"
```

---

## Task 17: Update TGMPA Plugin Requirements

**Files:**
- Modify: `hpaccountants-theme/inc/tgmpa.php`

**Step 1: Ensure Contact Form 7 is listed as required**

In `hpaccountants-theme/inc/tgmpa.php`, find the Contact Form 7 entry in the `$plugins` array and change `'required' => false` to `'required' => true`. If it doesn't have a required field, add one.

Find the entry that looks like:
```php
array(
    'name'     => 'Contact Form 7',
    'slug'     => 'contact-form-7',
    'required' => false,
),
```

Change to:
```php
array(
    'name'     => 'Contact Form 7',
    'slug'     => 'contact-form-7',
    'required' => true,
),
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/inc/tgmpa.php
git commit -m "feat: make Contact Form 7 a required plugin"
```

---

## Task 18: Create Deployment Documentation

**Files:**
- Create: `hpaccountants-theme/migration/DEPLOYMENT.md`

**Step 1: Create deployment guide**

Create `hpaccountants-theme/migration/DEPLOYMENT.md`:

```markdown
# HP Accountants WordPress - Deployment Guide

## Prerequisites

- A2 Hosting VPS with WordPress installed
- MySQL database created
- PHP 7.0+ with required WordPress extensions
- SSH or FTP access to the server

## Step 1: Upload Theme

Upload the entire `hpaccountants-theme/` folder to:
```
/path/to/wordpress/wp-content/themes/hpaccountants-theme/
```

## Step 2: Activate Theme

1. Log in to WordPress admin
2. Go to Appearance > Themes
3. Activate "HP Accountants"

## Step 3: Install Required Plugins

1. Go to Appearance > Install Plugins
2. Install and activate **Contact Form 7**
3. Install other recommended plugins as needed

## Step 4: Run Migration

Option A - Via WP-CLI (recommended):
```bash
cd /path/to/wordpress
wp eval-file wp-content/themes/hpaccountants-theme/migration/import-content.php
```

Option B - Copy to WordPress root and access via browser:
```bash
cp wp-content/themes/hpaccountants-theme/migration/import-content.php .
```
Then visit: `https://yourdomain.com/import-content.php` (logged in as admin)

## Step 5: Configure Contact Form 7

1. Go to Contact > Add New
2. Create form with fields:
   - Name: `[text* your-name placeholder "Your Name"]`
   - Email: `[email* your-email placeholder "Your Email"]`
   - Message: `[textarea* your-message placeholder "Your Message"]`
   - Submit: `[submit "Send Message"]`
3. Set Mail tab recipient to: `price@hpaccountants.com.au`
4. Set subject to: `Email submitted via Holland Price & Associates website`
5. Copy the shortcode (e.g., `[contact-form-7 id="123" title="Contact Form"]`)
6. Edit the Contact page and paste the shortcode in the content

## Step 6: Set Up Menus

1. Go to Appearance > Menus
2. Create "Primary Menu":
   - Home (page)
   - About Us (page)
   - Services (custom link to /services/)
   - Testimonials (custom link to /testimonials/)
   - Downloads (custom link to /downloads/)
   - Contact Us (page)
3. Assign to "Primary Menu" location
4. Create "Footer Menu" with same or subset of links

## Step 7: Configure Widgets

1. Go to Appearance > Widgets
2. Footer Column 1: Add Text widget with contact details
3. Footer Column 2: Add Text widget with social links
4. Footer Column 3: Newsletter form auto-appears in footer

## Step 8: Flush Permalinks

Go to Settings > Permalinks and click "Save Changes" (even without changing anything).

## Step 9: Final Checks

- [ ] Homepage displays correctly with services, testimonials, and links
- [ ] About page shows team bios
- [ ] Services archive lists all services
- [ ] Individual service pages work
- [ ] Testimonials page displays quotes
- [ ] Downloads page shows files grouped by category
- [ ] Download links to S3 work
- [ ] Contact form sends emails
- [ ] Newsletter signup works
- [ ] Mobile responsive design works
- [ ] Replace placeholder images with actual team photos
```

**Step 2: Commit**

```bash
git add hpaccountants-theme/migration/DEPLOYMENT.md
git commit -m "docs: add deployment guide"
```

---

## Task 19: Final Verification and Cleanup

**Step 1: Verify theme file structure**

```bash
find hpaccountants-theme/ -type f -name "*.php" | sort
```

Expected output should include:
```
hpaccountants-theme/archive-hp_download.php
hpaccountants-theme/archive-hp_link.php
hpaccountants-theme/archive-hp_service.php
hpaccountants-theme/archive-hp_testimonial.php
hpaccountants-theme/front-page.php
hpaccountants-theme/functions.php
hpaccountants-theme/inc/hp-custom-post-types.php
hpaccountants-theme/inc/hp-meta-boxes.php
hpaccountants-theme/inc/hp-newsletter.php
hpaccountants-theme/migration/import-content.php
hpaccountants-theme/page-about.php
hpaccountants-theme/page-contact.php
hpaccountants-theme/single-hp_service.php
```
Plus all original Ruki theme files.

**Step 2: Verify no PHP syntax errors**

```bash
for f in hpaccountants-theme/inc/hp-*.php hpaccountants-theme/archive-*.php hpaccountants-theme/page-*.php hpaccountants-theme/single-*.php hpaccountants-theme/front-page.php hpaccountants-theme/migration/import-content.php; do
    php -l "$f"
done
```

Expected: All files should return "No syntax errors detected".

**Step 3: Final commit**

```bash
git add -A
git commit -m "chore: final verification - all theme files in place"
```
