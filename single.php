<?php get_header(); ?>


<!-- <?php get_template_part('templates/banner'); ?> -->


<section class="">
	<div class="row">
		<div class="col-12">
			<?php
			if (has_post_thumbnail()) {
				the_post_thumbnail('full', array('class' => 'img-fluid mt-2 mb-3'));
			} else {
				// leave empty
			}
			?>
			<?php the_content(); ?>
		</div>
	</div>
</section>


<?php get_footer(); ?>