/**
 * Main JavaScript for GrindCTRL T-Shirt E-commerce Site
 * Handles form interactions, cart functionality, and n8n webhook integration
 */

// Global state management
const AppState = {
    cart: {
        count: 0,
        items: []
    },
    product: {
        name: 'Luxury Cropped Black T-Shirt',
        price: 300.00,
        currency: 'EGP',
        originalPrice: 350.00
    },
    webhookUrl: '', // Will be set from environment or fallback
    isSubmitting: false
};

// Configuration
const CONFIG = {
    // Get webhook URL from environment variable with fallback
    webhookUrl: getWebhookUrl(),
    maxQuantity: 10,
    minQuantity: 1,
    shippingCost: 0, // Free shipping
    notifications: {
        duration: 5000, // 5 seconds
        position: 'top-right'
    }
};

/**
 * Get webhook URL from configuration or environment
 */
function getWebhookUrl() {
    // Try to get from global config first
    if (window.CONFIG && window.CONFIG.WEBHOOK_URL && window.CONFIG.WEBHOOK_URL !== 'PLACEHOLDER_WEBHOOK_URL') {
        return window.CONFIG.WEBHOOK_URL;
    }
    
    // For development/testing, check if we have it in a meta tag or other source
    const metaWebhook = document.querySelector('meta[name="webhook-url"]');
    if (metaWebhook) {
        return metaWebhook.getAttribute('content');
    }
    
    // Fallback - this should be replaced during deployment
    return null;
}

/**
 * DOM Content Loaded Event Handler
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeQuantityControls();
    initializeFormHandlers();
    initializeCartFunctionality();
    initializeNotificationSystem();
    initializeKeyboardNavigation();
    
    // Update initial display
    updateOrderSummary();
    updateCartDisplay();
    
    // Set up smooth scrolling for anchor links
    initializeSmoothScrolling();
});

/**
 * Initialize quantity selector controls
 */
function initializeQuantityControls() {
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const quantityInput = document.getElementById('quantity');
    
    if (!decreaseBtn || !increaseBtn || !quantityInput) {
        return;
    }
    
    decreaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value) || CONFIG.minQuantity;
        if (currentValue > CONFIG.minQuantity) {
            quantityInput.value = currentValue - 1;
            updateOrderSummary();
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value) || CONFIG.minQuantity;
        if (currentValue < CONFIG.maxQuantity) {
            quantityInput.value = currentValue + 1;
            updateOrderSummary();
        }
    });
    
    // Handle direct input changes
    quantityInput.addEventListener('change', function() {
        let value = parseInt(this.value) || CONFIG.minQuantity;
        value = Math.max(CONFIG.minQuantity, Math.min(CONFIG.maxQuantity, value));
        this.value = value;
        updateOrderSummary();
    });
    
    console.log('Quantity controls initialized');
}

/**
 * Initialize form event handlers
 */
function initializeFormHandlers() {
    const orderForm = document.getElementById('orderForm');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const buyNowBtn = document.getElementById('buyNowBtn');
    
    if (!orderForm) {
        console.error('Order form not found');
        return;
    }
    
    // Add to cart functionality
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', handleAddToCart);
    }
    
    // Form submission
    orderForm.addEventListener('submit', handleFormSubmission);
    
    // Real-time form validation
    const formInputs = orderForm.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
    
    console.log('Form handlers initialized');
}

/**
 * Handle add to cart functionality
 */
function handleAddToCart(event) {
    event.preventDefault();
    
    const formData = getFormData();
    if (!validateRequiredFields(['size', 'quantity'])) {
        showNotification('Please select size and quantity', 'warning');
        return;
    }
    
    const cartItem = {
        id: Date.now(),
        name: AppState.product.name,
        size: formData.size,
        quantity: parseInt(formData.quantity),
        price: AppState.product.price,
        total: AppState.product.price * parseInt(formData.quantity)
    };
    
    AppState.cart.items.push(cartItem);
    AppState.cart.count += parseInt(formData.quantity);
    
    updateCartDisplay();
    showNotification(`Added ${formData.quantity} item(s) to cart`, 'success');
    
    console.log('Item added to cart:', cartItem);
}

/**
 * Handle form submission and webhook integration
 */
async function handleFormSubmission(event) {
    event.preventDefault();
    
    if (AppState.isSubmitting) {
        return;
    }
    
    const formData = getFormData();
    
    // Validate all required fields
    if (!validateForm(formData)) {
        showNotification('Please fill in all required fields correctly', 'error');
        return;
    }
    
    AppState.isSubmitting = true;
    setLoadingState(true);
    
    try {
        // Form data validation completed
        
        // Prepare order data for webhook
        const orderData = prepareOrderData(formData);
        
        // Send to n8n webhook
        const response = await sendOrderToWebhook(orderData);
        
        if (response.success) {
            showOrderSuccess(orderData);
            resetForm();
            AppState.cart = { count: 0, items: [] };
            updateCartDisplay();
        } else {
            throw new Error(response.message || 'Order processing failed');
        }
        
    } catch (error) {
        showNotification(`Order failed: ${error.message}`, 'error');
    } finally {
        AppState.isSubmitting = false;
        setLoadingState(false);
    }
}

/**
 * Get all form data with sanitization
 */
function getFormData() {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        // Sanitize input data
        data[key] = sanitizeInput(value);
    }
    
    return data;
}

/**
 * Sanitize input data
 */
function sanitizeInput(input) {
    if (typeof input !== 'string') return input;
    
    // Remove potential XSS characters and trim whitespace
    return input
        .trim()
        .replace(/[<>'"&]/g, function(match) {
            const escapeMap = {
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#x27;',
                '&': '&amp;'
            };
            return escapeMap[match];
        });
}

/**
 * Validate form data
 */
function validateForm(data) {
    const requiredFields = [
        'size', 'quantity', 'paymentMethod', 'firstName', 'lastName', 
        'email', 'phone', 'address', 'city', 'postalCode'
    ];
    
    return validateRequiredFields(requiredFields) && validateEmailFormat(data.email);
}

/**
 * Validate required fields
 */
function validateRequiredFields(fields) {
    let isValid = true;
    
    fields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && (!field.value || field.value.trim() === '')) {
            markFieldAsError(field, `${getFieldLabel(fieldName)} is required`);
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Validate email format
 */
function validateEmailFormat(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailField = document.getElementById('email');
    
    if (!emailRegex.test(email)) {
        markFieldAsError(emailField, 'Please enter a valid email address');
        return false;
    }
    
    return true;
}

/**
 * Validate individual field
 */
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    clearFieldError(field);
    
    if (field.required && !value) {
        markFieldAsError(field, `${getFieldLabel(field.name)} is required`);
        return false;
    }
    
    if (field.type === 'email' && value && !validateEmailFormat(value)) {
        return false;
    }
    
    return true;
}

/**
 * Mark field as having an error with ARIA support
 */
function markFieldAsError(field, message) {
    field.classList.add('error');
    field.setAttribute('aria-invalid', 'true');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message with unique ID
    const errorId = field.id + '-error';
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.id = errorId;
    errorElement.textContent = message;
    errorElement.style.color = 'var(--error-color)';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '4px';
    errorElement.setAttribute('role', 'alert');
    errorElement.setAttribute('aria-live', 'polite');
    
    // Associate error with field
    field.setAttribute('aria-describedby', errorId);
    
    field.parentNode.appendChild(errorElement);
}

/**
 * Clear field error state with ARIA cleanup
 */
function clearFieldError(field) {
    if (typeof field === 'object' && field.target) {
        field = field.target;
    }
    
    field.classList.remove('error');
    field.setAttribute('aria-invalid', 'false');
    field.removeAttribute('aria-describedby');
    
    const errorElement = field.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Get human-readable field label
 */
function getFieldLabel(fieldName) {
    const labels = {
        size: 'Size',
        quantity: 'Quantity',
        paymentMethod: 'Payment Method',
        firstName: 'First Name',
        lastName: 'Last Name',
        email: 'Email Address',
        phone: 'Phone Number',
        address: 'Shipping Address',
        city: 'City',
        postalCode: 'Postal Code'
    };
    
    return labels[fieldName] || fieldName;
}

/**
 * Prepare order data for webhook - Updated to match Excel columns exactly
 */
function prepareOrderData(formData) {
    
    const quantity = parseInt(formData.quantity);
    const subtotal = AppState.product.price * quantity;
    const total = subtotal + CONFIG.shippingCost;
    const customerName = `${formData.firstName} ${formData.lastName}`.trim();
    const orderId = generateOrderId();
    const orderDate = new Date().toISOString();
    
    // Auto-generate system values
    const codAmount = formData.paymentMethod === "Cash on Delivery" ? total : 0; // COD Amount = Total only for COD, otherwise 0
    const trackingNumber = generateTrackingNumber();
    const courier = "BOSTA"; // Fixed courier company
    
    // Create product name with size
    const productName = formData.size ? `${AppState.product.name} - ${formData.size}` : AppState.product.name;
    
    // Return data in the exact 14-field format required for Excel
    return {
        "Order ID": orderId,
        "Customer Name": customerName,
        "Phone": formData.phone,
        "City": formData.city,
        "Address": formData.address,
        "COD Amount": codAmount.toFixed(2),
        "Tracking Number": trackingNumber,
        "Courier": courier,
        "Total": total.toFixed(2),
        "Date": orderDate,
        "Status": "New", // System-generated status - always starts as "New"
        "Payment Method": formData.paymentMethod,
        "Product": productName,
        "Quantity": quantity.toString()
    };
}

/**
 * Generate unique order ID
 */
function generateOrderId() {
    const timestamp = Date.now().toString(36);
    const randomStr = Math.random().toString(36).substring(2, 8);
    return `GC-${timestamp}-${randomStr}`.toUpperCase();
}

/**
 * Generate random tracking number
 */
function generateTrackingNumber() {
    const prefix = 'TRK';
    const randomNum = Math.floor(Math.random() * 1000000000); // 9-digit number
    return `${prefix}${randomNum.toString().padStart(9, '0')}`;
}

/**
 * Send order data to n8n webhook
 */
async function sendOrderToWebhook(orderData) {
    const webhookUrl = CONFIG.webhookUrl;
    
    // Check if webhook URL is configured
    if (!webhookUrl || webhookUrl === 'WEBHOOK_URL_NOT_CONFIGURED' || webhookUrl === '123') {
        console.error('Webhook URL not properly configured');
        return {
            success: false,
            message: 'Webhook URL not configured. Please set up your n8n webhook URL in the configuration.',
            error: new Error('WEBHOOK_NOT_CONFIGURED')
        };
    }
    
    try {
        console.log('Sending order to webhook:', webhookUrl);
        console.log('Order data:', orderData);
        
        const response = await fetch(webhookUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Agent': 'GrindCTRL-Website/1.0',
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        console.log('Webhook response status:', response.status);
        
        if (response.ok) {
            let responseData = {};
            try {
                responseData = await response.json();
            } catch (e) {
                console.log('Webhook response is not JSON, treating as success');
                responseData = { message: 'Order processed successfully' };
            }
            
            console.log('Webhook response:', responseData);
            
            return {
                success: true,
                data: responseData,
                message: 'Order submitted successfully'
            };
        } else {
            const errorText = await response.text();
            console.error('Webhook error response:', errorText);
            
            return {
                success: false,
                message: `Server error: ${response.status} - ${errorText}`,
                error: new Error(`HTTP ${response.status}`)
            };
        }
        
    } catch (error) {
        console.error('Webhook request failed:', error);
        
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            return {
                success: false,
                message: 'Network error. Please check your internet connection and try again.',
                error: error
            };
        }
        
        return {
            success: false,
            message: `Request failed: ${error.message}`,
            error: error
        };
    }
}

/**
 * Update order summary display
 */
function updateOrderSummary() {
    const quantityInput = document.getElementById('quantity');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    
    if (!quantityInput || !subtotalElement || !totalElement) {
        return;
    }
    
    const quantity = parseInt(quantityInput.value) || 1;
    const subtotal = AppState.product.price * quantity;
    const total = subtotal + CONFIG.shippingCost;
    
    subtotalElement.textContent = `${subtotal.toFixed(2)} ${AppState.product.currency}`;
    totalElement.textContent = `${total.toFixed(2)} ${AppState.product.currency}`;
}

/**
 * Update cart display
 */
function updateCartDisplay() {
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = AppState.cart.count;
        cartCountElement.style.display = AppState.cart.count > 0 ? 'flex' : 'none';
    }
}

/**
 * Set loading state for form submission
 */
function setLoadingState(loading) {
    const buyNowBtn = document.getElementById('buyNowBtn');
    const btnText = buyNowBtn.querySelector('.btn-text');
    const btnLoader = buyNowBtn.querySelector('.btn-loader');
    
    if (loading) {
        buyNowBtn.disabled = true;
        buyNowBtn.classList.add('loading');
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-flex';
    } else {
        buyNowBtn.disabled = false;
        buyNowBtn.classList.remove('loading');
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    }
}

/**
 * Show order success modal
 */
function showOrderSuccess(orderData) {
    const modal = document.getElementById('successModal');
    const orderDetails = document.getElementById('orderDetails');
    
    if (orderDetails) {
        orderDetails.innerHTML = `
            <div class="order-info">
                <p><strong>Order ID:</strong> ${orderData['Order ID']}</p>
                <p><strong>Product:</strong> ${orderData.Product}</p>
                <p><strong>Quantity:</strong> ${orderData.Quantity}</p>
                <p><strong>Total:</strong> ${orderData.Total} ${AppState.product.currency}</p>
                <p><strong>Payment Method:</strong> ${orderData['Payment Method']}</p>
            </div>
        `;
    }
    
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    showNotification('Order placed successfully!', 'success');
}

/**
 * Close modal
 */
function closeModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

/**
 * Reset form to initial state
 */
function resetForm() {
    const form = document.getElementById('orderForm');
    if (form) {
        form.reset();
        
        // Reset quantity to 1
        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantityInput.value = 1;
        }
        
        // Reset status to Confirmed
        const statusField = document.getElementById('status');
        if (statusField) {
            statusField.value = 'Confirmed';
        }
        
        // Reset payment method to Cash on Delivery
        const paymentField = document.getElementById('paymentMethod');
        if (paymentField) {
            paymentField.value = 'Cash on Delivery';
        }
        
        // Clear any field errors
        const errorElements = form.querySelectorAll('.field-error');
        errorElements.forEach(error => error.remove());
        
        const errorFields = form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));
        
        updateOrderSummary();
    }
}

/**
 * Initialize notification system
 */
function initializeNotificationSystem() {
    // Create notification styles if not already present
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--light-grey);
                color: var(--text-color);
                padding: var(--spacing-md);
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-heavy);
                z-index: 1000;
                max-width: 300px;
                border-left: 4px solid var(--primary-color);
                opacity: 0;
                transform: translateX(100%);
                transition: all var(--transition-medium);
            }
            
            .notification.show {
                opacity: 1;
                transform: translateX(0);
            }
            
            .notification.success {
                border-left-color: var(--success-color);
            }
            
            .notification.warning {
                border-left-color: var(--warning-color);
            }
            
            .notification.error {
                border-left-color: var(--error-color);
            }
            
            .notification-content {
                display: flex;
                align-items: center;
                gap: var(--spacing-sm);
            }
            
            .notification-close {
                background: none;
                border: none;
                color: var(--text-color);
                cursor: pointer;
                padding: 0;
                margin-left: auto;
                opacity: 0.7;
            }
            
            .notification-close:hover {
                opacity: 1;
            }
        `;
        document.head.appendChild(styles);
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const messageElement = notification.querySelector('.notification-message');
    const iconElement = notification.querySelector('.notification-icon');
    
    if (!notification || !messageElement || !iconElement) {
        return;
    }
    
    // Set message
    messageElement.textContent = message;
    
    // Set icon based on type
    const icons = {
        success: 'fas fa-check-circle',
        warning: 'fas fa-exclamation-triangle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };
    
    iconElement.className = `notification-icon ${icons[type] || icons.info}`;
    
    // Set notification class
    notification.className = `notification ${type}`;
    
    // Show notification
    notification.style.display = 'block';
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Auto-hide after duration
    setTimeout(() => {
        hideNotification();
    }, CONFIG.notifications.duration);
}

/**
 * Hide notification
 */
function hideNotification() {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }
}

/**
 * Initialize smooth scrolling
 */
function initializeSmoothScrolling() {
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Initialize cart functionality (placeholder for future enhancement)
 */
function initializeCartFunctionality() {
    // Cart functionality is handled by other functions
    // This is a placeholder for future cart-related features
}

/**
 * Initialize keyboard navigation support
 */
function initializeKeyboardNavigation() {
    // Add keyboard support for cart icon
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                // Future: Open cart modal
                showNotification('Cart functionality coming soon!', 'info');
            }
        });
    }
    
    // Add keyboard support for modal close buttons
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close any open modals
            const modals = document.querySelectorAll('.modal[style*="display: block"]');
            modals.forEach(modal => {
                modal.style.display = 'none';
            });
            
            // Hide notifications
            hideNotification();
        }
    });
    
    // Add keyboard support for quantity buttons
    const qtyButtons = document.querySelectorAll('.qty-btn');
    qtyButtons.forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    });
}

// Export functions for global access
window.closeModal = closeModal;
window.hideNotification = hideNotification;
