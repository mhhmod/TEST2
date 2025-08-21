# Files to Copy for GrindCTRL WooCommerce Theme

## 📋 Essential Theme Files (Copy these to your `grindctrl` folder)

### Root Files:
```
✅ style.css                    (WooCommerce theme header)
✅ functions.php                (Main theme functions)
✅ header.php                   (Header template)
✅ footer.php                   (Footer template)
✅ index.php                    (Main template)
✅ config.js                    (Original config preserved)
```

### CSS Folder:
```
✅ css/styles.css               (Your original styles)
```

### JavaScript Folder:
```
✅ js/main.js                   (Enhanced with WooCommerce)
```

### Assets Folder:
```
✅ assets/product-main.png      (Your product image)
```

### WooCommerce Templates:
```
✅ woocommerce/single-product.php
✅ woocommerce/archive-product.php
✅ woocommerce/content-product.php
✅ woocommerce/cart/cart.php
✅ woocommerce/single-product/add-to-cart/simple.php
```

### Includes Folder:
```
✅ includes/webhook-integration.php
✅ includes/customizations.php
```

### Documentation:
```
✅ sample-data/initial-setup.md
```

## 🚫 Files to SKIP (Don't copy these):
```
❌ index.html                   (Original HTML - not needed)
❌ README.md                    (Project readme)
❌ DEPLOYMENT_GUIDE.md          (Deployment info)
❌ UPDATE_SUMMARY.md            (Update notes)
❌ replit.md                    (Replit specific)
❌ MANUAL-ZIP-INSTRUCTIONS.md   (Instructions only)
❌ THEME-STRUCTURE.md           (Documentation only)
❌ COPY-FILES-LIST.md           (This file)
❌ create-theme-zip.sh          (Script file)
❌ .git/ folder                 (Git files)
❌ .github/ folder              (GitHub files)
```

## 📁 Final Folder Structure:
```
grindctrl/                      (Your theme folder)
├── style.css
├── functions.php
├── header.php
├── footer.php
├── index.php
├── config.js
├── css/
│   └── styles.css
├── js/
│   └── main.js
├── assets/
│   └── product-main.png
├── woocommerce/
│   ├── single-product.php
│   ├── archive-product.php
│   ├── content-product.php
│   ├── cart/
│   │   └── cart.php
│   └── single-product/
│       └── add-to-cart/
│           └── simple.php
├── includes/
│   ├── webhook-integration.php
│   └── customizations.php
└── sample-data/
    └── initial-setup.md
```

## 🎯 Quick Copy Commands:

If you're in a terminal, you can copy like this:
```bash
# Create theme folder
mkdir grindctrl

# Copy essential files
cp style.css functions.php header.php footer.php index.php config.js grindctrl/

# Copy folders
cp -r css js assets woocommerce includes sample-data grindctrl/

# Create ZIP
zip -r grindctrl-woocommerce-theme.zip grindctrl/
```

## ⚡ **RECOMMENDED: Just download the ZIP file that's already created!**
File: `grindctrl-woocommerce-theme.zip` (2MB) - Ready to install!