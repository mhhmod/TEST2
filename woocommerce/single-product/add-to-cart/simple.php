<?php
/**
 * Simple product add to cart - Custom WooCommerce Template Override
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * @package GrindCTRL
 * @version 7.0.1
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

if (!$product->is_purchasable()) {
    return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="cart grindctrl-add-to-cart-form" 
          action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" 
          method="post" 
          enctype='multipart/form-data'
          novalidate>
        
        <fieldset class="product-options-fieldset">
            <legend class="sr-only"><?php esc_html_e('Product options', 'grindctrl'); ?></legend>
            
            <?php do_action('woocommerce_before_add_to_cart_button'); ?>

            <!-- Quantity Section -->
            <div class="quantity-section" role="group" aria-labelledby="quantity-label">
                <h3 id="quantity-label" class="section-title"><?php esc_html_e('Quantity', 'grindctrl'); ?></h3>
                
                <?php
                do_action('woocommerce_before_add_to_cart_quantity');

                woocommerce_quantity_input(
                    array(
                        'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                        'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                        'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                        'classes'     => array('input-text', 'qty', 'text', 'grindctrl-quantity-input'),
                    )
                );

                do_action('woocommerce_after_add_to_cart_quantity');
                ?>

                <!-- Stock Information -->
                <?php if ($product->managing_stock() && $product->is_in_stock()) : ?>
                    <div class="stock-info" aria-live="polite">
                        <div class="stock-status in-stock">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            <span class="stock-text">
                                <?php
                                $stock_quantity = $product->get_stock_quantity();
                                if ($stock_quantity > 0) {
                                    if ($stock_quantity <= 5) {
                                        printf(
                                            esc_html(_n('Only %s left in stock', 'Only %s left in stock', $stock_quantity, 'grindctrl')),
                                            '<strong>' . esc_html($stock_quantity) . '</strong>'
                                        );
                                    } else {
                                        printf(
                                            esc_html__('%s in stock', 'grindctrl'),
                                            '<strong>' . esc_html($stock_quantity) . '</strong>'
                                        );
                                    }
                                } else {
                                    esc_html_e('In stock', 'grindctrl');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Variations (if applicable) -->
            <?php if ($product->is_type('variable')) : ?>
                <div class="variations-section">
                    <?php do_action('woocommerce_single_product_summary_single_variations'); ?>
                </div>
            <?php endif; ?>

            <!-- Custom Product Options -->
            <?php
            $custom_options = get_post_meta($product->get_id(), '_grindctrl_custom_options', true);
            if (!empty($custom_options) && is_array($custom_options)) :
            ?>
                <div class="custom-options-section">
                    <h3 class="section-title"><?php esc_html_e('Customization Options', 'grindctrl'); ?></h3>
                    
                    <?php foreach ($custom_options as $option_id => $option) : ?>
                        <div class="custom-option-group">
                            <label for="custom_option_<?php echo esc_attr($option_id); ?>" class="option-label">
                                <?php echo esc_html($option['label']); ?>
                                <?php if ($option['required']) : ?>
                                    <abbr title="<?php esc_attr_e('required', 'grindctrl'); ?>" aria-label="<?php esc_attr_e('required', 'grindctrl'); ?>">*</abbr>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ($option['type'] === 'select') : ?>
                                <select id="custom_option_<?php echo esc_attr($option_id); ?>" 
                                        name="custom_options[<?php echo esc_attr($option_id); ?>]"
                                        <?php echo $option['required'] ? 'required' : ''; ?>
                                        class="custom-option-select">
                                    <option value=""><?php esc_html_e('Select an option', 'grindctrl'); ?></option>
                                    <?php foreach ($option['choices'] as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>">
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($option['type'] === 'text') : ?>
                                <input type="text" 
                                       id="custom_option_<?php echo esc_attr($option_id); ?>"
                                       name="custom_options[<?php echo esc_attr($option_id); ?>]"
                                       <?php echo $option['required'] ? 'required' : ''; ?>
                                       class="custom-option-text"
                                       placeholder="<?php echo esc_attr($option['placeholder'] ?? ''); ?>" />
                            <?php endif; ?>
                            
                            <?php if (!empty($option['description'])) : ?>
                                <p class="option-description"><?php echo esc_html($option['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Add to Cart Button -->
            <div class="add-to-cart-section">
                <button type="submit" 
                        name="add-to-cart" 
                        value="<?php echo esc_attr($product->get_id()); ?>" 
                        class="single_add_to_cart_button btn btn-primary alt wp-element-button"
                        aria-describedby="add-to-cart-description">
                    <span class="button-text">
                        <i class="fas fa-cart-plus" aria-hidden="true"></i>
                        <?php echo esc_html($product->single_add_to_cart_text()); ?>
                    </span>
                    <span class="loading-spinner" aria-hidden="true">
                        <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                        <?php esc_html_e('Adding...', 'grindctrl'); ?>
                    </span>
                </button>
                
                <div id="add-to-cart-description" class="sr-only">
                    <?php esc_html_e('Add this product to your shopping cart', 'grindctrl'); ?>
                </div>
            </div>

            <?php do_action('woocommerce_after_add_to_cart_button'); ?>

        </fieldset>

        <!-- Product Actions -->
        <div class="product-actions-section">
            
            <!-- Wishlist Button -->
            <button type="button" 
                    class="btn btn-secondary wishlist-btn" 
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                    aria-label="<?php esc_attr_e('Add to wishlist', 'grindctrl'); ?>">
                <i class="far fa-heart" aria-hidden="true"></i>
                <span class="btn-text"><?php esc_html_e('Add to Wishlist', 'grindctrl'); ?></span>
            </button>

            <!-- Compare Button -->
            <button type="button" 
                    class="btn btn-secondary compare-btn" 
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                    aria-label="<?php esc_attr_e('Add to compare', 'grindctrl'); ?>">
                <i class="fas fa-balance-scale" aria-hidden="true"></i>
                <span class="btn-text"><?php esc_html_e('Compare', 'grindctrl'); ?></span>
            </button>

            <!-- Share Button -->
            <div class="share-product-section">
                <button type="button" 
                        class="btn btn-secondary share-toggle-btn"
                        aria-expanded="false"
                        aria-controls="share-options"
                        aria-label="<?php esc_attr_e('Share this product', 'grindctrl'); ?>">
                    <i class="fas fa-share-alt" aria-hidden="true"></i>
                    <span class="btn-text"><?php esc_html_e('Share', 'grindctrl'); ?></span>
                </button>
                
                <div id="share-options" class="share-options" hidden>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn facebook-share"
                           aria-label="<?php esc_attr_e('Share on Facebook', 'grindctrl'); ?>">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn twitter-share"
                           aria-label="<?php esc_attr_e('Share on Twitter', 'grindctrl'); ?>">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn whatsapp-share"
                           aria-label="<?php esc_attr_e('Share on WhatsApp', 'grindctrl'); ?>">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        </a>
                        <button type="button" 
                                class="share-btn copy-link-btn"
                                data-url="<?php echo esc_url(get_permalink()); ?>"
                                aria-label="<?php esc_attr_e('Copy link', 'grindctrl'); ?>">
                            <i class="fas fa-link" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Guarantees -->
        <div class="product-guarantees">
            <div class="guarantee-item">
                <i class="fas fa-shipping-fast" aria-hidden="true"></i>
                <div class="guarantee-content">
                    <span class="guarantee-title"><?php esc_html_e('Free Shipping', 'grindctrl'); ?></span>
                    <span class="guarantee-desc"><?php esc_html_e('On all orders worldwide', 'grindctrl'); ?></span>
                </div>
            </div>
            <div class="guarantee-item">
                <i class="fas fa-undo-alt" aria-hidden="true"></i>
                <div class="guarantee-content">
                    <span class="guarantee-title"><?php esc_html_e('Free Returns', 'grindctrl'); ?></span>
                    <span class="guarantee-desc"><?php esc_html_e('Within 14 days', 'grindctrl'); ?></span>
                </div>
            </div>
            <div class="guarantee-item">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                <div class="guarantee-content">
                    <span class="guarantee-title"><?php esc_html_e('Quality Guarantee', 'grindctrl'); ?></span>
                    <span class="guarantee-desc"><?php esc_html_e('1-year warranty', 'grindctrl'); ?></span>
                </div>
            </div>
        </div>

    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php else : ?>
    
    <!-- Out of Stock Message -->
    <div class="out-of-stock-section" role="alert">
        <div class="stock-status out-of-stock">
            <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
            <div class="stock-content">
                <span class="stock-title"><?php esc_html_e('Out of Stock', 'grindctrl'); ?></span>
                <span class="stock-desc"><?php esc_html_e('This product is currently unavailable', 'grindctrl'); ?></span>
            </div>
        </div>
        
        <!-- Notify When Available -->
        <div class="notify-section">
            <h3><?php esc_html_e('Get notified when back in stock', 'grindctrl'); ?></h3>
            <form class="notify-form" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <div class="form-group">
                    <label for="notify-email" class="sr-only"><?php esc_html_e('Email address', 'grindctrl'); ?></label>
                    <input type="email" 
                           id="notify-email" 
                           name="email" 
                           required
                           placeholder="<?php esc_attr_e('Enter your email address', 'grindctrl'); ?>"
                           autocomplete="email" />
                    <button type="submit" class="btn btn-primary">
                        <?php esc_html_e('Notify Me', 'grindctrl'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced add to cart functionality
    const addToCartForm = document.querySelector('.grindctrl-add-to-cart-form');
    const addToCartBtn = document.querySelector('.single_add_to_cart_button');
    const quantityInput = document.querySelector('.grindctrl-quantity-input');
    const shareToggle = document.querySelector('.share-toggle-btn');
    const shareOptions = document.querySelector('#share-options');
    const copyLinkBtn = document.querySelector('.copy-link-btn');
    const wishlistBtn = document.querySelector('.wishlist-btn');
    const compareBtn = document.querySelector('.compare-btn');
    
    // Quantity input enhancements
    if (quantityInput) {
        // Add custom quantity controls if not present
        if (!quantityInput.parentElement.querySelector('.qty-btn')) {
            addQuantityControls(quantityInput);
        }
        
        // Validate quantity on change
        quantityInput.addEventListener('change', function() {
            validateQuantity(this);
        });
    }
    
    // Add to cart form submission
    if (addToCartForm && addToCartBtn) {
        addToCartForm.addEventListener('submit', function(e) {
            // Show loading state
            addToCartBtn.classList.add('loading');
            addToCartBtn.disabled = true;
            
            // Reset after 3 seconds (fallback)
            setTimeout(() => {
                addToCartBtn.classList.remove('loading');
                addToCartBtn.disabled = false;
            }, 3000);
        });
    }
    
    // Share functionality
    if (shareToggle && shareOptions) {
        shareToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            shareOptions.hidden = isExpanded;
        });
        
        // Close share options when clicking outside
        document.addEventListener('click', function(e) {
            if (!shareToggle.contains(e.target) && !shareOptions.contains(e.target)) {
                shareToggle.setAttribute('aria-expanded', 'false');
                shareOptions.hidden = true;
            }
        });
    }
    
    // Copy link functionality
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Link copied to clipboard!', 'success');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showNotification('Link copied to clipboard!', 'success');
                } catch (err) {
                    showNotification('Failed to copy link', 'error');
                }
                document.body.removeChild(textArea);
            }
        });
    }
    
    // Wishlist functionality
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            const text = this.querySelector('.btn-text');
            
            // Toggle wishlist state
            const isInWishlist = icon.classList.contains('fas');
            
            if (isInWishlist) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.textContent = '<?php esc_js(_e("Add to Wishlist", "grindctrl")); ?>';
                showNotification('Removed from wishlist', 'info');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.textContent = '<?php esc_js(_e("Remove from Wishlist", "grindctrl")); ?>';
                showNotification('Added to wishlist!', 'success');
            }
            
            // Here you would make an AJAX call to update the wishlist
            // updateWishlist(productId, !isInWishlist);
        });
    }
    
    // Compare functionality
    if (compareBtn) {
        compareBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            showNotification('Product added to compare list!', 'success');
            // Here you would make an AJAX call to add to compare
            // addToCompare(productId);
        });
    }
    
    // Out of stock notification form
    const notifyForm = document.querySelector('.notify-form');
    if (notifyForm) {
        notifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('[name="email"]').value;
            const productId = this.dataset.productId;
            
            // Here you would make an AJAX call to subscribe for notifications
            showNotification('You will be notified when this product is back in stock!', 'success');
        });
    }
    
    function addQuantityControls(input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'quantity-selector';
        
        const decreaseBtn = document.createElement('button');
        decreaseBtn.type = 'button';
        decreaseBtn.className = 'qty-btn decrease';
        decreaseBtn.innerHTML = '<i class="fas fa-minus" aria-hidden="true"></i>';
        decreaseBtn.setAttribute('aria-label', '<?php esc_js(_e("Decrease quantity", "grindctrl")); ?>');
        
        const increaseBtn = document.createElement('button');
        increaseBtn.type = 'button';
        increaseBtn.className = 'qty-btn increase';
        increaseBtn.innerHTML = '<i class="fas fa-plus" aria-hidden="true"></i>';
        increaseBtn.setAttribute('aria-label', '<?php esc_js(_e("Increase quantity", "grindctrl")); ?>');
        
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(decreaseBtn);
        wrapper.appendChild(input);
        wrapper.appendChild(increaseBtn);
        
        // Event listeners
        decreaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value) || 1;
            const min = parseInt(input.getAttribute('min')) || 1;
            if (currentValue > min) {
                input.value = currentValue - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        increaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value) || 1;
            const max = parseInt(input.getAttribute('max')) || 999;
            if (currentValue < max) {
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    }
    
    function validateQuantity(input) {
        const value = parseInt(input.value);
        const min = parseInt(input.getAttribute('min')) || 1;
        const max = parseInt(input.getAttribute('max')) || 999;
        
        if (value < min) {
            input.value = min;
            showNotification(`Minimum quantity is ${min}`, 'warning');
        } else if (value > max) {
            input.value = max;
            showNotification(`Maximum quantity is ${max}`, 'warning');
        }
    }
    
    function showNotification(message, type = 'info') {
        // Use the theme's notification system
        if (window.GrindCTRLTheme && window.GrindCTRLTheme.showNotification) {
            window.GrindCTRLTheme.showNotification(message, type);
        } else {
            // Fallback
            alert(message);
        }
    }
});
</script>
