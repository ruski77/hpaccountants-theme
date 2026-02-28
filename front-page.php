<?php
/**
 * HP Accountants - Static Front Page
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Professional Accounting Services</h1>
            <p>Holland Price &amp; Associates &mdash; trusted accounting for small to medium businesses in Dayboro and beyond.</p>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="btn-secondary">Get In Touch</a>
        </div>
    </section>

    <!-- About Summary -->
    <section class="section">
        <div class="container about-summary">
            <?php
            $about_page = get_page_by_path( 'about' );
            if ( $about_page ) :
                $about_content = wp_strip_all_tags( $about_page->post_content );
                $about_excerpt = wp_trim_words( $about_content, 80, '...' );
            ?>
            <p><?php echo esc_html( $about_excerpt ); ?></p>
            <a href="<?php echo esc_url( get_permalink( $about_page ) ); ?>" class="btn-secondary">Read More</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Services -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="grid grid-3">
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
                <article class="card">
                    <h3 class="card-title"><?php the_title(); ?></h3>
                    <div class="card-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'hp_service' ) ); ?>" class="btn-primary">View All Services</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">What Our Clients Say</h2>
            <?php
            $testimonials = new WP_Query( array(
                'post_type'      => 'hp_testimonial',
                'posts_per_page' => -1,
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
                $slide_index = 0;
            ?>
            <div class="testimonial-carousel">
                <div class="testimonial-track">
                    <?php while ( $testimonials->have_posts() ) : $testimonials->the_post();
                        $client_name   = get_post_meta( get_the_ID(), '_hp_client_name', true );
                        $client_title  = get_post_meta( get_the_ID(), '_hp_client_title', true );
                        $business_name = get_post_meta( get_the_ID(), '_hp_business_name', true );
                    ?>
                    <blockquote class="testimonial-slide<?php echo 0 === $slide_index ? ' is-active' : ''; ?>">
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
                    <?php $slide_index++; endwhile; ?>
                </div>
                <div class="testimonial-controls">
                    <button class="testimonial-prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'hpaccountants' ); ?>">&larr;</button>
                    <div class="testimonial-dots">
                        <?php for ( $i = 0; $i < $slide_index; $i++ ) : ?>
                        <button class="testimonial-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-index="<?php echo $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Testimonial %d', 'hpaccountants' ), $i + 1 ) ); ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <button class="testimonial-next" aria-label="<?php esc_attr_e( 'Next testimonial', 'hpaccountants' ); ?>">&rarr;</button>
                </div>
            </div>
            <?php
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>

    <!-- Partners -->
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-title">Our Partners</h2>
            <div class="grid grid-3">
                <?php
                $links = new WP_Query( array(
                    'post_type'      => 'hp_link',
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
                if ( $links->have_posts() ) :
                    while ( $links->have_posts() ) : $links->the_post();
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
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
