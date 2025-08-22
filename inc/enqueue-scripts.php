<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 */
function grindctrl_enqueue_scripts(): void {
    // Theme version for cache busting
    $theme_version = wp_get_theme()->get('Version');
    
    // Google Fonts
    wp_enqueue_style(
        'grindctrl-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    // Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    // Main theme stylesheet
    wp_enqueue_style(
        'grindctrl-style',
        GRINDCTRL_THEME_URI . '/assets/css/theme.css',
        array('grindctrl-fonts'),
        $theme_version
    );

    // Main theme script
    wp_enqueue_script(
        'grindctrl-script',
        GRINDCTRL_THEME_URI . '/assets/js/theme.js',
        array(),
        $theme_version,
        true
    );

    // Conditional WooCommerce scripts
    if (class_exists('WooCommerce')) {
        if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
            wp_enqueue_script(
                'grindctrl-woocommerce',
                GRINDCTRL_THEME_URI . '/assets/js/woocommerce.js',
                array('jquery', 'grindctrl-script'),
                $theme_version,
                true
            );

            // Localize script for AJAX
            wp_localize_script('grindctrl-woocommerce', 'grindctrl_wc_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('grindctrl_wc_nonce'),
            ));
        }
    }

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Customizer preview script
    if (is_customize_preview()) {
        wp_enqueue_script(
            'grindctrl-customizer',
            GRINDCTRL_THEME_URI . '/assets/js/customizer.js',
            array('customize-preview'),
            $theme_version,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'grindctrl_enqueue_scripts');

/**
 * Enqueue admin styles and scripts
 */
function grindctrl_admin_enqueue_scripts(string $hook): void {
    // Only load on specific admin pages
    $allowed_pages = array(
        'post.php',
        'post-new.php',
        'edit.php',
        'customize.php',
        'themes.php'
    );

    if (!in_array($hook, $allowed_pages, true)) {
        return;
    }

    $theme_version = wp_get_theme()->get('Version');

    // Admin styles
    wp_enqueue_style(
        'grindctrl-admin',
        GRINDCTRL_THEME_URI . '/assets/css/admin.css',
        array(),
        $theme_version
    );

    // Block editor styles
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_script(
            'grindctrl-block-editor',
            GRINDCTRL_THEME_URI . '/assets/js/block-editor.js',
            array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
            $theme_version,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'grindctrl_admin_enqueue_scripts');

/**
 * Enqueue block editor assets
 */
function grindctrl_block_editor_assets(): void {
    $theme_version = wp_get_theme()->get('Version');

    // Block editor styles
    wp_enqueue_style(
        'grindctrl-block-editor-styles',
        GRINDCTRL_THEME_URI . '/assets/css/block-editor.css',
        array(),
        $theme_version
    );
}
add_action('enqueue_block_editor_assets', 'grindctrl_block_editor_assets');

/**
 * Add async/defer attributes to scripts
 */
function grindctrl_script_attributes(string $tag, string $handle, string $src): string {
    // Scripts to defer
    $defer_scripts = array(
        'grindctrl-script',
        'grindctrl-woocommerce',
        'font-awesome'
    );

    // Scripts to async load
    $async_scripts = array(
        'grindctrl-fonts'
    );

    if (in_array($handle, $defer_scripts, true)) {
        $tag = str_replace(' src', ' defer="defer" src', $tag);
    }

    if (in_array($handle, $async_scripts, true)) {
        $tag = str_replace(' src', ' async="async" src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'grindctrl_script_attributes', 10, 3);

/**
 * Preload critical assets
 */
function grindctrl_preload_assets(): void {
    // Preload critical fonts
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <?php
    // Preload critical CSS
    $theme_version = wp_get_theme()->get('Version');
    ?>
    <link rel="preload" href="<?php echo esc_url(GRINDCTRL_THEME_URI . '/assets/css/theme.css?ver=' . $theme_version); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?php echo esc_url(GRINDCTRL_THEME_URI . '/assets/css/theme.css?ver=' . $theme_version); ?>"></noscript>
    <?php
}
add_action('wp_head', 'grindctrl_preload_assets', 1);

/**
 * Remove unnecessary scripts and styles
 */
function grindctrl_remove_unnecessary_assets(): void {
    // Remove jQuery Migrate if not needed
    if (!is_admin()) {
        wp_deregister_script('jquery-migrate');
    }

    // Remove emoji scripts if not needed
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Remove block library CSS on non-block pages
    if (!is_admin() && !has_blocks()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }
}
add_action('wp_enqueue_scripts', 'grindctrl_remove_unnecessary_assets', 100);

/**
 * Inline critical CSS
 */
function grindctrl_inline_critical_css(): void {
    // Only inline on homepage for better performance
    if (!is_front_page()) {
        return;
    }

    $critical_css = '
        body { margin: 0; font-family: Inter, sans-serif; background: #1a1a1a; color: #fff; }
        .header { position: sticky; top: 0; z-index: 100; background: #1a1a1a; border-bottom: 1px solid #404040; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.5rem; }
        .logo i { color: #E74C3C; }
    ';

    echo "<style id='grindctrl-critical-css'>{$critical_css}</style>";
}
add_action('wp_head', 'grindctrl_inline_critical_css', 2);

/**
 * Add Resource Hints
 */
function grindctrl_resource_hints(array $urls, string $relation_type): array {
    switch ($relation_type) {
        case 'dns-prefetch':
            $urls[] = '//fonts.googleapis.com';
            $urls[] = '//fonts.gstatic.com';
            $urls[] = '//cdnjs.cloudflare.com';
            break;

        case 'preconnect':
            $urls[] = array(
                'href' => 'https://fonts.gstatic.com',
                'crossorigin'
            );
            break;

        case 'prefetch':
            if (is_front_page() && class_exists('WooCommerce')) {
                $urls[] = wc_get_page_permalink('shop');
            }
            break;
    }

    return $urls;
}
add_filter('wp_resource_hints', 'grindctrl_resource_hints', 10, 2);
