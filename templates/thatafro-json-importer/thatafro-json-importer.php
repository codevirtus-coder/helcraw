<?php
/*
Plugin Name: thatAfro JSON Importer
Description: Import pages and posts from JSON files (overwrites existing content)
Version: 1.0.0
Author: Hikwa Mehluli
Author URI: https://thatafro.netlify.app/
Text Domain: thatafro-json-importer
*/

// Add settings link on plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'json_importer_settings_link');

function json_importer_settings_link($links) {
    $settings_link = '<a href="tools.php?page=thatafro-json-importer">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Add admin menu
add_action('admin_menu', 'json_importer_menu');

function json_importer_menu() {
    add_management_page(
        'thatAfro JSON Importer',
        'Import Content',
        'manage_options',
        'thatafro-json-importer',
        'json_importer_page'
    );
}

function json_importer_page() {
    $plugin_dir = plugin_dir_url(__FILE__);
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'pages';
    ?>
    <div class="wrap">
        <h1>Import Content from JSON</h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=thatafro-json-importer&tab=pages" class="nav-tab <?php echo $active_tab == 'pages' ? 'nav-tab-active' : ''; ?>">Pages</a>
            <a href="?page=thatafro-json-importer&tab=posts" class="nav-tab <?php echo $active_tab == 'posts' ? 'nav-tab-active' : ''; ?>">Posts</a>
        </h2>

        <?php if ($active_tab == 'pages'): ?>
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Example Pages JSON File</h2>
                <p>Need an example? Download the sample JSON file to see the correct format for pages:</p>
                <a href="<?php echo $plugin_dir; ?>pages-example.json" class="button button-secondary" download>
                    Download pages-example.json
                </a>
            </div>
            
            <h2>Import Pages from JSON</h2>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('import_pages', 'import_pages_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="json_file">Select JSON File</label>
                        </th>
                        <td>
                            <input type="file" name="json_file" id="json_file" accept=".json" required>
                            <p class="description">Upload a JSON file containing your page structure with parent-child relationships.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="import_pages" class="button button-primary" value="Import Pages">
                </p>
            </form>
        <?php endif; ?>

        <?php if ($active_tab == 'posts'): ?>
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Example Posts JSON File</h2>
                <p>Need an example? Download the sample JSON file to see the correct format for posts:</p>
                <a href="<?php echo $plugin_dir; ?>posts-example.json" class="button button-secondary" download>
                    Download posts-example.json
                </a>
            </div>
            
            <h2>Import Posts from JSON</h2>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('import_posts', 'import_posts_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="json_file_posts">Select JSON File</label>
                        </th>
                        <td>
                            <input type="file" name="json_file_posts" id="json_file_posts" accept=".json" required>
                            <p class="description">Upload a JSON file containing your posts with categories.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="import_posts" class="button button-primary" value="Import Posts">
                </p>
            </form>
        <?php endif; ?>
    </div>
    <?php
    
    // Handle Pages Import
    if (isset($_POST['import_pages']) && check_admin_referer('import_pages', 'import_pages_nonce')) {
        if (isset($_FILES['json_file'])) {
            $file = $_FILES['json_file']['tmp_name'];
            $json = file_get_contents($file);
            $pages = json_decode($json, true);
            
            if ($pages) {
                $result = import_pages_recursive($pages);
                echo '<div class="notice notice-success"><p><strong>Pages Import Complete!</strong><br>' . 
                     $result['created'] . ' pages created, ' . 
                     $result['updated'] . ' pages updated.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Invalid JSON file format.</p></div>';
            }
        }
    }
    
    // Handle Posts Import
    if (isset($_POST['import_posts']) && check_admin_referer('import_posts', 'import_posts_nonce')) {
        if (isset($_FILES['json_file_posts'])) {
            $file = $_FILES['json_file_posts']['tmp_name'];
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            
            if ($data && isset($data['articles'])) {
                $result = import_posts($data['articles']);
                echo '<div class="notice notice-success"><p><strong>Posts Import Complete!</strong><br>' . 
                     $result['created'] . ' posts created, ' . 
                     $result['updated'] . ' posts updated, ' . 
                     $result['categories'] . ' categories processed.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Invalid JSON file format. Expected "articles" array.</p></div>';
            }
        }
    }
}

// Import Pages Function
function import_pages_recursive($pages, $parent_id = 0, &$stats = array('created' => 0, 'updated' => 0)) {
    foreach ($pages as $page) {
        $existing_page = get_page_by_title($page['title'], OBJECT, 'page');
        
        if ($existing_page && $existing_page->post_parent == $parent_id) {
            $page_data = array(
                'ID'            => $existing_page->ID,
                'post_title'    => $page['title'],
                'post_content'  => isset($page['content']) ? $page['content'] : '',
                'post_status'   => isset($page['status']) ? $page['status'] : 'publish',
                'post_type'     => 'page',
                'post_parent'   => $parent_id
            );
            
            wp_update_post($page_data);
            $page_id = $existing_page->ID;
            $stats['updated']++;
        } else {
            $page_data = array(
                'post_title'    => $page['title'],
                'post_content'  => isset($page['content']) ? $page['content'] : '',
                'post_status'   => isset($page['status']) ? $page['status'] : 'publish',
                'post_type'     => 'page',
                'post_parent'   => $parent_id
            );
            
            $page_id = wp_insert_post($page_data);
            $stats['created']++;
        }
        
        if (isset($page['children']) && is_array($page['children'])) {
            import_pages_recursive($page['children'], $page_id, $stats);
        }
    }
    
    return $stats;
}

// Import Posts Function
function import_posts($articles) {
    $stats = array('created' => 0, 'updated' => 0, 'categories' => 0);
    $processed_categories = array();
    
    foreach ($articles as $article) {
        // Handle categories
        $category_ids = array();
        if (isset($article['categories']) && is_array($article['categories'])) {
            foreach ($article['categories'] as $cat_name) {
                $category = get_term_by('name', $cat_name, 'category');
                
                if (!$category) {
                    $new_cat = wp_insert_term($cat_name, 'category');
                    if (!is_wp_error($new_cat)) {
                        $category_ids[] = $new_cat['term_id'];
                        if (!in_array($cat_name, $processed_categories)) {
                            $processed_categories[] = $cat_name;
                            $stats['categories']++;
                        }
                    }
                } else {
                    $category_ids[] = $category->term_id;
                }
            }
        }
        
        // Check if post exists
        $existing_post = get_page_by_title($article['title'], OBJECT, 'post');
        
        if ($existing_post) {
            $post_data = array(
                'ID'            => $existing_post->ID,
                'post_title'    => $article['title'],
                'post_content'  => isset($article['body']) ? $article['body'] : '',
                'post_status'   => 'publish',
                'post_type'     => 'post'
            );
            
            $post_id = wp_update_post($post_data);
            $stats['updated']++;
        } else {
            $post_data = array(
                'post_title'    => $article['title'],
                'post_content'  => isset($article['body']) ? $article['body'] : '',
                'post_status'   => 'publish',
                'post_type'     => 'post'
            );
            
            $post_id = wp_insert_post($post_data);
            $stats['created']++;
        }
        
        // Assign categories to post
        if (!empty($category_ids) && !is_wp_error($post_id)) {
            wp_set_post_categories($post_id, $category_ids);
        }
    }
    
    return $stats;
}
?>