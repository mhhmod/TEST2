<?php
/**
 * GrindCTRL n8n Webhook Integration for WooCommerce
 * 
 * This file handles sending WooCommerce order data to n8n webhook
 * in the exact format that matches your Excel columns.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send WooCommerce order to n8n webhook
 * Triggered when order status changes to processing or completed
 */
function grindctrl_send_order_to_webhook($order_id, $old_status, $new_status) {
    // Only send for new orders or when status changes to processing/completed
    if (!in_array($new_status, ['processing', 'completed']) && $old_status !== 'pending') {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    if (empty($webhook_url)) {
        error_log('GrindCTRL: Webhook URL not configured');
        return;
    }
    
    // Prepare order data in your exact Excel format
    $order_data = grindctrl_prepare_webhook_data($order);
    
    // Send to webhook
    $response = wp_remote_post($webhook_url, array(
        'method' => 'POST',
        'timeout' => 30,
        'headers' => array(
            'Content-Type' => 'application/json',
            'User-Agent' => 'GrindCTRL-WooCommerce/1.0',
        ),
        'body' => json_encode($order_data),
    ));
    
    if (is_wp_error($response)) {
        error_log('GrindCTRL Webhook Error: ' . $response->get_error_message());
        $order->add_order_note('Failed to send order to webhook: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code === 200) {
            $order->add_order_note('Order successfully sent to n8n webhook');
            error_log('GrindCTRL: Order ' . $order_id . ' sent to webhook successfully');
        } else {
            error_log('GrindCTRL Webhook Error: HTTP ' . $response_code);
            $order->add_order_note('Webhook returned error code: ' . $response_code);
        }
    }
}
add_action('woocommerce_order_status_changed', 'grindctrl_send_order_to_webhook', 10, 3);

/**
 * Prepare order data in exact Excel column format
 * Maps WooCommerce data to your 14 Excel columns
 */
function grindctrl_prepare_webhook_data($order) {
    $order_items = $order->get_items();
    $first_item = reset($order_items);
    
    // Get product name with variation (size)
    $product_name = '';
    $quantity = 0;
    
    if ($first_item) {
        $product = $first_item->get_product();
        $product_name = $first_item->get_name();
        $quantity = $first_item->get_quantity();
        
        // Add variation info (size) if available
        $variation_data = $first_item->get_formatted_meta_data();
        foreach ($variation_data as $meta) {
            if (strtolower($meta->display_key) === 'size' || strtolower($meta->display_key) === 'pa_size') {
                $product_name .= ' - ' . $meta->display_value;
                break;
            }
        }
    }
    
    // Calculate COD amount (only for COD payments)
    $payment_method = $order->get_payment_method_title();
    $is_cod = (strtolower($payment_method) === 'cash on delivery' || 
               strpos(strtolower($payment_method), 'cod') !== false);
    $cod_amount = $is_cod ? $order->get_total() : 0;
    
    // Generate tracking number if not exists
    $tracking_number = $order->get_meta('_tracking_number');
    if (empty($tracking_number)) {
        $tracking_number = 'TRK' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $order->update_meta_data('_tracking_number', $tracking_number);
        $order->save();
    }
    
    // Get courier (default to BOSTA)
    $courier = $order->get_meta('_courier');
    if (empty($courier)) {
        $courier = 'BOSTA';
        $order->update_meta_data('_courier', $courier);
        $order->save();
    }
    
    // Map WooCommerce order status to your system
    $status_mapping = array(
        'pending' => 'New',
        'processing' => 'Processing', 
        'on-hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'failed' => 'Failed',
    );
    
    $order_status = isset($status_mapping[$order->get_status()]) ? 
                   $status_mapping[$order->get_status()] : 'New';
    
    // Return data in exact Excel column format (14 columns)
    return array(
        "Order ID" => $order->get_order_number(),
        "Customer Name" => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
        "Phone" => $order->get_billing_phone(),
        "City" => $order->get_billing_city(),
        "Address" => $order->get_billing_address_1() . 
                    ($order->get_billing_address_2() ? ', ' . $order->get_billing_address_2() : ''),
        "COD Amount" => number_format($cod_amount, 2, '.', ''),
        "Tracking Number" => $tracking_number,
        "Courier" => $courier,
        "Total" => number_format($order->get_total(), 2, '.', ''),
        "Date" => $order->get_date_created()->date('c'), // ISO 8601 format
        "Status" => $order_status,
        "Payment Method" => $payment_method,
        "Product" => $product_name,
        "Quantity" => (string)$quantity
    );
}

/**
 * Add webhook test functionality to admin
 */
function grindctrl_test_webhook_ajax() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    check_ajax_referer('grindctrl_nonce', 'nonce');
    
    $webhook_url = get_option('grindctrl_webhook_url', '');
    if (empty($webhook_url)) {
        wp_send_json_error('Webhook URL not configured');
    }
    
    // Create test data
    $test_data = array(
        "Order ID" => "TEST-" . time(),
        "Customer Name" => "Test Customer",
        "Phone" => "+1234567890",
        "City" => "Test City",
        "Address" => "123 Test Street",
        "COD Amount" => "100.00",
        "Tracking Number" => "TRK123456789",
        "Courier" => "BOSTA",
        "Total" => "100.00",
        "Date" => date('c'),
        "Status" => "New",
        "Payment Method" => "Cash on Delivery",
        "Product" => "Test Product - M",
        "Quantity" => "1"
    );
    
    $response = wp_remote_post($webhook_url, array(
        'method' => 'POST',
        'timeout' => 30,
        'headers' => array(
            'Content-Type' => 'application/json',
            'User-Agent' => 'GrindCTRL-WooCommerce/1.0',
        ),
        'body' => json_encode($test_data),
    ));
    
    if (is_wp_error($response)) {
        wp_send_json_error('Webhook test failed: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code === 200) {
            wp_send_json_success('Webhook test successful!');
        } else {
            wp_send_json_error('Webhook returned error code: ' . $response_code);
        }
    }
}
add_action('wp_ajax_grindctrl_test_webhook', 'grindctrl_test_webhook_ajax');

/**
 * Add tracking number and courier fields to order admin
 */
function grindctrl_add_order_meta_boxes() {
    add_meta_box(
        'grindctrl_order_details',
        'GrindCTRL Order Details',
        'grindctrl_order_details_meta_box',
        'shop_order',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'grindctrl_add_order_meta_boxes');

function grindctrl_order_details_meta_box($post) {
    $order = wc_get_order($post->ID);
    $tracking_number = $order->get_meta('_tracking_number');
    $courier = $order->get_meta('_courier');
    
    ?>
    <p>
        <label for="tracking_number">Tracking Number:</label><br>
        <input type="text" id="tracking_number" name="tracking_number" 
               value="<?php echo esc_attr($tracking_number); ?>" style="width: 100%;" />
    </p>
    <p>
        <label for="courier">Courier:</label><br>
        <select id="courier" name="courier" style="width: 100%;">
            <option value="BOSTA" <?php selected($courier, 'BOSTA'); ?>>BOSTA</option>
            <option value="Aramex" <?php selected($courier, 'Aramex'); ?>>Aramex</option>
            <option value="DHL" <?php selected($courier, 'DHL'); ?>>DHL</option>
            <option value="FedEx" <?php selected($courier, 'FedEx'); ?>>FedEx</option>
            <option value="Other" <?php selected($courier, 'Other'); ?>>Other</option>
        </select>
    </p>
    <p>
        <button type="button" id="resend_webhook" class="button">Resend to Webhook</button>
    </p>
    
    <script>
    jQuery(document).ready(function($) {
        $('#resend_webhook').click(function() {
            var button = $(this);
            button.prop('disabled', true).text('Sending...');
            
            $.post(ajaxurl, {
                action: 'grindctrl_resend_webhook',
                order_id: <?php echo $post->ID; ?>,
                nonce: '<?php echo wp_create_nonce('grindctrl_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    alert('Order resent to webhook successfully!');
                } else {
                    alert('Failed to resend: ' + response.data);
                }
                button.prop('disabled', false).text('Resend to Webhook');
            });
        });
    });
    </script>
    <?php
}

// Save order meta box data
function grindctrl_save_order_meta_box($post_id) {
    if (isset($_POST['tracking_number'])) {
        $order = wc_get_order($post_id);
        $order->update_meta_data('_tracking_number', sanitize_text_field($_POST['tracking_number']));
        $order->update_meta_data('_courier', sanitize_text_field($_POST['courier']));
        $order->save();
    }
}
add_action('save_post', 'grindctrl_save_order_meta_box');

// Resend webhook AJAX handler
function grindctrl_resend_webhook_ajax() {
    if (!current_user_can('edit_shop_orders')) {
        wp_die('Unauthorized');
    }
    
    check_ajax_referer('grindctrl_nonce', 'nonce');
    
    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);
    
    if (!$order) {
        wp_send_json_error('Order not found');
    }
    
    // Trigger webhook manually
    grindctrl_send_order_to_webhook($order_id, '', 'processing');
    
    wp_send_json_success('Order resent to webhook');
}
add_action('wp_ajax_grindctrl_resend_webhook', 'grindctrl_resend_webhook_ajax');
?>