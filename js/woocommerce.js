/**
 * WooCommerce Enhanced Functionality for GrindCTRL Theme
 * 
 * @package GrindCTRL
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // WooCommerce App State
    const WooCommerceApp = {
        init: function() {
            this.initQuantityControls();
            this.initCartFunctionality();
            this.initCheckoutEnhancements();
            this.initProductTabs();
            this.initNotifications();
            this.initWebhookIntegration();
        },

        // Enhanced quantity controls
        initQuantityControls: function() {
            $(document).on('click', '.qty-btn', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const $input = $button.siblings('input[type="number"]');
                const currentVal = parseInt($input.val()) || 1;
                const min = parseInt($input.attr('min')) || 1;
                const max = parseInt($input.attr('max')) || 999;
                
                if ($button.hasClass('qty-decrease') && currentVal > min) {
                    $input.val(currentVal - 1).trigger('change');
                } else if ($button.hasClass('qty-increase') && currentVal < max) {
                    $input.val(currentVal + 1).trigger('change');
                }
                
                // Update totals
                this.updateProductTotals($input);
            });
            
            // Handle direct input changes
            $(document).on('change', 'input[name="quantity"]', function() {
                WooCommerceApp.updateProductTotals($(this));
            });
        },

        // Update product totals in real-time
        updateProductTotals: function($input) {
            const quantity = parseInt($input.val()) || 1;
            const $form = $input.closest('form');
            const $subtotal = $form.find('.subtotal-amount');
            const $total = $form.find('.total-amount');
            
            if ($subtotal.length && $total.length) {
                // Get price from data attribute or parse from existing price
                let price = parseFloat($form.data('product-price'));
                if (!price) {
                    const priceText = $('.price-current').text().replace(/[^\d.,]/g, '');
                    price = parseFloat(priceText.replace(',', '.')) || 0;
                }
                
                const subtotal = quantity * price;
                const currency = grindctrl_ajax.currency_symbol || 'EGP';
                
                $subtotal.text(currency + ' ' + subtotal.toFixed(2));
                $total.text(currency + ' ' + subtotal.toFixed(2));
            }
        },

        // Enhanced cart functionality
        initCartFunctionality: function() {
            // Update cart count in header
            $(document.body).on('wc_fragments_refreshed added_to_cart', function() {
                WooCommerceApp.updateCartCount();
            });
            
            // Add to cart button enhancements
            $(document).on('click', '.single_add_to_cart_button', function() {
                const $button = $(this);
                const $text = $button.find('.btn-text');
                const $loader = $button.find('.btn-loader');
                
                $text.hide();
                $loader.show();
                $button.prop('disabled', true);
                
                // Re-enable after a delay (WooCommerce will handle the actual response)
                setTimeout(function() {
                    $text.show();
                    $loader.hide();
                    $button.prop('disabled', false);
                }, 2000);
            });
        },

        // Update cart count
        updateCartCount: function() {
            $.get(grindctrl_ajax.ajax_url, {
                action: 'get_cart_count'
            }, function(data) {
                if (data.count !== undefined) {
                    $('.cart-count').text(data.count);
                }
            });
        },

        // Checkout enhancements
        initCheckoutEnhancements: function() {
            // Auto-format phone numbers
            $(document).on('input', 'input[name="billing_phone"]', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length >= 10) {
                    // Format as: (XXX) XXX-XXXX
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                }
                $(this).val(value);
            });
            
            // Validate required fields
            $(document).on('blur', 'input[required], select[required]', function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (!value) {
                    $field.addClass('error');
                    WooCommerceApp.showFieldError($field, 'This field is required');
                } else {
                    $field.removeClass('error');
                    WooCommerceApp.hideFieldError($field);
                }
            });
        },

        // Show field error
        showFieldError: function($field, message) {
            const $error = $field.siblings('.field-error');
            if ($error.length) {
                $error.text(message);
            } else {
                $field.after('<span class="field-error" style="color: var(--error-color); font-size: 0.875rem; margin-top: 5px; display: block;">' + message + '</span>');
            }
        },

        // Hide field error
        hideFieldError: function($field) {
            $field.siblings('.field-error').remove();
        },

        // Product tabs functionality
        initProductTabs: function() {
            $(document).on('click', '.woocommerce-tabs .tabs li a', function(e) {
                e.preventDefault();
                
                const $tab = $(this);
                const $tabsContainer = $tab.closest('.woocommerce-tabs');
                const targetPanel = $tab.attr('href');
                
                // Remove active class from all tabs and panels
                $tabsContainer.find('.tabs li').removeClass('active');
                $tabsContainer.find('.panel').hide();
                
                // Add active class to current tab and show panel
                $tab.closest('li').addClass('active');
                $tabsContainer.find(targetPanel).show();
            });
        },

        // Enhanced notifications
        initNotifications: function() {
            // Show notification function
            window.showNotification = function(message, type = 'success', duration = 5000) {
                const $notification = $('#notification');
                const $icon = $notification.find('.notification-icon');
                const $message = $notification.find('.notification-message');
                
                // Set icon based on type
                let iconClass = 'fas fa-check-circle';
                if (type === 'error') iconClass = 'fas fa-exclamation-circle';
                if (type === 'warning') iconClass = 'fas fa-exclamation-triangle';
                if (type === 'info') iconClass = 'fas fa-info-circle';
                
                $icon.attr('class', 'notification-icon ' + iconClass);
                $message.text(message);
                $notification.removeClass('success error warning info').addClass(type);
                
                $notification.fadeIn(300);
                
                // Auto hide
                setTimeout(function() {
                    $notification.fadeOut(300);
                }, duration);
            };
            
            // Hide notification function
            window.hideNotification = function() {
                $('#notification').fadeOut(300);
            };
        },

        // Webhook integration for orders
        initWebhookIntegration: function() {
            // Listen for order completion
            $(document.body).on('checkout_place_order', function() {
                // This will be handled by the PHP webhook integration
                console.log('Order placed, webhook will be triggered server-side');
            });
            
            // Custom order tracking
            if (typeof grindctrl_ajax.webhook_url !== 'undefined' && grindctrl_ajax.webhook_url) {
                console.log('Webhook integration active');
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        WooCommerceApp.init();
        
        // Add smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 500);
            }
        });
    });

    // Export for global access
    window.WooCommerceApp = WooCommerceApp;

})(jQuery);

// AJAX handler for cart count
jQuery(document).ready(function($) {
    // Add AJAX action for cart count
    if (typeof grindctrl_ajax !== 'undefined') {
        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', grindctrl_ajax.nonce);
            }
        });
    }
});