<?php
/**
 * GrindCTRL Theme Functions and Definitions
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('GRINDCTRL_VERSION', '1.0.0');
define('GRINDCTRL_THEME_DIR', get_template_directory());
define('GRINDCTRL_THEME_URI', get_template_directory_uri());

/**
 * Include theme setup and customizations
 */
require_once GRINDCTRL_THEME_DIR . '/inc/theme-setup.php';
require_once GRINDCTRL_THEME_DIR . '/inc/enqueue-scripts.php';
require_once GRINDCTRL_THEME_DIR . '/inc/woocommerce-customizations.php';
require_once GRINDCTRL_THEME_DIR . '/inc/custom-post-types.php';

/**
 * After setup theme hook
 */
function grindctrl_after_setup_theme(): void {
    // Make theme available for translation
    load_theme_textdomain('grindctrl', GRINDCTRL_THEME_DIR . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Enqueue editor styles
    add_editor_style('assets/css/editor-style.css');

    // Add support for wide alignment
    add_theme_support('align-wide');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'grindctrl'),
        'footer'  => esc_html__('Footer Menu', 'grindctrl'),
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Custom image sizes
    add_image_size('grindctrl-product-large', 800, 800, true);
    add_image_size('grindctrl-product-thumbnail', 300, 300, true);
    add_image_size('grindctrl-hero', 1920, 800, true);
}
add_action('after_setup_theme', 'grindctrl_after_setup_theme');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet
 */
function grindctrl_content_width(): void {
    $GLOBALS['content_width'] = apply_filters('grindctrl_content_width', 1200);
}
add_action('after_setup_theme', 'grindctrl_content_width', 0);

/**
 * Register widget areas
 */
function grindctrl_widgets_init(): void {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'grindctrl'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer 1', 'grindctrl'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer 2', 'grindctrl'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer 3', 'grindctrl'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'grindctrl'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'grindctrl_widgets_init');

/**
 * Custom excerpt length
 */
function grindctrl_excerpt_length(int $length): int {
    return 25;
}
add_filter('excerpt_length', 'grindctrl_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function grindctrl_excerpt_more(string $more): string {
    return '...';
}
add_filter('excerpt_more', 'grindctrl_excerpt_more');

/**
 * Security enhancements
 */
function grindctrl_security_headers(): void {
    // Remove WordPress version from head
    remove_action('wp_head', 'wp_generator');
    
    // Remove version from RSS feeds
    add_filter('the_generator', '__return_empty_string');
}
add_action('init', 'grindctrl_security_headers');

/**
 * Performance optimizations
 */
function grindctrl_performance_optimizations(): void {
    // Remove emoji scripts if not needed
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'grindctrl_performance_optimizations');

/**
 * Add preconnect for Google Fonts
 */
function grindctrl_resource_hints(array $urls, string $relation_type): array {
    if (wp_style_is('grindctrl-fonts', 'queue') && 'preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'grindctrl_resource_hints', 10, 2);

/**
 * Custom body classes
 */
function grindctrl_body_classes(array $classes): array {
    // Add class for WooCommerce pages
    if (class_exists('WooCommerce')) {
        if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
            $classes[] = 'woocommerce-page';
        }
    }
    
    // Add class for mobile detection
    if (wp_is_mobile()) {
        $classes[] = 'mobile-device';
    }
    
    return $classes;
}
add_filter('body_class', 'grindctrl_body_classes');
