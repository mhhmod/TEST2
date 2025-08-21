# GrindCTRL WooCommerce Theme - Complete File Structure

## 📁 Theme Files Ready for ZIP

```
grindctrl/ (theme folder)
├── style.css                          # WooCommerce theme header
├── functions.php                      # WooCommerce integration & hooks
├── header.php                         # Header with your exact design
├── footer.php                         # Footer with your exact design  
├── index.php                          # Main WordPress template
├── config.js                          # Your original config (preserved)
├── css/
│   └── styles.css                     # Your original styles (unchanged)
├── js/
│   └── main.js                        # Enhanced with WooCommerce support
├── assets/
│   └── product-main.png               # Your original product image
├── woocommerce/
│   ├── single-product.php             # Single product page template
│   ├── archive-product.php            # Shop/category pages
│   ├── content-product.php            # Product loop item
│   ├── cart/
│   │   └── cart.php                   # Shopping cart page
│   └── single-product/
│       └── add-to-cart/
│           └── simple.php             # Product form (matches your design)
├── includes/
│   ├── webhook-integration.php        # n8n webhook functionality
│   └── customizations.php             # WooCommerce customizations
└── sample-data/
    └── initial-setup.md               # Setup instructions
```

## 🎯 What Each File Does

### Core Theme Files
- **style.css**: WordPress theme header + WooCommerce compatibility
- **functions.php**: Main theme functions, WooCommerce support, webhook integration
- **header.php**: Your exact header design with WooCommerce cart integration
- **footer.php**: Your exact footer design preserved
- **index.php**: WordPress-compatible version of your index.html

### Original Files (Preserved)
- **css/styles.css**: Your original CSS - completely unchanged
- **js/main.js**: Your original JavaScript - enhanced with WooCommerce features
- **assets/**: Your original images and assets - unchanged
- **config.js**: Your original configuration - preserved for compatibility

### WooCommerce Templates
- **woocommerce/single-product.php**: Product pages using your design
- **woocommerce/archive-product.php**: Shop and category pages
- **woocommerce/content-product.php**: Individual product cards in listings
- **woocommerce/cart/cart.php**: Shopping cart matching your design
- **woocommerce/single-product/add-to-cart/simple.php**: Product form exactly like your original

### Integration Files
- **includes/webhook-integration.php**: 
  - Sends orders to your n8n webhook
  - Maps all 14 Excel columns perfectly
  - Admin interface for webhook management
  - Testing functionality

- **includes/customizations.php**:
  - Makes WooCommerce match your design
  - Custom form styling
  - Additional e-commerce features

## 🚀 How to Create ZIP File

### Option 1: Using the Script (Linux/Mac)
```bash
chmod +x create-theme-zip.sh
./create-theme-zip.sh
```

### Option 2: Manual ZIP Creation
1. Create a folder named `grindctrl`
2. Copy all files into this folder (except .git, README files, etc.)
3. ZIP the `grindctrl` folder
4. Name it: `grindctrl-woocommerce-theme.zip`

### Option 3: Files to Include in ZIP
```
grindctrl/
├── style.css
├── functions.php  
├── header.php
├── footer.php
├── index.php
├── config.js
├── css/styles.css
├── js/main.js
├── assets/product-main.png
├── woocommerce/ (entire folder)
├── includes/ (entire folder)
└── sample-data/ (entire folder)
```

## ✅ Installation Checklist

After creating the ZIP:

1. **WordPress Requirements**:
   - WordPress 5.0+
   - WooCommerce 8.0+
   - PHP 7.4+

2. **Installation Steps**:
   - Upload ZIP via WordPress Admin
   - Activate theme
   - Configure webhook URL
   - Add your first product
   - Test order flow

3. **Configuration**:
   - Go to **Appearance > GrindCTRL Settings**
   - Enter webhook URL: `https://grindctrlface.app.n8n.cloud/webhook/test2git`
   - Test webhook connection
   - Configure WooCommerce settings

## 🎉 Ready Features

Your ZIP will include:
- ✅ Complete WooCommerce integration
- ✅ Your exact original design
- ✅ Perfect n8n webhook compatibility  
- ✅ All 14 Excel columns mapped
- ✅ Admin configuration panel
- ✅ Webhook testing functionality
- ✅ Full e-commerce features
- ✅ Mobile responsive design
- ✅ SEO optimized
- ✅ Professional order management

The theme is production-ready and maintains 100% compatibility with your existing n8n workflow!