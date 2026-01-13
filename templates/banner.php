<!-- <?php
        $post_id = get_the_ID();

        if (has_post_thumbnail()) {
            $banner_image_url = get_the_post_thumbnail_url($post_id, 'full');
        } else {
            $banner_image_url = get_template_directory_uri() . '/img/default/banner-image.webp';
        }
        ?>


<section class="container">
    <div class="banner" data-aos="fade-up" style="background-image: url('<?php echo esc_url($banner_image_url); ?>');">
        <div class="container">
        <div class="row">
            <div class="col-12">
                <h1><?php echo is_category() ? single_cat_title('', false) : get_the_title(); ?></h1>
            </div>
            
            <div class="col-12">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<p class="breadcrumbs" id="breadcrumbs">', '</p>');
                } else {
                    echo '<p class="breadcrumbs" id="breadcrumbs">';
                    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';

                    // Get the current post/page
                    global $post;

                    if (is_page()) {
                        // For pages with parents
                        if ($post->post_parent) {
                            $parent_id = $post->post_parent;
                            $breadcrumbs = array();

                            while ($parent_id) {
                                $page = get_page($parent_id);
                                $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a>';
                                $parent_id = $page->post_parent;
                            }

                            $breadcrumbs = array_reverse($breadcrumbs);
                            foreach ($breadcrumbs as $crumb) {
                                echo ' &gt; ' . $crumb;
                            }
                        }
                        echo ' &gt; ' . esc_html(get_the_title());
                    } elseif (is_single()) {
                        // For blog posts
                        $categories = get_the_category();
                        if ($categories) {
                            echo ' &gt; <a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                        }
                        echo ' &gt; ' . esc_html(get_the_title());
                    } elseif (is_category()) {
                        echo ' &gt; ' . single_cat_title('', false);
                    } elseif (is_archive()) {
                        echo ' &gt; ' . get_the_archive_title();
                    } elseif (is_search()) {
                        echo ' &gt; Search Results for "' . esc_html(get_search_query()) . '"';
                    } elseif (is_404()) {
                        echo ' &gt; 404 Not Found';
                    }

                    echo '</p>';
                }
                ?>
            </div>
        </div>
        </div>
        <div class="overlay" aria-hidden="true"></div>
    </div>    
</section> -->