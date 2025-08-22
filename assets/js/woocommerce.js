/**
 * WooCommerce Integration JavaScript
 * Handles cart, checkout, and product interactions
 */

(function() {
    'use strict';

    // WooCommerce Cart Management
    class WooCommerceCart {
        constructor() {
            this.cart = JSON.parse(localStorage.getItem('grindctrl_cart')) || [];
            this.init();
        }

        init() {
            this.updateCartCount();
            this.bindEvents();
        }

        bindEvents() {
            // Add to cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleAddToCart(e));
            });

            // Quantity controls
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleQuantityChange(e));
            });

            // Cart icon click
            const cartIcon = document.querySelector('.cart-icon');
            if (cartIcon) {
                cartIcon.addEventListener('click', () => this.toggleCartDrawer());
            }
        }

        handleAddToCart(e) {
            e.preventDefault();
            
            const form = e.target.closest('form');
            const productId = Date.now(); // Generate unique ID
            const size = form.querySelector('input[name="size"]:checked')?.value;
            const quantity = parseInt(form.querySelector('#quantity')?.value) || 1;
            
            if (!size) {
                this.showNotification('Please select a size', 'error');
                return;
            }

            const product = {
                id: productId,
                name: 'Luxury Cropped Black T-Shirt',
                size: size,
                quantity: quantity,
                price: 300.00,
                currency: 'EGP',
                image: 'assets/images/product-main.png'
            };

            this.addToCart(product);
        }

        addToCart(product) {
            // Check if product with same size already exists
            const existingItem = this.cart.find(item => 
                item.name === product.name && item.size === product.size
            );

            if (existingItem) {
                existingItem.quantity += product.quantity;
            } else {
                this.cart.push(product);
            }

            this.saveCart();
            this.updateCartCount();
            this.showNotification('Added to cart successfully!', 'success');
        }

        handleQuantityChange(e) {
            const action = e.target.dataset.action || e.target.closest('.qty-btn').dataset.action;
            const quantityInput = e.target.closest('.quantity-selector').querySelector('#quantity');
            const currentValue = parseInt(quantityInput.value);
            
            if (action === 'increase' && currentValue < 10) {
                quantityInput.value = currentValue + 1;
            } else if (action === 'decrease' && currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }

        updateCartCount() {
            const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                cartCountElement.textContent = totalItems;
                cartCountElement.setAttribute('aria-label', `${totalItems} items in cart`);
            }
        }

        saveCart() {
            localStorage.setItem('grindctrl_cart', JSON.stringify(this.cart));
        }

        toggleCartDrawer() {
            // Simple cart display - can be enhanced with a proper drawer
            if (this.cart.length === 0) {
                this.showNotification('Your cart is empty', 'info');
                return;
            }

            const cartSummary = this.cart.map(item => 
                `${item.name} (${item.size}) - Qty: ${item.quantity} - ${item.price} ${item.currency}`
            ).join('\n');

            alert(`Cart Contents:\n${cartSummary}`);
        }

        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.setAttribute('role', 'alert');
            notification.setAttribute('aria-live', 'polite');
            notification.innerHTML = `
                <span class="notification-message">${message}</span>
                <button class="notification-close" aria-label="Close notification">&times;</button>
            `;

            // Add to page
            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);

            // Close button functionality
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.parentNode.removeChild(notification);
            });
        }
    }

    // Product Gallery Management
    class ProductGallery {
        constructor() {
            this.thumbnails = document.querySelectorAll('.thumbnail');
            this.mainImage = document.getElementById('productMainImage');
            this.init();
        }

        init() {
            if (!this.mainImage || this.thumbnails.length === 0) return;

            this.thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => this.switchImage(thumbnail, index));
                thumbnail.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.switchImage(thumbnail, index);
                    }
                });
            });
        }

        switchImage(clickedThumbnail, index) {
            // Update main image
            const newImageSrc = clickedThumbnail.dataset.image;
            if (newImageSrc) {
                this.mainImage.src = newImageSrc;
                this.mainImage.alt = clickedThumbnail.querySelector('img').alt.replace('thumbnail', 'main view');
            }

            // Update active states
            this.thumbnails.forEach((thumb, idx) => {
                thumb.classList.toggle('active', idx === index);
                thumb.setAttribute('aria-selected', idx === index);
                thumb.setAttribute('tabindex', idx === index ? '0' : '-1');
            });
        }
    }

    // Form Validation
    class FormValidator {
        constructor() {
            this.form = document.getElementById('orderForm');
            this.init();
        }

        init() {
            if (!this.form) return;

            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            this.bindFieldValidation();
        }

        bindFieldValidation() {
            const fields = this.form.querySelectorAll('input[required], textarea[required]');
            fields.forEach(field => {
                field.addEventListener('blur', () => this.validateField(field));
                field.addEventListener('input', () => this.clearFieldError(field));
            });
        }

        handleSubmit(e) {
            e.preventDefault();
            
            if (this.validateForm()) {
                this.submitOrder();
            }
        }

        validateForm() {
            let isValid = true;
            const requiredFields = this.form.querySelectorAll('input[required], textarea[required]');
            
            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            // Validate size selection
            const sizeSelected = this.form.querySelector('input[name="size"]:checked');
            if (!sizeSelected) {
                this.showFieldError('size', 'Please select a size');
                isValid = false;
            }

            return isValid;
        }

        validateField(field) {
            const value = field.value.trim();
            const fieldName = field.name;
            
            // Check if required field is empty
            if (field.hasAttribute('required') && !value) {
                this.showFieldError(fieldName, 'This field is required');
                return false;
            }

            // Email validation
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid email address');
                    return false;
                }
            }

            // Phone validation
            if (field.type === 'tel' && value) {
                const phoneRegex = /^[\d\s\+\-\(\)]{10,}$/;
                if (!phoneRegex.test(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid phone number');
                    return false;
                }
            }

            this.clearFieldError(fieldName);
            return true;
        }

        showFieldError(fieldName, message) {
            const errorElement = document.getElementById(`${fieldName}-error`);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.removeAttribute('hidden');
            }

            const field = document.getElementById(fieldName) || document.querySelector(`input[name="${fieldName}"]`);
            if (field) {
                field.setAttribute('aria-invalid', 'true');
                field.classList.add('field-error-state');
            }
        }

        clearFieldError(fieldName) {
            const field = typeof fieldName === 'string' 
                ? (document.getElementById(fieldName) || document.querySelector(`input[name="${fieldName}"]`))
                : fieldName;
                
            if (!field) return;
            
            const actualFieldName = typeof fieldName === 'string' ? fieldName : field.name;
            const errorElement = document.getElementById(`${actualFieldName}-error`);
            
            if (errorElement) {
                errorElement.setAttribute('hidden', '');
            }
            
            field.setAttribute('aria-invalid', 'false');
            field.classList.remove('field-error-state');
        }

        async submitOrder() {
            const formData = new FormData(this.form);
            const orderData = Object.fromEntries(formData);
            
            // Add cart items to order
            const cart = JSON.parse(localStorage.getItem('grindctrl_cart')) || [];
            orderData.items = cart;
            orderData.total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            try {
                // Show loading state
                const submitBtn = this.form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Processing...';
                submitBtn.disabled = true;

                // Submit order (replace with actual endpoint)
                const response = await this.sendOrder(orderData);
                
                if (response.success) {
                    this.showSuccess('Order submitted successfully! You will receive a confirmation email shortly.');
                    this.form.reset();
                    localStorage.removeItem('grindctrl_cart');
                    
                    // Update cart count
                    const cartCountElement = document.getElementById('cartCount');
                    if (cartCountElement) {
                        cartCountElement.textContent = '0';
                    }
                } else {
                    throw new Error(response.message || 'Order submission failed');
                }
            } catch (error) {
                console.error('Order submission error:', error);
                this.showError('Failed to submit order. Please try again.');
            } finally {
                // Reset button state
                const submitBtn = this.form.querySelector('button[type="submit"]');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        async sendOrder(orderData) {
            const config = window.GrindCTRLConfig;
            
            // If webhook is configured, send to n8n
            if (config && config.webhooks.enabled && config.webhooks.url) {
                const response = await fetch(config.webhooks.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                    timeout: config.webhooks.timeout || 30000
                });
                
                return await response.json();
            } else {
                // Fallback: log to console (for development)
                console.log('Order Data:', orderData);
                return { success: true, message: 'Order received (development mode)' };
            }
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showNotification(message, type) {
            // Reuse the notification system from WooCommerceCart
            const cart = new WooCommerceCart();
            cart.showNotification(message, type);
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        new WooCommerceCart();
        new ProductGallery();
        new FormValidator();
        
        console.log('WooCommerce integration initialized');
    });

})();