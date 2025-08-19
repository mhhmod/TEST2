# GrindCTRL - Luxury Cropped Black T-Shirt E-commerce Store

A minimal, single-product e-commerce website for selling luxury cropped black t-shirts. Built with vanilla HTML, CSS, and JavaScript, designed specifically for GitHub Pages deployment with n8n webhook integration.

## 🚀 Features

- **Single Product Focus**: Clean, focused product page for luxury cropped black t-shirt
- **Dark Theme Design**: Modern dark aesthetic matching contemporary fashion e-commerce
- **Responsive Design**: Mobile-first approach with seamless desktop experience
- **Cart Functionality**: Add to cart with quantity management and visual feedback
- **Order Processing**: Direct integration with n8n webhook for automated order handling
- **Excel Export**: Complete order data export functionality
- **Admin Dashboard**: Order management and status tracking
- **Form Validation**: Client-side validation for customer information and order details
- **Static Deployment**: Fully compatible with GitHub Pages - no server required

## 🛠 Technology Stack

- **Frontend**: Vanilla HTML5, CSS3, JavaScript (ES6+)
- **Fonts**: Google Fonts (Inter & Poppins)
- **Icons**: Font Awesome 6
- **Excel Export**: SheetJS (xlsx)
- **Deployment**: GitHub Pages with automated CI/CD
- **Order Processing**: n8n webhook integration

## 📋 Excel Column Structure

Orders are exported with the following exact structure:
```
Order ID, Customer Name, Phone, City, Address, COD Amount, Tracking Number, Courier, Total, Date, Status, Payment Method, Product, Quantity
```

### System-Generated Fields
- **COD Amount**: Auto-calculated (5% of total for COD orders)
- **Tracking Number**: Unique identifier (TRK + 9-digit number)
- **Total**: Complete calculation with taxes and delivery
- **Status**: Order lifecycle (Pending → Processing → Shipped → Delivered)

## 🚀 Quick Setup for GitHub Pages

### 1. Fork or Clone Repository
```bash
git clone https://github.com/yourusername/grindctrl-shop.git
cd grindctrl-shop
```

### 2. Configure Webhook URL
Update the webhook URL in `config.js`:
```javascript
window.CONFIG.WEBHOOK_URL = 'https://your-n8n-instance.com/webhook/grindctrl-orders';
```

### 3. Enable GitHub Pages
1. Go to repository **Settings**
2. Navigate to **Pages** section
3. Source: **Deploy from a branch**
4. Branch: **main** (or **master**)
5. Folder: **/ (root)**

### 4. Your Site is Live!
Your store will be available at: `https://yourusername.github.io/grindctrl-shop`

## 📁 Project Structure

```
├── index.html              # Main product page
├── styles.css              # Dark theme styling and responsive design
├── config.js               # Configuration (webhook URL, product settings)
├── main.js                 # Core application logic and form handling
├── order-manager.js        # Order processing and management
├── excel-export.js         # Excel export functionality
├── webhook-handler.js      # n8n webhook integration
├── script.js               # Additional UI interactions and admin panel
├── server.js               # Optional: Local development server
├── README.md               # This file
└── .github/workflows/      # Optional: GitHub Actions for automated deployment
```

## ⚙️ Configuration

### Product Settings (config.js)
```javascript
window.CONFIG.PRODUCT = {
    name: 'Luxury Cropped Black T-Shirt',
    price: 300.00,
    currency: 'EGP',
    originalPrice: 350.00,
    deliveryCharges: 0, // Free shipping
    maxQuantity: 10
};
```

### Order Processing Settings
```javascript
window.CONFIG.ORDER = {
    codChargePercentage: 5, // 5% COD charges
    trackingNumberPrefix: 'GC',
    defaultStatus: 'Pending',
    statusOptions: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled']
};
```

## 📊 Order Data Structure

Orders sent to the webhook include the complete Excel-compatible format:

```json
{
  "Order ID": "GC-ABC123-XYZ789",
  "Customer Name": "John Doe",
  "Phone": "+1234567890",
  "City": "Cairo",
  "Address": "123 Main St",
  "COD Amount": "315.00",
  "Tracking Number": "TRK123456789",
  "Courier": "BOSTA",
  "Total": "300.00",
  "Date": "2025-01-19T10:30:00.000Z",
  "Status": "Pending",
  "Payment Method": "COD",
  "Product": "Luxury Cropped Black T-Shirt",
  "Quantity": 1
}
```

## 🎨 Customization

### Colors & Theme (styles.css)
```css
:root {
    --primary-color: #E74C3C;      /* Coral red accent */
    --background-color: #1a1a1a;   /* Dark background */
    --text-color: #ffffff;         /* White text */
    --light-grey: #2a2a2a;        /* Dark grey cards */
    --border-color: #404040;       /* Border grey */
}
```

### Product Image
Replace the image URL in `index.html` or add your product image to the repository and update the src attribute.

## 🔧 n8n Webhook Setup

### 1. Create Webhook Trigger
- Add a **Webhook** trigger node in your n8n workflow
- Set method to **POST**
- Copy the **Production URL**

### 2. Update Configuration
Replace the webhook URL in `config.js` with your n8n webhook URL:
```javascript
window.CONFIG.WEBHOOK_URL = 'https://your-n8n-instance.com/webhook/your-webhook-id';
```

### 3. Process Order Data
Your n8n workflow will receive the complete order data in Excel-compatible format, ready for:
- Email notifications
- Database storage
- Excel file generation
- Inventory management
- Customer communication

## 💾 Local Development

### Option 1: Simple HTTP Server (Python)
```bash
python -m http.server 8000
# Visit http://localhost:8000
```

### Option 2: Node.js Server (Included)
```bash
npm install express
node server.js
# Visit http://localhost:5000
```

### Option 3: Live Server (VS Code Extension)
Install the "Live Server" extension and click "Go Live" in VS Code.

## 📱 Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## 🔒 Security & Privacy

- All form data is validated client-side before submission
- Webhook communication uses HTTPS
- No sensitive data is stored in localStorage
- Order data is sent directly to your configured n8n webhook

## 📈 Analytics Integration

To add Google Analytics or other tracking:

1. Add tracking script to `index.html` head section
2. Add event tracking to order completion in `main.js`
3. Track cart additions and product views as needed

## 🆘 Troubleshooting

### Common Issues

1. **Orders not reaching webhook**
   - Verify webhook URL in `config.js`
   - Check browser console for errors
   - Test webhook URL with a tool like Postman

2. **Styling issues**
   - Clear browser cache
   - Check CSS file is loading properly
   - Verify Font Awesome and Google Fonts CDN links

3. **Form validation not working**
   - Check JavaScript console for errors
   - Ensure all required form fields have proper IDs
   - Verify main.js is loading correctly

### Debug Mode
Open browser console to see detailed logs about:
- Order processing
- Webhook communication
- Form validation
- Cart operations

## 📄 License

MIT License - feel free to use for your own projects.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For issues related to:
- **Code/Features**: Create an issue in this repository
- **n8n Integration**: Refer to [n8n documentation](https://docs.n8n.io/)
- **GitHub Pages**: Check [GitHub Pages documentation](https://docs.github.com/en/pages)

---

**Built with ❤️ for the GrindCTRL brand**