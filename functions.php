<?php
/**
 * CT Storefront Starter functions and definitions
 *
 * @package CT_Storefront
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function ct_storefront_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');

    register_nav_menus(array(
        'menu-1' => esc_html__('Primary Menu', 'ct-storefront'),
        'footer-menu' => esc_html__('Footer Menu', 'ct-storefront'),
    ));

    add_image_size('product-thumbnail', 300, 300, true);
    add_image_size('product-large', 600, 600, true);
    add_image_size('hero-image', 1200, 600, true);
}
add_action('after_setup_theme', 'ct_storefront_setup');

/**
 * Enqueue scripts and styles
 */
function ct_storefront_scripts() {
    wp_enqueue_style('ct-storefront-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('jquery');
    wp_enqueue_script('ct-storefront-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    wp_localize_script('ct-storefront-script', 'ct_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ct_ajax_nonce'),
        'loading_text' => __('Loading...', 'ct-storefront'),
        'no_results_text' => __('No products found', 'ct-storefront'),
    ));
}
add_action('wp_enqueue_scripts', 'ct_storefront_scripts');

/**
 * Fallback menu function
 */
function ct_storefront_fallback_menu() {
    echo '<ul id="primary-menu" class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'ct-storefront') . '</a></li>';
    echo '<li><a href="#products">' . __('Products', 'ct-storefront') . '</a></li>';
    echo '<li><a href="#about">' . __('About', 'ct-storefront') . '</a></li>';
    echo '<li><a href="#contact">' . __('Contact', 'ct-storefront') . '</a></li>';
    echo '</ul>';
}

/**
 * Register widget areas
 */
function ct_storefront_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'ct-storefront'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'ct-storefront'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'ct_storefront_widgets_init');

/**
 * Custom post type for products
 */
function ct_storefront_create_product_post_type() {
    $labels = array(
        'name'               => __('Products', 'ct-storefront'),
        'singular_name'      => __('Product', 'ct-storefront'),
        'menu_name'          => __('Products', 'ct-storefront'),
        'add_new'            => __('Add New', 'ct-storefront'),
        'add_new_item'       => __('Add New Product', 'ct-storefront'),
        'edit_item'          => __('Edit Product', 'ct-storefront'),
        'new_item'           => __('New Product', 'ct-storefront'),
        'view_item'          => __('View Product', 'ct-storefront'),
        'search_items'       => __('Search Products', 'ct-storefront'),
        'not_found'          => __('No products found', 'ct-storefront'),
        'not_found_in_trash' => __('No products found in trash', 'ct-storefront'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'product'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-cart',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'        => true,
    );

    register_post_type('product', $args);
}
add_action('init', 'ct_storefront_create_product_post_type');

/**
 * AJAX handler for product filtering
 */
function ct_storefront_filter_products() {
    check_ajax_referer('ct_ajax_nonce', 'nonce');
    
    $category = sanitize_text_field($_POST['category']);
    $price_min = floatval($_POST['price_min']);
    $price_max = floatval($_POST['price_max']);
    $rating = floatval($_POST['rating']);
    $sort = sanitize_text_field($_POST['sort']);
    
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'meta_query' => array(),
    );
    
    if (!empty($category)) {
        $args['meta_query'][] = array(
            'key' => '_product_category',
            'value' => $category,
            'compare' => '='
        );
    }
    
    if ($price_min > 0 || $price_max > 0) {
        $price_query = array('key' => '_product_price', 'type' => 'NUMERIC');
        
        if ($price_min > 0 && $price_max > 0) {
            $price_query['value'] = array($price_min, $price_max);
            $price_query['compare'] = 'BETWEEN';
        } elseif ($price_min > 0) {
            $price_query['value'] = $price_min;
            $price_query['compare'] = '>=';
        } elseif ($price_max > 0) {
            $price_query['value'] = $price_max;
            $price_query['compare'] = '<=';
        }
        
        $args['meta_query'][] = $price_query;
    }
    
    if ($rating > 0) {
        $args['meta_query'][] = array(
            'key' => '_product_rating',
            'value' => $rating,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }
    
    switch ($sort) {
        case 'price-low':
            $args['meta_key'] = '_product_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-high':
            $args['meta_key'] = '_product_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_product_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popular':
            $args['orderby'] = 'comment_count';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }
    
    $products = new WP_Query($args);
    $response = array();
    
    if ($products->have_posts()) {
        ob_start();
        while ($products->have_posts()) {
            $products->the_post();
            $price = get_post_meta(get_the_ID(), '_product_price', true);
            $category = get_post_meta(get_the_ID(), '_product_category', true);
            $rating = get_post_meta(get_the_ID(), '_product_rating', true);
            $badge = get_post_meta(get_the_ID(), '_product_badge', true);
            
            ?>
            <div class="product-card fade-in">
                <div class="product-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('product-thumbnail'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                    
                    <?php if ($badge) : ?>
                        <span class="product-badge <?php echo esc_attr($badge); ?>">
                            <?php echo esc_html(ucfirst($badge)); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="product-content">
                    <h3 class="product-title"><?php the_title(); ?></h3>
                    <p class="product-category"><?php echo esc_html(ucfirst($category)); ?></p>
                    
                    <div class="product-price">
                        <span class="current-price">$<?php echo number_format($price, 2); ?></span>
                    </div>
                    
                    <?php if ($rating) : ?>
                        <div class="product-rating">
                            <div class="stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-text">(<?php echo number_format($rating, 1); ?>)</span>
                        </div>
                    <?php endif; ?>
                    
                    <button class="add-to-cart" data-product-id="<?php echo get_the_ID(); ?>">
                        Add to Cart
                    </button>
                </div>
            </div>
            <?php
        }
        $response['html'] = ob_get_clean();
        $response['count'] = $products->found_posts;
    } else {
        $response['html'] = '<div class="no-results"><h3>No products found</h3><p>Try adjusting your filters.</p></div>';
        $response['count'] = 0;
    }
    
    wp_reset_postdata();
    wp_send_json($response);
}
add_action('wp_ajax_ct_filter_products', 'ct_storefront_filter_products');
add_action('wp_ajax_nopriv_ct_filter_products', 'ct_storefront_filter_products');

/**
 * Add custom meta boxes for products
 */
function ct_storefront_add_product_meta_boxes() {
    add_meta_box(
        'product_details',
        __('Product Details', 'ct-storefront'),
        'ct_storefront_product_meta_box_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ct_storefront_add_product_meta_boxes');

/**
 * Product meta box callback
 */
function ct_storefront_product_meta_box_callback($post) {
    wp_nonce_field('ct_storefront_save_product_meta', 'ct_storefront_product_meta_nonce');
    
    $price = get_post_meta($post->ID, '_product_price', true);
    $category = get_post_meta($post->ID, '_product_category', true);
    $rating = get_post_meta($post->ID, '_product_rating', true);
    $badge = get_post_meta($post->ID, '_product_badge', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="product_price"><?php _e('Price', 'ct-storefront'); ?></label></th>
            <td><input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" /></td>
        </tr>
        <tr>
            <th><label for="product_category"><?php _e('Category', 'ct-storefront'); ?></label></th>
            <td>
                <select id="product_category" name="product_category">
                    <option value=""><?php _e('Select Category', 'ct-storefront'); ?></option>
                    <option value="electronics" <?php selected($category, 'electronics'); ?>><?php _e('Electronics', 'ct-storefront'); ?></option>
                    <option value="clothing" <?php selected($category, 'clothing'); ?>><?php _e('Clothing', 'ct-storefront'); ?></option>
                    <option value="home" <?php selected($category, 'home'); ?>><?php _e('Home & Garden', 'ct-storefront'); ?></option>
                    <option value="sports" <?php selected($category, 'sports'); ?>><?php _e('Sports & Outdoors', 'ct-storefront'); ?></option>
                    <option value="books" <?php selected($category, 'books'); ?>><?php _e('Books & Media', 'ct-storefront'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="product_rating"><?php _e('Rating', 'ct-storefront'); ?></label></th>
            <td><input type="number" id="product_rating" name="product_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="0.1" /></td>
        </tr>
        <tr>
            <th><label for="product_badge"><?php _e('Badge', 'ct-storefront'); ?></label></th>
            <td>
                <select id="product_badge" name="product_badge">
                    <option value=""><?php _e('No Badge', 'ct-storefront'); ?></option>
                    <option value="sale" <?php selected($badge, 'sale'); ?>><?php _e('Sale', 'ct-storefront'); ?></option>
                    <option value="new" <?php selected($badge, 'new'); ?>><?php _e('New', 'ct-storefront'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save product meta data
 */
function ct_storefront_save_product_meta($post_id) {
    if (!isset($_POST['ct_storefront_product_meta_nonce']) || !wp_verify_nonce($_POST['ct_storefront_product_meta_nonce'], 'ct_storefront_save_product_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['product_price'])) {
        update_post_meta($post_id, '_product_price', sanitize_text_field($_POST['product_price']));
    }
    
    if (isset($_POST['product_category'])) {
        update_post_meta($post_id, '_product_category', sanitize_text_field($_POST['product_category']));
    }
    
    if (isset($_POST['product_rating'])) {
        update_post_meta($post_id, '_product_rating', sanitize_text_field($_POST['product_rating']));
    }
    
    if (isset($_POST['product_badge'])) {
        update_post_meta($post_id, '_product_badge', sanitize_text_field($_POST['product_badge']));
    }
}
add_action('save_post', 'ct_storefront_save_product_meta');
