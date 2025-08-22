<?php
/**
 * Single Product template
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

get_header(); ?>

<main class="main" id="home">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <?php global $product; ?>
            
            <!-- Product Section -->
            <section class="product-section" id="product">
                <div class="product-grid">
                    <!-- Product Image -->
                    <div class="product-image-container">
                        <div class="product-image">
                            <?php echo woocommerce_get_product_thumbnail('large'); ?>
                        </div>
                        <div class="product-tags">
                            <?php if ($product->is_on_sale()) : ?>
                                <span class="tag sale-tag"><?php _e('Sale', 'grindctrl'); ?></span>
                            <?php endif; ?>
                            <?php if ($product->is_featured()) : ?>
                                <span class="tag featured-tag"><?php _e('Hot Drop', 'grindctrl'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="product-info">
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        
                        <?php if ($product->get_short_description()) : ?>
                            <p class="product-subtitle"><?php echo $product->get_short_description(); ?></p>
                        <?php endif; ?>
                        
                        <div class="price-section">
                            <?php if ($product->get_regular_price() != $product->get_sale_price() && $product->is_on_sale()) : ?>
                                <span class="price-original"><?php echo wc_price($product->get_regular_price()); ?></span>
                            <?php endif; ?>
                            <span class="price-current"><?php echo wc_price($product->get_price()); ?></span>
                        </div>

                        <div class="product-features">
                            <div class="feature">
                                <i class="fas fa-truck"></i>
                                <span><?php _e('Fast checkout. Cart shipped.', 'grindctrl'); ?></span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-clock"></i>
                                <span><?php _e('Free returns within 14 days', 'grindctrl'); ?></span>
                            </div>
                        </div>

                        <!-- WooCommerce Add to Cart Form -->
                        <div class="woocommerce-product-form">
                            <?php woocommerce_template_single_add_to_cart(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Product Details Tabs -->
            <section class="product-details">
                <?php woocommerce_output_product_data_tabs(); ?>
            </section>

            <!-- Related Products -->
            <?php woocommerce_output_related_products(); ?>
            
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>