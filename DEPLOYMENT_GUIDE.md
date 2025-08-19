# Complete GitHub Pages Deployment Guide

## 🎯 Quick Summary
Your GrindCTRL t-shirt store is ready for GitHub Pages deployment with automatic n8n webhook integration. The webhook URL is stored in `config.js` and can be configured in two ways.

## 📁 Webhook Configuration Location

**File:** `config.js` (line 5)
```javascript
window.CONFIG.WEBHOOK_URL = '123'; // ← Change this line
```

## 🚀 Deployment Methods

### Method 1: Automatic Deployment (Recommended)

#### Step 1: Create GitHub Repository
1. Create a new repository on GitHub
2. Upload all your project files to the repository
3. Ensure your repository has the following structure:
```
your-repo/
├── index.html
├── config.js
├── css/styles.css
├── js/main.js
├── assets/product-main.png
├── .github/workflows/deploy.yml
└── README.md
```

#### Step 2: Configure Webhook Secret
1. Go to your GitHub repository
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add secret:
   - **Name:** `N8N_WEBHOOK_URL`
   - **Value:** `https://your-n8n-instance.com/webhook/order-processing`

#### Step 3: Enable GitHub Pages
1. Go to repository **Settings**
2. Scroll to **Pages** section
3. Source: **Deploy from a branch**
4. Branch: **GitHub Actions**

#### Step 4: Deploy
1. Push your code to the `main` or `master` branch
2. GitHub Actions will automatically:
   - Replace the webhook URL in `config.js`
   - Deploy your site to GitHub Pages
3. Your site will be live at: `https://yourusername.github.io/repository-name`

### Method 2: Manual Deployment

#### Step 1: Update Webhook URL
Edit `config.js` directly:
```javascript
// Replace this line:
window.CONFIG.WEBHOOK_URL = '123';

// With your actual webhook URL:
window.CONFIG.WEBHOOK_URL = 'https://your-n8n-instance.com/webhook/order-processing';
```

#### Step 2: Deploy to GitHub Pages
1. Push to your GitHub repository
2. Enable GitHub Pages in repository settings
3. Select source branch (usually `main`)

## 🔧 n8n Webhook Setup

### Getting Your Webhook URL
1. Open your n8n workflow
2. Add/find the **Webhook** trigger node
3. Copy the **Production URL** (looks like):
   ```
   https://your-n8n-instance.com/webhook/abc123def456
   ```

### Expected Order Data Structure
Your n8n webhook will receive this JSON structure:
```json
{
  "orderDate": "2025-01-19T10:30:00.000Z",
  "orderId": "GC-ABC123-XYZ789",
  "customer": {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890"
  },
  "shipping": {
    "address": "123 Main St",
    "city": "Cairo",
    "postalCode": "12345"
  },
  "product": {
    "name": "Luxury Cropped Black T-Shirt",
    "size": "M",
    "quantity": 2,
    "unitPrice": 300.00,
    "currency": "EGP"
  },
  "pricing": {
    "subtotal": 600.00,
    "shipping": 0,
    "total": 600.00,
    "currency": "EGP"
  },
  "source": "grindctrl-website",
  "userAgent": "Mozilla/5.0...",
  "timestamp": 1705745400000
}
```

## 🛠️ Testing Your Setup

### Local Testing
1. Open browser console (F12)
2. Fill out the order form
3. Submit order
4. Check console for:
   - ✅ "Sending order to webhook: [your-url]"
   - ✅ "Webhook response: ..."
   - ❌ "Webhook URL not properly configured" (if not set up)

### Production Testing
1. Visit your deployed site
2. Place a test order
3. Check your n8n workflow execution logs
4. Verify data was received correctly

## 🔍 Troubleshooting

### Common Issues

#### 1. "Webhook URL not configured" Error
**Problem:** Webhook URL is still set to '123' or placeholder
**Solution:** 
- Method 1: Add `N8N_WEBHOOK_URL` secret in GitHub
- Method 2: Edit `config.js` manually

#### 2. CORS Error
**Problem:** n8n rejecting requests from your domain
**Solution:** Configure CORS in your n8n instance or use a proxy

#### 3. Orders Not Reaching n8n
**Problem:** Webhook URL incorrect or n8n workflow not running
**Solution:**
- Verify webhook URL is correct
- Test webhook URL directly with curl/Postman
- Check n8n workflow is active

#### 4. GitHub Actions Deployment Fails
**Problem:** Deployment workflow can't access secrets
**Solution:**
- Verify secret name is exactly `N8N_WEBHOOK_URL`
- Check repository permissions for Actions

## 📝 File Structure Overview

```
├── index.html              # Main product page
├── config.js               # 🔧 WEBHOOK CONFIGURED HERE
├── css/styles.css          # Dark theme styling
├── js/main.js             # Cart & webhook logic
├── assets/
│   └── product-main.png   # Your t-shirt image
├── .github/workflows/
│   └── deploy.yml         # Auto-deployment script
├── README.md              # Project documentation
└── DEPLOYMENT_GUIDE.md    # This guide
```

## 🎉 Next Steps After Deployment

1. **Test Your Store:** Place a test order to verify webhook integration
2. **Customize Product:** Update product details in `js/main.js` if needed
3. **Add Analytics:** Consider adding Google Analytics or similar
4. **Custom Domain:** Configure a custom domain in GitHub Pages settings
5. **SSL Certificate:** GitHub Pages provides HTTPS automatically
6. **Monitor Orders:** Set up n8n notifications for new orders

## 🆘 Need Help?

- **Webhook Issues:** Check n8n documentation
- **GitHub Pages:** Check repository Actions tab for deployment logs
- **Order Form:** Test in browser console for JavaScript errors

Your store is now ready for production! 🚀