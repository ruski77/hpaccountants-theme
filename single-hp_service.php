<?php
/**
 * HP Accountants - Single Service
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <section class="section">
        <div class="container single-content">
            <?php while ( have_posts() ) : the_post(); ?>

            <h1><?php the_title(); ?></h1>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <div class="single-footer">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'hp_service' ) ); ?>" class="btn-secondary">&larr; All Services</a>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="btn-primary">Contact Us</a>
            </div>

            <?php endwhile; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
