<?php
/**
 * HP Accountants - Generic Page Template
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

            <?php endwhile; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
