<?php
/**
 * Cart Page - maintains original design
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="cart-container">
    <h1 class="product-title">Shopping Cart</h1>
    
    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="cart-items">
            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <div class="cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
                        <div class="cart-item-image">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                            if (!$product_permalink) {
                                echo $thumbnail; // PHPCS: XSS ok.
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                            }
                            ?>
                        </div>

                        <div class="cart-item-details">
                            <h3 class="cart-item-name">
                                <?php
                                if (!$product_permalink) {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                } else {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                }

                                do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                // Meta data.
                                echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                // Backorder notification.
                                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                }
                                ?>
                            </h3>

                            <div class="cart-item-price">
                                <?php
                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                ?>
                            </div>

                            <div class="cart-item-quantity">
                                <?php
                                if ($_product->is_sold_individually()) {
                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                } else {
                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                            'min_value'    => '0',
                                            'product_name' => $_product->get_name(),
                                        ),
                                        $_product,
                                        false
                                    );
                                }

                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                ?>
                            </div>

                            <div class="cart-item-subtotal">
                                <?php
                                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                ?>
                            </div>

                            <div class="cart-item-remove">
                                <?php
                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fas fa-times"></i></a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__('Remove this item', 'woocommerce'),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <?php do_action('woocommerce_cart_contents'); ?>
        
        <div class="cart-actions">
            <button type="submit" class="btn btn-secondary" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
            <?php do_action('woocommerce_cart_actions'); ?>
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
        </div>

        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

    <?php do_action('woocommerce_before_cart_collaterals'); ?>

    <div class="cart-collaterals">
        <div class="cart-totals">
            <?php
            /**
             * Cart collaterals hook.
             *
             * @hooked woocommerce_cart_totals - 10
             * @hooked woocommerce_shipping_calculator - 20
             */
            do_action('woocommerce_cart_collaterals');
            ?>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>

<style>
.cart-container {
    max-width: 800px;
    margin: 0 auto;
}

.cart-item {
    display: flex;
    align-items: center;
    background-color: var(--light-grey);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-md);
    gap: var(--spacing-md);
}

.cart-item-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-sm);
}

.cart-item-details {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: var(--spacing-md);
    align-items: center;
}

.cart-item-name {
    color: var(--text-color);
    font-size: 1.125rem;
    font-weight: 600;
}

.cart-item-price,
.cart-item-subtotal {
    color: var(--primary-color);
    font-weight: 600;
}

.cart-item-quantity input {
    width: 60px;
    background-color: var(--background-color);
    border: 1px solid var(--border-color);
    color: var(--text-color);
    text-align: center;
    padding: 4px;
    border-radius: var(--radius-sm);
}

.cart-item-remove a {
    color: var(--error-color);
    padding: 8px;
    border-radius: 50%;
    transition: var(--transition-fast);
}

.cart-item-remove a:hover {
    background-color: var(--error-color);
    color: white;
}

.cart-actions {
    text-align: center;
    margin: var(--spacing-lg) 0;
}

.cart-totals {
    background-color: var(--light-grey);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
}

@media (max-width: 768px) {
    .cart-item {
        flex-direction: column;
        text-align: center;
    }
    
    .cart-item-details {
        grid-template-columns: 1fr;
        text-align: center;
        gap: var(--spacing-sm);
    }
}
</style>