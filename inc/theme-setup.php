<?php
/**
 * Theme Setup Functions
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set up theme defaults and register support for various WordPress features
 */
function grindctrl_theme_setup(): void {
    // Add theme support for WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Add support for post formats
    add_theme_support('post-formats', array(
        'aside',
        'gallery',
        'link',
        'image',
        'quote',
        'status',
        'video',
        'audio',
        'chat'
    ));

    // Add support for starter content
    add_theme_support('starter-content', array(
        'widgets' => array(
            'footer-1' => array(
                'text_about',
            ),
            'footer-2' => array(
                'text_business_info',
            ),
            'footer-3' => array(
                'text_contact',
            ),
        ),
        'posts' => array(
            'home',
            'about' => array(
                'thumbnail' => '{{image-sandwich}}',
            ),
            'contact' => array(
                'thumbnail' => '{{image-espresso}}',
            ),
            'blog' => array(
                'thumbnail' => '{{image-coffee}}',
            ),
        ),
        'nav_menus' => array(
            'primary' => array(
                'name' => esc_html__('Primary', 'grindctrl'),
                'items' => array(
                    'link_home',
                    'page_about',
                    'page_blog',
                    'page_contact',
                ),
            ),
            'footer' => array(
                'name' => esc_html__('Footer', 'grindctrl'),
                'items' => array(
                    'link_privacy',
                    'link_terms',
                    'link_returns',
                    'page_contact',
                ),
            ),
        ),
        'options' => array(
            'show_on_front'  => 'page',
            'page_on_front'  => '{{home}}',
            'page_for_posts' => '{{blog}}',
        ),
    ));

    // Custom header support
    add_theme_support('custom-header', array(
        'default-image'      => '',
        'default-text-color' => 'ffffff',
        'width'              => 1920,
        'height'             => 400,
        'flex-width'         => true,
        'flex-height'        => true,
    ));

    // Custom background support
    add_theme_support('custom-background', array(
        'default-color'      => '1a1a1a',
        'default-image'      => '',
        'default-preset'     => 'default',
        'default-position-x' => 'left',
        'default-position-y' => 'top',
        'default-size'       => 'auto',
        'default-repeat'     => 'repeat',
        'default-attachment' => 'scroll',
    ));
}
add_action('after_setup_theme', 'grindctrl_theme_setup');

/**
 * Default menu fallback
 */
function grindctrl_default_menu(): void {
    ?>
    <ul class="nav-menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'grindctrl'); ?></a></li>
        <?php if (class_exists('WooCommerce')) : ?>
            <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Shop', 'grindctrl'); ?></a></li>
        <?php endif; ?>
        <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><?php esc_html_e('Blog', 'grindctrl'); ?></a></li>
    </ul>
    <?php
}

/**
 * Customize the excerpt for blog posts
 */
function grindctrl_custom_excerpt(string $excerpt): string {
    if (is_admin()) {
        return $excerpt;
    }

    // Only modify on blog/archive pages
    if (is_home() || is_archive() || is_search()) {
        $excerpt_length = 150; // characters
        if (strlen($excerpt) > $excerpt_length) {
            $excerpt = substr($excerpt, 0, $excerpt_length);
            $excerpt = substr($excerpt, 0, strrpos($excerpt, ' '));
            $excerpt .= '...';
        }
    }

    return $excerpt;
}
add_filter('get_the_excerpt', 'grindctrl_custom_excerpt');

/**
 * Add custom classes to body tag
 */
function grindctrl_custom_body_classes(array $classes): array {
    // Add page slug
    if (is_page()) {
        global $post;
        $classes[] = 'page-' . $post->post_name;
    }

    // Add template name
    $template = get_page_template_slug();
    if ($template) {
        $classes[] = 'template-' . sanitize_html_class(str_replace('.php', '', basename($template)));
    }

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
add_filter('body_class', 'grindctrl_custom_body_classes');

/**
 * Customize theme login page
 */
function grindctrl_custom_login_logo(): void {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        ?>
        <style type="text/css">
            #login h1 a,
            .login h1 a {
                background-image: url('<?php echo esc_url($logo[0]); ?>');
                background-size: contain;
                background-repeat: no-repeat;
                padding-bottom: 10px;
                width: 100%;
                height: 80px;
            }
        </style>
        <?php
    }
}
add_action('login_enqueue_scripts', 'grindctrl_custom_login_logo');

/**
 * Change login logo URL
 */
function grindctrl_login_logo_url(): string {
    return home_url('/');
}
add_filter('login_headerurl', 'grindctrl_login_logo_url');

/**
 * Change login logo title
 */
function grindctrl_login_logo_url_title(): string {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'grindctrl_login_logo_url_title');
