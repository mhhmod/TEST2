# Manual ZIP Creation Instructions

## 📁 How to Create the ZIP File Manually

### Step 1: Create Theme Folder
Create a new folder named `grindctrl` on your computer

### Step 2: Copy These Files Into the `grindctrl` Folder

```
grindctrl/
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

### Step 3: Create ZIP File

**On Windows:**
1. Right-click the `grindctrl` folder
2. Select "Send to" > "Compressed (zipped) folder"
3. Rename to `grindctrl-woocommerce-theme.zip`

**On Mac:**
1. Right-click the `grindctrl` folder
2. Select "Compress grindctrl"
3. Rename to `grindctrl-woocommerce-theme.zip`

**On Linux:**
```bash
zip -r grindctrl-woocommerce-theme.zip grindctrl/
```

### Step 4: Verify ZIP Contents
The ZIP should contain:
- Root folder named `grindctrl`
- All theme files inside that folder
- Total size: ~2MB

## ⚠️ Important Notes:

1. **Folder Structure**: WordPress themes must be in a folder, not loose files
2. **Folder Name**: The folder inside the ZIP must be named `grindctrl`
3. **File Permissions**: Make sure all files are readable
4. **No Extra Files**: Don't include .git, .DS_Store, or other system files

## 🚀 Installation After ZIP Creation:

1. Upload ZIP to WordPress: **Appearance > Themes > Add New > Upload**
2. Activate the theme
3. Configure webhook in **Appearance > GrindCTRL Settings**
4. Add your first product
5. Test the complete flow

Your theme will work exactly like your original site but with full WooCommerce functionality!