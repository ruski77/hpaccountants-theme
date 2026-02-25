<?php
/**
 * HP Accountants - Partner Links Archive
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <header class="page-header">
        <div class="container">
            <h1>Our Partners</h1>
        </div>
    </header>

    <section class="section section-alt">
        <div class="container">
            <?php if ( have_posts() ) : ?>
            <div class="grid grid-4">
                <?php while ( have_posts() ) : the_post();
                    $url = get_post_meta( get_the_ID(), '_hp_url', true );
                ?>
                <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="partner-card">
                    <?php
                    $logo_file = get_post_meta( get_the_ID(), '_hp_logo', true );
                    if ( $logo_file ) :
                    ?>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/images/' . $logo_file ); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php else : ?>
                    <h4><?php the_title(); ?></h4>
                    <?php endif; ?>
                    <p><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                </a>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No partner links found.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
