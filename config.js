// Configuration for GrindCTRL E-commerce Site
window.CONFIG = window.CONFIG || {};

// Get webhook URL from environment variable (injected at runtime)
window.CONFIG.WEBHOOK_URL = '123'; // This will be replaced with actual webhook URL

// For GitHub Pages deployment, you'll need to update this value manually
// Or use the deployment script to inject the real webhook URL

// Export for use in main.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.CONFIG;
}