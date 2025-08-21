<?php
/**
 * Simple product add to cart - matches original form design
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
    return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="cart order-form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        
        <!-- Price Section (moved up to match original) -->
        <div class="price-section">
            <?php if ($product->get_regular_price() && $product->get_sale_price()) : ?>
                <span class="price-original"><?php echo wc_price($product->get_regular_price()); ?></span>
                <span class="price-current"><?php echo wc_price($product->get_sale_price()); ?></span>
            <?php else : ?>
                <span class="price-current"><?php echo $product->get_price_html(); ?></span>
            <?php endif; ?>
        </div>

        <!-- Product Variations (Size) -->
        <?php if ($product->is_type('variable')) : ?>
            <?php
            $available_variations = $product->get_available_variations();
            $variation_attributes = $product->get_variation_attributes();
            ?>
            <div class="form-group">
                <label for="size">Size</label>
                <select id="size" name="attribute_pa_size" required>
                    <option value="">Select Size</option>
                    <?php foreach ($variation_attributes['pa_size'] as $size) : ?>
                        <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html(strtoupper($size)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Quantity -->
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <div class="quantity-selector">
                <button type="button" class="qty-btn" id="decreaseQty">-</button>
                <?php
                woocommerce_quantity_input(
                    array(
                        'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                        'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                        'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                        'classes'     => array('input-text', 'qty', 'text'),
                        'readonly'    => true,
                    )
                );
                ?>
                <button type="button" class="qty-btn" id="increaseQty">+</button>
            </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="form-group">
            <label for="paymentMethod">Payment Method *</label>
            <select id="paymentMethod" name="payment_method_preview" required>
                <option value="">Choose your payment method</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Mobile Wallet">Mobile Wallet</option>
            </select>
        </div>

        <!-- Customer Information Fields -->
        <div class="form-row">
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="billing_first_name" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="billing_last_name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="billing_email" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="billing_phone" required>
        </div>

        <div class="form-group">
            <label for="address">Shipping Address *</label>
            <textarea id="address" name="billing_address_1" rows="3" required placeholder="Enter your complete shipping address"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="billing_city" required>
            </div>
            <div class="form-group">
                <label for="postalCode">Postal Code *</label>
                <input type="text" id="postalCode" name="billing_postcode" required>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal"><?php echo $product->get_price_html(); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="total"><?php echo $product->get_price_html(); ?></span>
            </div>
        </div>

        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="addToCartBtn">Add to Cart</button>
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn-primary single_add_to_cart_button button alt" id="buyNowBtn">
                <span class="btn-text">Buy Now</span>
                <span class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

    <script>
    jQuery(document).ready(function($) {
        // Quantity controls
        $('#decreaseQty').click(function() {
            var qty = parseInt($('.qty').val());
            if (qty > 1) {
                $('.qty').val(qty - 1).trigger('change');
            }
        });
        
        $('#increaseQty').click(function() {
            var qty = parseInt($('.qty').val());
            var max = parseInt($('.qty').attr('max')) || 10;
            if (qty < max) {
                $('.qty').val(qty + 1).trigger('change');
            }
        });
        
        // Update total when quantity changes
        $('.qty').change(function() {
            var qty = parseInt($(this).val());
            var price = <?php echo $product->get_price(); ?>;
            var total = qty * price;
            $('#subtotal').text('<?php echo get_woocommerce_currency_symbol(); ?>' + total.toFixed(2));
            $('#total').text('<?php echo get_woocommerce_currency_symbol(); ?>' + total.toFixed(2));
        });
        
        // Add to cart button
        $('#addToCartBtn').click(function() {
            $('.single_add_to_cart_button').click();
        });
        
        // Buy now functionality - redirect to checkout after adding to cart
        $('#buyNowBtn').click(function() {
            $(this).find('.btn-text').hide();
            $(this).find('.btn-loader').show();
            $(this).prop('disabled', true);
        });
    });
    </script>

<?php endif; ?>