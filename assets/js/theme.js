/**
 * GrindCTRL Theme JavaScript
 * Handles theme interactions and WooCommerce enhancements
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
    });

    /**
     * Initialize all theme functionality
     */
    function initializeTheme() {
        console.log('GrindCTRL Theme Initialized');
        
        // Initialize components
        initializeNavigation();
        initializeCart();
        initializeAccessibility();
        initializePerformance();
        
        // WooCommerce specific features
        if (typeof wc_add_to_cart_params !== 'undefined') {
            initializeWooCommerce();
        }
    }

    /**
     * Navigation functionality
     */
    function initializeNavigation() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                this.setAttribute('aria-expanded', !isExpanded);
                navMenu.classList.toggle('toggled');
                
                // Focus management
                if (!isExpanded) {
                    const firstLink = navMenu.querySelector('a');
                    if (firstLink) {
                        firstLink.focus();
                    }
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMenu.classList.contains('toggled')) {
                    navMenu.classList.remove('toggled');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.focus();
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    navMenu.classList.remove('toggled');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Smooth scrolling for anchor links
        initializeSmoothScrolling();
    }

    /**
     * Smooth scrolling for anchor links
     */
    function initializeSmoothScrolling() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const target = document.querySelector(href);
                
                if (target && href !== '#') {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update URL without triggering scroll
                    history.pushState(null, null, href);
                    
                    // Focus target for accessibility
                    target.focus();
                }
            });
        });
    }

    /**
     * Cart functionality
     */
    function initializeCart() {
        // Update cart count on AJAX add to cart
        document.body.addEventListener('added_to_cart', function(e) {
            updateCartCount();
            showNotification('Product added to cart!', 'success');
        });

        // Cart count update function
        function updateCartCount() {
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement && typeof wc_add_to_cart_params !== 'undefined') {
                fetch(wc_add_to_cart_params.wc_ajax_url + '?action=get_cart_count', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.count !== undefined) {
                        cartCountElement.textContent = data.count;
                        cartCountElement.style.display = data.count > 0 ? 'flex' : 'none';
                    }
                })
                .catch(error => {
                    console.warn('Cart count update failed:', error);
                });
            }
        }

        // Initial cart count update
        updateCartCount();
    }

    /**
     * WooCommerce specific functionality
     */
    function initializeWooCommerce() {
        // Quantity input enhancements
        const quantityInputs = document.querySelectorAll('input[type="number"].qty');
        
        quantityInputs.forEach(function(input) {
            // Add increment/decrement buttons if they don't exist
            if (!input.parentNode.querySelector('.qty-btn')) {
                addQuantityButtons(input);
            }
        });

        // Variation form enhancements
        const variationForms = document.querySelectorAll('.variations_form');
        variationForms.forEach(function(form) {
            enhanceVariationForm(form);
        });

        // Product image gallery
        initializeProductGallery();
    }

    /**
     * Add quantity increment/decrement buttons
     */
    function addQuantityButtons(input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'quantity-selector';
        
        const decreaseBtn = document.createElement('button');
        decreaseBtn.type = 'button';
        decreaseBtn.className = 'qty-btn decrease';
        decreaseBtn.textContent = '-';
        decreaseBtn.setAttribute('aria-label', 'Decrease quantity');
        
        const increaseBtn = document.createElement('button');
        increaseBtn.type = 'button';
        increaseBtn.className = 'qty-btn increase';
        increaseBtn.textContent = '+';
        increaseBtn.setAttribute('aria-label', 'Increase quantity');
        
        // Wrap input and add buttons
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(decreaseBtn);
        wrapper.appendChild(input);
        wrapper.appendChild(increaseBtn);
        
        // Add event listeners
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const min = parseInt(input.getAttribute('min')) || 1;
            if (currentValue > min) {
                input.value = currentValue - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const max = parseInt(input.getAttribute('max')) || 999;
            if (currentValue < max) {
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    /**
     * Enhance variation forms
     */
    function enhanceVariationForm(form) {
        const selectElements = form.querySelectorAll('select');
        
        selectElements.forEach(function(select) {
            // Add custom styling class
            select.classList.add('enhanced-select');
            
            // Custom change handler
            select.addEventListener('change', function() {
                // Add visual feedback
                this.classList.add('changed');
                setTimeout(() => {
                    this.classList.remove('changed');
                }, 300);
            });
        });
    }

    /**
     * Product image gallery initialization
     */
    function initializeProductGallery() {
        const productImages = document.querySelectorAll('.woocommerce-product-gallery__image');
        
        productImages.forEach(function(image) {
            // Add keyboard navigation
            image.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
            
            // Add loading lazy attribute if not present
            const img = image.querySelector('img');
            if (img && !img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
        });
    }

    /**
     * Accessibility enhancements
     */
    function initializeAccessibility() {
        // Skip link focus fix
        const skipLink = document.querySelector('.skip-link');
        if (skipLink) {
            skipLink.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.focus();
                    // Ensure focus is visible
                    target.style.outline = '2px solid #E74C3C';
                    setTimeout(() => {
                        target.style.outline = '';
                    }, 3000);
                }
            });
        }

        // Form validation enhancements
        const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
        requiredFields.forEach(function(field) {
            field.addEventListener('invalid', function(e) {
                e.preventDefault();
                
                // Custom validation message
                const message = this.validationMessage;
                showFieldError(this, message);
                
                // Focus the field
                this.focus();
            });
            
            field.addEventListener('input', function() {
                clearFieldError(this);
            });
        });

        // Announce dynamic content changes
        createAriaLiveRegion();
    }

    /**
     * Performance optimizations
     */
    function initializePerformance() {
        // Lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        imageObserver.unobserve(img);
                    }
                });
            });

            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        }

        // Preload critical resources
        preloadCriticalResources();
    }

    /**
     * Utility Functions
     */

    /**
     * Show notification to user
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${escapeHtml(message)}</span>
                <button class="notification-close" aria-label="Close notification">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', function() {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });
        
        // Announce to screen readers
        announceToScreenReader(message);
    }

    /**
     * Show field error
     */
    function showFieldError(field, message) {
        clearFieldError(field);
        
        field.classList.add('error');
        
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        errorElement.id = field.id + '-error';
        
        field.setAttribute('aria-describedby', errorElement.id);
        field.parentNode.appendChild(errorElement);
    }

    /**
     * Clear field error
     */
    function clearFieldError(field) {
        field.classList.remove('error');
        
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
        
        field.removeAttribute('aria-describedby');
    }

    /**
     * Create ARIA live region for announcements
     */
    function createAriaLiveRegion() {
        if (!document.getElementById('aria-live-region')) {
            const liveRegion = document.createElement('div');
            liveRegion.id = 'aria-live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.style.position = 'absolute';
            liveRegion.style.left = '-10000px';
            liveRegion.style.width = '1px';
            liveRegion.style.height = '1px';
            liveRegion.style.overflow = 'hidden';
            
            document.body.appendChild(liveRegion);
        }
    }

    /**
     * Announce message to screen readers
     */
    function announceToScreenReader(message) {
        const liveRegion = document.getElementById('aria-live-region');
        if (liveRegion) {
            liveRegion.textContent = message;
            
            // Clear after announcement
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 1000);
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }

    /**
     * Preload critical resources
     */
    function preloadCriticalResources() {
        // Preload critical fonts
        const fontLinks = [
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
            'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
        ];
        
        fontLinks.forEach(function(href) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'style';
            link.href = href;
            link.onload = function() {
                this.rel = 'stylesheet';
            };
            document.head.appendChild(link);
        });
    }

    // Export for testing purposes
    window.GrindCTRLTheme = {
        showNotification: showNotification,
        announceToScreenReader: announceToScreenReader
    };

})();
