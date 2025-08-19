# Overview

GrindCTRL is a single-product e-commerce website built as a static web application for selling luxury cropped black t-shirts. The project uses vanilla HTML, CSS, and JavaScript to create a minimal, elegant shopping experience with cart functionality and order processing through webhooks. The application is designed to be deployed on static hosting platforms like GitHub Pages.

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Frontend Architecture
- **Static Single Page Application**: Built with vanilla HTML, CSS, and JavaScript without frameworks
- **Responsive Design**: Mobile-first approach using CSS Grid and Flexbox
- **Component-Based Styling**: CSS custom properties (CSS variables) for consistent theming and easy maintenance
- **Font Integration**: Google Fonts (Inter and Poppins) and Font Awesome icons for enhanced typography and iconography

## State Management
- **Client-Side State**: Global AppState object manages cart items, product information, and application status
- **Local Storage**: Persistence layer for cart data across browser sessions
- **Event-Driven Updates**: DOM manipulation through JavaScript event handlers for real-time UI updates

## Product Management
- **Single Product Focus**: Hard-coded product information for the luxury cropped black t-shirt
- **Pricing Structure**: Supports original price and discounted price display
- **Inventory Management**: Client-side quantity validation with configurable min/max limits

## Order Processing
- **n8n Webhook Integration**: Orders submitted via HTTP POST to configured n8n webhook endpoint
- **Configuration Management**: Webhook URL configured through config.js file with GitHub Actions deployment automation
- **Environment Variables**: N8N_WEBHOOK_URL secret configured for automatic deployment
- **Error Handling**: Comprehensive webhook error handling with user feedback
- **Form Validation**: Client-side validation for customer information and order details
- **Order Data Structure**: Complete JSON payload with customer, product, and pricing information

## User Experience Features
- **Shopping Cart**: Add/remove items with visual feedback and counter updates
- **Product Gallery**: Image carousel or gallery for product visualization
- **Responsive Navigation**: Mobile-friendly navigation with cart icon and item count
- **Notification System**: User feedback for actions like adding to cart or order submission

# External Dependencies

## Third-Party Services
- **n8n Webhook**: Order processing and data collection endpoint
- **Google Fonts API**: Typography assets (Inter and Poppins font families)
- **Font Awesome CDN**: Icon library for UI elements
- **Static Hosting**: Designed for deployment on GitHub Pages or similar static hosting services

## Browser APIs
- **Local Storage**: Cart persistence across sessions
- **Fetch API**: HTTP requests for order submission
- **DOM APIs**: Dynamic content updates and event handling

## Development Dependencies
- **CSS Custom Properties**: Modern CSS features for theming
- **ES6+ JavaScript**: Modern JavaScript features including arrow functions, destructuring, and modules
- **Responsive Design**: CSS media queries for cross-device compatibility