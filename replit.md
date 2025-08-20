# GrindCTRL E-commerce Site - Order Form Enhancement

## Repository Description
Enhance existing single-product website order form to capture all required Excel column data without changing file structure or logic

## Project Overview
GrindCTRL is a single-product e-commerce website for luxury cropped black t-shirts. The site currently processes orders through n8n webhooks but needs enhancement to capture all Excel column data fields.

## Current Excel Requirements
**Excel Columns Required:**
- Order ID *(auto-generated)*
- Customer Name *(customer input)*
- Phone *(customer input)*  
- City *(customer input)*
- Address *(customer input)*
- COD Amount *(auto-calculated)*
- Tracking Number *(auto-generated)*
- Courier *(auto-generated)*
- Total *(auto-calculated)*
- Date *(auto-generated)*
- Status *(customer/system input)*
- Payment Method *(customer selection)*
- Product *(customer selection)*
- Quantity *(customer input)*

## Current Form Analysis (Updated)
**Currently Captured:**
- Customer Name (firstName + lastName)
- Phone
- City
- Address
- Quantity
- Payment Method (customer selection) ✓ ADDED
- Size (not in Excel requirements)
- Email (not in Excel requirements)
- Postal Code (not in Excel requirements)

**System-Generated Fields:**
- Order ID (auto-generated)
- Status (system-controlled - starts as "New") ✓ IMPLEMENTED
- Product (auto-filled as "Luxury Cropped Black T-Shirt")
- COD Amount (auto-calculated from total)
- Tracking Number (auto-generated)
- Courier (auto-set as "BOSTA")
- Total (auto-calculated)
- Date (auto-generated)

**COMPLETED CHANGES:**
✓ Added Payment Method dropdown with options
✓ Removed Status from customer input (now system-controlled)
✓ Updated form validation to exclude Status field
✓ Modified webhook data structure to include all Excel columns
✓ Status automatically set to "New" for all orders

## User Preferences
- Keep existing file structure unchanged
- No new files or folders
- GitHub deployment compatible
- Simple edits only
- Maintain existing logic and design

## Technical Architecture
- Static HTML/CSS/JS website
- n8n webhook integration for order processing  
- GitHub Pages deployment with automated CI/CD
- Dark theme design with responsive layout