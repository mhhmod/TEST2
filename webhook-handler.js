// Webhook Handler for GrindCTRL order processing
class WebhookHandler {
    constructor() {
        this.retryAttempts = CONFIG.webhook.retryAttempts;
        this.timeout = CONFIG.webhook.timeout;
        this.init();
    }

    init() {
        this.setupWebhookListeners();
    }

    // Setup webhook event listeners
    setupWebhookListeners() {
        // Listen for order events
        document.addEventListener('orderCreated', this.handleOrderCreated.bind(this));
        document.addEventListener('orderUpdated', this.handleOrderUpdated.bind(this));
        document.addEventListener('orderStatusChanged', this.handleOrderStatusChanged.bind(this));
    }

    // Handle new order creation
    async handleOrderCreated(event) {
        const orderData = event.detail;
        
        try {
            await this.sendOrderWebhook(orderData, 'order.created');
            console.log('Order webhook sent successfully:', orderData.orderId);
        } catch (error) {
            console.error('Failed to send order webhook:', error);
            this.handleWebhookError(orderData, error);
        }
    }

    // Handle order updates
    async handleOrderUpdated(event) {
        const orderData = event.detail;
        
        try {
            await this.sendOrderWebhook(orderData, 'order.updated');
            console.log('Order update webhook sent:', orderData.orderId);
        } catch (error) {
            console.error('Failed to send order update webhook:', error);
            this.handleWebhookError(orderData, error);
        }
    }

    // Handle order status changes
    async handleOrderStatusChanged(event) {
        const { orderId, newStatus, oldStatus } = event.detail;
        
        const webhookData = {
            orderId: orderId,
            newStatus: newStatus,
            oldStatus: oldStatus,
            timestamp: new Date().toISOString(),
            event: 'status_change'
        };
        
        try {
            await this.sendStatusWebhook(webhookData);
            console.log('Status change webhook sent:', orderId);
        } catch (error) {
            console.error('Failed to send status webhook:', error);
        }
    }

    // Send order webhook with retry logic
    async sendOrderWebhook(orderData, eventType) {
        const webhookPayload = {
            event: eventType,
            timestamp: new Date().toISOString(),
            data: this.formatOrderForWebhook(orderData)
        };

        return await this.sendWebhookWithRetry(webhookPayload);
    }

    // Send status change webhook
    async sendStatusWebhook(statusData) {
        const webhookPayload = {
            event: 'order.status_changed',
            timestamp: new Date().toISOString(),
            data: statusData
        };

        return await this.sendWebhookWithRetry(webhookPayload);
    }

    // Send webhook with retry logic
    async sendWebhookWithRetry(payload, attempt = 1) {
        try {
            const response = await this.makeWebhookRequest(payload);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // Try to parse response, but don't fail if it's not JSON
            let responseData = {};
            try {
                responseData = await response.json();
            } catch (parseError) {
                console.warn('Webhook response is not JSON:', parseError);
            }

            return {
                success: true,
                status: response.status,
                data: responseData
            };

        } catch (error) {
            console.error(`Webhook attempt ${attempt} failed:`, error);

            if (attempt < this.retryAttempts) {
                const delay = this.calculateRetryDelay(attempt);
                console.log(`Retrying webhook in ${delay}ms...`);
                
                await this.sleep(delay);
                return await this.sendWebhookWithRetry(payload, attempt + 1);
            }

            throw error;
        }
    }

    // Make the actual webhook HTTP request
    async makeWebhookRequest(payload) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeout);

        try {
            const response = await fetch(CONFIG.webhook.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'User-Agent': 'GrindCTRL-Webhook/1.0',
                    'X-Webhook-Source': 'grindctrl-ecommerce'
                },
                body: JSON.stringify(payload),
                signal: controller.signal
            });

            return response;

        } finally {
            clearTimeout(timeoutId);
        }
    }

    // Format order data for webhook
    formatOrderForWebhook(orderData) {
        return {
            'Order ID': orderData.orderId,
            'Customer Name': orderData.customerName,
            'Phone': orderData.phone,
            'City': orderData.city,
            'Address': orderData.address,
            'COD Amount': orderData.codAmount ? orderData.codAmount.toFixed(2) : '0.00',
            'Tracking Number': orderData.trackingNumber,
            'Courier': orderData.courier,
            'Total': orderData.total ? orderData.total.toFixed(2) : '0.00',
            'Date': this.formatDate(orderData.date),
            'Status': orderData.status,
            'Payment Method': orderData.paymentMethod,
            'Product': orderData.product,
            'Quantity': orderData.quantity,
            // Additional metadata
            'Subtotal': orderData.subtotal ? orderData.subtotal.toFixed(2) : '0.00',
            'Delivery Charges': orderData.deliveryCharges ? orderData.deliveryCharges.toFixed(2) : '0.00',
            'Product Price': orderData.productPrice ? orderData.productPrice.toFixed(2) : '0.00'
        };
    }

    // Format date for webhook
    formatDate(dateString) {
        if (!dateString) return new Date().toISOString();
        
        try {
            return new Date(dateString).toISOString();
        } catch (error) {
            return new Date().toISOString();
        }
    }

    // Calculate exponential backoff delay
    calculateRetryDelay(attempt) {
        const baseDelay = 1000; // 1 second
        const maxDelay = 30000; // 30 seconds
        const delay = Math.min(baseDelay * Math.pow(2, attempt - 1), maxDelay);
        
        // Add some jitter to prevent thundering herd
        const jitter = Math.random() * 1000;
        return delay + jitter;
    }

    // Sleep utility function
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Handle webhook errors
    handleWebhookError(orderData, error) {
        console.error('Webhook error for order:', orderData.orderId, error);
        
        // Store failed webhook for retry later
        this.storeFailedWebhook(orderData, error);
        
        // Notify user if order manager is available
        if (orderManager) {
            orderManager.showNotification(
                `Order placed successfully but notification failed. Order ID: ${orderData.orderId}`,
                'warning'
            );
        }
    }

    // Store failed webhook for later retry
    storeFailedWebhook(orderData, error) {
        try {
            const failedWebhooks = JSON.parse(localStorage.getItem('failed_webhooks') || '[]');
            
            failedWebhooks.push({
                orderData: orderData,
                error: error.message,
                timestamp: new Date().toISOString(),
                retryCount: 0
            });
            
            localStorage.setItem('failed_webhooks', JSON.stringify(failedWebhooks));
        } catch (storageError) {
            console.error('Failed to store failed webhook:', storageError);
        }
    }

    // Retry failed webhooks
    async retryFailedWebhooks() {
        try {
            const failedWebhooks = JSON.parse(localStorage.getItem('failed_webhooks') || '[]');
            
            if (failedWebhooks.length === 0) {
                console.log('No failed webhooks to retry');
                return;
            }

            const retryResults = [];
            
            for (const failedWebhook of failedWebhooks) {
                try {
                    await this.sendOrderWebhook(failedWebhook.orderData, 'order.retry');
                    retryResults.push({ success: true, orderId: failedWebhook.orderData.orderId });
                } catch (error) {
                    failedWebhook.retryCount++;
                    retryResults.push({ 
                        success: false, 
                        orderId: failedWebhook.orderData.orderId, 
                        error: error.message 
                    });
                }
            }

            // Remove successful retries and update failed ones
            const stillFailed = failedWebhooks.filter((webhook, index) => 
                !retryResults[index].success && webhook.retryCount < 5
            );
            
            localStorage.setItem('failed_webhooks', JSON.stringify(stillFailed));
            
            const successCount = retryResults.filter(r => r.success).length;
            console.log(`Webhook retry completed: ${successCount}/${failedWebhooks.length} succeeded`);
            
            return retryResults;
            
        } catch (error) {
            console.error('Error retrying failed webhooks:', error);
            return [];
        }
    }

    // Get webhook statistics
    getWebhookStats() {
        try {
            const failedWebhooks = JSON.parse(localStorage.getItem('failed_webhooks') || '[]');
            const webhookLogs = JSON.parse(localStorage.getItem('webhook_logs') || '[]');
            
            return {
                failedCount: failedWebhooks.length,
                totalSent: webhookLogs.length,
                successRate: webhookLogs.length > 0 ? 
                    ((webhookLogs.length - failedWebhooks.length) / webhookLogs.length * 100).toFixed(2) + '%' : 
                    'N/A'
            };
        } catch (error) {
            console.error('Error getting webhook stats:', error);
            return {
                failedCount: 0,
                totalSent: 0,
                successRate: 'N/A'
            };
        }
    }

    // Log webhook activity
    logWebhookActivity(orderData, success, error = null) {
        try {
            const webhookLogs = JSON.parse(localStorage.getItem('webhook_logs') || '[]');
            
            const logEntry = {
                orderId: orderData.orderId,
                timestamp: new Date().toISOString(),
                success: success,
                error: error ? error.message : null,
                url: CONFIG.webhook.url
            };
            
            webhookLogs.push(logEntry);
            
            // Keep only last 100 logs
            if (webhookLogs.length > 100) {
                webhookLogs.splice(0, webhookLogs.length - 100);
            }
            
            localStorage.setItem('webhook_logs', JSON.stringify(webhookLogs));
        } catch (storageError) {
            console.error('Failed to log webhook activity:', storageError);
        }
    }

    // Test webhook connectivity
    async testWebhook() {
        try {
            const testPayload = {
                event: 'webhook.test',
                timestamp: new Date().toISOString(),
                data: {
                    message: 'GrindCTRL webhook test',
                    source: 'grindctrl-ecommerce',
                    version: '1.0'
                }
            };

            const result = await this.sendWebhookWithRetry(testPayload);
            
            return {
                success: true,
                message: 'Webhook test successful',
                response: result
            };
            
        } catch (error) {
            return {
                success: false,
                message: 'Webhook test failed: ' + error.message,
                error: error
            };
        }
    }
}

// Initialize webhook handler
let webhookHandler;
document.addEventListener('DOMContentLoaded', function() {
    webhookHandler = new WebhookHandler();
});

// Utility function to trigger webhook events
function triggerWebhookEvent(eventType, data) {
    const event = new CustomEvent(eventType, { detail: data });
    document.dispatchEvent(event);
}

// Global functions for webhook management
function testWebhookConnection() {
    if (webhookHandler) {
        webhookHandler.testWebhook().then(result => {
            if (orderManager) {
                orderManager.showNotification(result.message, result.success ? 'success' : 'error');
            }
        });
    }
}

function retryFailedWebhooks() {
    if (webhookHandler) {
        webhookHandler.retryFailedWebhooks().then(results => {
            if (orderManager) {
                const successCount = results.filter(r => r.success).length;
                orderManager.showNotification(
                    `Webhook retry completed: ${successCount}/${results.length} succeeded`,
                    successCount > 0 ? 'success' : 'warning'
                );
            }
        });
    }
}
