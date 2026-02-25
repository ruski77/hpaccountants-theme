<?php
/**
 * HP Accountants - Downloads Archive (grouped by category)
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <header class="page-header">
        <div class="container">
            <h1>Downloads</h1>
        </div>
    </header>

    <section class="section">
        <div class="container">
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
            <div class="download-category">
                <h2><?php echo esc_html( $category->name ); ?> (<?php echo esc_html( $downloads->found_posts ); ?>)</h2>
                <?php while ( $downloads->have_posts() ) : $downloads->the_post();
                    $s3_url     = get_post_meta( get_the_ID(), '_hp_s3_url', true );
                    $file_type  = get_post_meta( get_the_ID(), '_hp_file_type', true );
                    $view_count = get_post_meta( get_the_ID(), '_hp_view_count', true );
                ?>
                <div class="download-row">
                    <div class="download-info">
                        <span class="file-badge file-badge-<?php echo esc_attr( $file_type ); ?>">
                            <?php echo esc_html( strtoupper( $file_type ) ); ?>
                        </span>
                        <a href="<?php echo esc_url( $s3_url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="download-link"
                           data-post-id="<?php the_ID(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </div>
                    <span class="download-views"><?php echo esc_html( number_format( (int) $view_count ) ); ?> views</span>
                </div>
                <?php endwhile; ?>
            </div>
            <?php
                    endif;
                    wp_reset_postdata();
                endforeach;
            else :
            ?>
            <p>No downloads found.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
