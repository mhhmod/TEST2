<?php
/**
 * WooCommerce Customizations
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Declare WooCommerce support
 */
function grindctrl_woocommerce_support(): void {
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 400,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ),
    ));
}
add_action('after_setup_theme', 'grindctrl_woocommerce_support');

/**
 * Remove WooCommerce default styles
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Customize WooCommerce wrapper
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

function grindctrl_woocommerce_wrapper_start(): void {
    echo '<div class="woocommerce-content">';
}

function grindctrl_woocommerce_wrapper_end(): void {
    echo '</div>';
}

add_action('woocommerce_before_main_content', 'grindctrl_woocommerce_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'grindctrl_woocommerce_wrapper_end', 10);

/**
 * Customize WooCommerce sidebar
 */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/**
 * Change number of products per row
 */
function grindctrl_products_per_row(): int {
    return 3;
}
add_filter('loop_shop_columns', 'grindctrl_products_per_row');

/**
 * Change number of products per page
 */
function grindctrl_products_per_page(): int {
    return 12;
}
add_filter('loop_shop_per_page', 'grindctrl_products_per_page');

/**
 * Customize product thumbnails on shop page
 */
function grindctrl_single_product_image_thumbnail_html(string $html, int $post_thumbnail_id): string {
    $full_size_image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
    $thumbnail       = wp_get_attachment_image_src($post_thumbnail_id, 'grindctrl-product-thumbnail');
    $attributes      = array(
        'title'                   => get_post_field('post_title', $post_thumbnail_id),
        'data-caption'            => get_post_field('post_excerpt', $post_thumbnail_id),
        'data-src'                => esc_url($full_size_image[0]),
        'data-large_image'        => esc_url($full_size_image[0]),
        'data-large_image_width'  => esc_attr($full_size_image[1]),
        'data-large_image_height' => esc_attr($full_size_image[2]),
        'class'                   => 'wp-post-image',
    );

    if ($thumbnail) {
        $html = sprintf(
            '<img src="%s" alt="%s" %s />',
            esc_url($thumbnail[0]),
            esc_attr(get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true)),
            wc_implode_html_attributes($attributes)
        );
    }

    return $html;
}
add_filter('woocommerce_single_product_image_thumbnail_html', 'grindctrl_single_product_image_thumbnail_html', 10, 2);

/**
 * Customize add to cart button text
 */
function grindctrl_add_to_cart_text(string $text, WC_Product $product): string {
    if ($product->is_type('simple')) {
        return esc_html__('Add to Cart', 'grindctrl');
    }
    
    if ($product->is_type('variable')) {
        return esc_html__('Select Options', 'grindctrl');
    }
    
    if ($product->is_type('grouped')) {
        return esc_html__('View Products', 'grindctrl');
    }
    
    if ($product->is_type('external')) {
        return esc_html__('Buy Product', 'grindctrl');
    }
    
    return $text;
}
add_filter('woocommerce_product_add_to_cart_text', 'grindctrl_add_to_cart_text', 10, 2);

/**
 * Customize single product tabs
 */
function grindctrl_product_tabs(array $tabs): array {
    // Reorder tabs
    $tabs['description']['priority'] = 10;
    $tabs['additional_information']['priority'] = 20;
    $tabs['reviews']['priority'] = 30;

    // Customize tab titles
    $tabs['description']['title'] = esc_html__('Product Details', 'grindctrl');
    $tabs['additional_information']['title'] = esc_html__('Specifications', 'grindctrl');

    return $tabs;
}
add_filter('woocommerce_product_tabs', 'grindctrl_product_tabs');

/**
 * Add custom product badges
 */
function grindctrl_product_badges(): void {
    global $product;

    $badges = array();

    // Sale badge
    if ($product->is_on_sale()) {
        $percentage = '';
        if ($product->get_type() === 'simple' || $product->get_type() === 'external') {
            $regular_price = (float) $product->get_regular_price();
            $sale_price = (float) $product->get_sale_price();
            if ($regular_price > 0) {
                $percentage = round(((($regular_price - $sale_price) / $regular_price) * 100));
                $percentage = sprintf('-%s%%', $percentage);
            }
        }
        $badges[] = sprintf('<span class="badge badge-sale">%s %s</span>', 
            esc_html__('Sale', 'grindctrl'), 
            $percentage
        );
    }

    // New badge (products created in last 30 days)
    $created = strtotime($product->get_date_created());
    if ($created > strtotime('-30 days')) {
        $badges[] = sprintf('<span class="badge badge-new">%s</span>', esc_html__('New', 'grindctrl'));
    }

    // Out of stock badge
    if (!$product->is_in_stock()) {
        $badges[] = sprintf('<span class="badge badge-out-of-stock">%s</span>', esc_html__('Out of Stock', 'grindctrl'));
    }

    if (!empty($badges)) {
        echo '<div class="product-badges">' . implode('', $badges) . '</div>';
    }
}
add_action('woocommerce_before_single_product_summary', 'grindctrl_product_badges', 15);
add_action('woocommerce_before_shop_loop_item_title', 'grindctrl_product_badges', 15);

/**
 * Customize cart fragments for AJAX
 */
function grindctrl_cart_count_fragments(array $fragments): array {
    if (!class_exists('WooCommerce') || !WC()->cart) {
        return $fragments;
    }
    
    $cart_count = WC()->cart->get_cart_contents_count();
    
    $fragments['#cartCount'] = '<span class="cart-count" id="cartCount">' . esc_html($cart_count) . '</span>';
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'grindctrl_cart_count_fragments');

/**
 * Add AJAX endpoint for cart count
 */
function grindctrl_get_cart_count(): void {
    if (!class_exists('WooCommerce') || !WC()->cart) {
        wp_send_json(array('count' => 0));
        return;
    }
    
    $cart_count = WC()->cart->get_cart_contents_count();
    wp_send_json(array('count' => $cart_count));
}
add_action('wp_ajax_get_cart_count', 'grindctrl_get_cart_count');
add_action('wp_ajax_nopriv_get_cart_count', 'grindctrl_get_cart_count');

/**
 * Customize WooCommerce breadcrumbs
 */
function grindctrl_woocommerce_breadcrumbs(array $args): array {
    $args['delimiter']   = ' <span class="breadcrumb-separator">/</span> ';
    $args['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'grindctrl') . '">';
    $args['wrap_after']  = '</nav>';
    $args['before']      = '<span class="breadcrumb-item">';
    $args['after']       = '</span>';
    $args['home']        = esc_html__('Home', 'grindctrl');

    return $args;
}
add_filter('woocommerce_breadcrumb_defaults', 'grindctrl_woocommerce_breadcrumbs');

/**
 * Customize checkout fields
 */
function grindctrl_checkout_fields(array $fields): array {
    // Add placeholder text
    $fields['billing']['billing_first_name']['placeholder'] = esc_html__('Enter your first name', 'grindctrl');
    $fields['billing']['billing_last_name']['placeholder'] = esc_html__('Enter your last name', 'grindctrl');
    $fields['billing']['billing_email']['placeholder'] = esc_html__('Enter your email address', 'grindctrl');
    $fields['billing']['billing_phone']['placeholder'] = esc_html__('Enter your phone number', 'grindctrl');

    // Reorder fields
    $fields['billing']['billing_email']['priority'] = 25;
    $fields['billing']['billing_phone']['priority'] = 30;

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'grindctrl_checkout_fields');

/**
 * Add security headers for WooCommerce pages
 */
function grindctrl_woocommerce_security_headers(): void {
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('send_headers', 'grindctrl_woocommerce_security_headers');

/**
 * Optimize WooCommerce performance
 */
function grindctrl_woocommerce_optimizations(): void {
    // Disable cart fragments on non-shop pages
    if (!is_woocommerce() && !is_cart() && !is_checkout()) {
        wp_dequeue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'grindctrl_woocommerce_optimizations', 99);
