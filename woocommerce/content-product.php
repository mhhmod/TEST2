<?php
/**
 * The template for displaying product content within loops
 * Matches the original design style
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<div <?php wc_product_class('product-card', $product); ?>>
    <div class="product-image-container">
        <div class="product-image">
            <?php
            /**
             * Hook: woocommerce_before_shop_loop_item.
             *
             * @hooked woocommerce_template_loop_product_link_open - 10
             */
            do_action('woocommerce_before_shop_loop_item');

            /**
             * Hook: woocommerce_before_shop_loop_item_title.
             *
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked woocommerce_template_loop_product_thumbnail - 10
             */
            do_action('woocommerce_before_shop_loop_item_title');
            ?>
        </div>
        
        <div class="product-tags">
            <?php if ($product->is_on_sale()) : ?>
                <span class="tag sale-tag">Sale</span>
            <?php elseif ($product->is_featured()) : ?>
                <span class="tag featured-tag">Featured</span>
            <?php else : ?>
                <span class="tag">New</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-info">
        <?php
        /**
         * Hook: woocommerce_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_product_title - 10
         */
        do_action('woocommerce_shop_loop_item_title');

        /**
         * Hook: woocommerce_after_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action('woocommerce_after_shop_loop_item_title');

        /**
         * Hook: woocommerce_after_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_close - 5
         * @hooked woocommerce_template_loop_add_to_cart - 10
         */
        do_action('woocommerce_after_shop_loop_item');
        ?>
        
        <div class="product-features">
            <div class="feature">
                <i class="fas fa-truck"></i>
                <span>Free shipping</span>
            </div>
            <div class="feature">
                <i class="fas fa-clock"></i>
                <span>Fast delivery</span>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    background-color: var(--light-grey);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: var(--transition-medium);
    margin-bottom: var(--spacing-lg);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-heavy);
}

.product-card .product-image-container {
    position: relative;
    overflow: hidden;
}

.product-card .product-image img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: var(--transition-medium);
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-card .product-tags {
    position: absolute;
    top: var(--spacing-sm);
    left: var(--spacing-sm);
}

.product-card .tag {
    background-color: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 500;
}

.product-card .sale-tag {
    background-color: var(--success-color);
}

.product-card .featured-tag {
    background-color: var(--warning-color);
}

.product-card .product-info {
    padding: var(--spacing-md);
}

.product-card .woocommerce-loop-product__title {
    color: var(--text-color);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.product-card .price {
    color: var(--primary-color);
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.product-card .price del {
    color: #999;
    margin-right: var(--spacing-xs);
}

.product-card .add_to_cart_button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-sm);
    font-weight: 600;
    transition: var(--transition-fast);
    width: 100%;
    margin-top: var(--spacing-sm);
}

.product-card .add_to_cart_button:hover {
    background-color: #c0392b;
}

.shop-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg);
    margin-top: var(--spacing-lg);
}

@media (max-width: 768px) {
    .shop-grid {
        grid-template-columns: 1fr;
    }
}
</style>