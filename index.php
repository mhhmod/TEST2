<?php
/**
 * The main template file - WooCommerce version of original index.html
 * 
 * This template maintains the original design while adding WooCommerce functionality
 */

get_header(); ?>

<main class="main" id="home">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php the_content(); ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <!-- Default content when no posts - show product section -->
            <?php grindctrl_default_product_section(); ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

<?php
/**
 * Display default product section when no content is available
 */
function grindctrl_default_product_section() {
    // Get the first product or featured product
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );
    
    $products = new WP_Query($args);
    
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            ?>
            <!-- Product Section -->
            <section class="product-section" id="product">
                <div class="product-grid">
                    <!-- Product Image -->
                    <div class="product-image-container">
                        <div class="product-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('alt' => get_the_title(), 'id' => 'productImage')); ?>
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/product-main.png" alt="<?php the_title(); ?>" id="productImage">
                            <?php endif; ?>
                        </div>
                        <div class="product-tags">
                            <span class="tag">Hot Drop</span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="product-info">
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        <?php 
                        $subtitle = get_post_meta(get_the_ID(), '_product_subtitle', true);
                        if (!empty($subtitle)) {
                            echo '<p class="product-subtitle">' . esc_html($subtitle) . '</p>';
                        } else {
                            echo '<p class="product-subtitle">Minimal. Premium cotton. Built for grind.</p>';
                        }
                        ?>
                        
                        <div class="price-section">
                            <?php if ($product->get_regular_price() && $product->get_sale_price()) : ?>
                                <span class="price-original"><?php echo wc_price($product->get_regular_price()); ?></span>
                                <span class="price-current"><?php echo wc_price($product->get_sale_price()); ?></span>
                            <?php else : ?>
                                <span class="price-current"><?php echo $product->get_price_html(); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="product-features">
                            <div class="feature">
                                <i class="fas fa-truck"></i>
                                <span>Fast checkout. Cart shipped.</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-clock"></i>
                                <span>Free returns within 14 days</span>
                            </div>
                        </div>

                        <!-- WooCommerce Product Form -->
                        <?php woocommerce_template_single_add_to_cart(); ?>

                    </div>
                </div>
            </section>

            <!-- Product Details -->
            <section class="product-details">
                <div class="details-grid">
                    <div class="detail-item">
                        <h3>Material</h3>
                        <p>100% premium cotton with a soft, breathable finish. Pre-shrunk for consistent fit.</p>
                    </div>
                    <div class="detail-item">
                        <h3>Care Instructions</h3>
                        <p>Machine wash cold with like colors. Tumble dry low. Do not bleach. Iron if needed.</p>
                    </div>
                    <div class="detail-item">
                        <h3>Fit</h3>
                        <p>Cropped silhouette with a relaxed fit. Designed for comfort and style versatility.</p>
                    </div>
                    <div class="detail-item">
                        <h3>Shipping</h3>
                        <p>Free shipping on all orders. Processing time: 1-2 business days. Delivery: 3-7 business days.</p>
                    </div>
                </div>
            </section>
            <?php
        }
        wp_reset_postdata();
    } else {
        // No products found - show placeholder
        ?>
        <section class="product-section" id="product">
            <div class="product-grid">
                <div class="product-image-container">
                    <div class="product-image">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/product-main.png" alt="Luxury Cropped Black T-Shirt" id="productImage">
                    </div>
                    <div class="product-tags">
                        <span class="tag">Coming Soon</span>
                    </div>
                </div>
                <div class="product-info">
                    <h1 class="product-title">Add Your First Product</h1>
                    <p class="product-subtitle">Go to WooCommerce → Products to add your products.</p>
                    <div class="price-section">
                        <span class="price-current">Price will appear here</span>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
?>