<?php
/**
 * GrindCTRL Theme Functions - Minimal Version (Emergency Backup)
 * Use this if the main functions.php causes errors
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Basic theme setup
function grindctrl_minimal_setup() {
    // Add basic theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Only add WooCommerce support if plugin exists
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
}
add_action('after_setup_theme', 'grindctrl_minimal_setup');

// Enqueue basic styles and scripts
function grindctrl_minimal_scripts() {
    // Enqueue original styles
    wp_enqueue_style('grindctrl-style', get_template_directory_uri() . '/css/styles.css', array(), '1.0.0');
    
    // Enqueue Google Fonts
    wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Enqueue JavaScript
    wp_enqueue_script('grindctrl-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Basic localization
    wp_localize_script('grindctrl-main', 'grindctrl_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('grindctrl_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'grindctrl_minimal_scripts');

// Basic cart functionality (only if WooCommerce exists)
if (class_exists('WooCommerce')) {
    // Add cart to header
    function grindctrl_minimal_cart_link() {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = wc_get_cart_url();
        
        echo '<div class="cart-icon">';
        echo '<a href="' . esc_url($cart_url) . '">';
        echo '<i class="fas fa-shopping-cart"></i>';
        echo '<span class="cart-count" id="cartCount">' . esc_html($cart_count) . '</span>';
        echo '</a>';
        echo '</div>';
    }

    // Remove default WooCommerce styles
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // Basic WooCommerce customizations
    function grindctrl_minimal_wc_setup() {
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        
        add_action('woocommerce_before_main_content', 'grindctrl_minimal_wrapper_start', 10);
        add_action('woocommerce_after_main_content', 'grindctrl_minimal_wrapper_end', 10);
    }
    add_action('init', 'grindctrl_minimal_wc_setup');

    function grindctrl_minimal_wrapper_start() {
        echo '<main class="main" id="home"><div class="container">';
    }

    function grindctrl_minimal_wrapper_end() {
        echo '</div></main>';
    }
}

// Basic admin settings
function grindctrl_minimal_admin_menu() {
    add_theme_page(
        'GrindCTRL Settings',
        'GrindCTRL Settings', 
        'manage_options',
        'grindctrl-settings',
        'grindctrl_minimal_settings_page'
    );
}
add_action('admin_menu', 'grindctrl_minimal_admin_menu');

function grindctrl_minimal_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('grindctrl_webhook_url', sanitize_url($_POST['webhook_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    ?>
    <div class="wrap">
        <h1>GrindCTRL Settings</h1>
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
            <p><strong>WooCommerce Not Detected!</strong></p>
            <p>Please install and activate the WooCommerce plugin to use all theme features.</p>
            <p><a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" class="button">Install WooCommerce</a></p>
        </div>
        <?php endif; ?>
        
        <h3>Theme Status</h3>
        <ul>
            <li>WordPress Version: <?php echo get_bloginfo('version'); ?></li>
            <li>PHP Version: <?php echo phpversion(); ?></li>
            <li>WooCommerce: <?php echo class_exists('WooCommerce') ? 'Active ✅' : 'Not Installed ❌'; ?></li>
            <li>Theme Files: <?php echo file_exists(get_template_directory() . '/css/styles.css') ? 'Present ✅' : 'Missing ❌'; ?></li>
        </ul>
    </div>
    <?php
}

// Widget areas
function grindctrl_minimal_widgets_init() {
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
add_action('widgets_init', 'grindctrl_minimal_widgets_init');

// Error handling
function grindctrl_minimal_error_handler() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>GrindCTRL Theme:</strong> WooCommerce plugin is required. <a href="' . admin_url('plugin-install.php?s=woocommerce&tab=search&type=term') . '">Install WooCommerce</a></p></div>';
        });
    }
}
add_action('admin_init', 'grindctrl_minimal_error_handler');
?>