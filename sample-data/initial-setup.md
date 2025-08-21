# GrindCTRL WooCommerce Setup Guide

## Quick Start Instructions

### 1. Install WordPress & WooCommerce
1. Install WordPress on your hosting
2. Install WooCommerce plugin
3. Upload this theme to `/wp-content/themes/grindctrl/`
4. Activate the theme

### 2. Configure Theme Settings
1. Go to **Appearance > GrindCTRL Settings**
2. Enter your n8n webhook URL
3. Test the webhook connection

### 3. Create Your First Product
1. Go to **Products > Add New**
2. Add product title: "Luxury Cropped Black T-Shirt"
3. Set price: Regular Price 350, Sale Price 300
4. Add product image (use your existing `assets/product-main.png`)
5. In **Product Data**:
   - Set as **Simple Product**
   - Add **Product Subtitle**: "Minimal. Premium cotton. Built for grind."
   - Fill in custom fields (Material, Care Instructions, etc.)

### 4. Create Product Variations (Optional)
If you want size options:
1. Change product type to **Variable Product**
2. Go to **Attributes** tab
3. Add attribute "Size" with values: XS, S, M, L, XL, XXL
4. Go to **Variations** tab
5. Create variations for each size

### 5. Configure WooCommerce Settings
1. **WooCommerce > Settings > General**:
   - Set currency to EGP
   - Set country to Egypt

2. **WooCommerce > Settings > Shipping**:
   - Set up free shipping zones

3. **WooCommerce > Settings > Payments**:
   - Enable Cash on Delivery
   - Configure other payment methods as needed

### 6. Test Order Flow
1. Visit your site
2. Add product to cart
3. Go through checkout
4. Check if webhook receives data correctly

## Excel Column Mapping

Your n8n webhook will receive orders in this exact format:

```json
{
  "Order ID": "12345",
  "Customer Name": "John Doe",
  "Phone": "+1234567890",
  "City": "Cairo",
  "Address": "123 Main Street",
  "COD Amount": "300.00",
  "Tracking Number": "TRK123456789",
  "Courier": "BOSTA",
  "Total": "300.00",
  "Date": "2025-01-19T10:30:00.000Z",
  "Status": "New",
  "Payment Method": "Cash on Delivery",
  "Product": "Luxury Cropped Black T-Shirt - M",
  "Quantity": "1"
}
```

## Troubleshooting

### Webhook Not Working?
1. Check webhook URL in **Appearance > GrindCTRL Settings**
2. Use the "Test Webhook" button
3. Check WordPress error logs
4. Verify n8n webhook is active

### Design Issues?
1. Make sure original CSS files are in `/css/` folder
2. Check if theme is properly activated
3. Clear any caching plugins

### Missing Features?
All WooCommerce features are supported:
- Product management
- Order management
- Customer accounts
- Inventory tracking
- Coupon system
- Email notifications

## Support

For issues:
1. Check WooCommerce system status
2. Enable WordPress debug mode
3. Check error logs in hosting panel
4. Verify all theme files are uploaded correctly