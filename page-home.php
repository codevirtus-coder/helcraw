<?php get_header();
$slides = carbon_get_post_meta(get_option('page_on_front'), 'helcraw_slider');
?>

<section class="site-container mt-5 mb-5">
	<div id="home-hero">

		<div class="hero-inner">

			<div class="hero-left">
				<div>
					<div class="hero-badge">
						<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/tap-bg.png" alt="">
						Providing prepaid water metering
					</div>

					<h1 class="hero-title">
						smart water<br>
						<span class="hl">
							<img class="hl-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/pipe.png" alt="">
							reliable
						</span><br>
						supply
					</h1>

					<a href="#" class="hc-btn-icon ">Request A Meter</a>

				</div>

				<div class="hero-mini-image">
					<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/water.png'); ?>" alt="">
				</div>

			</div>


			<div class="hero-right">

				<?php
				if (! empty($slides)) :
					foreach ($slides as $slide) :
						$img = ! empty($slide['helcraw_slider_image']) ? $slide['helcraw_slider_image'] : '';
				?>
						<div class="hero-image-wrapper">


							<img class="hero-main-image" src="<?php echo esc_url($img); ?>" alt="">


							<div class="hero-overlay-card">
								<div class="stars">★★★★★</div>
								<p>
									Reliable prepaid water metering, pipe replacement,
									and modern water-network solutions.
								</p>
								<div class="avatars">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/avator-1.png" alt="">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/avator-2.png" alt="">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/avator-3.png" alt="">
								</div>
							</div>

						</div>
				<?php
					endforeach;
				endif;
				?>

			</div> <!-- /.hero-right -->

		</div> <!-- /.hero-inner -->

	</div>
</section>


<section class=" service-section mt-5 mb-5">
	<div class="site-container" id=" home-hero">

		<div class="hero-inner">

			<div class="hero-left">
				<div>
					<div class="hero-badge mb-5">
						<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/tap-bg.png" alt="">
						Our Core Services
					</div>

					<h1 class="main-heading">
						We provide expert
						prepaid water metering & modern water-network solutions
					</h1>

					<a href="#" class="hc-btn-icon">All Service</a>

				</div>


			</div> <!-- /.hero-left -->


			<div class="hero-right">

				<?php
				if (! empty($slides)) :
					foreach ($slides as $slide) :
						$img = ! empty($slide['helcraw_slider_image']) ? $slide['helcraw_slider_image'] : '';
				?>
						<div class="hero-image-wrapper-service">


							<div class="hero-overlay-card-service mb-3">

								<div class="">
									<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/gas.png" alt="">

									<h1 class="title-service">
										Prepaid Water Meter <br> Installation
									</h1>

									<p>
										Reliable prepaid water metering, pipe replacement,
										and modern water-network solutions.
									</p>
								</div>
								<img class="hero-main-image-service" src="<?php echo esc_url($img); ?>" alt="">

							</div>


							<div class="hero-overlay-card-service mb-3">

								<div class="">
									<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/gas.png" alt="">

									<h1 class="title-service ">
										Prepaid Water Meter <br> Installation
									</h1>

									<p>
										Reliable prepaid water metering, pipe replacement,
										and modern water-network solutions.
									</p>
								</div>
								<img class="hero-main-image-service" src="<?php echo esc_url($img); ?>" alt="">

							</div>
							<div class="hero-overlay-card-service mb-3">

								<div class="">
									<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/gas.png" alt="">

									<h1 class="title-service">
										Prepaid Water Meter <br> Installation
									</h1>

									<p>
										Reliable prepaid water metering, pipe replacement,
										and modern water-network solutions.
									</p>
								</div>
								<img class="hero-main-image-service" src="<?php echo esc_url($img); ?>" alt="">

							</div>
							<div class="hero-overlay-card-service mb-3">

								<div class="">
									<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/gas.png" alt="">

									<h1 class="title-service">
										Prepaid Water Meter <br> Installation
									</h1>

									<p>
										Reliable prepaid water metering, pipe replacement,
										and modern water-network solutions.
									</p>
								</div>
								<img class="hero-main-image-service" src="<?php echo esc_url($img); ?>" alt="">

							</div>


						</div>
				<?php
					endforeach;
				endif;
				?>

			</div>






		</div>

	</div>
</section>



<!-- <section class="site-container mt-5 mb-5 pt-5">
	<div class="row g-4">
		<div class="col-12" data-aos="fade-up">
			<h2 class="display-5 fw-bold mb-4">Our Services</h2>
		</div>

		<?php
		// Get the parent page ID
		$parent_page = get_page_by_path('services');
		$parent_page_id = $parent_page->ID;

		// Loop through child pages
		$args = [
			'post_type'      => 'page',
			'post_parent'    => $parent_page_id,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		];
		$child_pages = new WP_Query($args);
		?>

		<?php if ($child_pages->have_posts()) : ?>
			<?php while ($child_pages->have_posts()) : $child_pages->the_post(); ?>

				<div class="col-12 col-md-6 col-lg-3">
					<div class="card h-100">
						<?php
						if (has_post_thumbnail()) {
						?>
							<img class="card-img-top" alt="#" src="<?php echo get_the_post_thumbnail_url(); ?>">
						<?php
						} else {
						?>
							<img class="card-img-top" alt="#" src="<?php echo esc_url(get_stylesheet_directory_uri()) . '/img/default/slider.webp' ?>" />
						<?php
						}
						?>

						<div class="card-body">
							<a href="<?php the_permalink(); ?>">
								<h5 class="card-title"><?php the_title(); ?></h5>
							</a>

							<p class="card-text excerpt-lines"><?php echo get_the_excerpt(); ?></p>
							<a href="<?php the_permalink(); ?>" class="btn btn-primary">Read More</a>
						</div>
					</div>
				</div>

			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section> -->


<?php get_template_part('templates/news-loop'); ?>


<?php get_footer(); ?>