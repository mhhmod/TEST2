#!/bin/bash

# GrindCTRL WooCommerce Theme Packaging Script
# This script creates a ZIP file ready for WordPress theme installation

echo "Creating GrindCTRL WooCommerce Theme ZIP package..."

# Create temporary directory for theme files
mkdir -p grindctrl-theme-package/grindctrl

# Copy all theme files to package directory
cp -r * grindctrl-theme-package/grindctrl/ 2>/dev/null || true

# Remove the package directory from itself (avoid recursion)
rm -rf grindctrl-theme-package/grindctrl/grindctrl-theme-package 2>/dev/null || true

# Remove git files and other non-theme files
rm -rf grindctrl-theme-package/grindctrl/.git* 2>/dev/null || true
rm -f grindctrl-theme-package/grindctrl/create-theme-zip.sh 2>/dev/null || true
rm -f grindctrl-theme-package/grindctrl/replit.md 2>/dev/null || true
rm -f grindctrl-theme-package/grindctrl/README.md 2>/dev/null || true
rm -f grindctrl-theme-package/grindctrl/UPDATE_SUMMARY.md 2>/dev/null || true
rm -f grindctrl-theme-package/grindctrl/DEPLOYMENT_GUIDE.md 2>/dev/null || true

# Create the ZIP file
cd grindctrl-theme-package
zip -r ../grindctrl-woocommerce-theme.zip grindctrl/

# Clean up
cd ..
rm -rf grindctrl-theme-package

echo "✅ Theme packaged successfully!"
echo "📦 File created: grindctrl-woocommerce-theme.zip"
echo ""
echo "🚀 Installation Instructions:"
echo "1. Download the grindctrl-woocommerce-theme.zip file"
echo "2. Go to WordPress Admin > Appearance > Themes"
echo "3. Click 'Add New' > 'Upload Theme'"
echo "4. Upload the ZIP file"
echo "5. Activate the theme"
echo "6. Configure webhook URL in Appearance > GrindCTRL Settings"
echo ""
echo "📋 Theme includes:"
echo "- Complete WooCommerce integration"
echo "- Your original design preserved"
echo "- n8n webhook compatibility"
echo "- Excel column mapping"
echo "- Admin configuration panel"
echo "- All e-commerce features"