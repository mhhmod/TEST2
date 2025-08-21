<?php
/**
 * Front Page template to mirror one-product layout
 */
if (!defined('ABSPATH')) { exit; }
get_header();

$product_id = (int) get_option('grindctrl_primary_product_id');
$product = $product_id ? wc_get_product($product_id) : null;
$price_html = $product ? $product->get_price_html() : '';
$image_id = $product ? $product->get_image_id() : 0;
$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : get_template_directory_uri() . '/assets/img/product-main.png';

$sizes = [];
if ($product && $product->is_type('variable')) {
	$attributes = $product->get_attributes();
	foreach ($attributes as $attribute) {
		if ($attribute->get_name() === 'pa_size' || stripos($attribute->get_name(), 'size') !== false) {
			$sizes = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
			break;
		}
	}
}
?>

<main class="main" id="home">
    <div class="container">
        <section class="product-section" id="product">
            <div class="product-grid">
                <div class="product-image-container">
                    <div class="product-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product ? $product->get_name() : get_bloginfo('name')); ?>" id="productImage">
                    </div>
                    <div class="product-tags">
                        <span class="tag">Hot Drop</span>
                    </div>
                </div>

                <div class="product-info">
                    <h1 class="product-title"><?php echo esc_html($product ? $product->get_name() : 'Product'); ?></h1>
                    <p class="product-subtitle">Minimal. Premium cotton. Built for grind.</p>

                    <div class="price-section">
                        <?php if ($product): ?>
                            <span class="price-current"><?php echo wp_kses_post($price_html); ?></span>
                        <?php else: ?>
                            <span class="price-current">300.00 EGP</span>
                        <?php endif; ?>
                    </div>

                    <form class="order-form" id="orderForm" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input type="hidden" name="grindctrl_buy_now" value="1" />
                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product ? $product->get_id() : 0); ?>" />

                        <div class="form-group">
                            <label for="size">Size</label>
                            <select id="size" name="attribute_pa_size" <?php echo empty($sizes) ? '' : 'required'; ?>>
                                <option value="">Select Size</option>
                                <?php foreach ($sizes as $size): ?>
                                    <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn" id="decreaseQty">-</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" readonly>
                                <button type="button" class="qty-btn" id="increaseQty">+</button>
                            </div>
                        </div>

                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">—</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="total">—</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <?php if ($product): ?>
                                <button type="submit" class="btn btn-primary" id="buyNowBtn">
                                    <span class="btn-text">Buy Now</span>
                                    <span class="btn-loader" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>" class="btn btn-primary">Create Product in Admin</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </section>

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
    </div>
</main>

<?php get_footer(); ?>

