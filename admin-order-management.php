<?php
/**
 * Admin Order Management System
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to orders admin
 */
function grindctrl_add_order_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;
        
        if ($key === 'order_status') {
            $new_columns['tracking_number'] = __('Tracking Number', 'grindctrl');
            $new_columns['courier'] = __('Courier', 'grindctrl');
            $new_columns['webhook_status'] = __('Webhook Status', 'grindctrl');
        }
    }
    
    return $new_columns;
}
add_filter('manage_edit-shop_order_columns', 'grindctrl_add_order_columns');

/**
 * Display custom column content
 */
function grindctrl_display_order_columns($column, $post_id) {
    switch ($column) {
        case 'tracking_number':
            $tracking = get_post_meta($post_id, '_billing_tracking_number', true);
            echo $tracking ? esc_html($tracking) : '—';
            break;
            
        case 'courier':
            $courier = get_post_meta($post_id, '_billing_courier', true);
            echo $courier ? esc_html($courier) : '—';
            break;
            
        case 'webhook_status':
            $webhook_sent = get_post_meta($post_id, '_webhook_sent', true);
            if ($webhook_sent) {
                echo '<span style="color: green;">✓ Sent</span>';
            } else {
                echo '<span style="color: orange;">⏳ Pending</span>';
            }
            break;
    }
}
add_action('manage_shop_order_posts_custom_column', 'grindctrl_display_order_columns', 10, 2);

/**
 * Add order meta box for tracking information
 */
function grindctrl_add_order_meta_box() {
    add_meta_box(
        'grindctrl_order_tracking',
        __('Order Tracking & Webhook', 'grindctrl'),
        'grindctrl_order_tracking_meta_box',
        'shop_order',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'grindctrl_add_order_meta_box');

/**
 * Order tracking meta box content
 */
function grindctrl_order_tracking_meta_box($post) {
    $order = wc_get_order($post->ID);
    $tracking_number = get_post_meta($post->ID, '_billing_tracking_number', true);
    $courier = get_post_meta($post->ID, '_billing_courier', true);
    $webhook_sent = get_post_meta($post->ID, '_webhook_sent', true);
    $webhook_response = get_post_meta($post->ID, '_webhook_response', true);
    
    wp_nonce_field('grindctrl_order_tracking', 'grindctrl_order_tracking_nonce');
    ?>
    
    <div class="grindctrl-order-tracking">
        <p>
            <label for="tracking_number"><strong><?php _e('Tracking Number:', 'grindctrl'); ?></strong></label><br>
            <input type="text" id="tracking_number" name="tracking_number" value="<?php echo esc_attr($tracking_number); ?>" style="width: 100%;" />
        </p>
        
        <p>
            <label for="courier"><strong><?php _e('Courier Service:', 'grindctrl'); ?></strong></label><br>
            <select id="courier" name="courier" style="width: 100%;">
                <option value=""><?php _e('Select Courier', 'grindctrl'); ?></option>
                <option value="DHL" <?php selected($courier, 'DHL'); ?>>DHL</option>
                <option value="FedEx" <?php selected($courier, 'FedEx'); ?>>FedEx</option>
                <option value="UPS" <?php selected($courier, 'UPS'); ?>>UPS</option>
                <option value="Aramex" <?php selected($courier, 'Aramex'); ?>>Aramex</option>
                <option value="Egypt Post" <?php selected($courier, 'Egypt Post'); ?>>Egypt Post</option>
                <option value="Other" <?php selected($courier, 'Other'); ?>>Other</option>
            </select>
        </p>
        
        <hr>
        
        <p>
            <strong><?php _e('Webhook Status:', 'grindctrl'); ?></strong><br>
            <?php if ($webhook_sent): ?>
                <span style="color: green;">✓ <?php _e('Sent successfully', 'grindctrl'); ?></span>
                <br><small><?php echo esc_html($webhook_sent); ?></small>
            <?php else: ?>
                <span style="color: orange;">⏳ <?php _e('Not sent yet', 'grindctrl'); ?></span>
            <?php endif; ?>
        </p>
        
        <?php if ($webhook_response): ?>
            <p>
                <strong><?php _e('Webhook Response:', 'grindctrl'); ?></strong><br>
                <small><code><?php echo esc_html($webhook_response); ?></code></small>
            </p>
        <?php endif; ?>
        
        <p>
            <button type="button" id="resend-webhook" class="button button-secondary" data-order-id="<?php echo $post->ID; ?>">
                <?php _e('Resend Webhook', 'grindctrl'); ?>
            </button>
        </p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#resend-webhook').on('click', function() {
            var button = $(this);
            var orderId = button.data('order-id');
            
            button.prop('disabled', true).text('Sending...');
            
            $.post(ajaxurl, {
                action: 'grindctrl_resend_webhook',
                order_id: orderId,
                nonce: '<?php echo wp_create_nonce('grindctrl_resend_webhook'); ?>'
            }, function(response) {
                if (response.success) {
                    alert('Webhook sent successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }).always(function() {
                button.prop('disabled', false).text('Resend Webhook');
            });
        });
    });
    </script>
    
    <?php
}

/**
 * Save order tracking meta
 */
function grindctrl_save_order_tracking_meta($post_id) {
    if (!isset($_POST['grindctrl_order_tracking_nonce']) || !wp_verify_nonce($_POST['grindctrl_order_tracking_nonce'], 'grindctrl_order_tracking')) {
        return;
    }
    
    if (!current_user_can('edit_shop_order', $post_id)) {
        return;
    }
    
    if (isset($_POST['tracking_number'])) {
        update_post_meta($post_id, '_billing_tracking_number', sanitize_text_field($_POST['tracking_number']));
    }
    
    if (isset($_POST['courier'])) {
        update_post_meta($post_id, '_billing_courier', sanitize_text_field($_POST['courier']));
    }
}
add_action('save_post_shop_order', 'grindctrl_save_order_tracking_meta');

/**
 * AJAX handler for resending webhook
 */
function grindctrl_resend_webhook() {
    check_ajax_referer('grindctrl_resend_webhook', 'nonce');
    
    if (!current_user_can('edit_shop_orders')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'grindctrl'));
    }
    
    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);
    
    if (!$order) {
        wp_send_json_error('Invalid order ID');
        return;
    }
    
    $webhook_url = get_option('grindctrl_webhook_url');
    if (!$webhook_url) {
        wp_send_json_error('Webhook URL not configured');
        return;
    }
    
    // Prepare order data
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
    
    // Send webhook
    $response = wp_remote_post($webhook_url, array(
        'body' => json_encode($order_data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        update_post_meta($order_id, '_webhook_response', $response->get_error_message());
        wp_send_json_error($response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        update_post_meta($order_id, '_webhook_sent', current_time('mysql'));
        update_post_meta($order_id, '_webhook_response', "Code: {$response_code}, Body: {$response_body}");
        
        if ($response_code >= 200 && $response_code < 300) {
            wp_send_json_success('Webhook sent successfully');
        } else {
            wp_send_json_error("Webhook failed with code: {$response_code}");
        }
    }
}
add_action('wp_ajax_grindctrl_resend_webhook', 'grindctrl_resend_webhook');

/**
 * Enhanced webhook function with better error handling
 */
function grindctrl_send_order_to_webhook_enhanced($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    
    $webhook_url = get_option('grindctrl_webhook_url');
    if (!$webhook_url) return;
    
    // Check if webhook already sent
    $webhook_sent = get_post_meta($order_id, '_webhook_sent', true);
    if ($webhook_sent) return;
    
    // Prepare order data with all required fields
    $order_data = array(
        'order_id' => $order->get_id(),
        'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        'phone' => $order->get_billing_phone(),
        'city' => $order->get_billing_city(),
        'address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
        'cod_amount' => $order->get_total(),
        'tracking_number' => get_post_meta($order_id, '_billing_tracking_number', true) ?: '',
        'courier' => get_post_meta($order_id, '_billing_courier', true) ?: '',
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
    $response = wp_remote_post($webhook_url, array(
        'body' => json_encode($order_data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 30
    ));
    
    // Log response
    if (is_wp_error($response)) {
        update_post_meta($order_id, '_webhook_response', $response->get_error_message());
        error_log('GrindCTRL Webhook Error: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        update_post_meta($order_id, '_webhook_sent', current_time('mysql'));
        update_post_meta($order_id, '_webhook_response', "Code: {$response_code}, Body: {$response_body}");
        
        if ($response_code < 200 || $response_code >= 300) {
            error_log("GrindCTRL Webhook failed with code: {$response_code}");
        }
    }
}

// Replace the original webhook function
remove_action('woocommerce_order_status_processing', 'grindctrl_send_order_to_webhook');
remove_action('woocommerce_order_status_completed', 'grindctrl_send_order_to_webhook');
add_action('woocommerce_order_status_processing', 'grindctrl_send_order_to_webhook_enhanced');
add_action('woocommerce_order_status_completed', 'grindctrl_send_order_to_webhook_enhanced');