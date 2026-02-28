<?php
/**
 * Template Name: About Page
 * HP Accountants - About Us page
 *
 * @package HP_Accountants
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <section class="section section-alt">
        <div class="container about-intro">
            <div class="about-photo">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/family.jpg' ); ?>" alt="The Holland Price Family">
            </div>
            <h2>Approachable. Passionate. Accurate.</h2>
            <p>Holland Price &amp; Associates is a husband and wife team with over 30 years of combined experience in public practice and commercial accounting. Servicing small to medium-sized businesses is their focus.</p>
            <p>They provide genuine business advice to their clients and not just tax updates. Is your existing accountant providing you with value for money advice or just doing your books once a year?</p>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 750px;">
            <div class="expertise">
                <h3>Areas of Expert Advice</h3>
                <ul>
                    <li>Accounting software (Online or desktop) - are you on the most appropriate system</li>
                    <li>Employment - are you fulfilling all of your employer requirements</li>
                    <li>Business structuring/restructuring - are you in the most tax effective and asset protective structure</li>
                    <li>Pricing - is the pricing of your goods and services set correctly</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <div class="team-member">
                <div class="grid grid-3">
                    <div class="team-photo">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/scott.jpg' ); ?>" alt="Scott Price">
                    </div>
                    <div class="team-bio">
                        <h3 class="team-name">Scott Price</h3>
                        <p class="team-title">Principal Accountant</p>
                        <p>Scott graduated with a Bachelor of Commerce (Major in Accounting) from the University of Southern Queensland. He is a registered tax agent, a Fellow of the Institute of Public Accountants Australia and is Treasurer of the local Football (soccer) Club.</p>
                        <p>For 15 years Scott was employed as a manager of a number of Brisbane city accounting firms, providing complex taxation and business advice to small and medium-sized businesses. His industries of expertise include the construction, medical, legal and education industries.</p>
                        <p>Scott is passionate about advising his clients on how to grow and maintain their business by setting up appropriate strategies and systems. Further, Scott seeks to help his clients minimize tax and protect assets by advising of the most suitable structure.</p>
                        <p>Some of the specialist tax areas that Scott can assist with are capital gains tax, residency, research and development tax offsets and investment properties.</p>
                    </div>
                </div>
            </div>

            <div class="team-member">
                <div class="grid grid-3">
                    <div class="team-photo">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/christy.jpg' ); ?>" alt="Christy Price">
                    </div>
                    <div class="team-bio">
                        <h3 class="team-name">Christy Price (nee Holland)</h3>
                        <p class="team-title">Chartered Accountant</p>
                        <p>Christy attained her Bachelor of Business (Accountancy) at Griffith University Gold Coast. She continued with postgraduate education by becoming a member of the Institute of Chartered Accountants in Australia.</p>
                        <p>Christy's work experience spans 15 years, including financial auditing of public and private sector organisations, along with financial and management accounting for small to medium sized businesses. She has been exposed to a wide number of industries, such as property development, real estate, agriculture, aged care, childcare and not-for-profit entities.</p>
                        <p>When she is not running around after her two small children, Christy likes to play netball and performs volunteer roles at local community groups.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
