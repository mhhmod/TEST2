// Configuration for GrindCTRL E-commerce Site
window.CONFIG = window.CONFIG || {};

// Get webhook URL from environment variable (injected at runtime)
window.CONFIG.WEBHOOK_URL = 'https://grindctrlface.app.n8n.cloud/webhook-test/bb598178-224e-4526-bfa4-91c3483677ab'; // This will be replaced with actual webhook URL

// For GitHub Pages deployment, you'll need to update this value manually
// Or use the deployment script to inject the real webhook URL

// Export for use in main.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.CONFIG;
}
