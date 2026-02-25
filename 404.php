<?php
/**
 * HP Accountants - 404 Page
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <section class="section">
        <div class="container not-found">
            <h1>404</h1>
            <p>The page you're looking for doesn't exist or has been moved.</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary">Back to Homepage</a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
