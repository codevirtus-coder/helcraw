	<!-- <?php if (! is_front_page() && ! is_home()) : ?>
	
		<section class="container mt-5 contact-form">
			<div class="row">
				<div class="col-12 col-md-6">
					<h2>Do You Have Any Questions?</h2>
					<p>Let's work together. Explore our services or contact our team to discuss your project.</p>
				</div>

				<div class="col-12 col-md-6">
					<?php
					$contact_shortcode = carbon_get_theme_option('helcraw_contact-form');
					if (! empty($contact_shortcode)) {
						echo do_shortcode($contact_shortcode);
					} else {
					?>
						<h2>Contact Form Not Available.</h2>
					<?php
					}
					?>
				</div>
			</div>
			<div class="overlay" aria-hidden="true"></div>
		</section>
	<?php endif; ?> -->



	<footer>
		<section class="container mt-5">
			<section class="footer-section">
				<div class="container-fluid">

					<div class="row footer-row justify-content-between">


						<div class="col-12 col-md-3 mb-3 footer-col" data-aos="fade-up">
							<?php
							$args = array(
								'menu' => 'support_menu',
								'theme_location' => 'support_menu',
							);
							wp_nav_menu($args);
							?>
							<h1 class="footer-title">
								We provide expert
								prepaid water metering & modern water-network solutions
							</h1>
							<a href="#" class="btn btn-primary footer-cta">Get in touch</a>
						</div>

						<!-- COLUMN 2 -->
						<div class="col-12 col-md-3 mb-3 footer-col" data-aos="fade-up">
							<h5>Quick Links</h5>
							<?php
							$args = array(
								'menu' => 'quick_links_menu',
								'theme_location' => 'quick_links_menu',
							);
							wp_nav_menu($args);
							?>

							<div class="footer-bottom-note"></div>
						</div>


						<div class="col-12 col-md-3 mb-3 footer-col" data-aos="fade-up">
							<h5>Contact</h5>
							<span>
								<?php
								$address = carbon_get_theme_option('helcraw_address');
								$email = carbon_get_theme_option('helcraw_email');
								$phoneNumber = carbon_get_theme_option('helcraw_number');
								?>
								<?php if ($address) : ?>
									<p><?php echo $address; ?></p>
								<?php endif; ?>

								<?php if ($email) : ?>
									<p><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
								<?php endif; ?>

								<?php if ($phoneNumber) : ?>
									<p><?php echo $phoneNumber; ?></p>
								<?php endif; ?>
							</span>


							<div class="footer-bottom-note"></div>
						</div>

					</div>
				</div>
			</section>

		</section>

		<div class="col-12 col-md-4 offset-md-4 d-flex justify-content-center" data-aos="fade-up">
			<img class="mb-4 footer-image" src="<?php echo get_stylesheet_directory_uri(); ?>/img/helcraw-footer.png" />
		</div>

		<div class="" data-aos="fade-up">
			<span class="social-wrap">
				<?php
				$twitter = carbon_get_theme_option('helcraw_custom_tw');
				$facebook = carbon_get_theme_option('helcraw_custom_fb');
				$instagram = carbon_get_theme_option('helcraw_custom_in');
				$linkedin = carbon_get_theme_option('helcraw_custom_li');
				?>
				<?php if ($twitter) : ?>
					<a class="social" data-bs-toggle="tooltip" data-bs-placement="top"
						data-bs-title="Follow us on X" href="<?php echo $twitter; ?>" target="_blank">
						<i class="bi bi-twitter-x"></i>
					</a>
				<?php else : ?>
				<?php endif; ?>
				<?php if ($facebook) : ?>
					<a class="social" data-bs-toggle="tooltip" data-bs-placement="top"
						data-bs-title="Link us on Facebook" href="<?php echo $facebook; ?>" target="_blank">
						<i class="bi bi-facebook"></i>
					</a>
				<?php else : ?>
				<?php endif; ?>
				<?php if ($instagram) : ?>
					<a class="social" data-bs-toggle="tooltip" data-bs-placement="top"
						data-bs-title="Follow us on Instagram" href="<?php echo $instagram; ?>" target="_blank">
						<i class="bi bi-instagram"></i>
					</a>
				<?php else : ?>
				<?php endif; ?>
				<?php if ($linkedin) : ?>
					<a class="social" data-bs-toggle="tooltip" data-bs-placement="top"
						data-bs-title="Follow our Page" href="<?php echo $linkedin; ?>" target="_blank">
						<i class="bi bi-linkedin"></i>
					</a>
				<?php else : ?>
				<?php endif; ?>
			</span>
		</div>

		<hr>

	</footer>
	</main>


	<?php wp_footer(); ?>

	</body>

	</html>