// Configuration for GrindCTRL E-commerce Site
window.CONFIG = window.CONFIG || {};

// Get webhook URL from environment variable (injected at runtime)
window.CONFIG.WEBHOOK_URL = 'https://grindctrlface.app.n8n.cloud/webhook/test2git'; // This will be replaced with actual webhook URL

// Product configuration
window.CONFIG.PRODUCT = {
    name: 'Luxury Cropped Black T-Shirt',
    price: 300.00,
    currency: 'EGP',
    originalPrice: 350.00,
    deliveryCharges: 0, // Free shipping
    maxQuantity: 10,
    minQuantity: 1
};

// Order configuration
window.CONFIG.ORDER = {
    defaultStatus: 'Pending'
};

// Webhook data structure (for reference)
window.CONFIG.WEBHOOK_DATA = {
    description: 'Customer order data sent to webhook',
    fields: [
        'Order ID',
        'Customer Name', 
        'Phone',
        'City',
        'Address',
        'Total',
        'Date',
        'Payment Method',
        'Product',
        'Quantity'
    ]
};

// Remove courier services (not needed for simple webhook)

// Storage keys for localStorage
window.CONFIG.STORAGE = {
    orders: 'grindctrl_orders',
    cart: 'grindctrl_cart',
    orderCounter: 'grindctrl_order_counter'
};

// Webhook configuration
window.CONFIG.WEBHOOK = {
    timeout: 30000,
    retryAttempts: 3
};

// For GitHub Pages deployment, you'll need to update this value manually
// Or use the deployment script to inject the real webhook URL

// Export for use in main.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.CONFIG;
}
