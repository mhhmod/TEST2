<?php
/**
 * Product content template
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $product;

// Ensure woocommerce.php is running if using a product outside of the loop
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<li <?php wc_product_class('product-item', $product); ?>>
    
    <div class="product-image">
        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" 
           aria-label="<?php echo esc_attr($product->get_name()); ?>">
            <?php
            /**
             * Hook: woocommerce_before_shop_loop_item_title.
             *
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked woocommerce_template_loop_product_thumbnail - 10
             */
            do_action('woocommerce_before_shop_loop_item_title');
            ?>
        </a>
    </div>

    <div class="product-content">
        
        <h3 class="product-title">
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h3>

        <?php
        /**
         * Hook: woocommerce_after_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action('woocommerce_after_shop_loop_item_title');
        ?>

        <div class="product-actions">
            <?php
            /**
             * Hook: woocommerce_after_shop_loop_item.
             *
             * @hooked woocommerce_template_loop_product_link_close - 5
             * @hooked woocommerce_template_loop_add_to_cart - 10
             */
            do_action('woocommerce_after_shop_loop_item');
            ?>
        </div>
        
    </div>
    
</li>
