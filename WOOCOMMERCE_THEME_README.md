# GrindCTRL WooCommerce Theme

An elegant, modern WooCommerce theme designed specifically for clothing e-commerce with built-in n8n webhook integration and comprehensive order management.

## 🌟 Features

### Design & UX
- **Elegant Dark Theme** - Modern, professional design with red accent colors
- **Fully Responsive** - Optimized for desktop, tablet, and mobile devices
- **User-Friendly Interface** - Intuitive navigation and smooth interactions
- **Fast Loading** - Optimized CSS and JavaScript for performance
- **Accessibility** - WCAG compliant with high contrast and reduced motion support

### WooCommerce Integration
- **Complete WooCommerce Support** - All standard WooCommerce features
- **Custom Product Templates** - Elegant single product pages
- **Enhanced Cart & Checkout** - Streamlined purchasing process
- **Product Variations** - Support for sizes, colors, and other attributes
- **Inventory Management** - Stock tracking and low stock alerts
- **Multiple Payment Methods** - Support for various payment gateways

### Order Management & Tracking
- **N8N Webhook Integration** - Automatic order data transmission
- **Custom Order Fields** - Tracking numbers, courier information
- **Admin Order Dashboard** - Enhanced order management interface
- **Webhook Status Tracking** - Monitor webhook delivery status
- **Order Export Capability** - Export orders with all custom fields

### Advanced Features
- **Real-time Cart Updates** - AJAX-powered cart functionality
- **Quantity Controls** - Enhanced quantity selectors
- **Form Validation** - Client-side and server-side validation
- **Notification System** - User-friendly success/error messages
- **SEO Optimized** - Proper meta tags and structured data
- **Translation Ready** - Full i18n support

## 📋 Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## 🚀 Installation

### Method 1: Direct Upload

1. **Download the theme files** from this repository
2. **Create a ZIP file** containing all theme files
3. **Upload via WordPress Admin**:
   - Go to `Appearance > Themes`
   - Click `Add New > Upload Theme`
   - Select your ZIP file and click `Install Now`
   - Activate the theme

### Method 2: FTP Upload

1. **Download theme files** to your computer
2. **Upload via FTP** to `/wp-content/themes/grindctrl/`
3. **Activate in WordPress Admin**:
   - Go to `Appearance > Themes`
   - Find "GrindCTRL WooCommerce" and click `Activate`

## ⚙️ Setup & Configuration

### 1. WooCommerce Setup

```bash
# Install WooCommerce if not already installed
# Go to Plugins > Add New > Search "WooCommerce" > Install & Activate
```

**Configure WooCommerce:**
- Run the WooCommerce Setup Wizard
- Configure your store location, currency (EGP), and payment methods
- Set up shipping zones and rates
- Configure tax settings if applicable

### 2. N8N Webhook Configuration

**In WordPress Admin:**
1. Go to `Settings > GrindCTRL Settings`
2. Enter your N8N webhook URL: `https://your-n8n-instance.com/webhook/your-endpoint`
3. Save settings

**Webhook Data Structure:**
```json
{
  "order_id": "123",
  "customer_name": "John Doe",
  "phone": "+1234567890",
  "city": "Cairo",
  "address": "123 Main Street, Apt 4B",
  "cod_amount": "300.00",
  "tracking_number": "TRK123456789",
  "courier": "DHL",
  "total": "300.00",
  "date": "2024-01-15 14:30:00",
  "status": "processing",
  "payment_method": "Cash on Delivery",
  "products": ["Luxury Cropped Black T-Shirt"],
  "quantities": [1]
}
```

### 3. Theme Customization

**Logo & Branding:**
- Go to `Appearance > Customize > Site Identity`
- Upload your logo and set site title
- The theme will automatically use your site name in the header

**Colors (Optional):**
- Edit `/css/styles.css` to modify the color scheme
- Main colors are defined in CSS variables at the top of the file

**Menus:**
- Go to `Appearance > Menus`
- Create menus for "Primary Menu" and "Footer Menu"
- Assign them to the respective menu locations

### 4. Required Pages

Create these essential pages:
- **Shop** - WooCommerce will create this automatically
- **Cart** - Use the custom `page-cart.php` template
- **Checkout** - Use the custom `page-checkout.php` template
- **My Account** - Use the custom `page-my-account.php` template

## 🛍️ Product Setup

### Adding Products

1. **Go to Products > Add New**
2. **Fill in product details:**
   - Product name
   - Short description (appears under title)
   - Long description (appears in tabs)
   - Product price (regular and sale price)
   - Product images

3. **Configure product attributes:**
   - Sizes (XS, S, M, L, XL, XXL)
   - Colors (if applicable)
   - Materials, etc.

4. **Set up inventory:**
   - Manage stock
   - Stock quantity
   - Low stock threshold

### Product Categories

Create logical categories for your clothing:
- T-Shirts
- Hoodies
- Pants
- Accessories
- Sale Items

## 🎨 Customization Guide

### CSS Customization

The theme uses CSS variables for easy customization:

```css
:root {
    --primary-color: #E74C3C;        /* Main brand color */
    --secondary-color: #E74C3C;      /* Secondary brand color */
    --background-color: #1a1a1a;     /* Dark background */
    --text-color: #ffffff;           /* Text color */
    --accent-color: #E74C3C;         /* Accent color */
    --light-grey: #2a2a2a;           /* Light grey background */
    --border-color: #404040;         /* Border color */
}
```

### JavaScript Customization

The theme includes modular JavaScript:
- `/js/main.js` - Core functionality
- `/js/woocommerce.js` - WooCommerce-specific features

### Template Customization

Key template files:
- `single-product.php` - Individual product pages
- `archive-product.php` - Shop page
- `woocommerce.php` - General WooCommerce pages
- `woocommerce/single-product/add-to-cart/simple.php` - Add to cart form

## 📊 Order Management

### Admin Features

**Order Dashboard Enhancements:**
- Tracking number column
- Courier service column
- Webhook status indicator

**Order Detail Page:**
- Tracking information meta box
- Webhook status and response
- Manual webhook resend option

**Webhook Monitoring:**
- View webhook delivery status
- See webhook response codes
- Resend failed webhooks

### Order Tracking Workflow

1. **Order Placed** - Customer completes checkout
2. **Webhook Sent** - Order data automatically sent to N8N
3. **Admin Updates** - Add tracking number and courier info
4. **Customer Notification** - Send tracking info to customer
5. **Order Fulfillment** - Mark as completed when delivered

## 🔧 Troubleshooting

### Common Issues

**Webhook Not Sending:**
1. Check webhook URL in Settings > GrindCTRL Settings
2. Verify N8N endpoint is accessible
3. Check WordPress error logs
4. Test webhook manually from order page

**Styling Issues:**
1. Clear all caches (WordPress, CDN, browser)
2. Check for plugin conflicts
3. Verify CSS files are loading correctly
4. Check browser console for JavaScript errors

**WooCommerce Compatibility:**
1. Ensure WooCommerce is up to date
2. Check for conflicting plugins
3. Verify theme template overrides are working
4. Test with default WooCommerce theme

### Debug Mode

Enable WordPress debug mode for troubleshooting:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 🔒 Security Considerations

- **Webhook Security** - Use HTTPS for webhook URLs
- **Input Validation** - All form inputs are sanitized
- **Nonce Verification** - CSRF protection on all AJAX calls
- **Permission Checks** - Proper capability checks for admin functions

## 📱 Mobile Optimization

The theme is fully responsive with:
- Mobile-first design approach
- Touch-friendly buttons and controls
- Optimized images for mobile
- Fast loading on mobile networks
- Mobile-specific navigation

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+ (limited support)

## 📞 Support

For theme support and customization:
1. Check this documentation first
2. Review WordPress and WooCommerce documentation
3. Test with default themes to isolate issues
4. Check community forums for similar issues

## 📝 Changelog

### Version 1.0.0
- Initial release
- Complete WooCommerce integration
- N8N webhook functionality
- Responsive design
- Admin order management
- Custom product templates
- Enhanced cart and checkout

## 📄 License

This theme is licensed under GPL v2 or later.

## 🙏 Credits

- **WordPress** - Content management system
- **WooCommerce** - E-commerce functionality
- **Font Awesome** - Icons
- **Google Fonts** - Typography (Inter & Poppins)
- **N8N** - Workflow automation platform

---

**Made with ❤️ for elegant e-commerce experiences**