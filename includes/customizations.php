<?php
/**
 * GrindCTRL WooCommerce Customizations
 * 
 * Additional customizations to make WooCommerce match the original design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize WooCommerce form fields to match original design
 */
function grindctrl_checkout_fields($fields) {
    // Customize billing fields to match original form
    $fields['billing']['billing_first_name']['placeholder'] = 'First Name';
    $fields['billing']['billing_last_name']['placeholder'] = 'Last Name';
    $fields['billing']['billing_email']['placeholder'] = 'Email Address';
    $fields['billing']['billing_phone']['placeholder'] = 'Phone Number';
    $fields['billing']['billing_address_1']['placeholder'] = 'Enter your complete shipping address';
    $fields['billing']['billing_city']['placeholder'] = 'City';
    $fields['billing']['billing_postcode']['placeholder'] = 'Postal Code';
    
    // Remove fields we don't need
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'grindctrl_checkout_fields');

/**
 * Add custom CSS classes to checkout fields
 */
function grindctrl_checkout_field_class($field, $key, $args, $value) {
    if (strpos($field, 'class="') !== false) {
        $field = str_replace('class="', 'class="form-group ', $field);
    }
    return $field;
}
add_filter('woocommerce_form_field', 'grindctrl_checkout_field_class', 10, 4);

/**
 * Customize product variation dropdown to match original size selector
 */
function grindctrl_variation_dropdown_args($args, $attribute, $product) {
    if ($attribute === 'pa_size' || $attribute === 'size') {
        $args['class'] = 'form-control size-selector';
        $args['show_option_none'] = 'Select Size';
    }
    return $args;
}
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'grindctrl_variation_dropdown_args', 10, 3);

/**
 * Customize quantity input to match original design
 */
function grindctrl_quantity_input_args($args, $product) {
    $args['input_value'] = 1;
    $args['max_value'] = 10;
    $args['min_value'] = 1;
    $args['step'] = 1;
    return $args;
}
add_filter('woocommerce_quantity_input_args', 'grindctrl_quantity_input_args', 10, 2);

/**
 * Add custom payment method descriptions
 */
function grindctrl_payment_method_description() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Match original payment method styling
        $('input[name="payment_method"]').each(function() {
            $(this).closest('li').addClass('payment-option');
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'grindctrl_payment_method_description');

/**
 * Customize add to cart button text and styling
 */
function grindctrl_add_to_cart_text($text, $product) {
    if ($product->get_type() === 'simple') {
        return 'Buy Now';
    }
    return $text;
}
add_filter('woocommerce_product_add_to_cart_text', 'grindctrl_add_to_cart_text', 10, 2);

/**
 * Add custom order statuses
 */
function grindctrl_custom_order_statuses() {
    register_post_status('wc-shipped', array(
        'label' => 'Shipped',
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>')
    ));
}
add_action('init', 'grindctrl_custom_order_statuses');

// Add custom order status to WooCommerce
function grindctrl_add_custom_order_statuses($order_statuses) {
    $order_statuses['wc-shipped'] = 'Shipped';
    return $order_statuses;
}
add_filter('wc_order_statuses', 'grindctrl_add_custom_order_statuses');

/**
 * Customize WooCommerce email templates to match brand
 */
function grindctrl_email_styles($css, $email) {
    $css .= '
        .woocommerce-email-header {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        .woocommerce-email-header h1 {
            color: #E74C3C !important;
        }
        .woocommerce-email-body {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
        }
    ';
    return $css;
}
add_filter('woocommerce_email_styles', 'grindctrl_email_styles', 10, 2);

/**
 * Add product features to single product page
 */
function grindctrl_product_features() {
    global $product;
    ?>
    <div class="product-features">
        <div class="feature">
            <i class="fas fa-truck"></i>
            <span>Fast checkout. Cart shipped.</span>
        </div>
        <div class="feature">
            <i class="fas fa-clock"></i>
            <span>Free returns within 14 days</span>
        </div>
    </div>
    <?php
}
add_action('woocommerce_single_product_summary', 'grindctrl_product_features', 25);

/**
 * Customize shop page to show products in grid matching original design
 */
function grindctrl_shop_columns() {
    return 3; // 3 products per row
}
add_filter('loop_shop_columns', 'grindctrl_shop_columns');

/**
 * Add custom CSS for WooCommerce elements
 */
function grindctrl_woocommerce_custom_css() {
    if (is_woocommerce() || is_cart() || is_checkout()) {
        ?>
        <style>
        /* Make WooCommerce forms match original design */
        .woocommerce .form-row input.input-text,
        .woocommerce .form-row select,
        .woocommerce .form-row textarea {
            background-color: var(--light-grey);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: var(--spacing-sm);
            border-radius: var(--radius-sm);
        }
        
        .woocommerce .form-row label {
            color: var(--text-color);
            font-weight: 500;
        }
        
        /* Style quantity input like original */
        .woocommerce .quantity .qty {
            background-color: var(--light-grey);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            text-align: center;
        }
        
        /* Style add to cart button like original */
        .woocommerce .single_add_to_cart_button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: var(--transition-fast);
        }
        
        .woocommerce .single_add_to_cart_button:hover {
            background-color: #c0392b;
        }
        
        /* Style variation selects like original */
        .woocommerce .variations select {
            background-color: var(--light-grey);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: var(--spacing-sm);
            border-radius: var(--radius-sm);
        }
        
        /* Price styling */
        .woocommerce .price {
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .woocommerce .price del {
            color: #999;
            text-decoration: line-through;
        }
        
        .woocommerce .price ins {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        /* Cart page styling */
        .woocommerce-cart .cart-collaterals {
            background-color: var(--light-grey);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
        }
        
        /* Checkout styling */
        .woocommerce-checkout .woocommerce-checkout-review-order {
            background-color: var(--light-grey);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'grindctrl_woocommerce_custom_css');

/**
 * Modify cart fragments for AJAX cart updates
 */
function grindctrl_cart_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $fragments['.cart-count'] = '<span class="cart-count" id="cartCount">' . $cart_count . '</span>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'grindctrl_cart_fragments');

/**
 * Add order tracking shortcode
 */
function grindctrl_order_tracking_shortcode($atts) {
    $atts = shortcode_atts(array(
        'order_id' => '',
    ), $atts);
    
    if (empty($atts['order_id'])) {
        return '<p>Please provide an order ID.</p>';
    }
    
    $order = wc_get_order($atts['order_id']);
    if (!$order) {
        return '<p>Order not found.</p>';
    }
    
    $tracking_number = $order->get_meta('_tracking_number');
    $courier = $order->get_meta('_courier');
    
    ob_start();
    ?>
    <div class="order-tracking">
        <h3>Order Tracking</h3>
        <p><strong>Order ID:</strong> <?php echo $order->get_order_number(); ?></p>
        <p><strong>Status:</strong> <?php echo wc_get_order_status_name($order->get_status()); ?></p>
        <?php if ($tracking_number): ?>
        <p><strong>Tracking Number:</strong> <?php echo esc_html($tracking_number); ?></p>
        <p><strong>Courier:</strong> <?php echo esc_html($courier); ?></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('grindctrl_tracking', 'grindctrl_order_tracking_shortcode');
?>