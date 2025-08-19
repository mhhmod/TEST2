// Main application script for GrindCTRL
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize the application
function initializeApp() {
    console.log('🚀 GrindCTRL Application Starting...');
    
    // Initialize quantity controls
    initializeQuantityControls();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize smooth scrolling
    initializeSmoothScrolling();
    
    // Initialize payment method change handler
    initializePaymentMethodHandler();
    
    // Load admin data if needed
    loadAdminData();
    
    console.log('✅ GrindCTRL Application Ready');
}

// Initialize quantity controls
function initializeQuantityControls() {
    const quantityInput = document.getElementById('quantity');
    
    if (quantityInput) {
        // Update summary when quantity changes
        quantityInput.addEventListener('input', function() {
            validateQuantity();
            if (orderManager) {
                orderManager.updateOrderSummary();
            }
        });
        
        // Initialize with default value
        updateQuantity(0);
    }
}

// Update quantity with bounds checking
function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) return;
    
    let newQuantity = parseInt(quantityInput.value) + change;
    
    // Enforce min/max limits
    newQuantity = Math.max(1, Math.min(CONFIG.product.maxQuantity, newQuantity));
    
    quantityInput.value = newQuantity;
    
    // Trigger input event to update summary
    quantityInput.dispatchEvent(new Event('input'));
}

// Validate quantity input
function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) return;
    
    let quantity = parseInt(quantityInput.value);
    
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
    } else if (quantity > CONFIG.product.maxQuantity) {
        quantity = CONFIG.product.maxQuantity;
        if (orderManager) {
            orderManager.showNotification(
                `Maximum quantity is ${CONFIG.product.maxQuantity}`,
                'warning'
            );
        }
    }
    
    quantityInput.value = quantity;
}

// Initialize form validation
function initializeFormValidation() {
    const form = document.getElementById('order-form');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(input);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(input);
        });
    });
}

// Validate individual form field
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Specific field validations
    switch (field.type) {
        case 'tel':
            if (value && !validatePhone(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
            break;
        case 'email':
            if (value && !validateEmail(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
            break;
    }
    
    // Show/hide error
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

// Show field error
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = 'var(--error-color)';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '4px';
    
    field.parentNode.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('error');
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Phone validation
function validatePhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Initialize smooth scrolling
function initializeSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
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

// Initialize payment method change handler
function initializePaymentMethodHandler() {
    const paymentMethodSelect = document.getElementById('payment-method');
    
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            if (orderManager) {
                orderManager.updateOrderSummary();
            }
        });
    }
}

// Add to cart function (global)
function addToCart() {
    if (orderManager) {
        orderManager.addToCart();
    }
}

// Toggle cart display
function toggleCart() {
    // For future cart sidebar implementation
    console.log('Cart toggle clicked');
    
    if (orderManager) {
        orderManager.showNotification('Cart functionality coming soon!', 'info');
    }
}

// Admin Panel Functions
function toggleAdminPanel() {
    const modal = document.getElementById('admin-modal');
    if (modal) {
        modal.style.display = 'flex';
        loadAdminData();
    }
}

function closeAdminPanel() {
    const modal = document.getElementById('admin-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function loadAdminData() {
    if (!orderManager) return;
    
    const stats = orderManager.getOrderStats();
    const orders = orderManager.getOrders();
    
    // Update statistics
    document.getElementById('total-orders').textContent = stats.totalOrders;
    document.getElementById('pending-orders').textContent = stats.pendingOrders;
    document.getElementById('total-revenue').textContent = `$${stats.totalRevenue}`;
    
    // Update orders list
    displayOrdersList(orders);
}

function displayOrdersList(orders) {
    const ordersList = document.getElementById('orders-list');
    if (!ordersList) return;
    
    if (orders.length === 0) {
        ordersList.innerHTML = '<p>No orders found.</p>';
        return;
    }
    
    // Sort orders by date (newest first)
    const sortedOrders = orders.sort((a, b) => new Date(b.date) - new Date(a.date));
    
    // Show only last 10 orders in admin panel
    const recentOrders = sortedOrders.slice(0, 10);
    
    const ordersHtml = recentOrders.map(order => `
        <div class="order-item">
            <h5>Order ${order.orderId}</h5>
            <p><strong>Customer:</strong> ${order.customerName}</p>
            <p><strong>Phone:</strong> ${order.phone}</p>
            <p><strong>City:</strong> ${order.city}</p>
            <p><strong>Total:</strong> $${(order.total || 0).toFixed(2)}</p>
            <p><strong>Date:</strong> ${new Date(order.date).toLocaleDateString()}</p>
            <p><strong>Status:</strong> <span class="order-status ${order.status.toLowerCase()}">${order.status}</span></p>

            <div style="margin-top: 10px;">
                <select onchange="updateOrderStatus('${order.orderId}', this.value)" style="margin-right: 10px;">
                    <option value="Pending" ${(order.status || 'Pending') === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Processing" ${order.status === 'Processing' ? 'selected' : ''}>Processing</option>
                    <option value="Completed" ${order.status === 'Completed' ? 'selected' : ''}>Completed</option>
                </select>
                <button class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;" onclick="viewOrderDetails('${order.orderId}')">
                    View Details
                </button>
            </div>
        </div>
    `).join('');
    
    ordersList.innerHTML = ordersHtml;
}

function updateOrderStatus(orderId, newStatus) {
    if (orderManager && orderManager.updateOrderStatus(orderId, newStatus)) {
        orderManager.showNotification(`Order ${orderId} status updated to ${newStatus}`, 'success');
        
        // Trigger webhook event
        if (webhookHandler) {
            triggerWebhookEvent('orderStatusChanged', {
                orderId: orderId,
                newStatus: newStatus,
                oldStatus: 'Previous Status' // Could be tracked more precisely
            });
        }
        
        // Refresh admin data
        loadAdminData();
    } else {
        orderManager.showNotification('Failed to update order status', 'error');
    }
}

function viewOrderDetails(orderId) {
    if (!orderManager) return;
    
    const orders = orderManager.getOrders();
    const order = orders.find(o => o.orderId === orderId);
    
    if (order) {
        // Create and show order details modal
        showOrderDetailsModal(order);
    }
}

function showOrderDetailsModal(order) {
    // Create modal HTML
    const modalHtml = `
        <div id="order-details-modal" class="modal" style="display: flex;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Order Details - ${order.orderId}</h2>
                    <button class="modal-close" onclick="closeOrderDetailsModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="order-details">
                        <h4>Customer Information</h4>
                        <p><strong>Name:</strong> ${order.customerName}</p>
                        <p><strong>Phone:</strong> ${order.phone}</p>
                        <p><strong>City:</strong> ${order.city}</p>
                        <p><strong>Address:</strong> ${order.address}</p>
                        
                        <h4 style="margin-top: 20px;">Order Information</h4>
                        <p><strong>Product:</strong> ${order.product}</p>
                        <p><strong>Quantity:</strong> ${order.quantity}</p>
                        <p><strong>Product Price:</strong> $${(order.productPrice || 0).toFixed(2)}</p>
                        <p><strong>Subtotal:</strong> $${(order.subtotal || 0).toFixed(2)}</p>
                        <p><strong>Delivery Charges:</strong> $${(order.deliveryCharges || 0).toFixed(2)}</p>

                        <p><strong>Total:</strong> $${(order.total || 0).toFixed(2)}</p>
                        
                        <h4 style="margin-top: 20px;">Order Status</h4>
                        <p><strong>Status:</strong> <span class="order-status ${(order.status || 'pending').toLowerCase()}">${order.status || 'Pending'}</span></p>
                        <p><strong>Payment Method:</strong> ${order.paymentMethod}</p>
                        <p><strong>Order Date:</strong> ${new Date(order.date).toLocaleString()}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    closeOrderDetailsModal();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeOrderDetailsModal() {
    const modal = document.getElementById('order-details-modal');
    if (modal) {
        modal.remove();
    }
}

function refreshOrders() {
    loadAdminData();
    
    if (orderManager) {
        orderManager.showNotification('Orders data refreshed', 'success');
    }
}

function clearOrderHistory() {
    if (confirm('Are you sure you want to clear all order history? This cannot be undone.')) {
        localStorage.removeItem('grindctrl_orders');
        loadAdminData();
        if (orderManager) {
            orderManager.showNotification('Order history cleared', 'success');
        }
    }
}

// Success modal functions
function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Handle window events
window.addEventListener('beforeunload', function(e) {
    // Save any pending data before page unload
    if (orderManager && orderManager.orders.length > 0) {
        console.log('Saving orders before page unload...');
    }
});

// Handle clicks outside modals
document.addEventListener('click', function(e) {
    // Close modals when clicking outside
    if (e.target.classList.contains('modal')) {
        if (e.target.id === 'admin-modal') {
            closeAdminPanel();
        } else if (e.target.id === 'success-modal') {
            closeSuccessModal();
        } else if (e.target.id === 'order-details-modal') {
            closeOrderDetailsModal();
        }
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // ESC key closes modals
    if (e.key === 'Escape') {
        closeAdminPanel();
        closeSuccessModal();
        closeOrderDetailsModal();
    }
    
    // Ctrl/Cmd + R for refresh data (in admin panel)
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        const adminModal = document.getElementById('admin-modal');
        if (adminModal && adminModal.style.display === 'flex') {
            e.preventDefault();
            refreshOrders();
        }
    }
});

// Performance monitoring
let performanceMetrics = {
    pageLoadTime: 0,
    orderSubmissionTime: 0,
    webhookResponseTime: 0
};

window.addEventListener('load', function() {
    performanceMetrics.pageLoadTime = performance.now();
    console.log(`📊 Page loaded in ${performanceMetrics.pageLoadTime.toFixed(2)}ms`);
});

// Console welcome message
console.log(`
🎽 GrindCTRL E-commerce System
================================
Version: 1.0
Features: Order Management, Excel Export, Webhook Integration
Environment: ${window.location.hostname}
================================
`);
