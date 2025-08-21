<?php
/**
 * Simple Webhook Integration - NO COMPLEX FUNCTIONS
 * This will be loaded only if WooCommerce exists
 */

// Only proceed if WooCommerce is active
if (!class_exists('WooCommerce')) {
    return;
}

/**
 * Simple webhook function
 */
function grindctrl_simple_webhook($order_id) {
    $webhook_url = get_option('grindctrl_webhook_url', '');
    if (empty($webhook_url)) {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    // Simple order data
    $data = array(
        'Order ID' => $order->get_order_number(),
        'Customer Name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        'Phone' => $order->get_billing_phone(),
        'City' => $order->get_billing_city(),
        'Address' => $order->get_billing_address_1(),
        'Total' => $order->get_total(),
        'Date' => date('Y-m-d H:i:s'),
        'Status' => 'New',
        'Payment Method' => $order->get_payment_method_title(),
    );
    
    // Send to webhook
    wp_remote_post($webhook_url, array(
        'body' => json_encode($data),
        'headers' => array('Content-Type' => 'application/json'),
    ));
}

// Hook to new orders
add_action('woocommerce_new_order', 'grindctrl_simple_webhook');
?>