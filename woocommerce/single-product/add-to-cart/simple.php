<?php
/**
 * Simple product add to cart
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product->is_purchasable()) {
    return;
}

echo wc_get_stock_html($product);

if ($product->is_in_stock()) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="order-form cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>
        
        <!-- Product Variations/Options -->
        <?php if ($product->get_attributes()) : ?>
            <div class="product-attributes">
                <?php foreach ($product->get_attributes() as $attribute) : ?>
                    <?php if ($attribute->get_variation()) : ?>
                        <div class="form-group">
                            <label for="<?php echo sanitize_title($attribute->get_name()); ?>">
                                <?php echo wc_attribute_label($attribute->get_name()); ?>
                            </label>
                            <?php
                            wc_dropdown_variation_attribute_options(array(
                                'options' => $attribute->get_options(),
                                'attribute' => $attribute->get_name(),
                                'product' => $product,
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Quantity Selector -->
        <div class="form-group">
            <label for="quantity"><?php _e('Quantity', 'grindctrl'); ?></label>
            <div class="quantity-selector">
                <button type="button" class="qty-btn qty-decrease">-</button>
                <?php
                woocommerce_quantity_input(array(
                    'min_value' => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                    'max_value' => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                    'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(),
                ));
                ?>
                <button type="button" class="qty-increase qty-btn">+</button>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-row">
                <span><?php _e('Subtotal:', 'grindctrl'); ?></span>
                <span class="subtotal-amount"><?php echo wc_price($product->get_price()); ?></span>
            </div>
            <div class="summary-row">
                <span><?php _e('Shipping:', 'grindctrl'); ?></span>
                <span><?php _e('Free', 'grindctrl'); ?></span>
            </div>
            <div class="summary-row total">
                <span><?php _e('Total:', 'grindctrl'); ?></span>
                <span class="total-amount"><?php echo wc_price($product->get_price()); ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn-primary single_add_to_cart_button">
                <span class="btn-text"><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
                <span class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>

<script>
jQuery(document).ready(function($) {
    // Quantity controls
    $('.qty-decrease').on('click', function() {
        var $input = $(this).siblings('input[type="number"]');
        var currentVal = parseInt($input.val()) || 1;
        var min = parseInt($input.attr('min')) || 1;
        if (currentVal > min) {
            $input.val(currentVal - 1).trigger('change');
        }
    });
    
    $('.qty-increase').on('click', function() {
        var $input = $(this).siblings('input[type="number"]');
        var currentVal = parseInt($input.val()) || 1;
        var max = parseInt($input.attr('max')) || 999;
        if (currentVal < max) {
            $input.val(currentVal + 1).trigger('change');
        }
    });
    
    // Update totals when quantity changes
    $('input[name="quantity"]').on('change', function() {
        var quantity = parseInt($(this).val()) || 1;
        var price = <?php echo $product->get_price(); ?>;
        var subtotal = quantity * price;
        
        $('.subtotal-amount, .total-amount').text('<?php echo get_woocommerce_currency_symbol(); ?>' + subtotal.toFixed(2));
    });
});
</script>