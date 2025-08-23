# GrindCTRL - Premium Streetwear E-commerce Platform

## Overview
GrindCTRL is a modern e-commerce platform for premium streetwear and urban fashion, built as a single-page application optimized for GitHub Pages deployment.

## Features

- **Dark Theme Design**: Modern dark aesthetic matching contemporary fashion e-commerce
- **Responsive Design**: Mobile-first approach with seamless desktop experience
- **Accessibility Compliant**: WCAG 2.1 AA compliant with ARIA attributes and keyboard navigation
- **Order Processing**: Direct integration with n8n webhook for automated order handling
- **Form Validation**: Client-side validation with input sanitization and error handling
- **Static Deployment**: Fully compatible with GitHub Pages - no server required
- **Performance Optimized**: Font preloading, optimized images, and efficient loading

## Technology Stack

- **Frontend**: Vanilla HTML5, CSS3, JavaScript (ES6+)
- **Styling**: CSS Custom Properties with dark theme
- **Fonts**: Google Fonts (Inter & Poppins) with display swap
- **Icons**: Font Awesome 6
- **Deployment**: GitHub Pages with automated CI/CD
- **Order Processing**: n8n webhook integration
- **Accessibility**: ARIA attributes, keyboard navigation, screen reader support

## Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/grindctrl-shop.git
   cd grindctrl-shop
   ```

2. **Configure webhook URL**
   Update the webhook URL in `config.js`:
   ```javascript
   window.CONFIG.WEBHOOK_URL = 'https://your-n8n-instance.com/webhook/order-processing';
   ```

3. **Add GitHub Secret (for automatic deployment)**
   - Go to your repository settings
   - Navigate to "Secrets and variables" → "Actions"
   - Add a new repository secret:
     - Name: `N8N_WEBHOOK_URL`
     - Value: Your actual n8n webhook URL

4. **Deploy to GitHub Pages**
   - Push to your repository
   - Enable GitHub Pages in repository settings
   - GitHub Actions will automatically deploy your site with the webhook configured

## Project Structure

```
├── index.html              # Main product page
├── css/
│   └── styles.css          # Complete styling with dark theme
├── js/
│   └── main.js             # Cart functionality & webhook integration
├── assets/
│   ├── product-main.png    # Product image
│   └── product-image.svg   # Fallback SVG illustration
├── .github/
│   └── workflows/
│       └── deploy.yml      # GitHub Pages deployment
└── README.md
```

## Configuration

### Webhook Integration

The site integrates with n8n for order processing. Update the webhook URL in `js/main.js`:

```javascript
function getWebhookUrl() {
    const fallbackWebhook = 'YOUR_N8N_WEBHOOK_URL_HERE';
    return fallbackWebhook;
}
```

### Product Information

Product details are configured in `js/main.js`:

```javascript
const AppState = {
    product: {
        name: 'Luxury Cropped Black T-Shirt',
        price: 300.00,
        currency: 'EGP',
        originalPrice: 350.00
    }
};
```

## Order Data Structure

Orders sent to the webhook include:

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
  }
}
```

## Customization

### Colors & Theme

The dark theme colors are defined in CSS custom properties:

```css
:root {
    --primary-color: #E74C3C;    /* Coral red accent */
    --background-color: #1a1a1a;  /* Dark background */
    --text-color: #ffffff;        /* White text */
    --light-grey: #2a2a2a;       /* Dark grey cards */
    --border-color: #404040;      /* Border grey */
}
```

### Product Image

Replace `assets/product-main.png` with your product image. Recommended dimensions: 800x800px minimum.

## Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## License

MIT License - feel free to use for your own projects.

## Support

For issues related to the codebase, please create an issue in this repository.
For n8n webhook setup, refer to the [n8n documentation](https://docs.n8n.io/).