<?php
/**
 * GrindCTRL WooCommerce Theme Functions
 * 
 * @package GrindCTRL
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('GRINDCTRL_VERSION', '1.0.0');
define('GRINDCTRL_THEME_DIR', get_template_directory());
define('GRINDCTRL_THEME_URL', get_template_directory_uri());

/**
 * Theme setup
 */
function grindctrl_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    
    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'grindctrl'),
        'footer' => __('Footer Menu', 'grindctrl'),
    ));
    
    // Add image sizes
    add_image_size('grindctrl-featured', 800, 600, true);
    add_image_size('grindctrl-thumbnail', 300, 300, true);
}
add_action('after_setup_theme', 'grindctrl_setup');

/**
 * Enqueue scripts and styles
 */
function grindctrl_scripts() {
    // Main stylesheet
    wp_enqueue_style('grindctrl-style', get_stylesheet_uri(), array(), GRINDCTRL_VERSION);
    
    // Google Fonts
    wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // WooCommerce styles
    wp_enqueue_style('grindctrl-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', array('grindctrl-style'), GRINDCTRL_VERSION);
    
    // Theme JavaScript
    wp_enqueue_script('grindctrl-main', get_template_directory_uri() . '/js/main.js', array('jquery'), GRINDCTRL_VERSION, true);
    
    // WooCommerce JavaScript
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('grindctrl-woocommerce', get_template_directory_uri() . '/js/woocommerce.js', array('jquery', 'wc-cart-fragments'), GRINDCTRL_VERSION, true);
    }
    
    // Webhook configuration
    wp_localize_script('grindctrl-main', 'grindctrl_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('grindctrl_nonce'),
        'webhook_url' => get_option('grindctrl_webhook_url', ''),
        'currency_symbol' => get_woocommerce_currency_symbol(),
    ));
    
    // WooCommerce scripts
    if (is_woocommerce() || is_cart() || is_checkout()) {
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'grindctrl_scripts');

/**
 * Custom WooCommerce hooks
 */
function grindctrl_woocommerce_setup() {
    // Remove default WooCommerce styling
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');
    
    // Custom product gallery
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    add_action('woocommerce_before_single_product_summary', 'grindctrl_product_images', 20);
    
    // Custom product summary
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    
    add_action('woocommerce_single_product_summary', 'grindctrl_product_title', 5);
    add_action('woocommerce_single_product_summary', 'grindctrl_product_price', 10);
    add_action('woocommerce_single_product_summary', 'grindctrl_product_excerpt', 15);
    add_action('woocommerce_single_product_summary', 'grindctrl_product_features', 20);
    add_action('woocommerce_single_product_summary', 'grindctrl_add_to_cart_form', 30);
}
add_action('init', 'grindctrl_woocommerce_setup');

/**
 * Custom product images
 */
function grindctrl_product_images() {
    global $product;
    $attachment_ids = $product->get_gallery_image_ids();
    ?>
    <div class="product-image-container">
        <div class="product-image">
            <?php echo woocommerce_get_product_thumbnail(); ?>
        </div>
        <div class="product-tags">
            <span class="tag"><?php _e('Hot Drop', 'grindctrl'); ?></span>
        </div>
    </div>
    <?php
}

/**
 * Custom product title
 */
function grindctrl_product_title() {
    global $product;
    echo '<h1 class="product-title">' . get_the_title() . '</h1>';
}

/**
 * Custom product price
 */
function grindctrl_product_price() {
    global $product;
    echo '<div class="price-section">';
    if ($product->get_regular_price() != $product->get_sale_price()) {
        echo '<span class="price-original">' . wc_price($product->get_regular_price()) . '</span>';
    }
    echo '<span class="price-current">' . wc_price($product->get_price()) . '</span>';
    echo '</div>';
}

/**
 * Custom product excerpt
 */
function grindctrl_product_excerpt() {
    global $product;
    $excerpt = $product->get_short_description();
    if ($excerpt) {
        echo '<p class="product-subtitle">' . $excerpt . '</p>';
    }
}

/**
 * Product features
 */
function grindctrl_product_features() {
    ?>
    <div class="product-features">
        <div class="feature">
            <i class="fas fa-truck"></i>
            <span><?php _e('Fast checkout. Cart shipped.', 'grindctrl'); ?></span>
        </div>
        <div class="feature">
            <i class="fas fa-clock"></i>
            <span><?php _e('Free returns within 14 days', 'grindctrl'); ?></span>
        </div>
    </div>
    <?php
}

/**
 * Custom add to cart form
 */
function grindctrl_add_to_cart_form() {
    global $product;
    if ($product->is_type('simple')) {
        wc_get_template('single-product/add-to-cart/simple.php');
    }
}

/**
 * Add custom order fields
 */
function grindctrl_add_order_fields($fields) {
    $fields['billing']['billing_tracking_number'] = array(
        'label' => __('Tracking Number', 'grindctrl'),
        'placeholder' => _x('Tracking Number', 'placeholder', 'grindctrl'),
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true
    );
    
    $fields['billing']['billing_courier'] = array(
        'label' => __('Courier', 'grindctrl'),
        'placeholder' => _x('Courier Service', 'placeholder', 'grindctrl'),
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true
    );
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'grindctrl_add_order_fields');

/**
 * Save custom order fields
 */
function grindctrl_save_order_fields($order_id) {
    if (!empty($_POST['billing_tracking_number'])) {
        update_post_meta($order_id, '_billing_tracking_number', sanitize_text_field($_POST['billing_tracking_number']));
    }
    if (!empty($_POST['billing_courier'])) {
        update_post_meta($order_id, '_billing_courier', sanitize_text_field($_POST['billing_courier']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'grindctrl_save_order_fields');

/**
 * N8N Webhook Integration
 */
function grindctrl_send_order_to_webhook($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    
    $webhook_url = get_option('grindctrl_webhook_url');
    if (!$webhook_url) return;
    
    // Prepare order data for webhook
    $order_data = array(
        'order_id' => $order->get_id(),
        'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        'phone' => $order->get_billing_phone(),
        'city' => $order->get_billing_city(),
        'address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
        'cod_amount' => $order->get_total(),
        'tracking_number' => get_post_meta($order_id, '_billing_tracking_number', true),
        'courier' => get_post_meta($order_id, '_billing_courier', true),
        'total' => $order->get_total(),
        'date' => $order->get_date_created()->format('Y-m-d H:i:s'),
        'status' => $order->get_status(),
        'payment_method' => $order->get_payment_method_title(),
        'products' => array(),
        'quantities' => array()
    );
    
    // Add product details
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $order_data['products'][] = $product->get_name();
        $order_data['quantities'][] = $item->get_quantity();
    }
    
    // Send to webhook
    wp_remote_post($webhook_url, array(
        'body' => json_encode($order_data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 30
    ));
}
add_action('woocommerce_order_status_processing', 'grindctrl_send_order_to_webhook');
add_action('woocommerce_order_status_completed', 'grindctrl_send_order_to_webhook');

/**
 * Admin settings for webhook URL
 */
function grindctrl_admin_menu() {
    add_options_page(
        __('GrindCTRL Settings', 'grindctrl'),
        __('GrindCTRL Settings', 'grindctrl'),
        'manage_options',
        'grindctrl-settings',
        'grindctrl_settings_page'
    );
}
add_action('admin_menu', 'grindctrl_admin_menu');

function grindctrl_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('grindctrl_webhook_url', sanitize_url($_POST['webhook_url']));
        echo '<div class="notice notice-success"><p>' . __('Settings saved!', 'grindctrl') . '</p></div>';
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    ?>
    <div class="wrap">
        <h1><?php _e('GrindCTRL Settings', 'grindctrl'); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Webhook URL', 'grindctrl'); ?></th>
                    <td>
                        <input type="url" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>" class="regular-text" />
                        <p class="description"><?php _e('Enter your N8N webhook URL for order processing', 'grindctrl'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Customize WooCommerce messages
 */
function grindctrl_woocommerce_messages() {
    if (function_exists('wc_print_notices')) {
        wc_print_notices();
    }
}

/**
 * Remove WooCommerce breadcrumbs
 */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/**
 * Widget areas
 */
function grindctrl_widgets_init() {
    register_sidebar(array(
        'name' => __('Shop Sidebar', 'grindctrl'),
        'id' => 'shop-sidebar',
        'description' => __('Add widgets here to appear in your shop sidebar.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'grindctrl_widgets_init');

/**
 * AJAX handler for cart count
 */
function grindctrl_get_cart_count() {
    if (class_exists('WooCommerce')) {
        wp_send_json(array('count' => WC()->cart->get_cart_contents_count()));
    } else {
        wp_send_json(array('count' => 0));
    }
}
add_action('wp_ajax_get_cart_count', 'grindctrl_get_cart_count');
add_action('wp_ajax_nopriv_get_cart_count', 'grindctrl_get_cart_count');

/**
 * Add currency symbol to localized script
 */
function grindctrl_get_currency_symbol() {
    if (function_exists('get_woocommerce_currency_symbol')) {
        return get_woocommerce_currency_symbol();
    }
    return 'EGP';
}

// Include admin order management
if (is_admin()) {
    require_once get_template_directory() . '/admin-order-management.php';
}