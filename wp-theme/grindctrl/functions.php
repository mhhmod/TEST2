<?php
/**
 * GrindCTRL Theme Functions
 */

if (!defined('ABSPATH')) { exit; }

// Theme setup
add_action('after_setup_theme', function() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('woocommerce');
});

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
	$theme_uri = get_template_directory_uri();
	wp_enqueue_style('grindctrl-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', [], null);
	wp_enqueue_style('grindctrl-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [], '6.4.0');
	wp_enqueue_style('grindctrl-styles', $theme_uri . '/assets/css/styles.css', [], filemtime(get_template_directory() . '/assets/css/styles.css'));

	wp_enqueue_script('grindctrl-main', $theme_uri . '/assets/js/main.js', ['jquery'], filemtime(get_template_directory() . '/assets/js/main.js'), true);

	// Pass webhook URL and product data to JS
	$webhook_url = get_option('grindctrl_n8n_webhook_url', '');
	$product = wc_get_product(get_option('grindctrl_primary_product_id'));
	wp_localize_script('grindctrl-main', 'GRINDCTRL_DATA', [
		'webhookUrl' => $webhook_url,
		'product' => $product ? [
			'name' => $product->get_name(),
			'price' => (float) wc_get_price_to_display($product),
			'currency' => get_woocommerce_currency_symbol(),
		] : null,
	]);
});

// Admin settings: n8n webhook URL and primary product selector
add_action('admin_init', function() {
	register_setting('general', 'grindctrl_n8n_webhook_url', [
		'type' => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default' => ''
	]);
	add_settings_field('grindctrl_n8n_webhook_url', __('n8n Webhook URL', 'grindctrl'), function() {
		$value = esc_url(get_option('grindctrl_n8n_webhook_url', ''));
		echo '<input type="url" name="grindctrl_n8n_webhook_url" value="' . $value . '" class="regular-text" placeholder="https://...">';
	}, 'general');

	register_setting('general', 'grindctrl_primary_product_id', [
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => 0
	]);
	add_settings_field('grindctrl_primary_product_id', __('Primary Product (One-product layout)', 'grindctrl'), function() {
		$selected = (int) get_option('grindctrl_primary_product_id', 0);
		$products = wc_get_products(['limit' => -1]);
		echo '<select name="grindctrl_primary_product_id">';
		echo '<option value="0">' . esc_html__('— Select —', 'grindctrl') . '</option>';
		foreach ($products as $p) {
			printf('<option value="%d" %s>%s</option>', $p->get_id(), selected($selected, $p->get_id(), false), esc_html($p->get_name()));
		}
		echo '</select>';
	}, 'general');
});

// Order meta fields: Tracking Number, Courier
add_action('add_meta_boxes', function() {
	add_meta_box('grindctrl_tracking', __('Shipping Tracking', 'grindctrl'), function($post) {
		$tracking = get_post_meta($post->ID, '_grindctrl_tracking_number', true);
		$courier  = get_post_meta($post->ID, '_grindctrl_courier', true);
		echo '<p><label>' . esc_html__('Tracking Number', 'grindctrl') . '<br><input type="text" name="grindctrl_tracking_number" value="' . esc_attr($tracking) . '" class="regular-text"></label></p>';
		echo '<p><label>' . esc_html__('Courier', 'grindctrl') . '<br><input type="text" name="grindctrl_courier" value="' . esc_attr($courier) . '" class="regular-text"></label></p>';
	}, 'shop_order', 'side');
});

add_action('save_post_shop_order', function($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (isset($_POST['grindctrl_tracking_number'])) {
		update_post_meta($post_id, '_grindctrl_tracking_number', sanitize_text_field(wp_unslash($_POST['grindctrl_tracking_number'])));
	}
	if (isset($_POST['grindctrl_courier'])) {
		update_post_meta($post_id, '_grindctrl_courier', sanitize_text_field(wp_unslash($_POST['grindctrl_courier'])));
	}
    // Post updated order to n8n after saving meta
    grindctrl_post_order_to_n8n($post_id);
});

// Build JSON for n8n
function grindctrl_build_order_payload(WC_Order $order) {
	$items = [];
	foreach ($order->get_items() as $item) {
		$product = $item->get_product();
		$size = '';
		$all_meta = $item->get_formatted_meta_data('');
		foreach ($all_meta as $meta) {
			if (stripos($meta->display_key, 'size') !== false) {
				$size = wp_strip_all_tags($meta->display_value);
				break;
			}
		}
		$items[] = [
			'Product+Size' => trim($product ? $product->get_name() : $item->get_name()) . ($size ? ' / ' . $size : ''),
			'Quantity' => (string) $item->get_quantity(),
		];
	}

	$first_item = $items ? $items[0] : ['Product+Size' => '', 'Quantity' => ''];

	$billing = $order->get_address('billing');
	$payment_method_title = $order->get_payment_method_title();
	$tracking = get_post_meta($order->get_id(), '_grindctrl_tracking_number', true);
	$courier  = get_post_meta($order->get_id(), '_grindctrl_courier', true);

	$cod_amount = (strtolower($order->get_payment_method()) === 'cod' || stripos($payment_method_title, 'cod') !== false)
		? (float) $order->get_total() : 0.0;

	return [
		'Order ID' => (string) $order->get_order_number(),
		'Customer Name' => trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')),
		'Phone' => (string) ($billing['phone'] ?? ''),
		'City' => (string) ($billing['city'] ?? ''),
		'Address' => trim(($billing['address_1'] ?? '') . ' ' . ($billing['address_2'] ?? '')),
		'COD Amount' => number_format((float) $cod_amount, 2, '.', ''),
		'Tracking Number' => (string) $tracking,
		'Courier' => (string) ($courier ?: ($order->get_shipping_method() ?: '')),
		'Total' => number_format((float) $order->get_total(), 2, '.', ''),
		'Date' => gmdate('c', $order->get_date_created() ? $order->get_date_created()->getTimestamp() : time()),
		'Status' => $order->get_status(),
		'Payment Method' => (string) $payment_method_title,
		'Product+Size' => $first_item['Product+Size'],
		'Quantity' => $first_item['Quantity'],
	];
}

// Fire webhook on order created/updated
add_action('woocommerce_thankyou', function($order_id) {
	grindctrl_post_order_to_n8n($order_id);
}, 20);

add_action('woocommerce_order_status_changed', function($order_id) {
	grindctrl_post_order_to_n8n($order_id);
}, 20);

function grindctrl_post_order_to_n8n($order_id) {
	$webhook = get_option('grindctrl_n8n_webhook_url', '');
	if (empty($webhook)) return;
	$order = wc_get_order($order_id);
	if (!$order) return;

	$payload = grindctrl_build_order_payload($order);
	$payload_json = wp_json_encode($payload);
	$hash = md5($payload_json);
	$last_hash = get_post_meta($order_id, '_grindctrl_last_posted_hash', true);
	if ($last_hash === $hash) {
		return; // Skip duplicate
	}
	update_post_meta($order_id, '_grindctrl_last_posted_hash', $hash);

	wp_remote_post($webhook, [
		'timeout' => 15,
		'headers' => [
			'Content-Type' => 'application/json',
			'User-Agent' => 'GrindCTRL-Theme/1.0'
		],
		'body' => $payload_json
	]);
}

// Simple Buy Now: add to cart and redirect to checkout
add_action('init', function() {
	if (isset($_GET['grindctrl_buy_now']) && isset($_GET['product_id'])) {
		$product_id = absint($_GET['product_id']);
		$quantity = isset($_GET['quantity']) ? max(1, absint($_GET['quantity'])) : 1;
		$variation = isset($_GET['variation_id']) ? absint($_GET['variation_id']) : 0;
		$attributes = [];
		foreach ($_GET as $key => $val) {
			if (stripos($key, 'attribute_') === 0) {
				$attributes[$key] = sanitize_text_field(wp_unslash($val));
			}
		}

		$product = wc_get_product($product_id);
		if ($product && $product->is_type('variable') && !$variation && !empty($attributes)) {
			// Try to find matching variation
			$children = $product->get_children();
			foreach ($children as $child_id) {
				$var = wc_get_product($child_id);
				if (!$var || !$var->is_in_stock()) continue;
				$var_attrs = $var->get_attributes();
				$match = true;
				foreach ($var_attrs as $attr_key => $attr_val) {
					$req_key = 'attribute_' . $attr_key;
					if (!isset($attributes[$req_key])) { $match = false; break; }
					if (wc_sanitize_taxonomy_name($attributes[$req_key]) !== wc_sanitize_taxonomy_name($attr_val)) { $match = false; break; }
				}
				if ($match) { $variation = $child_id; break; }
			}
		}
		WC()->cart->empty_cart();
		if ($variation) {
			WC()->cart->add_to_cart($product_id, $quantity, $variation, $attributes);
		} else {
			WC()->cart->add_to_cart($product_id, $quantity, 0, $attributes);
		}
		wp_safe_redirect(wc_get_checkout_url());
		exit;
	}
});

