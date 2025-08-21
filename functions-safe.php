<?php
/**
 * GrindCTRL Theme Functions - SAFE VERSION
 * This version includes extensive error checking to prevent critical errors
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Safety check for WordPress functions
if (!function_exists('add_action') || !function_exists('wp_enqueue_style')) {
    return;
}

// Theme setup with error handling
function grindctrl_safe_setup() {
    try {
        // Add basic WordPress theme support
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
        add_theme_support('customize-selective-refresh-widgets');
        
        // Only add WooCommerce support if plugin is active
        if (class_exists('WooCommerce')) {
            add_theme_support('woocommerce');
            add_theme_support('wc-product-gallery-zoom');
            add_theme_support('wc-product-gallery-lightbox');
            add_theme_support('wc-product-gallery-slider');
        }
        
        // Register navigation menus
        register_nav_menus(array(
            'primary' => esc_html__('Primary Menu', 'grindctrl'),
        ));
        
    } catch (Exception $e) {
        error_log('GrindCTRL Theme Setup Error: ' . $e->getMessage());
    }
}
add_action('after_setup_theme', 'grindctrl_safe_setup');

// Enqueue styles and scripts safely
function grindctrl_safe_enqueue_scripts() {
    try {
        // Enqueue original styles
        $css_file = get_template_directory_uri() . '/css/styles.css';
        if (file_exists(get_template_directory() . '/css/styles.css')) {
            wp_enqueue_style('grindctrl-style', $css_file, array(), '1.0.0');
        }
        
        // Enqueue Google Fonts
        wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
        
        // Enqueue Font Awesome
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        
        // Enqueue JavaScript
        $js_file = get_template_directory_uri() . '/js/main.js';
        if (file_exists(get_template_directory() . '/js/main.js')) {
            wp_enqueue_script('grindctrl-main', $js_file, array('jquery'), '1.0.0', true);
            
            // Localize script with safe data
            wp_localize_script('grindctrl-main', 'grindctrl_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('grindctrl_nonce'),
                'webhook_url' => get_option('grindctrl_webhook_url', ''),
            ));
        }
        
    } catch (Exception $e) {
        error_log('GrindCTRL Enqueue Error: ' . $e->getMessage());
    }
}
add_action('wp_enqueue_scripts', 'grindctrl_safe_enqueue_scripts');

// Safe WooCommerce cart link
function grindctrl_safe_cart_link() {
    if (class_exists('WooCommerce') && function_exists('WC') && WC()->cart) {
        try {
            $cart_count = WC()->cart->get_cart_contents_count();
            $cart_url = wc_get_cart_url();
            
            echo '<div class="cart-icon">';
            echo '<a href="' . esc_url($cart_url) . '">';
            echo '<i class="fas fa-shopping-cart"></i>';
            echo '<span class="cart-count" id="cartCount">' . esc_html($cart_count) . '</span>';
            echo '</a>';
            echo '</div>';
        } catch (Exception $e) {
            // Fallback cart icon
            echo '<div class="cart-icon">';
            echo '<i class="fas fa-shopping-cart"></i>';
            echo '<span class="cart-count" id="cartCount">0</span>';
            echo '</div>';
        }
    } else {
        // Static cart icon when WooCommerce is not available
        echo '<div class="cart-icon">';
        echo '<i class="fas fa-shopping-cart"></i>';
        echo '<span class="cart-count" id="cartCount">0</span>';
        echo '</div>';
    }
}

// Safe AJAX cart update
function grindctrl_safe_update_cart_count() {
    try {
        if (class_exists('WooCommerce') && function_exists('WC') && WC()->cart) {
            wp_die(WC()->cart->get_cart_contents_count());
        }
        wp_die(0);
    } catch (Exception $e) {
        wp_die(0);
    }
}
add_action('wp_ajax_grindctrl_update_cart_count', 'grindctrl_safe_update_cart_count');
add_action('wp_ajax_nopriv_grindctrl_update_cart_count', 'grindctrl_safe_update_cart_count');

// Safe WooCommerce customizations
function grindctrl_safe_woocommerce_setup() {
    if (class_exists('WooCommerce')) {
        try {
            // Remove default WooCommerce styles
            add_filter('woocommerce_enqueue_styles', '__return_empty_array');
            
            // Customize WooCommerce wrappers
            remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
            remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
            
            add_action('woocommerce_before_main_content', 'grindctrl_safe_wrapper_start', 10);
            add_action('woocommerce_after_main_content', 'grindctrl_safe_wrapper_end', 10);
            
        } catch (Exception $e) {
            error_log('GrindCTRL WooCommerce Setup Error: ' . $e->getMessage());
        }
    }
}
add_action('init', 'grindctrl_safe_woocommerce_setup');

function grindctrl_safe_wrapper_start() {
    echo '<main class="main" id="home"><div class="container">';
}

function grindctrl_safe_wrapper_end() {
    echo '</div></main>';
}

// Safe admin menu
function grindctrl_safe_admin_menu() {
    try {
        add_theme_page(
            'GrindCTRL Settings',
            'GrindCTRL Settings', 
            'manage_options',
            'grindctrl-settings',
            'grindctrl_safe_settings_page'
        );
    } catch (Exception $e) {
        error_log('GrindCTRL Admin Menu Error: ' . $e->getMessage());
    }
}
add_action('admin_menu', 'grindctrl_safe_admin_menu');

function grindctrl_safe_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('grindctrl_webhook_url', sanitize_url($_POST['webhook_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    ?>
    <div class="wrap">
        <h1>GrindCTRL Settings</h1>
        
        <div class="notice notice-info">
            <p><strong>Theme Status:</strong></p>
            <ul>
                <li>WordPress: <?php echo get_bloginfo('version'); ?> ✅</li>
                <li>PHP: <?php echo phpversion(); ?> <?php echo version_compare(phpversion(), '7.4', '>=') ? '✅' : '❌ (Requires 7.4+)'; ?></li>
                <li>WooCommerce: <?php echo class_exists('WooCommerce') ? 'Active ✅' : 'Not Installed ❌'; ?></li>
            </ul>
        </div>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">n8n Webhook URL</th>
                    <td>
                        <input type="url" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>" class="regular-text" />
                        <p class="description">Enter your n8n webhook URL for order processing</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <?php if (!class_exists('WooCommerce')): ?>
        <div class="notice notice-warning">
            <h3>WooCommerce Required</h3>
            <p>This theme requires WooCommerce plugin for full functionality.</p>
            <p><a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" class="button button-primary">Install WooCommerce</a></p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// Safe widget areas
function grindctrl_safe_widgets_init() {
    try {
        register_sidebar(array(
            'name'          => esc_html__('Sidebar', 'grindctrl'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'grindctrl'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
    } catch (Exception $e) {
        error_log('GrindCTRL Widgets Error: ' . $e->getMessage());
    }
}
add_action('widgets_init', 'grindctrl_safe_widgets_init');

// Include additional files safely
function grindctrl_safe_includes() {
    $includes_dir = get_template_directory() . '/includes/';
    
    // Include webhook integration if file exists
    if (file_exists($includes_dir . 'webhook-integration.php')) {
        try {
            require_once $includes_dir . 'webhook-integration.php';
        } catch (Exception $e) {
            error_log('GrindCTRL Webhook Include Error: ' . $e->getMessage());
        }
    }
    
    // Include customizations if file exists
    if (file_exists($includes_dir . 'customizations.php')) {
        try {
            require_once $includes_dir . 'customizations.php';
        } catch (Exception $e) {
            error_log('GrindCTRL Customizations Include Error: ' . $e->getMessage());
        }
    }
}
add_action('after_setup_theme', 'grindctrl_safe_includes', 20);

// Error handling for missing dependencies
function grindctrl_safe_admin_notices() {
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>GrindCTRL Theme:</strong> For full functionality, please install the WooCommerce plugin. ';
        echo '<a href="' . admin_url('plugin-install.php?s=woocommerce&tab=search&type=term') . '">Install WooCommerce</a></p>';
        echo '</div>';
    }
    
    if (version_compare(phpversion(), '7.4', '<')) {
        echo '<div class="notice notice-error">';
        echo '<p><strong>GrindCTRL Theme:</strong> This theme requires PHP 7.4 or higher. Current version: ' . phpversion() . '</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'grindctrl_safe_admin_notices');
?>