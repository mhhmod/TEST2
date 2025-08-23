<?php
/**
 * Product summary template
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product) {
    return;
}
?>

<div class="product-summary">
    
    <div class="product-meta">
        
        <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
            <div class="product-sku">
                <span class="label"><?php esc_html_e('SKU:', 'grindctrl'); ?></span>
                <span class="value" id="product_sku">
                    <?php echo $product->get_sku() ? esc_html($product->get_sku()) : esc_html__('N/A', 'grindctrl'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($product->get_categories()) : ?>
            <div class="product-categories">
                <span class="label"><?php esc_html_e('Categories:', 'grindctrl'); ?></span>
                <span class="value">
                    <?php echo wc_get_product_category_list($product->get_id(), ', ', '<span class="posted_in">', '</span>'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($product->get_tags()) : ?>
            <div class="product-tags">
                <span class="label"><?php esc_html_e('Tags:', 'grindctrl'); ?></span>
                <span class="value">
                    <?php echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="tagged_as">', '</span>'); ?>
                </span>
            </div>
        <?php endif; ?>
        
    </div>

    <?php if ($product->is_in_stock()) : ?>
        <div class="stock-status in-stock">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span><?php esc_html_e('In Stock', 'grindctrl'); ?></span>
        </div>
    <?php else : ?>
        <div class="stock-status out-of-stock">
            <i class="fas fa-times-circle" aria-hidden="true"></i>
            <span><?php esc_html_e('Out of Stock', 'grindctrl'); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($product->get_shipping_class()) : ?>
        <div class="shipping-info">
            <i class="fas fa-shipping-fast" aria-hidden="true"></i>
            <span>
                <?php
                $shipping_class = get_term($product->get_shipping_class_id());
                if ($shipping_class && !is_wp_error($shipping_class)) {
                    echo esc_html($shipping_class->name);
                }
                ?>
            </span>
        </div>
    <?php endif; ?>

    <?php
    // Custom product features
    $features = get_post_meta($product->get_id(), '_product_features', true);
    if ($features && is_array($features)) :
    ?>
        <div class="product-features">
            <h4><?php esc_html_e('Features', 'grindctrl'); ?></h4>
            <ul>
                <?php foreach ($features as $feature) : ?>
                    <li>
                        <i class="fas fa-check" aria-hidden="true"></i>
                        <?php echo esc_html($feature); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="social-share">
        <h4><?php esc_html_e('Share', 'grindctrl'); ?></h4>
        <div class="share-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e('Share on Facebook', 'grindctrl'); ?>"
               class="share-facebook">
                <i class="fab fa-facebook-f" aria-hidden="true"></i>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e('Share on Twitter', 'grindctrl'); ?>"
               class="share-twitter">
                <i class="fab fa-twitter" aria-hidden="true"></i>
            </a>
            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e('Share on WhatsApp', 'grindctrl'); ?>"
               class="share-whatsapp">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
            </a>
            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" 
               aria-label="<?php esc_attr_e('Share via Email', 'grindctrl'); ?>"
               class="share-email">
                <i class="fas fa-envelope" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    
</div>
