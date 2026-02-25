<?php
/**
 * HP Accountants - Index (Fallback Template)
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <header class="page-header">
        <div class="container">
            <h1><?php
            if ( is_archive() ) {
                the_archive_title();
            } elseif ( is_search() ) {
                printf( esc_html__( 'Search Results for: %s', 'hpaccountants' ), get_search_query() );
            } else {
                esc_html_e( 'Latest Posts', 'hpaccountants' );
            }
            ?></h1>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <?php if ( have_posts() ) : ?>
            <div class="grid grid-2">
                <?php while ( have_posts() ) : the_post(); ?>
                <article class="card">
                    <h3 class="card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="card-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_navigation(); ?>
            <?php else : ?>
            <p><?php esc_html_e( 'No content found.', 'hpaccountants' ); ?></p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
