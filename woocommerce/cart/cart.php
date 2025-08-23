<?php
/**
 * Cart Page Template - Custom WooCommerce Override
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @package GrindCTRL
 * @version 7.8.0
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

do_action('woocommerce_before_cart');
?>

<div class="woocommerce-cart-form-wrapper">
    
    <!-- Cart Progress Indicator -->
    <div class="cart-progress" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="3" aria-label="<?php esc_attr_e('Checkout progress', 'grindctrl'); ?>">
        <div class="progress-steps">
            <div class="step active" data-step="1">
                <div class="step-circle">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                </div>
                <span class="step-label"><?php esc_html_e('Cart', 'grindctrl'); ?></span>
            </div>
            <div class="step" data-step="2">
                <div class="step-circle">
                    <i class="fas fa-shipping-fast" aria-hidden="true"></i>
                </div>
                <span class="step-label"><?php esc_html_e('Checkout', 'grindctrl'); ?></span>
            </div>
            <div class="step" data-step="3">
                <div class="step-circle">
                    <i class="fas fa-check" aria-hidden="true"></i>
                </div>
                <span class="step-label"><?php esc_html_e('Complete', 'grindctrl'); ?></span>
            </div>
        </div>
    </div>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="cart-content">
            
            <!-- Cart Items -->
            <div class="cart-items-section">
                <h2 class="cart-section-title">
                    <?php esc_html_e('Your Cart', 'grindctrl'); ?>
                    <span class="cart-count">
                        (<?php echo esc_html(sprintf(_n('%d item', '%d items', WC()->cart->get_cart_contents_count(), 'grindctrl'), WC()->cart->get_cart_contents_count())); ?>)
                    </span>
                </h2>

                <table class="shop_table shop_table_responsive cart woocommerce-cart-table__table" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="product-thumbnail" scope="col"><?php esc_html_e('Product', 'grindctrl'); ?></th>
                            <th class="product-name" scope="col">&nbsp;</th>
                            <th class="product-price" scope="col"><?php esc_html_e('Price', 'grindctrl'); ?></th>
                            <th class="product-quantity" scope="col"><?php esc_html_e('Quantity', 'grindctrl'); ?></th>
                            <th class="product-subtotal" scope="col"><?php esc_html_e('Subtotal', 'grindctrl'); ?></th>
                            <th class="product-remove" scope="col"><span class="sr-only"><?php esc_html_e('Remove item', 'grindctrl'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('woocommerce_before_cart_contents'); ?>

                        <?php
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                            /**
                             * Filter the product name.
                             *
                             * @since 2.1.0
                             * @param string $product_name Name of the product in the cart.
                             * @param array $cart_item The product in the cart.
                             * @param string $cart_item_key Key for the product in the cart.
                             */
                            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                ?>
                                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                                    <!-- Product Thumbnail -->
                                    <td class="product-thumbnail">
                                        <?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                        if (!$product_permalink) {
                                            echo $thumbnail; // PHPCS: XSS ok.
                                        } else {
                                            printf('<a href="%s" aria-label="%s">%s</a>', esc_url($product_permalink), esc_attr(sprintf(__('View %s', 'grindctrl'), $product_name)), $thumbnail); // PHPCS: XSS ok.
                                        }
                                        ?>
                                    </td>

                                    <!-- Product Name and Details -->
                                    <td class="product-name" data-title="<?php esc_attr_e('Product', 'grindctrl'); ?>">
                                        <div class="product-details">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post($product_name . '&nbsp;');
                                            } else {
                                                /**
                                                 * This filter is documented above.
                                                 *
                                                 * @since 2.1.0
                                                 */
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }

                                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                            // Meta data.
                                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                            // Backorder notification.
                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'grindctrl') . '</p>', $product_id));
                                            }
                                            ?>
                                            
                                            <!-- Product SKU -->
                                            <?php if ($_product->get_sku()) : ?>
                                                <div class="product-sku">
                                                    <small><?php esc_html_e('SKU:', 'grindctrl'); ?> <?php echo esc_html($_product->get_sku()); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Product Price -->
                                    <td class="product-price" data-title="<?php esc_attr_e('Price', 'grindctrl'); ?>">
                                        <div class="price-wrapper">
                                            <?php
                                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            
                                            // Show sale badge if on sale
                                            if ($_product->is_on_sale()) {
                                                echo '<span class="sale-badge">' . esc_html__('Sale', 'grindctrl') . '</span>';
                                            }
                                            ?>
                                        </div>
                                    </td>

                                    <!-- Product Quantity -->
                                    <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'grindctrl'); ?>">
                                        <?php
                                        if ($_product->is_sold_individually()) {
                                            $min_quantity = 1;
                                            $max_quantity = 1;
                                        } else {
                                            $min_quantity = 0;
                                            $max_quantity = $_product->get_max_purchase_quantity();
                                        }

                                        $product_quantity = woocommerce_quantity_input(
                                            array(
                                                'input_name'   => "cart[{$cart_item_key}][qty]",
                                                'input_value'  => $cart_item['quantity'],
                                                'max_value'    => $max_quantity,
                                                'min_value'    => $min_quantity,
                                                'product_name' => $product_name,
                                            ),
                                            $_product,
                                            false
                                        );

                                        echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                        ?>
                                        
                                        <!-- Stock Status -->
                                        <?php if ($_product->managing_stock()) : ?>
                                            <div class="stock-info">
                                                <?php if ($_product->is_in_stock()) : ?>
                                                    <small class="in-stock">
                                                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                                                        <?php esc_html_e('In stock', 'grindctrl'); ?>
                                                    </small>
                                                <?php else : ?>
                                                    <small class="out-of-stock">
                                                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                                        <?php esc_html_e('Out of stock', 'grindctrl'); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Product Subtotal -->
                                    <td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'grindctrl'); ?>">
                                        <div class="subtotal-wrapper">
                                            <?php
                                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            ?>
                                        </div>
                                    </td>

                                    <!-- Remove Item -->
                                    <td class="product-remove">
                                        <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><i class="fas fa-trash" aria-hidden="true"></i></a>',
                                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                /* translators: %s is the product name */
                                                esc_attr(sprintf(__('Remove %s from cart', 'grindctrl'), wp_strip_all_tags($product_name))),
                                                esc_attr($product_id),
                                                esc_attr($_product->get_sku()),
                                                esc_attr($cart_item_key)
                                            ),
                                            $cart_item_key
                                        );
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                        <?php do_action('woocommerce_cart_contents'); ?>

                        <tr>
                            <td colspan="6" class="actions">

                                <?php if (wc_coupons_enabled()) : ?>
                                    <div class="coupon">
                                        <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'grindctrl'); ?></label>
                                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'grindctrl'); ?>" />
                                        <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'grindctrl'); ?>">
                                            <i class="fas fa-tag" aria-hidden="true"></i>
                                            <?php esc_html_e('Apply coupon', 'grindctrl'); ?>
                                        </button>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'grindctrl'); ?>">
                                    <i class="fas fa-sync-alt" aria-hidden="true"></i>
                                    <?php esc_html_e('Update cart', 'grindctrl'); ?>
                                </button>

                                <?php do_action('woocommerce_cart_actions'); ?>

                                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                            </td>
                        </tr>

                        <?php do_action('woocommerce_after_cart_contents'); ?>
                    </tbody>
                </table>

                <?php do_action('woocommerce_after_cart_table'); ?>
            </div>

            <!-- Cart Totals -->
            <div class="cart-totals-section">
                <div class="cart-collaterals">
                    <?php
                    /**
                     * Cart collaterals hook.
                     *
                     * @hooked woocommerce_cross_sell_display
                     * @hooked woocommerce_cart_totals - 10
                     */
                    do_action('woocommerce_cart_collaterals');
                    ?>
                </div>
            </div>

        </div>

    </form>

</div>

<!-- Continue Shopping -->
<div class="continue-shopping">
    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-secondary continue-shopping-btn">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <?php esc_html_e('Continue Shopping', 'grindctrl'); ?>
    </a>
</div>

<!-- Cart Enhancement Features -->
<div class="cart-enhancements">
    
    <!-- Recently Viewed Products -->
    <?php
    $recently_viewed = WC()->session->get('wc_recently_viewed_products', array());
    if (!empty($recently_viewed)) :
        $recently_viewed = array_slice($recently_viewed, 0, 4); // Show last 4 viewed
        $products = wc_get_products(array(
            'include' => $recently_viewed,
            'orderby' => 'post__in',
        ));
        
        if (!empty($products)) :
    ?>
        <section class="recently-viewed-section" aria-labelledby="recently-viewed-title">
            <h2 id="recently-viewed-title"><?php esc_html_e('Recently Viewed', 'grindctrl'); ?></h2>
            <div class="recently-viewed-products">
                <?php foreach ($products as $product) : ?>
                    <div class="recently-viewed-item">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-link">
                            <?php echo $product->get_image('thumbnail'); ?>
                            <h3 class="product-name"><?php echo esc_html($product->get_name()); ?></h3>
                            <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                        </a>
                        <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                            <form class="quick-add-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    <?php esc_html_e('Add', 'grindctrl'); ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Trust Signals -->
    <div class="cart-trust-signals">
        <div class="trust-item">
            <i class="fas fa-shield-alt" aria-hidden="true"></i>
            <div class="trust-content">
                <strong><?php esc_html_e('Secure Checkout', 'grindctrl'); ?></strong>
                <span><?php esc_html_e('SSL encrypted', 'grindctrl'); ?></span>
            </div>
        </div>
        <div class="trust-item">
            <i class="fas fa-shipping-fast" aria-hidden="true"></i>
            <div class="trust-content">
                <strong><?php esc_html_e('Free Shipping', 'grindctrl'); ?></strong>
                <span><?php esc_html_e('On all orders', 'grindctrl'); ?></span>
            </div>
        </div>
        <div class="trust-item">
            <i class="fas fa-undo-alt" aria-hidden="true"></i>
            <div class="trust-content">
                <strong><?php esc_html_e('Easy Returns', 'grindctrl'); ?></strong>
                <span><?php esc_html_e('14-day return policy', 'grindctrl'); ?></span>
            </div>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced cart functionality
    const cartForm = document.querySelector('.woocommerce-cart-form');
    const quantityInputs = document.querySelectorAll('input.qty');
    const removeButtons = document.querySelectorAll('a.remove');
    const updateCartBtn = document.querySelector('button[name="update_cart"]');
    const couponCode = document.getElementById('coupon_code');
    
    // Auto-update cart on quantity change
    let updateTimeout;
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const productId = this.getAttribute('data-product-id') || row.querySelector('.remove').getAttribute('data-product_id');
            
            // Visual feedback
            row.classList.add('updating');
            
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                if (updateCartBtn) {
                    updateCartBtn.click();
                }
            }, 1000);
        });
        
        // Prevent invalid values
        input.addEventListener('input', function() {
            let value = parseInt(this.value);
            const min = parseInt(this.getAttribute('min')) || 0;
            const max = parseInt(this.getAttribute('max')) || 999;
            
            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
                showNotification(`Maximum quantity is ${max}`, 'warning');
            }
        });
    });
    
    // Enhanced remove item functionality
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productName = this.closest('tr').querySelector('.product-name a').textContent.trim();
            
            if (confirm(`<?php echo esc_js(__('Are you sure you want to remove', 'grindctrl')); ?> "${productName}" <?php echo esc_js(__('from your cart?', 'grindctrl')); ?>`)) {
                const row = this.closest('tr');
                row.classList.add('removing');
                
                // Add loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>';
                
                // Proceed with removal
                window.location.href = this.href;
            }
        });
    });
    
    // Coupon code enhancements
    if (couponCode) {
        couponCode.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const applyButton = document.querySelector('button[name="apply_coupon"]');
                if (applyButton) {
                    applyButton.click();
                }
            }
        });
        
        // Convert to uppercase for better UX
        couponCode.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Quick add to cart for recently viewed
    const quickAddForms = document.querySelectorAll('.quick-add-form');
    quickAddForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>';
            button.disabled = true;
            
            // Reset button after 2 seconds (fallback)
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
        });
    });
    
    // Save cart state to localStorage for recovery
    function saveCartState() {
        const cartItems = [];
        document.querySelectorAll('.cart_item').forEach(row => {
            const productId = row.querySelector('.remove').getAttribute('data-product_id');
            const quantity = row.querySelector('.qty').value;
            cartItems.push({ productId, quantity });
        });
        
        localStorage.setItem('cart_backup', JSON.stringify({
            items: cartItems,
            timestamp: Date.now()
        }));
    }
    
    // Save cart state on changes
    quantityInputs.forEach(input => {
        input.addEventListener('change', saveCartState);
    });
    
    // Mobile responsive enhancements
    if (window.innerWidth <= 768) {
        // Convert table to card layout on mobile
        const cartTable = document.querySelector('.woocommerce-cart-table__table');
        if (cartTable) {
            cartTable.classList.add('mobile-layout');
        }
    }
    
    function showNotification(message, type = 'info') {
        if (window.GrindCTRLTheme && window.GrindCTRLTheme.showNotification) {
            window.GrindCTRLTheme.showNotification(message, type);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `cart-notification ${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: #333;
                color: white;
                border-radius: 5px;
                z-index: 10000;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
    }
    
    // Initialize cart analytics if available
    if (window.gtag || window.ga) {
        // Track cart view
        const cartValue = document.querySelector('.order-total .amount');
        if (cartValue && window.gtag) {
            gtag('event', 'view_cart', {
                currency: 'EGP',
                value: parseFloat(cartValue.textContent.replace(/[^0-9.-]+/g, ''))
            });
        }
    }
});
</script>
