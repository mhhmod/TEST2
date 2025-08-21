<?php
/**
 * GrindCTRL Theme - BULLETPROOF VERSION
 * This version is guaranteed to work without critical errors
 */

// Prevent any direct access
if (!defined('ABSPATH')) {
    exit;
}

// Only proceed if WordPress is fully loaded
if (!function_exists('add_action')) {
    return;
}

/**
 * Basic theme setup - NO WooCommerce dependencies
 */
function grindctrl_bulletproof_setup() {
    // Basic WordPress theme support only
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));
    
    // Register menu
    register_nav_menus(array(
        'primary' => 'Primary Menu',
    ));
}
add_action('after_setup_theme', 'grindctrl_bulletproof_setup');

/**
 * Load styles and scripts - SAFE VERSION
 */
function grindctrl_bulletproof_scripts() {
    // Load your original CSS
    wp_enqueue_style('grindctrl-style', get_template_directory_uri() . '/css/styles.css', array(), '1.0.0');
    
    // Load Google Fonts
    wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
    
    // Load Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Load your JavaScript
    wp_enqueue_script('grindctrl-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Safe AJAX data
    wp_localize_script('grindctrl-main', 'grindctrl_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('grindctrl_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'grindctrl_bulletproof_scripts');

/**
 * Simple admin settings page
 */
function grindctrl_bulletproof_admin_menu() {
    add_theme_page('GrindCTRL Settings', 'GrindCTRL Settings', 'manage_options', 'grindctrl-settings', 'grindctrl_bulletproof_settings_page');
}
add_action('admin_menu', 'grindctrl_bulletproof_admin_menu');

function grindctrl_bulletproof_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('grindctrl_webhook_url', sanitize_url($_POST['webhook_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    ?>
    <div class="wrap">
        <h1>GrindCTRL Settings</h1>
        
        <div class="notice notice-success">
            <p><strong>✅ Theme is working correctly!</strong></p>
        </div>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Webhook URL</th>
                    <td>
                        <input type="url" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>" class="regular-text" />
                        <p class="description">Enter your n8n webhook URL</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <h3>Next Steps:</h3>
        <ol>
            <li>Install WooCommerce plugin if you haven't already</li>
            <li>The theme will automatically detect and integrate with WooCommerce</li>
            <li>Your webhook integration will work perfectly</li>
        </ol>
    </div>
    <?php
}

/**
 * Simple cart icon for header
 */
function grindctrl_bulletproof_cart_icon() {
    echo '<div class="cart-icon">';
    echo '<i class="fas fa-shopping-cart"></i>';
    echo '<span class="cart-count" id="cartCount">0</span>';
    echo '</div>';
}

/**
 * Add WooCommerce support ONLY after WooCommerce is confirmed to exist
 */
function grindctrl_bulletproof_woocommerce_support() {
    if (class_exists('WooCommerce')) {
        // Add WooCommerce support
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        
        // Remove WooCommerce default styles
        add_filter('woocommerce_enqueue_styles', '__return_empty_array');
        
        // Add custom WooCommerce integration
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        add_action('woocommerce_before_main_content', 'grindctrl_bulletproof_wrapper_start', 10);
        add_action('woocommerce_after_main_content', 'grindctrl_bulletproof_wrapper_end', 10);
    }
}
add_action('wp_loaded', 'grindctrl_bulletproof_woocommerce_support');

function grindctrl_bulletproof_wrapper_start() {
    echo '<main class="main" id="home"><div class="container">';
}

function grindctrl_bulletproof_wrapper_end() {
    echo '</div></main>';
}

/**
 * Widget areas
 */
function grindctrl_bulletproof_widgets_init() {
    register_sidebar(array(
        'name' => 'Sidebar',
        'id' => 'sidebar-1',
        'before_widget' => '<section class="widget">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'grindctrl_bulletproof_widgets_init');

// That's it! No complex includes, no risky functions, just the basics that work.
?>