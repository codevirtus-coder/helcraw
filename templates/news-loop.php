<section class="site-container mt-5 mb-5 pt-5">
	<div class="hero-badge">
		<img class="badge-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/tap-bg.png" alt="">
		Latest Updates
	</div>
	<div class="row">
		<div class="col-12 col-md-8" data-aos="fade-up">
			<h2 class="main-heading mb-4">Water tips & insights</h2>
		</div>

	</div>

	<div class="row row-cols-1 row-cols-md-3 g-4">
		<?php
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 3,
			'orderby' => 'date',
			'order' => 'DESC',
		);
		$news_query = new WP_Query($args);
		?>
		<?php if ($news_query->have_posts()) : ?>
			<?php while ($news_query->have_posts()) : $news_query->the_post(); ?>

				<div class="col-12 col-sm-6 col-md-4" data-aos="fade-up">
					<div id="<?php the_ID(); ?>" class="card h-100">
						<?php
						if (has_post_thumbnail()) {
							the_post_thumbnail('full', array('class' => 'card-img-top'));
						} else {
							echo '<img class="card-img-top" alt="' . esc_attr(get_the_title()) . '" src="' . esc_url(get_stylesheet_directory_uri()) . '/img/default/default-image.jpg" />';
						}
						?>

						<div class="card-body">


							<a href="<?php the_permalink(); ?>">
								<h5 class="card-title"><?php the_title(); ?></h5>
							</a>

							<div class="d-flex gap-5">
								<span class="badge-date mb-3">
									<?php echo get_the_date('D j M Y'); ?>
								</span>

								<?php
								$cat = get_the_category();
								$cat_id = $cat[0]->term_id;
								$cat_link = get_category_link($cat_id);
								?>

								<a href="<?php echo esc_url($cat_link); ?>">
									<span class="badge-type mb-3">
										<?php echo esc_html(get_cat_name($cat_id)); ?>
									</span>
								</a>
							</div>

						</div>
					</div>


				</div>

			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<?php endif; ?>


		<div class="col-12 col-md-4 offset-md-4 d-flex justify-content-center" data-aos="fade-up">
			<a href="<?php echo home_url('updates') ?>" class="btn btn-primary">
				More News
			</a>
		</div>


	</div>
</section>