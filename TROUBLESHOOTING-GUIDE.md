# GrindCTRL Theme Critical Error - Troubleshooting Guide

## 🚨 Critical Error Fix

This error typically occurs due to:
1. Missing WooCommerce plugin
2. PHP version compatibility
3. Theme file issues
4. Server configuration

## ⚡ QUICK FIXES (Try in order):

### Fix 1: Install WooCommerce Plugin
**MOST COMMON CAUSE**
1. Go to WordPress Admin (if accessible)
2. **Plugins > Add New**
3. Search for "WooCommerce"
4. Install and Activate WooCommerce
5. Refresh your site

### Fix 2: Check PHP Version
1. Contact your hosting provider
2. Ensure PHP version is 7.4 or higher
3. Enable required PHP extensions

### Fix 3: Switch to Default Theme Temporarily
1. Via FTP/cPanel File Manager:
   - Go to `/wp-content/themes/`
   - Rename `grindctrl` folder to `grindctrl-disabled`
2. Via WordPress Admin (if accessible):
   - **Appearance > Themes**
   - Activate a default theme (Twenty Twenty-Four)

### Fix 4: Enable WordPress Debug Mode
Add this to your `wp-config.php` file:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 🔧 STEP-BY-STEP RECOVERY:

### Step 1: Access Your Site
- Try accessing `/wp-admin/` directly
- If that fails, use FTP/cPanel File Manager

### Step 2: Check Error Logs
- Look in `/wp-content/debug.log`
- Check hosting control panel error logs
- Look for specific PHP errors

### Step 3: Verify Requirements
- ✅ WordPress 5.0+
- ✅ WooCommerce 8.0+
- ✅ PHP 7.4+
- ✅ Required PHP extensions enabled

### Step 4: Re-install Theme Correctly
1. Remove old theme files
2. Upload fresh theme ZIP
3. Activate step by step

## 🛠️ DETAILED SOLUTIONS:

### Solution A: Missing WooCommerce
```
Error: Call to undefined function wc_get_order()
Fix: Install WooCommerce plugin first
```

### Solution B: PHP Version Too Old
```
Error: Parse error, unexpected syntax
Fix: Upgrade to PHP 7.4 or higher
```

### Solution C: Memory Limit
```
Error: Fatal error, memory exhausted
Fix: Increase PHP memory limit to 256MB
```

### Solution D: File Permissions
```
Error: Permission denied
Fix: Set correct file permissions (644 for files, 755 for folders)
```

## 🔍 DEBUGGING STEPS:

### Check 1: Can you access wp-admin?
- YES: Go to Plugins, install WooCommerce
- NO: Use FTP to rename theme folder

### Check 2: What's in error logs?
- Look for specific function names
- Check line numbers mentioned
- Search for "Fatal error" or "Parse error"

### Check 3: Server Requirements
- PHP version: `php -v` or check hosting panel
- Memory limit: Look in hosting settings
- Required extensions: curl, zip, etc.

## 📞 IMMEDIATE RECOVERY:

### Emergency Access (Via FTP/cPanel):
1. Connect to your site files
2. Go to `/wp-content/themes/`
3. Rename `grindctrl` to `grindctrl-backup`
4. Your site should come back online
5. Then troubleshoot the theme

### Emergency Access (Via Database):
1. Access phpMyAdmin
2. Find `wp_options` table
3. Find row where `option_name = 'template'`
4. Change `option_value` to `twentytwentyfour`
5. Do same for `stylesheet` option

## ✅ PREVENTION CHECKLIST:

Before installing theme:
- [ ] WordPress updated to latest version
- [ ] WooCommerce plugin installed and activated  
- [ ] PHP 7.4+ confirmed
- [ ] Backup created
- [ ] Test on staging site first

## 🎯 MOST LIKELY SOLUTION:

**90% of the time, this error is because WooCommerce plugin is not installed.**

**Quick Fix:**
1. Access wp-admin
2. Install WooCommerce plugin
3. Activate it
4. Refresh your site
5. Theme should work perfectly!

## 📧 If Still Stuck:

1. Check your hosting error logs
2. Contact hosting support for PHP requirements
3. Send me the specific error message from debug.log
4. We can create a minimal version without WooCommerce dependencies