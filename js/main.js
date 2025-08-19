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
    console.warn('Webhook URL not configured. Orders will not be processed.');
    return null;
}

/**
 * DOM Content Loaded Event Handler
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('GrindCTRL E-commerce Site Initialized');
    
    // Initialize all components
    initializeQuantityControls();
    initializeFormHandlers();
    initializeCartFunctionality();
    initializeNotificationSystem();
    
    // Update initial display
    updateOrderSummary();
    updateCartDisplay();
    
    // Set up smooth scrolling for anchor links
    initializeSmoothScrolling();
    
    console.log('All components initialized successfully');
});

/**
 * Initialize quantity selector controls
 */
function initializeQuantityControls() {
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const quantityInput = document.getElementById('quantity');
    
    if (!decreaseBtn || !increaseBtn || !quantityInput) {
        console.warn('Quantity controls not found');
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
        console.error('Order submission error:', error);
        showNotification(`Order failed: ${error.message}`, 'error');
    } finally {
        AppState.isSubmitting = false;
        setLoadingState(false);
    }
}

/**
 * Get all form data
 */
function getFormData() {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    return data;
}

/**
 * Validate form data
 */
function validateForm(data) {
    const requiredFields = [
        'size', 'quantity', 'firstName', 'lastName', 
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
 * Mark field as having an error
 */
function markFieldAsError(field, message) {
    field.classList.add('error');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.color = 'var(--error-color)';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '4px';
    
    field.parentNode.appendChild(errorElement);
}

/**
 * Clear field error state
 */
function clearFieldError(field) {
    if (typeof field === 'object' && field.target) {
        field = field.target;
    }
    
    field.classList.remove('error');
    
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
 * Prepare order data for webhook
 */
function prepareOrderData(formData) {
    const quantity = parseInt(formData.quantity);
    const subtotal = AppState.product.price * quantity;
    const total = subtotal + CONFIG.shippingCost;
    const customerName = `${formData.firstName} ${formData.lastName}`.trim();
    const orderId = generateOrderId();
    const orderDate = new Date().toISOString();
    
    // Auto-generate system values
    const codAmount = total; // COD Amount = Total Amount
    const trackingNumber = generateTrackingNumber();
    const courier = "BOSTA"; // Fixed courier company
    
    // Return data in the exact 11-field format required
    return {
        "Order ID": orderId,
        "Customer Name": customerName,
        "Phone": formData.phone,
        "City": formData.city,
        "Address": formData.address,
        "COD Amount": codAmount.toFixed(2),
        "Tracking Number": trackingNumber,
        "Courier": courier,
        "Email": formData.email,
        "Total Amount": total.toFixed(2),
        "Date": orderDate,
        
        // Keep additional data for internal use
        _internal: {
            product: {
                name: AppState.product.name,
                size: formData.size,
                quantity: quantity,
                unitPrice: AppState.product.price,
                currency: AppState.product.currency
            },
            pricing: {
                subtotal: subtotal,
                shipping: CONFIG.shippingCost,
                currency: AppState.product.currency
            },
            source: 'grindctrl-website',
            userAgent: navigator.userAgent,
            timestamp: Date.now()
        }
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
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Handle different response types
        let responseData = {};
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            responseData = await response.json();
        } else {
            responseData = { message: await response.text() };
        }
        
        console.log('Webhook response:', responseData);
        
        return {
            success: true,
            data: responseData,
            orderId: orderData.orderId
        };
        
    } catch (error) {
        console.error('Webhook error:', error);
        
        // Return structured error response
        return {
            success: false,
            message: `Order submission failed: ${error.message}`,
            error: error
        };
    }
}

/**
 * Show order success modal
 */
function showOrderSuccess(orderData) {
    const modal = document.getElementById('successModal');
    const orderDetails = document.getElementById('orderDetails');
    
    if (!modal || !orderDetails) {
        console.error('Success modal elements not found');
        return;
    }
    
    // Populate order details
    orderDetails.innerHTML = `
        <h4>Order Details</h4>
        <p><strong>Order ID:</strong> ${orderData["Order ID"]}</p>
        <p><strong>Customer:</strong> ${orderData["Customer Name"]}</p>
        <p><strong>Product:</strong> ${orderData._internal.product.name}</p>
        <p><strong>Size:</strong> ${orderData._internal.product.size}</p>
        <p><strong>Quantity:</strong> ${orderData._internal.product.quantity}</p>
        <p><strong>Total Amount:</strong> ${orderData["Total Amount"]} EGP</p>
        <p><strong>COD Amount:</strong> ${orderData["COD Amount"]} EGP</p>
        <p><strong>Email:</strong> ${orderData["Email"]}</p>
    `;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    console.log('Order success modal displayed');
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
 * Set loading state for form submission
 */
function setLoadingState(isLoading) {
    const buyNowBtn = document.getElementById('buyNowBtn');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (buyNowBtn) {
        if (isLoading) {
            buyNowBtn.classList.add('loading');
            buyNowBtn.disabled = true;
        } else {
            buyNowBtn.classList.remove('loading');
            buyNowBtn.disabled = false;
        }
    }
    
    if (addToCartBtn) {
        addToCartBtn.disabled = isLoading;
    }
}

/**
 * Reset form to initial state
 */
function resetForm() {
    const form = document.getElementById('orderForm');
    if (form) {
        form.reset();
        document.getElementById('quantity').value = 1;
        updateOrderSummary();
        
        // Clear all error states
        const errorFields = form.querySelectorAll('.error');
        errorFields.forEach(field => clearFieldError(field));
        
        const errorMessages = form.querySelectorAll('.field-error');
        errorMessages.forEach(error => error.remove());
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
 * Initialize cart functionality
 */
function initializeCartFunctionality() {
    const cartIcon = document.querySelector('.cart-icon');
    
    if (cartIcon) {
        cartIcon.addEventListener('click', function() {
            if (AppState.cart.count > 0) {
                showCartSummary();
            } else {
                showNotification('Your cart is empty', 'info');
            }
        });
    }
    
    console.log('Cart functionality initialized');
}

/**
 * Update cart display
 */
function updateCartDisplay() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = AppState.cart.count;
        cartCount.style.display = AppState.cart.count > 0 ? 'flex' : 'none';
    }
}

/**
 * Show cart summary
 */
function showCartSummary() {
    const cartItems = AppState.cart.items;
    const totalItems = AppState.cart.count;
    const totalValue = cartItems.reduce((sum, item) => sum + item.total, 0);
    
    const message = `Cart: ${totalItems} item(s) - Total: ${totalValue.toFixed(2)} ${AppState.product.currency}`;
    showNotification(message, 'info');
}

/**
 * Initialize notification system
 */
function initializeNotificationSystem() {
    // Create notification container if it doesn't exist
    let notification = document.getElementById('notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.className = 'notification';
        notification.style.display = 'none';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="notification-icon"></i>
                <span class="notification-message"></span>
                <button class="notification-close" onclick="hideNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
    }
    
    console.log('Notification system initialized');
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const messageElement = notification.querySelector('.notification-message');
    const iconElement = notification.querySelector('.notification-icon');
    
    if (!notification || !messageElement || !iconElement) {
        console.warn('Notification elements not found');
        return;
    }
    
    // Set message
    messageElement.textContent = message;
    
    // Set icon based on type
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    iconElement.className = `notification-icon ${icons[type] || icons.info}`;
    
    // Set notification type class
    notification.className = `notification ${type}`;
    
    // Show notification
    notification.style.display = 'block';
    
    // Auto-hide after duration
    setTimeout(() => {
        hideNotification();
    }, CONFIG.notifications.duration);
    
    console.log(`Notification shown: ${type} - ${message}`);
}

/**
 * Hide notification
 */
function hideNotification() {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.style.display = 'none';
    }
}

/**
 * Initialize smooth scrolling
 */
function initializeSmoothScrolling() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    console.log('Smooth scrolling initialized');
}

/**
 * Handle window resize events
 */
window.addEventListener('resize', function() {
    // Update any responsive elements if needed
    updateCartDisplay();
});

/**
 * Handle page visibility changes
 */
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Page is now visible, refresh any time-sensitive data
        console.log('Page became visible');
    }
});

/**
 * Error handling for unhandled promises
 */
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showNotification('An unexpected error occurred. Please try again.', 'error');
});

/**
 * Global error handler
 */
window.addEventListener('error', function(event) {
    console.error('Global error:', event.error);
    showNotification('An error occurred. Please refresh the page.', 'error');
});

// Export functions for global access
window.closeModal = closeModal;
window.hideNotification = hideNotification;

console.log('GrindCTRL JavaScript loaded successfully');
