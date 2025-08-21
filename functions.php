<?php
/**
 * GrindCTRL WooCommerce Theme Functions
 * 
 * This file adds WooCommerce support to the original GrindCTRL design
 * while maintaining all existing functionality and webhook integration.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function grindctrl_setup() {
    // Add theme support for WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Add theme support for WordPress features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('customize-selective-refresh-widgets');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'grindctrl'),
    ));
}
add_action('after_setup_theme', 'grindctrl_setup');

// Enqueue styles and scripts
function grindctrl_enqueue_scripts() {
    // Enqueue original styles
    wp_enqueue_style('grindctrl-style', get_template_directory_uri() . '/css/styles.css', array(), '1.0.0');
    
    // Enqueue Google Fonts
    wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Enqueue original JavaScript with WordPress integration
    wp_enqueue_script('grindctrl-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Localize script with WordPress/WooCommerce data
    wp_localize_script('grindctrl-main', 'grindctrl_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('grindctrl_nonce'),
        'webhook_url' => get_option('grindctrl_webhook_url', ''),
    ));
}
add_action('wp_enqueue_scripts', 'grindctrl_enqueue_scripts');

// Include webhook integration
require_once get_template_directory() . '/includes/webhook-integration.php';

// Include customizations
require_once get_template_directory() . '/includes/customizations.php';

// Add WooCommerce cart to header
function grindctrl_cart_link() {
    if (class_exists('WooCommerce')) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = wc_get_cart_url();
        
        echo '<div class="cart-icon">';
        echo '<a href="' . esc_url($cart_url) . '">';
        echo '<i class="fas fa-shopping-cart"></i>';
        echo '<span class="cart-count" id="cartCount">' . esc_html($cart_count) . '</span>';
        echo '</a>';
        echo '</div>';
    }
}

// Update cart count via AJAX
function grindctrl_update_cart_count() {
    if (class_exists('WooCommerce')) {
        wp_die(WC()->cart->get_cart_contents_count());
    }
    wp_die(0);
}
add_action('wp_ajax_grindctrl_update_cart_count', 'grindctrl_update_cart_count');
add_action('wp_ajax_nopriv_grindctrl_update_cart_count', 'grindctrl_update_cart_count');

// Remove WooCommerce default styles that conflict with our design
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Customize WooCommerce to match our design
function grindctrl_woocommerce_customizations() {
    // Remove default WooCommerce wrappers
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    
    // Add our custom wrappers
    add_action('woocommerce_before_main_content', 'grindctrl_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'grindctrl_wrapper_end', 10);
}
add_action('init', 'grindctrl_woocommerce_customizations');

function grindctrl_wrapper_start() {
    echo '<main class="main" id="home"><div class="container">';
}

function grindctrl_wrapper_end() {
    echo '</div></main>';
}

// Add admin menu for webhook configuration
function grindctrl_admin_menu() {
    add_theme_page(
        'GrindCTRL Settings',
        'GrindCTRL Settings', 
        'manage_options',
        'grindctrl-settings',
        'grindctrl_settings_page'
    );
}
add_action('admin_menu', 'grindctrl_admin_menu');

function grindctrl_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('grindctrl_webhook_url', sanitize_url($_POST['webhook_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    ?>
    <div class="wrap">
        <h1>GrindCTRL Settings</h1>
        
        <h2>Webhook Configuration</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">n8n Webhook URL</th>
                    <td>
                        <input type="url" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>" class="regular-text" />
                        <p class="description">Enter your n8n webhook URL for order processing</p>
                        <?php if (!empty($webhook_url)): ?>
                            <button type="button" id="test-webhook" class="button button-secondary">Test Webhook</button>
                            <div id="webhook-test-result" style="margin-top: 10px;"></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <h2>Excel Column Mapping</h2>
        <p>Your orders will be sent to the webhook with these Excel columns:</p>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Excel Column</th>
                    <th>WooCommerce Source</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Order ID</td><td>Order Number</td><td>Unique order identifier</td></tr>
                <tr><td>Customer Name</td><td>Billing Name</td><td>First + Last name</td></tr>
                <tr><td>Phone</td><td>Billing Phone</td><td>Customer phone number</td></tr>
                <tr><td>City</td><td>Billing City</td><td>Customer city</td></tr>
                <tr><td>Address</td><td>Billing Address</td><td>Complete address</td></tr>
                <tr><td>COD Amount</td><td>Order Total (if COD)</td><td>Amount for COD payments only</td></tr>
                <tr><td>Tracking Number</td><td>Order Meta</td><td>Auto-generated tracking ID</td></tr>
                <tr><td>Courier</td><td>Order Meta</td><td>Shipping courier (default: BOSTA)</td></tr>
                <tr><td>Total</td><td>Order Total</td><td>Complete order amount</td></tr>
                <tr><td>Date</td><td>Order Date</td><td>ISO format date/time</td></tr>
                <tr><td>Status</td><td>Order Status</td><td>Mapped to your status names</td></tr>
                <tr><td>Payment Method</td><td>Payment Gateway</td><td>Selected payment method</td></tr>
                <tr><td>Product</td><td>Product + Variation</td><td>Product name with size</td></tr>
                <tr><td>Quantity</td><td>Item Quantity</td><td>Number of items</td></tr>
            </tbody>
        </table>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#test-webhook').click(function() {
            var button = $(this);
            var result = $('#webhook-test-result');
            
            button.prop('disabled', true).text('Testing...');
            result.html('');
            
            $.post(ajaxurl, {
                action: 'grindctrl_test_webhook',
                nonce: '<?php echo wp_create_nonce('grindctrl_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    result.html('<div class="notice notice-success inline"><p>' + response.data + '</p></div>');
                } else {
                    result.html('<div class="notice notice-error inline"><p>' + response.data + '</p></div>');
                }
                button.prop('disabled', false).text('Test Webhook');
            });
        });
    });
    </script>
    <?php
}

// Customize WooCommerce single product layout to match original design
function grindctrl_single_product_layout() {
    if (is_product()) {
        // Remove default single product hooks that don't match our design
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        
        // Customize product title
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        add_action('woocommerce_single_product_summary', 'grindctrl_custom_product_title', 5);
    }
}
add_action('wp', 'grindctrl_single_product_layout');

function grindctrl_custom_product_title() {
    global $product;
    echo '<h1 class="product-title">' . get_the_title() . '</h1>';
    echo '<p class="product-subtitle">Minimal. Premium cotton. Built for grind.</p>';
}

// Add custom fields to product admin
function grindctrl_product_custom_fields() {
    echo '<div class="options_group">';
    
    woocommerce_wp_text_input(array(
        'id' => '_product_subtitle',
        'label' => 'Product Subtitle',
        'description' => 'Enter the product subtitle that appears below the title',
    ));
    
    woocommerce_wp_textarea_input(array(
        'id' => '_material_description',
        'label' => 'Material Description',
        'description' => 'Describe the material composition and quality',
        'rows' => 3,
    ));
    
    woocommerce_wp_textarea_input(array(
        'id' => '_care_instructions',
        'label' => 'Care Instructions',
        'description' => 'How to care for this product',
        'rows' => 3,
    ));
    
    woocommerce_wp_textarea_input(array(
        'id' => '_fit_description',
        'label' => 'Fit Description',
        'description' => 'Describe the fit and sizing',
        'rows' => 3,
    ));
    
    woocommerce_wp_textarea_input(array(
        'id' => '_shipping_info',
        'label' => 'Shipping Information',
        'description' => 'Custom shipping details for this product',
        'rows' => 3,
    ));
    
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'grindctrl_product_custom_fields');

// Save custom fields
function grindctrl_save_product_custom_fields($post_id) {
    $fields = [
        '_product_subtitle',
        '_material_description', 
        '_care_instructions',
        '_fit_description',
        '_shipping_info'
    ];
    
    foreach ($fields as $field) {
        $value = isset($_POST[$field]) ? sanitize_textarea_field($_POST[$field]) : '';
        update_post_meta($post_id, $field, $value);
    }
}
add_action('woocommerce_process_product_meta', 'grindctrl_save_product_custom_fields');

// Widget areas
function grindctrl_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'grindctrl'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'grindctrl_widgets_init');
?>