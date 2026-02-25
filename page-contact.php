<?php
/**
 * Template Name: Contact Page
 * HP Accountants - Contact Us page
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <section class="section">
        <div class="container">
            <h1 class="section-title">Contact Us</h1>
            <div class="grid grid-2 contact-grid">

                <div class="contact-map">
                    <div class="map-embed">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3537.5!2d152.8209220!3d-27.1981510!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDExJzUzLjMiUyAxNTLCsDQ5JzE1LjMiRQ!5e0!3m2!1sen!2sau!4v1"
                            width="100%"
                            height="350"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div class="contact-details">
                        <h4>Office</h4>
                        <p>15 Roderick Street<br>Dayboro, QLD 4521</p>
                        <h4>Phone</h4>
                        <p><a href="tel:0447384179">0447 384 179</a></p>
                        <h4>Email</h4>
                        <p><a href="mailto:price@hpaccountants.com.au">price@hpaccountants.com.au</a></p>
                        <h4>Postal Address</h4>
                        <p>PO Box 141<br>Dayboro, QLD 4521</p>
                    </div>
                </div>

                <div class="contact-form">
                    <h3>Get In Touch</h3>
                    <?php
                    while ( have_posts() ) : the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>

            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
