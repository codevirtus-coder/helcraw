<?php

// // Check if we loading the right functions.php page
// die('This is the correct functions.php');

function helcraw_setup()
{
    // Navigation Menu/s
    add_theme_support('menus');
    register_nav_menus(array(
        'main_menu' => ('Main Menu'),
        'quick_links_menu' => ('Quick Links'),
        'support_menu' => ('Support Menu'),
    ));

    // Post Formats
    add_theme_support('post-formats', array('aside', 'image', 'video', 'gallery', 'quote', 'link'));

    // Switch default core markup to output valid HTML5.
    add_theme_support('html5', array(
        'search-form',
        // 'comment-form',
        // 'comment-list',
        'gallery',
        'caption',
    ));

    // Featured Image/s + Croping
    add_theme_support('post-thumbnails');
    add_image_size('slider-image', 1400, 510, true);

    // Custom Colors
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Yellow', 'helcrawYellow'),
            'slug'  => 'helcraw-yellow',
            'color' => '#C9A33E',
        ),
        array(
            'name'  => __('Red', 'helcrawBlack'),
            'slug'  => 'helcraw-black',
            'color' => '#0B1422',
        ),
    ));

    // uncomment code below if you need to Disable Custom Colors
    add_theme_support('disable-custom-colors');
}
add_action('after_setup_theme', 'helcraw_setup');






/************************************
	Enqueue scripts and styles
 ************************************/
function helcraw_scripts()
{
    wp_enqueue_style('theme-style', get_stylesheet_uri());
    wp_enqueue_script('bootstrap', get_theme_file_uri() . '/js/bootstrap.bundle.min.js', array(), '1.1.0', true);
    wp_enqueue_script('aos', get_theme_file_uri() . '/js/aos.js', array(), '1.1.0', true);
    wp_enqueue_script('swiper', get_theme_file_uri() . '/js/swiper-bundle.min.js', array(), '1.1.0', true);
    wp_enqueue_script('app', get_theme_file_uri() . '/js/app.js', array(), '1.1.0', true);
}
add_action('wp_enqueue_scripts', 'helcraw_scripts');





/************************************
    Completely Disable Comments
    https://www.wpbeginner.com/wp-tutorials/how-to-completely-disable-comments-in-wordpress
 ************************************/
add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});





/************************************
    Image Editor Library - upload error fix
    SOURCE: www.wpbeginner.com/wp-tutorials/how-to-fix-the-http-image-upload-error-in-wordpress
 *************************************/
function helcraw_image_editor($editors)
{
    $gd_editor = 'WP_Image_Editor_GD';
    $editors = array_diff($editors, array($gd_editor));
    array_unshift($editors, $gd_editor);
    return $editors;
}
add_filter('wp_image_editors', 'helcraw_image_editor');






/************************************
    User Last Login Time Stamp
    Author: thatAfro
    Author URI: https://thatafro.netlify.app/
    
    Features:
    - Tracks user login timestamps
    - Displays last login in admin users table
    - Provides shortcode for frontend display
    - Hidden display option for specific users
 ************************************/

/**
 * Configuration Settings
 * Set HIDE_ADMIN_LOGIN to false to show actual login times for specified users
 */
define('HIDE_ADMIN_LOGIN', true); // To toggle hiding on/off use true or false


/**
 * Users to hide login data from (while still recording it)
 * Add usernames and emails as needed
 */
function get_hidden_login_users()
{
    return [
        'usernames' => ['mehluli', 'admin', 'another_user'],
        'emails'    => ['mehlulihikwa@gmail.com', 'admin@site.com']
    ];
}

/**
 * Check if user should have hidden login display
 * 
 * @param int $user_id User ID to check
 * @return bool True if login should be hidden, false otherwise
 */
function should_hide_login_display($user_id)
{
    // If hiding is disabled, always show real data
    if (!HIDE_ADMIN_LOGIN) {
        return false;
    }

    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }

    $hidden_users = get_hidden_login_users();

    // Check if username or email matches hidden users list
    return in_array($user->user_login, $hidden_users['usernames']) ||
        in_array($user->user_email, $hidden_users['emails']);
}

/**
 * Capture and store user login timestamp
 * Triggered on every successful login
 * 
 * @param string $user_login Username of logged in user
 * @param WP_User $user User object
 */
function capture_user_login_timestamp($user_login, $user)
{
    // Always save the actual login time (even for hidden users)
    update_user_meta($user->ID, 'last_login', time());
}
add_action('wp_login', 'capture_user_login_timestamp', 10, 2);

/**
 * Add Last Login column to admin users table
 * 
 * @param array $columns Existing columns array
 * @return array Modified columns array with Last Login added
 */
function add_last_login_column($columns)
{
    $columns['last_login'] = 'Last Login';
    return $columns;
}
add_filter('manage_users_columns', 'add_last_login_column');

/**
 * Display last login data in admin users table column
 * Shows "No Record" for specified users when hiding is enabled
 * 
 * @param string $output Column output
 * @param string $column_id Column identifier
 * @param int $user_id User ID
 * @return string Formatted last login display
 */
function display_last_login_column($output, $column_id, $user_id)
{
    if ($column_id !== 'last_login') {
        return $output;
    }

    // Check if this user's login should be hidden
    if (should_hide_login_display($user_id)) {
        return 'No Record';
    }

    // Get and format actual login time
    $last_login = get_user_meta($user_id, 'last_login', true);

    if (!$last_login) {
        return 'No Record';
    }

    // Format dates for display and hover tooltip
    $hover_format = 'F j, Y, g:i a';  // Full date with time
    $hover_text = date($hover_format, $last_login);
    $human_diff = human_time_diff($last_login);

    return sprintf(
        '<div title="Last login: %s">%s ago</div>',
        esc_attr($hover_text),
        esc_html($human_diff)
    );
}
add_filter('manage_users_custom_column', 'display_last_login_column', 10, 3);

/**
 * Get formatted last login time for current user
 * Used by shortcode and can be called directly
 * 
 * @param int|null $user_id Optional user ID, defaults to current author
 * @return string Human readable time difference or "No Record"
 */
function get_user_last_login_display($user_id = null)
{
    // Use current author if no user ID provided
    if ($user_id === null) {
        $user_id = get_the_author_meta('ID');
    }

    // Check if login should be hidden for this user
    if (should_hide_login_display($user_id)) {
        return 'No Record';
    }

    // Get last login timestamp
    $last_login = get_user_meta($user_id, 'last_login', true);

    if (!$last_login) {
        return 'No Record';
    }

    return human_time_diff($last_login) . ' ago';
}

/**
 * Shortcode handler for displaying last login
 * Usage: [last_login] or [last_login user_id="123"]
 * 
 * @param array $atts Shortcode attributes
 * @return string Formatted last login display
 */
function last_login_shortcode($atts)
{
    $atts = shortcode_atts([
        'user_id' => null
    ], $atts, 'last_login');

    return get_user_last_login_display($atts['user_id']);
}
add_shortcode('last_login', 'last_login_shortcode');

// Backward compatibility - keep old shortcode name
add_shortcode('that_afro_themelastlogin', 'last_login_shortcode');

// Shortcode usage:
// [last_login]                    // Current author's login
// [last_login user_id="123"]      // Specific user's login
// [that_afro_themelastlogin]      // Backward compatibility



/************************************
	Naviagtion Menu CSS Clearing
 ************************************/

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function helcraw_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}
// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function helcraw_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

add_filter('wp_nav_menu_args', 'helcraw_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'helcraw_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes
add_filter('nav_menu_item_id', 'helcraw_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID
add_filter('page_css_class', 'helcraw_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's






/************************************
	Pagination - News and etc... IF added
 ************************************/
function pagination($pages = '', $range = 4)
{

    $showitems = ($range * 2) + 1;

    global $paged;
    if (empty($paged)) $paged = 1;

    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;

        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {
        echo "<div class=\"pagination\"><span>Page " . $paged . " of " . $pages . "</span>";
        if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) echo "<a href='" . get_pagenum_link(1) . "'>&laquo; First</a>";
        if ($paged > 1 && $showitems < $pages) echo "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo; Previous</a>";

        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                echo ($paged == $i) ? "<span class=\"current\">" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class=\"inactive\">" . $i . "</a>";
            }
        }

        if ($paged < $pages && $showitems < $pages) echo "<a href=\"" . get_pagenum_link($paged + 1) . "\">Next &rsaquo;</a>";
        if ($paged < $pages - 1 &&  $paged + $range - 1 < $pages && $showitems < $pages) echo "<a href='" . get_pagenum_link($pages) . "'>Last &raquo;</a>";
        echo "</div>\n";
    }
}




/************************************

	Carbon Fields
    
 ************************************/

use Carbon_Fields\Container;
use Carbon_Fields\Block;
use Carbon_Fields\Field;


// Register custom block category
add_filter('block_categories_all', function ($categories, $post) {
    return array_merge($categories, array(
        array(
            'slug'  => 'helcraw-blocks',
            'title' => __('helcraw Blocks'),
            'icon'  => 'groups',
        )
    ));
}, 10, 2);


add_action('carbon_fields_register_fields', 'helcraw_custom_theme_options');
function helcraw_custom_theme_options()
{
    // 
    // Theme Options Page
    // 
    Container::make('theme_options', __('Theme Settings'))
        // Default is gear icon
        ->set_icon('dashicons-buddicons-topics')

        ->set_page_menu_title('Theme Settings')

        // Position guide - https://developer.wordpress.org/reference/functions/add_menu_page/
        ->set_page_menu_position(75)

        ->add_tab(__('Contact Details'), array(
            Field::make('text', 'helcraw_address', __('Address'))
                ->set_width(100)
                ->set_attribute('placeholder', '1 Swiss Way, Southerton, Harare, Zimbabwe'),

            Field::make('text', 'helcraw_email', __('Email'))
                ->set_width(30)
                ->set_attribute('placeholder', 'info@helcrawwater.com'),

            Field::make('text', 'helcraw_number', __('Mobile/Phone Number'))
                ->set_width(30)
                ->set_attribute('placeholder', '+263 1 544 3456'),

            Field::make('text', 'helcraw_contact-form', __('Contact Form'))
                ->set_width(50)
                ->set_attribute('placeholder', 'paste short code...'),

            Field::make('textarea', 'helcraw_google_map', __('Google Map'))
                ->set_width(50)
                ->set_rows(7)
                ->set_attribute('placeholder', 'Paste Google map <iframe> here...')
        ))

        ->add_tab(__('Footer Text & Social Media'), array(
            // x f I Y
            Field::make('text', 'helcraw_custom_tw', __('Twitter/X Link'))
                ->set_width(50),
            Field::make('text', 'helcraw_custom_fb', __('Facebook Link'))
                ->set_width(50),
            Field::make('text', 'helcraw_custom_in', __('Instagram Link'))
                ->set_width(50),
            Field::make('text', 'helcraw_custom_li', __('LinkedIn Link'))
                ->set_width(50),
        ))

        ->add_tab(__('Header & Footer Scripts'), array(
            Field::make('header_scripts', 'helcraw_header_script', __('Header Scripts'))
                ->set_attribute('placeholder', 'Insert scripts here e.g Google Analytics Code...'),
        ));



    // Card Block
    // 
    Block::make(__('Card Block'))
        ->add_fields(array(
            Field::make('text', 'title_name', __('Page Title')),
            Field::make('image', 'card_image', __('Page Image')),
            Field::make('text', 'page_link', __('Page Link')),
        ))

        ->set_description(__('Display teams, staff members, etc as cards with image and link'))

        ->set_icon('list-view') // layout

        ->set_category('helcraw-blocks')

        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {

            // Title
            $title = $fields['title_name'];

            // Handle image
            $image_url = !empty($fields['card_image']) ? wp_get_attachment_image_url($fields['card_image'], 'full') : '';
            $alt = !empty($fields['member_name']) ? $fields['member_name'] : 'Team Member';

            $btn_link = $fields['page_link'] ?? '';
            $href = $btn_link;
            if ($btn_link && ! preg_match('/^(https?:\/\/|\/\/|#)/', $btn_link)) {
                $href = home_url('/') . ltrim($btn_link, '/');
            }
?>

        <div class="card content mx-4">
            <?php if ($image_url): ?>
                <img class="card-img" alt="<?= esc_attr($alt) ?>" src="<?= esc_url($image_url) ?>">
            <?php else: ?>
                <img class="card-img" alt="<?= esc_attr($alt) ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/img/default/slider.webp" />
            <?php endif; ?>

            <div class="card-img-overlay  d-flex flex-column justify-content-center align-items-center text-center">
                <?php if ($title): ?>
                    <h5 class="card-title text-white"><?= esc_attr($title) ?></h5>
                <?php else: ?>
                    <!-- No Title - leave blank -->
                <?php endif; ?>

                <?php if (! empty($href)) : ?>
                    <a class="btn btn-light mt-2" href="<?= esc_url($href) ?>">View</a>
                <?php endif; ?>
            </div>
        </div>

    <?php
        });


    // 
    // Card Content
    // 
    Block::make(__('Card Content'))
        ->add_fields(array(
            Field::make('image', 'card_content_image', __('Card Content Image')),
            Field::make('text', 'card_content_name', __('Card Content Title')),
            Field::make('textarea', 'card_content_description', __('Card Content Description'))
                ->set_rows(3),
            Field::make('text', 'card_content_link', __('Card Content Link')),
        ))

        ->set_description(__('Display content card with image, text and link'))

        ->set_icon('list-view') // layout

        ->set_category('helcraw-blocks')

        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {

            // Handle image
            $image_url = !empty($fields['card_content_image']) ? wp_get_attachment_image_url($fields['card_content_image'], 'full') : '';
            $alt = !empty($fields['member_name']) ? $fields['member_name'] : 'Team Member';

            $btn_link = $fields['card_page_link'] ?? '';
            $href = $btn_link;
            if ($btn_link && ! preg_match('/^(https?:\/\/|\/\/|#)/', $btn_link)) {
                $href = home_url('/') . ltrim($btn_link, '/');
            }
    ?>

        <div class="card">
            <?php if ($image_url): ?>
                <img class="card-img-top" alt="<?= esc_attr($alt) ?>" src="<?= esc_url($image_url) ?>">
            <?php else: ?>
                <img class="card-img-top" alt="<?= esc_attr($alt) ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/img/default/slider.webp" />
            <?php endif; ?>

            <div class="card-body">

                <p class="card-text"><?php echo esc_html($fields['card_content_description']); ?></p>
                <h5 class="card-title"><?php echo esc_html($fields['card_content_name']); ?></h5>


                <?php if (! empty($href)) : ?>
                    <a href="<?= esc_url($href) ?>" class="btn btn-primary">Read More</a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        });


    // Left to Right Block
    // 

    Block::make(__('Left to Right'))
        ->add_fields(array(
            Field::make('text', 'rl_heading', 'Heading')
                ->set_attribute('placeholder', 'Enter Heading if Needed')
                ->set_width(100),

            Field::make('textarea', 'rl_text', 'Body Text')
                ->set_width(100)
                ->set_rows(4),

            Field::make('text', 'rl_button_text', 'Button Text')
                ->set_attribute('placeholder', 'Enter Button Text if Needed')
                ->set_width(30),

            Field::make('text', 'rl_button_url', 'Button URL')
                ->set_attribute('placeholder', 'Enter Button URL if Needed')
                ->set_width(30),

            Field::make('checkbox', 'rl_button_external', 'Create external link')
                ->set_width(40)
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_help_text('Check this box to use an external URL (opens in new tab).'),

            // Primary image
            Field::make('image', 'rl_image', 'Image')
                ->set_width(100)
                ->set_value_type('url'),

            // New: second image (to place side-by-side)
            Field::make('image', 'rl_second_image', 'Second Image (optional)')
                ->set_width(100)
                ->set_value_type('url'),

            // New: checkbox to replace the text side with the second image
            Field::make('checkbox', 'rl_replace_text_with_image', __('Replace text side with second image'))
                ->set_width(100)
                ->set_option_value('yes'),

            Field::make('checkbox', 'rl_image_right', __('Check to display image on the right side'))
                ->set_width(100)
                ->set_option_value('yes'),
        ))

        ->set_description(__('Display content with image and text (optionally two images side-by-side)'))
        ->set_icon('controls-skipforward')
        ->set_category('helcraw-blocks')

        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {

            $btn_text    = isset($fields['rl_button_text']) ? $fields['rl_button_text'] : '';
            $btn_url_raw = isset($fields['rl_button_url']) ? trim($fields['rl_button_url']) : '';
            $is_external = ! empty($fields['rl_button_external']);
            $href        = '';

            if ($btn_url_raw) {
                if ($is_external) {
                    $href = esc_url($btn_url_raw);
                } else {
                    if (preg_match('#^(https?:)?//#', $btn_url_raw)) {
                        $href = esc_url($btn_url_raw);
                    } else {

                        $href = esc_url(home_url('/' . ltrim($btn_url_raw, '/')));
                    }
                }
            }

            $button_html = '';
            if ($btn_text && $href) {
                ob_start();
        ?>
            <a class="btn btn-light"
                href="<?php echo $href; ?>"
                <?php if ($is_external) : ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>>
                <?php echo esc_html($btn_text); ?>
            </a>
        <?php
                $button_html = ob_get_clean();
            }


            $image_url          = ! empty($fields['rl_image']) ? esc_url($fields['rl_image']) : '';
            $second_image       = ! empty($fields['rl_second_image']) ? esc_url($fields['rl_second_image']) : '';
            $replace_with_image = ! empty($fields['rl_replace_text_with_image']);
            $image_on_right     = ! empty($fields['rl_image_right']);

            if ($replace_with_image && $second_image) :

                if ($image_on_right) {

                    $left_col_order  = 'order-2 order-md-1';
                    $right_col_order = 'order-1 order-md-2';
                } else {
                    $left_col_order  = '';
                    $right_col_order = '';
                }
        ?>
            <div class="row my-5  site-container left-right images-side-by-side">
                <div class="col-12 col-md-6 <?php echo $left_col_order; ?>">
                    <div class="lr-image-box">
                        <img src="<?php echo $image_url; ?>" alt="Image 1" class="img-fluid" />
                    </div>
                </div>
                <div class="col-12 col-md-6 <?php echo $right_col_order; ?>">
                    <div class="lr-image-box">
                        <img src="<?php echo $second_image; ?>" alt="Image 2" class="img-fluid" />
                    </div>
                </div>
            </div>
            <?php
            else :

                if ($image_on_right) : ?>
                <div class="row my-5 left-right images-side-by-side">
                    <div class="col-12 col-md-5 mt-3 mt-md-0 order-2 order-md-1">
                        <div class="background">
                            <?php if (! empty($fields['rl_heading'])) : ?>
                                <h2 class="mb-2 mb-md-4"><?php echo esc_html($fields['rl_heading']); ?></h2>
                            <?php endif; ?>
                            <p><?php echo esc_html($fields['rl_text']); ?></p>
                            <?php echo $button_html; ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-7 order-1 order-md-2">
                        <div class="lr-image-box">
                            <img src="<?php echo $image_url; ?>" alt="Left to Right Image" class="img-fluid" />
                        </div>
                    </div>

                <?php else : ?>

                    <div class="row my-5 left-right">
                        <div class="col-12 col-md-7">
                            <div class="lr-image-box">
                                <img src="<?php echo $image_url; ?>" alt="Left to Right Image" class="img-fluid" />
                            </div>
                            <div class="col-12 col-md-5 mt-3 mt-md-0">
                                <div class="background">
                                    <?php if (! empty($fields['rl_heading'])) : ?>
                                        <h2 class="mb-2 mb-md-4"><?php echo esc_html($fields['rl_heading']); ?></h2>
                                    <?php endif; ?>
                                    <p><?php echo esc_html($fields['rl_text']); ?></p>
                                    <?php echo $button_html; ?>
                                </div>
                            </div>
                        </div>

                <?php endif;
            endif;
        });



    Block::make(__('Stats with Hero Image'))
        ->add_fields(array(

            Field::make('complex', 'stats', 'Stats (Number & Label)')
                ->set_layout('tabbed-vertical')
                ->set_min(1)
                ->set_max(8)
                ->set_header_template('{{number}}')
                ->add_fields(array(
                    Field::make('text', 'number', 'Number')
                        ->set_attribute('placeholder', 'e.g. 15+')
                        ->set_width(35),
                    Field::make('text', 'label', 'Label')
                        ->set_attribute('placeholder', 'e.g. Years of experience')
                        ->set_width(65),
                ))
                // DEFAULT: show one stat item by default
                ->set_default_value(array(
                    array('number' => '15+', 'label' => 'Years of experience'),
                )),

            // Hero image
            Field::make('image', 'hero_image', 'Hero Image')
                ->set_value_type('url')
                ->set_width(100),
        ))
        ->set_description(__('Repeatable stats (number + label) plus hero image'))
        ->set_icon('chart-bar')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
            $stats = ! empty($fields['stats']) && is_array($fields['stats']) ? $fields['stats'] : array();
            $hero_image = ! empty($fields['hero_image']) ? esc_url($fields['hero_image']) : '';

            // Helper to safely render number with accent span for trailing + or %
            $render_number = function ($num) {
                $num = (string) $num;
                if ($num === '') {
                    return '';
                }
                $last = mb_substr($num, -1);
                if ($last === '+' || $last === '%') {
                    $main = mb_substr($num, 0, mb_strlen($num) - 1);
                    return '<span class="helcraw-number-main">' . esc_html($main) . '</span>'
                        . '<span class="helcraw-number-accent">' . esc_html($last) . '</span>';
                }
                return '<span class="helcraw-number-main">' . esc_html($num) . '</span>';
            };
                ?>

                <div class="helcraw-stats-hero site-container">
                    <div class="helcraw-stats" role="list">
                        <?php
                        if (! empty($stats)) :
                            foreach ($stats as $stat) :
                                $num = isset($stat['number']) ? $stat['number'] : '';
                                $lbl = isset($stat['label'])  ? esc_html($stat['label'])  : '';
                        ?>
                                <div class="helcraw-stat" role="listitem">
                                    <div class="helcraw-number" aria-hidden="true">
                                        <?php echo $render_number($num);
                                        ?>
                                    </div>
                                    <div class="helcraw-label"><?php echo $lbl; ?></div>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <div class="helcraw-pointer-wrap" aria-hidden="true">
                        <div class="helcraw-pointer"></div>
                    </div>

                    <?php if ($hero_image) : ?>
                        <div class="helcraw-hero-card" role="img" aria-label="Hero image">
                            <img src="<?php echo $hero_image; ?>" alt="<?php echo esc_attr('Hero image'); ?>" />
                        </div>
                    <?php else : ?>
                        <div class="helcraw-hero-card helcraw-hero-empty">
                            <div class="helcraw-hero-empty-inner">No hero image set</div>
                        </div>
                    <?php endif; ?>
                </div>

            <?php
        });



    Block::make(__('Mission Block'))
        ->add_fields(array(
            // Badge text (small tag)
            Field::make('text', 'mission_badge', 'Badge text (small tag)')
                ->set_attribute('placeholder', 'Mission')
                ->set_width(30),

            // Badge icon upload (new)
            Field::make('image', 'mission_badge_icon', 'Badge Icon (optional)')
                ->set_value_type('url')
                ->set_help_text('Upload a small badge icon (e.g. tap-bg.png). Falls back to theme /img/tap-bg.png if empty.')
                ->set_width(30),

            Field::make('text', 'mission_heading', 'Heading')
                ->set_attribute('placeholder', 'Our Mission as Helcraw Water.')
                ->set_width(100),

            Field::make('textarea', 'mission_text', 'Body text (left column)')
                ->set_attribute('placeholder', 'Short paragraph')
                ->set_rows(4)
                ->set_width(100),

            Field::make('textarea', 'mission_text_secondary', 'Secondary body text (optional)')
                ->set_attribute('placeholder', 'Optional second paragraph')
                ->set_rows(4)
                ->set_width(100),

            // NEW: repeatable features list (optional)
            Field::make('complex', 'mission_features', 'Features list')
                ->set_layout('tabbed-vertical')
                ->set_min(0)
                ->set_max(12)
                ->set_header_template('{{feature}}')
                ->add_fields(array(
                    Field::make('text', 'feature', 'Feature')
                        ->set_attribute('placeholder', 'Airtime-style top-ups')
                        ->set_width(100),
                )),

            Field::make('text', 'mission_button_text', 'Button text')
                ->set_attribute('placeholder', 'Get In Touch')
                ->set_width(40),

            Field::make('text', 'mission_button_url', 'Button URL')
                ->set_attribute('placeholder', '/contact')
                ->set_width(60),

            Field::make('checkbox', 'mission_button_external', 'Open button in new tab')
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_width(40),

            Field::make('image', 'mission_image', 'Right column image')
                ->set_value_type('url')
                ->set_width(100),
        ))
        ->set_description(__('Mission section: badge, heading, paragraphs, features list and image card'))
        ->set_icon('welcome-learn-more')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
            // Gather and sanitize
            $badge = ! empty($fields['mission_badge']) ? esc_html($fields['mission_badge']) : '';
            $badge_icon_field = ! empty($fields['mission_badge_icon']) ? $fields['mission_badge_icon'] : '';
            // fallback to theme image if user didn't upload an icon
            $default_icon = get_stylesheet_directory_uri() . '/img/tap-bg.png';
            $badge_icon = $badge_icon_field ? esc_url($badge_icon_field) : esc_url($default_icon);

            $heading = ! empty($fields['mission_heading']) ? esc_html($fields['mission_heading']) : '';
            $text = ! empty($fields['mission_text']) ? wp_kses_post(nl2br($fields['mission_text'])) : '';
            $text2 = ! empty($fields['mission_text_secondary']) ? wp_kses_post(nl2br($fields['mission_text_secondary'])) : '';
            $btn_text_raw = ! empty($fields['mission_button_text']) ? $fields['mission_button_text'] : '';
            $btn_url_raw = ! empty($fields['mission_button_url']) ? trim($fields['mission_button_url']) : '';
            $btn_external = ! empty($fields['mission_button_external']);

            $image = ! empty($fields['mission_image']) ? esc_url($fields['mission_image']) : '';

            // Features (complex)
            $features = ! empty($fields['mission_features']) && is_array($fields['mission_features']) ? $fields['mission_features'] : array();

            // Build safe URL for button
            $btn_href = '';
            if ($btn_url_raw) {
                if (preg_match('#^(https?:)?//#', $btn_url_raw)) {
                    $btn_href = esc_url($btn_url_raw);
                } else {
                    $btn_href = esc_url(home_url('/' . ltrim($btn_url_raw, '/')));
                }
            }

            // Button html — keep both theme btn classes and helcraw class
            $button_html = '';
            if ($btn_text_raw && $btn_href) {
                $btn_text = esc_html($btn_text_raw);
                $target = $btn_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                $button_html = sprintf(
                    '<a class="btn btn-primary helcraw-mission-btn" href="%s"%s>%s</a>',
                    esc_url($btn_href),
                    $target,
                    $btn_text
                );
            }
            ?>
                <div class="helcraw-mission-block">
                    <div class="helcraw-mission-inner container">
                        <div class="helcraw-mission-row">
                            <div class="helcraw-mission-left">
                                <?php if ($badge) : ?>
                                    <div class="hero-badge mb-5">
                                        <img
                                            class="badge-icon"
                                            src="<?php echo esc_url($badge_icon); ?>"
                                            alt="<?php echo esc_attr($badge); ?>" />
                                        <?php echo $badge; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($heading) : ?>
                                    <h2 class="main-headings"><?php echo $heading; ?></h2>
                                <?php endif; ?>

                                <?php if ($text) : ?>
                                    <div class="helcraw-mission-text"><?php echo $text; ?></div>
                                <?php endif; ?>

                                <?php if ($text2) : ?>
                                    <div class="helcraw-mission-text-secondary"><?php echo $text2; ?></div>
                                <?php endif; ?>

                                <?php
                                // Render features list if any features were added
                                if (! empty($features)) :
                                    // sanitize each feature
                                ?>
                                    <ul class="helcraw-features">
                                        <?php foreach ($features as $item) :
                                            $feat = isset($item['feature']) ? trim($item['feature']) : '';
                                            if ($feat === '') continue;
                                        ?>
                                            <li><?php echo esc_html($feat); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if ($button_html) : ?>
                                    <div class="helcraw-mission-cta"><?php echo $button_html; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="helcraw-mission-right">
                                <?php if ($image) : ?>
                                    <div class="helcraw-mission-image-card">
                                        <img src="<?php echo $image; ?>" alt="<?php echo esc_attr($heading ?: 'Mission image'); ?>" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        });



    Block::make(__('feature-row-flexible'))
        ->add_fields(array(
            Field::make('select', 'fr_layout', 'Layout')
                ->set_options(array(
                    'a' => 'Variant A — side-card left, text middle, big image right',
                    'b' => 'Variant B — text left, big image center, side-card right',
                ))
                ->set_default_value('a')
                ->set_width(100),

            // Side card (small)
            Field::make('text', 'fr_side_title', 'Side card title')
                ->set_attribute('placeholder', 'Bulk Meters')
                ->set_width(100),

            Field::make('textarea', 'fr_side_text', 'Side card text (optional)')
                ->set_rows(3)
                ->set_width(100),

            Field::make('image', 'fr_side_image', 'Side card image (small)')
                ->set_value_type('url')
                ->set_width(100),

            // Optional side-card CTA (for overlay on small image, typically used in Variant B)
            Field::make('text', 'fr_side_cta_text', 'Side card CTA text (optional)')
                ->set_attribute('placeholder', 'Download Guides')
                ->set_width(50),
            Field::make('text', 'fr_side_cta_url', 'Side card CTA URL (optional)')
                ->set_attribute('placeholder', '/guides')
                ->set_width(50),
            Field::make('checkbox', 'fr_side_cta_external', 'Open side CTA in new tab')
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_width(100),

            // Main content
            Field::make('text', 'fr_heading', 'Heading')
                ->set_attribute('placeholder', 'Meter Specifications')
                ->set_width(100),

            Field::make('textarea', 'fr_text', 'Body / description')
                ->set_rows(6)
                ->set_width(100),

            // Optional features list (repeatable) — shows as bullets in A, numbers in B
            Field::make('complex', 'fr_features', 'Features (optional)')
                ->set_layout('tabbed-horizontal')
                ->set_min(0)
                ->set_max(12)
                ->set_header_template('{{feature}}')
                ->add_fields(array(
                    Field::make('text', 'feature', 'Feature')
                        ->set_attribute('placeholder', 'Accurate consumption tracking')
                        ->set_width(100),
                )),

            Field::make('text', 'fr_cta_text', 'CTA text')
                ->set_attribute('placeholder', 'Learn More')
                ->set_width(40),

            Field::make('text', 'fr_cta_url', 'CTA URL')
                ->set_attribute('placeholder', '/learn-more')
                ->set_width(60),

            Field::make('checkbox', 'fr_cta_external', 'Open CTA in new tab')
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_width(20),

            // Main big image
            Field::make('image', 'fr_main_image', 'Main (big) image')
                ->set_value_type('url')
                ->set_width(100),
        ))
        ->set_description(__('Flexible row that matches Variant A & B (exact clone)'))
        ->set_icon('columns')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields) {

            // collect fields
            $layout = ! empty($fields['fr_layout']) ? $fields['fr_layout'] : 'a';

            $side_title = ! empty($fields['fr_side_title']) ? esc_html($fields['fr_side_title']) : '';
            $side_text = ! empty($fields['fr_side_text']) ? wp_kses_post(nl2br($fields['fr_side_text'])) : '';
            $side_img = ! empty($fields['fr_side_image']) ? esc_url($fields['fr_side_image']) : '';

            $side_cta_text = ! empty($fields['fr_side_cta_text']) ? $fields['fr_side_cta_text'] : '';
            $side_cta_url = ! empty($fields['fr_side_cta_url']) ? trim($fields['fr_side_cta_url']) : '';
            $side_cta_external = ! empty($fields['fr_side_cta_external']);

            $heading = ! empty($fields['fr_heading']) ? esc_html($fields['fr_heading']) : '';
            $text = ! empty($fields['fr_text']) ? wp_kses_post(nl2br($fields['fr_text'])) : '';

            $features = ! empty($fields['fr_features']) && is_array($fields['fr_features']) ? $fields['fr_features'] : array();

            $cta_text_raw = ! empty($fields['fr_cta_text']) ? $fields['fr_cta_text'] : '';
            $cta_url_raw = ! empty($fields['fr_cta_url']) ? trim($fields['fr_cta_url']) : '';
            $cta_external = ! empty($fields['fr_cta_external']);

            $main_img = ! empty($fields['fr_main_image']) ? esc_url($fields['fr_main_image']) : '';

            // Build CTA (center/main)
            $cta_html = '';
            if ($cta_text_raw && $cta_url_raw) {
                if (preg_match('#^(https?:)?//#', $cta_url_raw)) {
                    $cta_href = esc_url($cta_url_raw);
                } else {
                    $cta_href = esc_url(home_url('/' . ltrim($cta_url_raw, '/')));
                }
                $target = $cta_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                $cta_html = sprintf('<a class="btn btn-outline-primary helcraw-fr-cta-btn" href="%s"%s>%s</a>', $cta_href, $target, esc_html($cta_text_raw));
            }

            // Build side CTA (overlay on small image)
            $side_cta_html = '';
            if ($side_cta_text && $side_cta_url) {
                if (preg_match('#^(https?:)?//#', $side_cta_url)) {
                    $side_href = esc_url($side_cta_url);
                } else {
                    $side_href = esc_url(home_url('/' . ltrim($side_cta_url, '/')));
                }
                $target = $side_cta_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                $side_cta_html = sprintf('<a class="helcraw-fr-side-cta" href="%s"%s>%s</a>', $side_href, $target, esc_html($side_cta_text));
            }

            // render features list — bullets for A, numbers for B
            $render_features = function ($features_arr, $layout_type) {
                if (empty($features_arr)) return '';
                $tag_open = ($layout_type === 'b') ? '<ol class="helcraw-fr-features ol">' : '<ul class="helcraw-fr-features">';
                $tag_close = ($layout_type === 'b') ? '</ol>' : '</ul>';
                $out = $tag_open;
                foreach ($features_arr as $f) {
                    $feat = isset($f['feature']) ? trim($f['feature']) : '';
                    if ($feat === '') continue;
                    $out .= '<li>' . esc_html($feat) . '</li>';
                }
                $out .= $tag_close;
                return $out;
            };
            ?>
                <div class="helcraw-fr-block">
                    <!-- pale panel that surrounds the whole row (matches screenshot) -->
                    <div class="helcraw-fr-panel">
                        <div class="helcraw-fr-inner container layout-<?php echo esc_attr($layout); ?>">

                            <?php if ($layout === 'a') : // Variant A: side-card left, text middle, big image right 
                            ?>
                                <div class="helcraw-fr-col col-side-left">
                                    <?php if ($side_title || $side_img || $side_text) : ?>
                                        <div class="helcraw-fr-side-card">
                                            <?php if ($side_img) : // image at top for Variant A 
                                            ?>
                                                <div class="helcraw-fr-side-image-box">
                                                    <img src="<?php echo $side_img; ?>" alt="<?php echo esc_attr($side_title ?: 'Side image'); ?>" />
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($side_title) : ?>
                                                <h3 class="helcraw-fr-side-title"><?php echo $side_title; ?></h3>
                                            <?php endif; ?>

                                            <?php if ($side_text) : ?>
                                                <div class="helcraw-fr-side-text"><?php echo $side_text; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="helcraw-fr-col col-text-center">
                                    <div class="helcraw-fr-text-wrap">
                                        <?php if ($heading) : ?>
                                            <h2 class="helcraw-fr-heading"><?php echo $heading; ?></h2>
                                        <?php endif; ?>

                                        <?php if ($text) : ?>
                                            <div class="helcraw-fr-text"><?php echo $text; ?></div>
                                        <?php endif; ?>

                                        <?php echo $render_features($features, $layout); ?>
                                    </div>

                                    <?php if ($cta_html) : // CTA forced to bottom in variant A 
                                    ?>
                                        <div class="helcraw-fr-cta"><?php echo $cta_html; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="helcraw-fr-col col-image-right">
                                    <?php if ($main_img) : ?>
                                        <div class="helcraw-fr-main-image">
                                            <img src="<?php echo $main_img; ?>" alt="<?php echo esc_attr($heading ?: 'Main image'); ?>" />
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php else : // Variant B: text left, main image center, side card right 
                            ?>
                                <div class="helcraw-fr-col col-text-left">
                                    <?php if ($heading) : ?>
                                        <h2 class="helcraw-fr-heading"><?php echo $heading; ?></h2>
                                    <?php endif; ?>

                                    <?php if ($text) : ?>
                                        <div class="helcraw-fr-text"><?php echo $text; ?></div>
                                    <?php endif; ?>

                                    <?php echo $render_features($features, $layout); ?>

                                    <?php if ($cta_html) : ?>
                                        <div class="helcraw-fr-cta"><?php echo $cta_html; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="helcraw-fr-col col-image-center">
                                    <?php if ($main_img) : ?>
                                        <div class="helcraw-fr-main-image">
                                            <img src="<?php echo $main_img; ?>" alt="<?php echo esc_attr($heading ?: 'Main image'); ?>" />
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="helcraw-fr-col col-side-right">
                                    <?php if ($side_title || $side_img || $side_text) : ?>
                                        <div class="helcraw-fr-side-card">
                                            <?php if ($side_title) : ?>
                                                <h3 class="helcraw-fr-side-title"><?php echo $side_title; ?></h3>
                                            <?php endif; ?>

                                            <?php if ($side_text) : ?>
                                                <div class="helcraw-fr-side-text"><?php echo $side_text; ?></div>
                                            <?php endif; ?>

                                            <?php if ($side_img) : // image comes AFTER text in Variant B 
                                            ?>
                                                <div class="helcraw-fr-side-image-box helcraw-fr-side-image-with-cta">
                                                    <img src="<?php echo $side_img; ?>" alt="<?php echo esc_attr($side_title ?: 'Side image'); ?>" />
                                                    <?php if ($side_cta_html) : // overlay CTA on the small image 
                                                    ?>
                                                        <div class="helcraw-fr-side-cta-wrap"><?php echo $side_cta_html; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div> <!-- .helcraw-fr-inner -->
                    </div> <!-- .helcraw-fr-panel -->
                </div>
            <?php
        });


    Block::make(__('FAQ Block'))
        ->add_fields(array(


            Field::make('image', 'faq_left_image', 'Left column image')
                ->set_value_type('url')
                ->set_help_text('Large image on the left')
                ->set_width(70),



            Field::make('complex', 'faq_items', 'FAQs')
                ->set_layout('tabbed-vertical')
                ->set_min(1)
                ->set_max(20)
                ->set_header_template('{{question}}')
                ->add_fields(array(
                    Field::make('text', 'question', 'Question')
                        ->set_attribute('placeholder', 'How do I book a meter installation?')
                        ->set_width(100),

                    Field::make('textarea', 'answer', 'Answer')
                        ->set_rows(4)
                        ->set_attribute('placeholder', 'Short answer or paragraph'),

                    Field::make('image', 'answer_image', 'Answer image (optional)')
                        ->set_value_type('url')
                        ->set_width(50),

                    Field::make('text', 'answer_cta_text', 'Answer CTA text (optional)')
                        ->set_width(50),

                    Field::make('text', 'answer_cta_url', 'Answer CTA URL (optional)')
                        ->set_width(50),

                    Field::make('checkbox', 'answer_cta_external', 'Open answer CTA in new tab')
                        ->set_option_value('yes')
                        ->set_default_value(false)
                        ->set_width(50),
                )),
        ))
        ->set_description(__('FAQ section: left image + right accordion with answers, images & CTA'))
        ->set_icon('editor-help')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields) {

            $badge = ! empty($fields['faq_badge']) ? esc_html($fields['faq_badge']) : '';
            $left_image = ! empty($fields['faq_left_image']) ? esc_url($fields['faq_left_image']) : '';
            $heading = ! empty($fields['faq_heading']) ? esc_html($fields['faq_heading']) : '';

            $items = ! empty($fields['faq_items']) && is_array($fields['faq_items']) ? $fields['faq_items'] : array();

            if (empty($left_image) && empty($items)) {
                return;
            }
            ?>
                <div class="helcraw-faq-block">
                    <div class="helcraw-faq-inner container">
                        <div class="helcraw-faq-left">
                            <?php if ($left_image) : ?>
                                <div class="helcraw-faq-left-image-card">
                                    <img src="<?php echo $left_image; ?>" alt="<?php echo esc_attr($heading ?: 'FAQ image'); ?>" />
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="helcraw-faq-right">




                            <div class="helcraw-faq-list" id="helcraw-faq-accordion">
                                <?php
                                $index = 0;
                                foreach ($items as $item) :
                                    $index++;
                                    $question = isset($item['question']) ? esc_html($item['question']) : '';
                                    $answer = isset($item['answer']) ? wp_kses_post(nl2br($item['answer'])) : '';
                                    $ans_img = isset($item['answer_image']) && $item['answer_image'] ? esc_url($item['answer_image']) : '';
                                    $ans_cta_text = isset($item['answer_cta_text']) ? $item['answer_cta_text'] : '';
                                    $ans_cta_url = isset($item['answer_cta_url']) ? trim($item['answer_cta_url']) : '';
                                    $ans_cta_external = ! empty($item['answer_cta_external']);

                                    // safe CTA href
                                    $ans_cta_html = '';
                                    if ($ans_cta_text && $ans_cta_url) {
                                        if (preg_match('#^(https?:)?//#', $ans_cta_url)) {
                                            $href = esc_url($ans_cta_url);
                                        } else {
                                            $href = esc_url(home_url('/' . ltrim($ans_cta_url, '/')));
                                        }
                                        $target = $ans_cta_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                                        $ans_cta_html = sprintf('<a class="helcraw-faq-answer-cta" href="%s"%s>%s</a>', $href, $target, esc_html($ans_cta_text));
                                    }

                                    // IDs for aria
                                    $q_id = 'helcraw-faq-q-' . $index;
                                    $panel_id = 'helcraw-faq-panel-' . $index;

                                    // open first item by default
                                    $is_open = ($index === 1);
                                    $item_classes = $is_open ? 'helcraw-faq-item is-open' : 'helcraw-faq-item';
                                    $aria_exp = $is_open ? 'true' : 'false';
                                ?>
                                    <div class="<?php echo esc_attr($item_classes); ?>">
                                        <button class="helcraw-faq-question" id="<?php echo esc_attr($q_id); ?>"
                                            aria-controls="<?php echo esc_attr($panel_id); ?>"
                                            aria-expanded="<?php echo esc_attr($aria_exp); ?>"
                                            type="button">
                                            <span class="helcraw-faq-question-text"><?php echo $question; ?></span>
                                            <span class="helcraw-faq-toggle" aria-hidden="true"></span>
                                        </button>

                                        <div class="helcraw-faq-answer" id="<?php echo esc_attr($panel_id); ?>"
                                            role="region" aria-labelledby="<?php echo esc_attr($q_id); ?>"
                                            <?php if (! $is_open) echo 'hidden'; ?>>
                                            <div class="helcraw-faq-answer-inner">
                                                <div class="helcraw-faq-answer-copy">
                                                    <?php echo $answer; ?>
                                                </div>

                                                <?php if ($ans_img || $ans_cta_html) : ?>
                                                    <div class="helcraw-faq-answer-media">
                                                        <?php if ($ans_img) : ?>
                                                            <div class="helcraw-faq-answer-img-wrap">
                                                                <img src="<?php echo $ans_img; ?>" alt="<?php echo esc_attr($question ?: 'FAQ image'); ?>" />
                                                                <?php if ($ans_cta_html) : ?>
                                                                    <div class="helcraw-faq-answer-cta-wrap">
                                                                        <?php echo $ans_cta_html; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php elseif ($ans_cta_html) : ?>
                                                            <div class="helcraw-faq-answer-cta-wrap">
                                                                <?php echo $ans_cta_html; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        });


    Block::make(__('Services Grid'))
        ->add_fields(array(

            Field::make('complex', 'services', 'Service cards')
                ->set_layout('tabbed-vertical')
                ->set_min(1)
                ->set_max(12)
                ->set_header_template('{{service_title}}')
                ->add_fields(array(
                    Field::make('text', 'service_badge', 'Badge text (small tag)')
                        ->set_width(30)
                        ->set_attribute('placeholder', 'Prepaid Water Meters'),

                    Field::make('image', 'service_badge_icon', 'Badge icon (optional)')
                        ->set_value_type('url')
                        ->set_width(30),

                    Field::make('text', 'service_title', 'Service Title')
                        ->set_width(100)
                        ->set_attribute('placeholder', 'Prepaid Water Meter Installation'),

                    Field::make('textarea', 'service_description', 'Service Description')
                        ->set_rows(3)
                        ->set_width(100)
                        ->set_attribute('placeholder', 'Short description'),

                    Field::make('image', 'service_image', 'Main service image')
                        ->set_value_type('url')
                        ->set_width(100),
                )),

            // optional grid settings
            Field::make('select', 'services_columns', 'Columns (desktop)')
                ->set_options(array(
                    '2' => '2 columns (default)',
                    '3' => '3 columns',
                    '4' => '4 columns',
                ))
                ->set_default_value('2')
                ->set_width(50),

            Field::make('checkbox', 'services_center_badge', 'Center badge inside card')
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_help_text('If checked, badge will be centered in the text area (useful for alternate style)')
                ->set_width(50),

        ))
        ->set_description(__('Grid of hero-overlay-style service cards (uses theme styles)'))
        ->set_icon('format-image')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {

            $services = ! empty($fields['services']) && is_array($fields['services']) ? $fields['services'] : array();
            $cols = ! empty($fields['services_columns']) ? intval($fields['services_columns']) : 2;
            $center_badge = ! empty($fields['services_center_badge']);

            if (empty($services)) {
                return;
            }
            ?>
                <div class="site-container">
                    <div class="hcrw-services-grid hcrw-services-cols-<?php echo esc_attr($cols); ?>">

                        <?php foreach ($services as $s) :
                            $badge = ! empty($s['service_badge']) ? esc_html($s['service_badge']) : '';
                            $badge_icon_field = ! empty($s['service_badge_icon']) ? $s['service_badge_icon'] : '';
                            $default_badge_icon = get_stylesheet_directory_uri() . '/img/gas.png';
                            $badge_icon = $badge_icon_field ? esc_url($badge_icon_field) : esc_url($default_badge_icon);

                            $title = ! empty($s['service_title']) ? esc_html($s['service_title']) : '';
                            $desc = ! empty($s['service_description']) ? wp_kses_post(nl2br($s['service_description'])) : '';
                            $img = ! empty($s['service_image']) ? esc_url($s['service_image']) : '';
                        ?>
                            <div class="hcrw-service-card site-container">
                                <div class="hcrw-overlay-card">
                                    <div class="hcrw-overlay-inner">
                                        <?php if ($badge || $badge_icon) : ?>
                                            <div class="hcrw-badge mb-3<?php echo $center_badge ? ' hcrw-center-badge' : ''; ?>">
                                                <?php if ($badge_icon) : ?>
                                                    <img class="hcrw-badge-icon" src="<?php echo esc_url($badge_icon); ?>" alt="<?php echo esc_attr($badge); ?>">
                                                <?php endif; ?>
                                                <?php echo esc_html($badge); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($title) : ?>
                                            <h3 class="hcrw-title"><?php echo $title; ?></h3>
                                        <?php endif; ?>

                                        <?php if ($desc) : ?>
                                            <div class="hcrw-desc"><?php echo $desc; ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($img) : ?>
                                        <div class="hcrw-image-box">
                                            <img class="hcrw-main-image" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title ?: 'Service image'); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>


            <?php
        });








    Block::make(__('Expertise Block'))
        ->add_fields(array(
            Field::make('text', 'expertise_heading', 'Heading')
                ->set_attribute('placeholder', 'Our expertise')
                ->set_width(100),

            Field::make('textarea', 'expertise_text', 'Body text')
                ->set_attribute('placeholder', 'Short description')
                ->set_rows(6)
                ->set_width(100),

            Field::make('image', 'expertise_image', 'Right column image')
                ->set_value_type('url')
                ->set_help_text('Upload the hero/expertise image')
                ->set_width(100),


            Field::make('checkbox', 'expertise_image_left', 'Place image on the left')
                ->set_option_value('yes')
                ->set_help_text('Check to place the image on the left and text on the right')
                ->set_width(50),
        ))
        ->set_description(__('Two-column expertise block: text + large image'))
        ->set_icon('format-image')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {

            $heading = ! empty($fields['expertise_heading']) ? esc_html($fields['expertise_heading']) : '';
            $text    = ! empty($fields['expertise_text']) ? wp_kses_post(nl2br($fields['expertise_text'])) : '';
            $image   = ! empty($fields['expertise_image']) ? esc_url($fields['expertise_image']) : '';
            $image_left = ! empty($fields['expertise_image_left']);

            $side_class = $image_left ? 'image-left' : 'image-right';
            ?>
                <div class="helcraw-expertise-block ">
                    <div class="helcraw-expertise-inner contain <?php echo esc_attr($side_class); ?>">
                        <div class="helcraw-expertise-column helcraw-expertise-text">
                            <?php if ($heading) : ?>
                                <h2 class="main-heading"><?php echo $heading; ?></h2>
                            <?php endif; ?>

                            <?php if ($text) : ?>
                                <div class="helcraw-expertise-copy"><?php echo $text; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="helcraw-expertise-column helcraw-expertise-media">
                            <?php if ($image) : ?>
                                <div class="helcraw-expertise-image">
                                    <img src="<?php echo $image; ?>" alt="<?php echo esc_attr($heading ?: 'Expertise image'); ?>" />
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
        });


    Block::make(__('Hero Badge'))
        ->add_fields(array(
            Field::make('text', 'badge_text', 'Badge Text')
                ->set_attribute('placeholder', 'Mission')
                ->set_width(40),

            Field::make('image', 'badge_icon', 'Badge Icon (optional)')
                ->set_value_type('url')
                ->set_help_text('Upload badge icon. Defaults to /img/tap-bg.png if empty.')
                ->set_width(40),

            Field::make('checkbox', 'badge_center', 'Center badge')
                ->set_option_value('yes')
                ->set_help_text('Center the badge horizontally')
                ->set_width(20),
        ))
        ->set_description(__('Reusable hero badge with optional centering'))
        ->set_icon('tag')
        ->set_category('helcraw-blocks')
        ->set_render_callback(function ($fields) {

            $badge_text = ! empty($fields['badge_text'])
                ? esc_html($fields['badge_text'])
                : '';

            if (! $badge_text) {
                return;
            }

            // Icon logic (uploaded OR fallback)
            $icon = ! empty($fields['badge_icon'])
                ? esc_url($fields['badge_icon'])
                : esc_url(get_stylesheet_directory_uri() . '/img/tap-bg.png');

            // Center toggle
            $is_centered = ! empty($fields['badge_center']);
            $wrapper_class = $is_centered ? 'hero-badge-wrapper hero-badge--center' : 'hero-badge-wrapper';
            ?>

                <div class="<?php echo esc_attr($wrapper_class); ?>">
                    <div class="hero-badge mb-5">
                        <img
                            class="badge-icon"
                            src="<?php echo $icon; ?>"
                            alt="<?php echo esc_attr($badge_text); ?>" />
                        <?php echo $badge_text; ?>
                    </div>
                </div>

            <?php
        });



    // 
    // Home Page - Fields
    //
    Container::make('post_meta', 'Helcraw Home Page')
        ->where('post_id', '=', get_option('page_on_front'))
        ->add_fields(array(
            Field::make('complex', 'helcraw_slider', __('Home Slider'))
                ->set_width(100)
                ->set_layout('tabbed-horizontal')
                ->add_fields(array(
                    Field::make('text', 'helcraw_slider_text', __('Slider Text'))
                        ->set_width(100),

                    Field::make('text', 'helcraw_slider_btn_text', __('Button Text'))
                        ->set_width(40),

                    Field::make('text', 'helcraw_slider_url', __('Button URL'))
                        ->set_width(40),

                    Field::make('image', 'helcraw_slider_image', __('Slider Image'))
                        ->set_value_type('url')
                        ->set_width(20),
                )),
        ));
}



/************************************
    Custom Login Form
    Author: thatAfro
    Author URI: https://thatafro.netlify.app/
 ************************************/

// Changes the Logo
function helcraw_login_form_customization()
{
            ?>
            <style type="text/css">
                body {
                    /* background-color: white !important; */
                    background: black url('<?php echo get_theme_file_uri() . '/img/login-bg.jpg' ?>') no-repeat center !important;
                    background-size: cover !important;
                }

                #login {
                    background: rgba(255, 255, 255, 0.8) !important;
                    border-radius: 5px !important;
                    width: 320px !important;
                    padding: 2% 20px 5px 20px !important;
                    margin: auto;
                }

                /* Logo */
                #login h1 a,
                .login h1 a {
                    background-image: url('<?php echo get_theme_file_uri() . '/img/helcraw-water-logo-color.svg' ?>') !important;
                    width: auto;
                    background-size: 200px auto;
                    background-repeat: no-repeat;
                    margin: 0px !important;
                }

                /* General Form */
                .login form {
                    background: transparent !important;
                    border: none !important;
                    padding: 0 !important;
                }

                .login label {
                    color: black !important;
                    font-weight: 500;
                }

                .login form .input,
                .login form input[type=checkbox],
                .login input[type=text] {
                    background: black;
                    border-radius: 0 !important;
                }

                /* Login Error */
                .login #login_error {
                    border-left-color: #d63638 !important;
                    background-color: #d63638 !important;
                    color: white !important;
                }

                .login #login_error a {
                    color: white !important;
                }

                /* Other Messages Being Displayed */
                .login .message,
                .login .success {
                    border-left-color: #036F3C !important;
                    background-color: #036F3C !important;
                    color: white !important;
                }

                .login .message a,
                .login .success a {
                    color: white !important;
                }

                /* Buttons */
                .wp-core-ui .button-group.button-large .button,
                .wp-core-ui .button.button-large {
                    width: 100%;
                    min-height: 40px !important;
                }

                .login .language-switcher .button {
                    margin: 0 !important;
                }

                .login .button.wp-hide-pw {
                    height: 2.35rem !important;
                }

                .wp-core-ui .button {
                    background: #09264C !important;
                    border-color: #09264C !important;
                    border-radius: 0 !important;
                    color: white !important;
                    min-height: 35px !important;
                    text-transform: uppercase;
                    font-weight: 600;
                    margin: 10px 0 0 0 !important;
                    transition: all .5s;
                }

                .wp-core-ui .button:hover {
                    background: #0a61e2 !important;
                    border-color: #0a61e2 !important;
                    color: white !important;
                }

                .login .button.wp-hide-pw {
                    top: -9px !important;
                }

                /* Forgot or Back to site links */
                .login #backtoblog a,
                .login #nav a {
                    padding-left: 0;
                    text-decoration: none;
                    color: black !important;
                    transition: all .5s;
                }

                .login #backtoblog a:hover,
                .login #nav a:hover {
                    padding-left: 5px;
                    text-decoration: underline;
                }

                .login #backtoblog,
                .login #nav {
                    font-size: 15px !important;
                    padding: 0 !important;
                }
            </style>
        <?php
    }
    add_action('login_enqueue_scripts', 'helcraw_login_form_customization');

    // Add link to the logo Image
    function helcraw_login_form_logo_url()
    {
        return home_url();
    }
    add_filter('login_headerurl', 'helcraw_login_form_logo_url');
