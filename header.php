<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<!-- TODO: Add Google analytics -->
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Mehluli Hikwa" />
	<title><?php the_title() ?> | <?php bloginfo('name'); ?></title>
	<meta name="description" content="<?php bloginfo('description'); ?>" />
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.png" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<main>
		<section class="desktop-header">
			<div class="site-container">
				<div class="row align-items-center text-center text-md-start">

					<div class="col-4">
						<a href="<?php echo home_url(); ?>">
							<img class="img-fluid desktop-logo"
								src="<?php echo get_stylesheet_directory_uri(); ?>/img/helcraw-water-logo-color.svg" />
						</a>
					</div>

					<div class="col-5">
						<nav class="menu pt-3">
							<?php
							wp_nav_menu(array(
								'theme_location' => 'main_menu',
								'container' => false,
							));
							?>
						</nav>
					</div>

					<div class="col-3 text-end">
						<a href="<?php the_permalink(); ?>/contact-us" class="hc-btn-icon-primary">
							Contact Us
						</a>
					</div>

				</div>

			</div>
		</section>


		<section class="mobile-header">
			<header>
				<div class="site-container">
					<div class="row">
						<div class="col-8">
							<a href="<?php echo home_url(); ?>">
								<img class="mobile-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/img/helcraw-water-logo-color.svg" />
							</a>
						</div>
						<div class="col-4">
							<a class="mobile-nav-trigger" href="#mobile-nav">
								Mobile Trigger
								<span></span>
							</a>
						</div>
					</div>
				</div>
			</header>

			<nav class="mobile-nav">
				<?php
				$args = array(
					'theme_location' => "main_menu",
					'name' => "main_menu",
					'menu_class' => "mobile-nav",
					'menu_id' => "mobile-nav",
				);
				wp_nav_menu($args);
				?>
			</nav>
		</section>