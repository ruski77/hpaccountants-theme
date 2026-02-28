<?php
/**
 * HP Accountants - Footer
 *
 * @package HP_Accountants
 */
?>

<footer class="site-footer">

    <div class="footer-newsletter">
        <div class="container footer-newsletter-inner">
            <h3>Stay Updated</h3>
            <p>Subscribe to our newsletter for the latest accounting news and updates.</p>
            <?php echo hp_newsletter_shortcode( array() ); ?>
        </div>
    </div>

    <div class="footer-info">
        <div class="container grid grid-3">
            <div class="footer-col">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo-footer.png' ); ?>"
                         alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                         class="footer-logo-img"
                         loading="lazy">
                </a>
                <p>Holland Price &amp; Associates is a husband and wife team providing professional accounting services to small and medium-sized businesses in Dayboro and surrounds.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer-nav',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ) );
                ?>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>15 Roderick Street<br>Dayboro, QLD 4521</p>
                <p><a href="tel:0447384179">0447 384 179</a></p>
                <p><a href="mailto:price@hpaccountants.com.au">price@hpaccountants.com.au</a></p>
            </div>
        </div>
    </div>

    <div class="footer-copyright">
        <div class="container">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Holland Price &amp; Associates. All rights reserved.</p>
        </div>
    </div>

</footer>

<button class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'hpaccountants' ); ?>">&#8593;</button>

<?php wp_footer(); ?>
</body>
</html>
