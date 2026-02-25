<?php
/**
 * HP Accountants - Testimonials Archive
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <header class="page-header">
        <div class="container">
            <h1>What Our Clients Say</h1>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : ?>
            <div class="grid grid-2">
                <?php while ( have_posts() ) : the_post();
                    $client_name   = get_post_meta( get_the_ID(), '_hp_client_name', true );
                    $client_title  = get_post_meta( get_the_ID(), '_hp_client_title', true );
                    $business_name = get_post_meta( get_the_ID(), '_hp_business_name', true );
                ?>
                <blockquote class="testimonial">
                    <div class="testimonial-content">
                        <?php the_content(); ?>
                    </div>
                    <footer class="testimonial-author">
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
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No testimonials found.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
