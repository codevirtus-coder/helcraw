<?php get_header(); ?>

<section class="banner">
	<div class="container-fluid">
    <h1>Search Results for: <?php echo get_search_query(); ?></h1>
	</div>
</section>

<section class="container-fluid">
        <?php if (have_posts()) : ?>

            <div id="<?php the_ID(); ?>" class="card">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card-body">
                            <a href="<?php the_permalink(); ?>"><h4 class="card-title"><?php the_title(); ?></h4></a>
                            <div class="card-text"><?php the_excerpt(); ?></div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-secondary">Visit Page</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else : ?>
            <p>No results found. Try another search.</p>
            <?php get_search_form(); ?>
        <?php endif; ?>

</section>

<?php get_footer(); ?>