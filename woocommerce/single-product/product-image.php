<?php
/**
 * Single Product Image - Custom WooCommerce Template Override
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * @package GrindCTRL
 * @version 7.8.0
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

$columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
    'woocommerce_single_product_image_gallery_classes',
    array(
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ($post_thumbnail_id ? 'with-images' : 'without-images'),
        'woocommerce-product-gallery--columns-' . absint($columns),
        'images',
    )
);

?>
<div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" 
     data-columns="<?php echo esc_attr($columns); ?>" 
     style="opacity: 0; transition: opacity .25s ease-in-out;">
     
    <!-- Product Badges -->
    <div class="product-badges">
        <?php
        // Sale badge with percentage
        if ($product->is_on_sale()) {
            $percentage = '';
            if ($product->get_type() === 'simple' || $product->get_type() === 'external') {
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                if ($regular_price > 0) {
                    $percentage = round(((($regular_price - $sale_price) / $regular_price) * 100));
                }
            }
            ?>
            <span class="badge badge-sale">
                <?php 
                echo esc_html__('Sale', 'grindctrl'); 
                if ($percentage) {
                    echo ' -' . esc_html($percentage) . '%';
                }
                ?>
            </span>
            <?php
        }

        // New product badge (products created within last 30 days)
        $created = strtotime($product->get_date_created());
        if ($created > strtotime('-30 days')) {
            ?>
            <span class="badge badge-new"><?php esc_html_e('New', 'grindctrl'); ?></span>
            <?php
        }

        // Out of stock badge
        if (!$product->is_in_stock()) {
            ?>
            <span class="badge badge-out-of-stock"><?php esc_html_e('Out of Stock', 'grindctrl'); ?></span>
            <?php
        }

        // Featured product badge
        if ($product->is_featured()) {
            ?>
            <span class="badge badge-featured"><?php esc_html_e('Featured', 'grindctrl'); ?></span>
            <?php
        }
        ?>
    </div>

    <figure class="woocommerce-product-gallery__wrapper">
        <?php
        if ($post_thumbnail_id) {
            $html = wc_get_gallery_image_html($post_thumbnail_id, true);
        } else {
            $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'grindctrl'));
            $html .= '</div>';
        }

        echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        
        do_action('woocommerce_product_thumbnails');
        ?>
    </figure>

    <!-- Zoom Button -->
    <?php if ($post_thumbnail_id) : ?>
        <button class="zoom-btn" 
                aria-label="<?php esc_attr_e('Zoom product image', 'grindctrl'); ?>"
                data-image-id="<?php echo esc_attr($post_thumbnail_id); ?>">
            <i class="fas fa-search-plus" aria-hidden="true"></i>
            <span class="sr-only"><?php esc_html_e('Click to zoom image', 'grindctrl'); ?></span>
        </button>
    <?php endif; ?>

    <!-- Image Gallery Navigation -->
    <?php 
    $attachment_ids = $product->get_gallery_image_ids();
    if ($attachment_ids && $post_thumbnail_id) :
        $total_images = count($attachment_ids) + 1; // +1 for main image
        if ($total_images > 1) :
        ?>
        <div class="gallery-navigation" aria-label="<?php esc_attr_e('Image gallery navigation', 'grindctrl'); ?>">
            <button class="gallery-prev" 
                    aria-label="<?php esc_attr_e('Previous image', 'grindctrl'); ?>"
                    disabled>
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
            <span class="gallery-counter" aria-live="polite">
                <span class="current-image">1</span> / <?php echo esc_html($total_images); ?>
            </span>
            <button class="gallery-next" 
                    aria-label="<?php esc_attr_e('Next image', 'grindctrl'); ?>">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Quick View for Related Images -->
    <div class="quick-view-thumbnails" 
         role="tablist" 
         aria-label="<?php esc_attr_e('Product images', 'grindctrl'); ?>">
        
        <!-- Main image thumbnail -->
        <?php if ($post_thumbnail_id) : ?>
            <button class="thumbnail-btn active" 
                    role="tab"
                    aria-selected="true"
                    aria-controls="product-main-image"
                    data-image-id="<?php echo esc_attr($post_thumbnail_id); ?>"
                    aria-label="<?php esc_attr_e('Main product image', 'grindctrl'); ?>">
                <?php 
                $thumbnail_src = wp_get_attachment_image_src($post_thumbnail_id, 'woocommerce_gallery_thumbnail');
                if ($thumbnail_src) :
                ?>
                    <img src="<?php echo esc_url($thumbnail_src[0]); ?>" 
                         alt="<?php echo esc_attr(get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true)); ?>"
                         width="80" 
                         height="80"
                         loading="lazy" />
                <?php endif; ?>
            </button>
        <?php endif; ?>

        <!-- Gallery thumbnails -->
        <?php 
        if ($attachment_ids) :
            $thumbnail_index = 1;
            foreach ($attachment_ids as $attachment_id) :
                $thumbnail_index++;
                $thumbnail_src = wp_get_attachment_image_src($attachment_id, 'woocommerce_gallery_thumbnail');
                if ($thumbnail_src) :
                ?>
                <button class="thumbnail-btn" 
                        role="tab"
                        aria-selected="false"
                        aria-controls="product-main-image"
                        data-image-id="<?php echo esc_attr($attachment_id); ?>"
                        aria-label="<?php printf(esc_attr__('Product image %d', 'grindctrl'), $thumbnail_index); ?>">
                    <img src="<?php echo esc_url($thumbnail_src[0]); ?>" 
                         alt="<?php echo esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)); ?>"
                         width="80" 
                         height="80"
                         loading="lazy" />
                </button>
                <?php
                endif;
            endforeach;
        endif;
        ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize product image gallery
    const gallery = document.querySelector('.woocommerce-product-gallery');
    const thumbnails = document.querySelectorAll('.thumbnail-btn');
    const mainImage = document.querySelector('.woocommerce-product-gallery__image img');
    const zoomBtn = document.querySelector('.zoom-btn');
    const galleryPrev = document.querySelector('.gallery-prev');
    const galleryNext = document.querySelector('.gallery-next');
    const currentImageSpan = document.querySelector('.current-image');
    
    if (!gallery) return;

    let currentIndex = 0;
    const totalImages = thumbnails.length;

    // Show gallery with fade-in effect
    gallery.style.opacity = '1';

    // Thumbnail click handlers
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', function() {
            switchToImage(index);
        });

        // Keyboard navigation for thumbnails
        thumbnail.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                switchToImage(index);
            } else if (e.key === 'ArrowRight' && index < totalImages - 1) {
                e.preventDefault();
                thumbnails[index + 1].focus();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                thumbnails[index - 1].focus();
            }
        });
    });

    // Navigation button handlers
    if (galleryPrev) {
        galleryPrev.addEventListener('click', function() {
            if (currentIndex > 0) {
                switchToImage(currentIndex - 1);
            }
        });
    }

    if (galleryNext) {
        galleryNext.addEventListener('click', function() {
            if (currentIndex < totalImages - 1) {
                switchToImage(currentIndex + 1);
            }
        });
    }

    // Zoom functionality
    if (zoomBtn && mainImage) {
        zoomBtn.addEventListener('click', function() {
            const imageUrl = mainImage.src.replace('-scaled', '').replace(/(-\d+x\d+)/, '');
            openImageModal(imageUrl, mainImage.alt);
        });
    }

    // Keyboard navigation for main image
    if (mainImage) {
        mainImage.setAttribute('tabindex', '0');
        mainImage.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight' && currentIndex < totalImages - 1) {
                switchToImage(currentIndex + 1);
            } else if (e.key === 'ArrowLeft' && currentIndex > 0) {
                switchToImage(currentIndex - 1);
            }
        });
    }

    function switchToImage(index) {
        if (index < 0 || index >= totalImages) return;

        // Update thumbnails
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
            thumb.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });

        // Update main image
        const selectedThumbnail = thumbnails[index];
        const imageId = selectedThumbnail.dataset.imageId;
        
        // Here you would implement the logic to change the main image
        // This is a simplified version - in a real implementation, you'd need
        // to fetch the full-size image URL and update the main image
        
        currentIndex = index;

        // Update navigation buttons
        if (galleryPrev) {
            galleryPrev.disabled = currentIndex === 0;
        }
        if (galleryNext) {
            galleryNext.disabled = currentIndex === totalImages - 1;
        }

        // Update counter
        if (currentImageSpan) {
            currentImageSpan.textContent = currentIndex + 1;
        }

        // Announce change to screen readers
        announceToScreenReader(`Image ${currentIndex + 1} of ${totalImages} selected`);
    }

    function openImageModal(imageUrl, altText) {
        // Create modal for zoomed image
        const modal = document.createElement('div');
        modal.className = 'image-zoom-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-label', 'Zoomed product image');
        
        modal.innerHTML = `
            <div class="modal-backdrop" aria-hidden="true"></div>
            <div class="modal-content">
                <button class="modal-close" aria-label="Close zoom view">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
                <img src="${imageUrl}" alt="${altText}" class="zoomed-image" />
            </div>
        `;

        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        // Focus management
        const closeBtn = modal.querySelector('.modal-close');
        closeBtn.focus();

        // Close handlers
        const closeModal = () => {
            document.body.removeChild(modal);
            document.body.style.overflow = '';
            zoomBtn.focus(); // Return focus to zoom button
        };

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
                closeModal();
            }
        });

        // Keyboard navigation
        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }

    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }
});
</script>
