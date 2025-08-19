// Order Management System for GrindCTRL
class OrderManager {
    constructor() {
        this.orders = this.loadOrders();
        this.orderCounter = this.loadOrderCounter();
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartCount();
    }

    bindEvents() {
        // Form submission
        const orderForm = document.getElementById('order-form');
        if (orderForm) {
            orderForm.addEventListener('submit', this.handleOrderSubmission.bind(this));
        }

        // Quantity updates
        const quantityInput = document.getElementById('quantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', this.updateOrderSummary.bind(this));
        }
    }

    // Generate unique order ID
    generateOrderId() {
        this.orderCounter++;
        this.saveOrderCounter();
        const timestamp = Date.now().toString().slice(-6);
        return `${CONFIG.order.trackingNumberPrefix}${this.orderCounter.toString().padStart(4, '0')}${timestamp}`;
    }

    // Generate tracking number
    generateTrackingNumber() {
        const timestamp = Date.now().toString().slice(-8);
        const random = Math.random().toString(36).substring(2, 6).toUpperCase();
        return `${CONFIG.order.trackingNumberPrefix}${timestamp}${random}`;
    }

    // Calculate order totals
    calculateOrderTotals(quantity, paymentMethod) {
        const productPrice = CONFIG.product.price;
        const subtotal = productPrice * quantity;
        const deliveryCharges = CONFIG.product.deliveryCharges;
        let codAmount = 0;
        
        if (paymentMethod === 'COD') {
            codAmount = subtotal * (CONFIG.order.codChargePercentage / 100);
        }
        
        const total = subtotal + deliveryCharges + codAmount;
        
        return {
            productPrice,
            subtotal,
            deliveryCharges,
            codAmount,
            total
        };
    }

    // Update order summary display
    updateOrderSummary() {
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const paymentMethod = document.getElementById('payment-method').value || 'COD';
        
        const totals = this.calculateOrderTotals(quantity, paymentMethod);
        
        document.getElementById('product-price').textContent = `$${totals.productPrice.toFixed(2)}`;
        document.getElementById('summary-quantity').textContent = quantity;
        document.getElementById('subtotal').textContent = `$${totals.subtotal.toFixed(2)}`;
        document.getElementById('delivery-charges').textContent = `$${totals.deliveryCharges.toFixed(2)}`;
        document.getElementById('total-amount').textContent = `$${totals.total.toFixed(2)}`;
    }

    // Handle order submission
    async handleOrderSubmission(event) {
        event.preventDefault();
        
        const submitBtn = event.target.querySelector('button[type="submit"]');
        this.setButtonLoading(submitBtn, true);

        try {
            const formData = new FormData(event.target);
            const orderData = this.extractOrderData(formData);
            
            // Validate order data
            if (!this.validateOrderData(orderData)) {
                throw new Error('Please fill in all required fields');
            }

            // Generate system fields
            const systemData = this.generateSystemData(orderData);
            const completeOrder = { ...orderData, ...systemData };

            // Save order locally
            this.saveOrder(completeOrder);

            // Send to webhook
            await this.sendToWebhook(completeOrder);

            // Show success message
            this.showOrderSuccess(completeOrder);

            // Reset form
            event.target.reset();
            this.updateOrderSummary();
            this.updateCartCount();

        } catch (error) {
            console.error('Order submission error:', error);
            this.showNotification('Order submission failed: ' + error.message, 'error');
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }

    // Extract order data from form
    extractOrderData(formData) {
        const quantity = parseInt(formData.get('quantity')) || 1;
        const paymentMethod = formData.get('paymentMethod') || 'COD';
        const totals = this.calculateOrderTotals(quantity, paymentMethod);

        return {
            customerName: formData.get('customerName'),
            phone: formData.get('phone'),
            city: formData.get('city'),
            address: formData.get('address'),
            paymentMethod: paymentMethod,
            product: CONFIG.product.name,
            quantity: quantity,
            ...totals
        };
    }

    // Generate system data
    generateSystemData(orderData) {
        const orderId = this.generateOrderId();
        const trackingNumber = this.generateTrackingNumber();
        const courier = this.getRandomCourier();
        const date = new Date().toISOString();
        const status = CONFIG.order.defaultStatus;

        return {
            orderId,
            trackingNumber,
            courier,
            date,
            status
        };
    }

    // Validate order data
    validateOrderData(orderData) {
        const required = ['customerName', 'phone', 'city', 'address'];
        return required.every(field => orderData[field] && orderData[field].trim());
    }

    // Get random courier
    getRandomCourier() {
        return CONFIG.couriers[Math.floor(Math.random() * CONFIG.couriers.length)];
    }

    // Save order to localStorage
    saveOrder(orderData) {
        this.orders.push(orderData);
        this.saveOrders();
    }

    // Send order to webhook
    async sendToWebhook(orderData) {
        const webhookData = this.formatForWebhook(orderData);
        
        const response = await fetch(CONFIG.webhook.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(webhookData),
            signal: AbortSignal.timeout(CONFIG.webhook.timeout)
        });

        if (!response.ok) {
            throw new Error(`Webhook failed: ${response.status} ${response.statusText}`);
        }

        return response.json().catch(() => ({}));
    }

    // Format data for webhook
    formatForWebhook(orderData) {
        return {
            'Order ID': orderData.orderId,
            'Customer Name': orderData.customerName,
            'Phone': orderData.phone,
            'City': orderData.city,
            'Address': orderData.address,
            'COD Amount': orderData.codAmount.toFixed(2),
            'Tracking Number': orderData.trackingNumber,
            'Courier': orderData.courier,
            'Total': orderData.total.toFixed(2),
            'Date': new Date(orderData.date).toLocaleDateString(),
            'Status': orderData.status,
            'Payment Method': orderData.paymentMethod,
            'Product': orderData.product,
            'Quantity': orderData.quantity
        };
    }

    // Show order success modal
    showOrderSuccess(orderData) {
        const modal = document.getElementById('success-modal');
        const orderDetails = document.getElementById('order-confirmation');
        
        orderDetails.innerHTML = `
            <h4>Order Details</h4>
            <p><strong>Order ID:</strong> ${orderData.orderId}</p>
            <p><strong>Tracking Number:</strong> ${orderData.trackingNumber}</p>
            <p><strong>Customer:</strong> ${orderData.customerName}</p>
            <p><strong>Phone:</strong> ${orderData.phone}</p>
            <p><strong>Address:</strong> ${orderData.address}, ${orderData.city}</p>
            <p><strong>Product:</strong> ${orderData.product} x ${orderData.quantity}</p>
            <p><strong>Payment Method:</strong> ${orderData.paymentMethod}</p>
            <p><strong>Total Amount:</strong> $${orderData.total.toFixed(2)}</p>
            <p><strong>Courier:</strong> ${orderData.courier}</p>
        `;
        
        modal.style.display = 'flex';
    }

    // Add to cart functionality
    addToCart() {
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const paymentMethod = document.getElementById('payment-method').value || 'COD';
        
        let cart = JSON.parse(localStorage.getItem(CONFIG.storage.cart) || '[]');
        
        const cartItem = {
            product: CONFIG.product.name,
            price: CONFIG.product.price,
            quantity: quantity,
            paymentMethod: paymentMethod,
            addedAt: new Date().toISOString()
        };
        
        cart.push(cartItem);
        localStorage.setItem(CONFIG.storage.cart, JSON.stringify(cart));
        
        this.updateCartCount();
        this.showNotification(`Added ${quantity}x ${CONFIG.product.name} to cart`, 'success');
    }

    // Update cart count display
    updateCartCount() {
        const cart = JSON.parse(localStorage.getItem(CONFIG.storage.cart) || '[]');
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = totalItems;
        }
    }

    // Get all orders
    getOrders() {
        return this.orders;
    }

    // Get order statistics
    getOrderStats() {
        const totalOrders = this.orders.length;
        const pendingOrders = this.orders.filter(order => order.status === 'Pending').length;
        const totalRevenue = this.orders.reduce((sum, order) => sum + (order.total || 0), 0);
        
        return {
            totalOrders,
            pendingOrders,
            totalRevenue: totalRevenue.toFixed(2)
        };
    }

    // Update order status
    updateOrderStatus(orderId, newStatus) {
        const orderIndex = this.orders.findIndex(order => order.orderId === orderId);
        if (orderIndex !== -1) {
            this.orders[orderIndex].status = newStatus;
            this.saveOrders();
            return true;
        }
        return false;
    }

    // Set button loading state
    setButtonLoading(button, loading) {
        if (loading) {
            button.classList.add('loading');
            button.disabled = true;
        } else {
            button.classList.remove('loading');
            button.disabled = false;
        }
    }

    // Show notification
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        notification.innerHTML = `
            <div class="notification-content">
                <i class="notification-icon fas ${type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-check-circle'}"></i>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        document.getElementById('notification-container').appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Load orders from localStorage
    loadOrders() {
        try {
            return JSON.parse(localStorage.getItem(CONFIG.storage.orders) || '[]');
        } catch (error) {
            console.error('Error loading orders:', error);
            return [];
        }
    }

    // Save orders to localStorage
    saveOrders() {
        try {
            localStorage.setItem(CONFIG.storage.orders, JSON.stringify(this.orders));
        } catch (error) {
            console.error('Error saving orders:', error);
        }
    }

    // Load order counter
    loadOrderCounter() {
        try {
            return parseInt(localStorage.getItem(CONFIG.storage.orderCounter) || '0');
        } catch (error) {
            return 0;
        }
    }

    // Save order counter
    saveOrderCounter() {
        try {
            localStorage.setItem(CONFIG.storage.orderCounter, this.orderCounter.toString());
        } catch (error) {
            console.error('Error saving order counter:', error);
        }
    }
}

// Initialize order manager when DOM is loaded
let orderManager;
document.addEventListener('DOMContentLoaded', function() {
    orderManager = new OrderManager();
});
