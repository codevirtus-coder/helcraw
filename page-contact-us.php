<?php get_header(); ?>

<?php get_template_part('templates/banner'); ?>

<section class="container">
    <div class="row my-5">
        <div class="col-12 col-md-4 contact-details" data-aos="fade-up">
            <h3>VISIT US</h3>

            <h5 class="row">
                <div class="col-2">
                    <i class="bi bi-telephone-fill me-2"></i>
                </div>
                <div class="col-10">
                    <p><?php echo carbon_get_theme_option('helcraw_number'); ?></p>
                </div>
            </h5>

            <h5 class="row">
                <div class="col-2">
                    <i class="bi bi-envelope-fill me-2"></i>
                </div>
                <div class="col-10">
                    <p>
                        <a href="mailto:<?php echo carbon_get_theme_option('helcraw_email'); ?>" target="_blank">
                            <?php echo carbon_get_theme_option('helcraw_email'); ?>
                        </a>
                    </p>
                </div>
            </h5>

            <h5 class="row">
                <div class="col-2">
                    <i class="bi bi-geo-alt-fill me-2"></i>
                </div>
                <div class="col-10">
                    <p><?php echo carbon_get_theme_option('helcraw_address'); ?></p>
                </div>
            </h5>
        </div>


        <div class="col-12 col-md-8" data-aos="fade-up">
            <div class="google-maps">
                <?php echo carbon_get_theme_option('helcraw_google_map'); ?>
            </div>
        </div>

        <hr>
        <div class="col-12" data-aos="fade-up">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h2 class="display-4">WORKING HOURS</h2>
                </div>
                <div class="col-12 col-md-6">
                    <p>Monday - Friday <strong>(8AM - 5PM)</strong></p>
                    <p>Saturday & Sunday <strong>(Closed)</strong></p>
                </div>
            </div>
        </div>
        <hr>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.wpcf7 form');
        if (!form) return;

        const realSubmit = form.querySelector('.cf7-hidden-submit');
        const prettyButton = form.querySelector('.btn-enquiry');

        if (realSubmit && prettyButton) {
            // hide CF7's real submit from layout
            realSubmit.style.position = 'absolute';
            realSubmit.style.opacity = '0';
            realSubmit.style.pointerEvents = 'none';
            realSubmit.style.width = '0';
            realSubmit.style.height = '0';

            // click pretty button -> trigger CF7 submit
            prettyButton.addEventListener('click', function(e) {
                e.preventDefault();
                realSubmit.click();
            });
        }
    });
</script>


<?php get_footer(); ?>