<?php
/**
 * The Template for displaying single products - maintains original design
 */

defined('ABSPATH') || exit;

get_header('shop'); ?>

<main class="main" id="home">
    <div class="container">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php global $product; ?>
            
            <!-- Product Section -->
            <section class="product-section" id="product">
                <div class="product-grid">
                    <!-- Product Image -->
                    <div class="product-image-container">
                        <div class="product-image">
                            <?php
                            /**
                             * Hook: woocommerce_before_single_product_summary.
                             *
                             * @hooked woocommerce_show_product_sale_flash - 10
                             * @hooked woocommerce_show_product_images - 20
                             */
                            do_action('woocommerce_before_single_product_summary');
                            ?>
                        </div>
                        <div class="product-tags">
                            <?php if ($product->is_on_sale()) : ?>
                                <span class="tag">On Sale</span>
                            <?php else : ?>
                                <span class="tag">Hot Drop</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="product-info">
                        <div class="summary entry-summary">
                            <?php
                            /**
                             * Hook: woocommerce_single_product_summary.
                             *
                             * @hooked woocommerce_template_single_title - 5
                             * @hooked woocommerce_template_single_rating - 10
                             * @hooked woocommerce_template_single_price - 10
                             * @hooked woocommerce_template_single_excerpt - 20
                             * @hooked woocommerce_template_single_add_to_cart - 30
                             * @hooked woocommerce_template_single_meta - 40
                             * @hooked woocommerce_template_single_sharing - 50
                             * @hooked WC_Structured_Data::generate_product_data() - 60
                             */
                            do_action('woocommerce_single_product_summary');
                            ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Product Details -->
            <section class="product-details">
                <div class="details-grid">
                    <div class="detail-item">
                        <h3>Material</h3>
                        <p><?php echo get_post_meta(get_the_ID(), '_material_description', true) ?: '100% premium cotton with a soft, breathable finish. Pre-shrunk for consistent fit.'; ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Care Instructions</h3>
                        <p><?php echo get_post_meta(get_the_ID(), '_care_instructions', true) ?: 'Machine wash cold with like colors. Tumble dry low. Do not bleach. Iron if needed.'; ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Fit</h3>
                        <p><?php echo get_post_meta(get_the_ID(), '_fit_description', true) ?: 'Cropped silhouette with a relaxed fit. Designed for comfort and style versatility.'; ?></p>
                    </div>
                    <div class="detail-item">
                        <h3>Shipping</h3>
                        <p><?php echo get_post_meta(get_the_ID(), '_shipping_info', true) ?: 'Free shipping on all orders. Processing time: 1-2 business days. Delivery: 3-7 business days.'; ?></p>
                    </div>
                </div>
            </section>

            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action('woocommerce_after_single_product_summary');
            ?>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */