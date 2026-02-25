<?php
/**
 * HP Accountants - Services Archive
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <header class="page-header">
        <div class="container">
            <h1>Our Services</h1>
        </div>
    </header>

    <section class="section section-alt">
        <div class="container">
            <?php if ( have_posts() ) : ?>
            <div class="grid grid-2">
                <?php while ( have_posts() ) : the_post(); ?>
                <article class="card">
                    <h3 class="card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="card-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
            <p>No services found.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
