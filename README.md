# GITSYSTEMNG - Complete Installation Guide

## 📋 What You're Getting

A complete website with backend admin panel for Greenpasture Tech System:

### Frontend Features:
- ✅ Beautiful landing page with animations
- ✅ Product showcase with filtering
- ✅ Contact form
- ✅ Services section
- ✅ Academy section
- ✅ Fully responsive design

### Backend Features:
- ✅ Admin login system
- ✅ Product management (Add/Edit/Delete)
- ✅ Image upload system
- ✅ Category management
- ✅ Inquiry management
- ✅ Dashboard with statistics
- ✅ Activity logging
- ✅ Secure authentication

---

## 🚀 Installation on Hostinger (Step-by-Step)

### STEP 1: Get Your Domain & Hosting
1. Purchase a domain and hosting plan on Hostinger
2. Access your Hostinger control panel (hPanel)

### STEP 2: Upload Files
1. Go to **File Manager** in hPanel
2. Navigate to `public_html` folder
3. Upload ALL files from this package:
   ```
   public_html/
   ├── index.html              (Your landing page)
   ├── config.php              (Database configuration)
   ├── api.php                 (API for products)
   ├── database.sql            (Database schema)
   ├── admin/                  (Admin panel folder)
   │   ├── login.php
   │   ├── index.php
   │   ├── products.php
   │   ├── inquiries.php
   │   ├── logout.php
   │   └── includes/
   │       ├── header.php
   │       └── footer.php
   └── uploads/                (Create this folder)
       └── products/           (Create this folder)
   ```

### STEP 3: Create Database
1. In hPanel, go to **MySQL Databases**
2. Click "Create New Database"
3. Database name: `gitsystemng`
4. Create database user with a strong password
5. Add user to database with ALL PRIVILEGES

### STEP 4: Import Database Schema
1. Go to **phpMyAdmin** in hPanel
2. Select your `gitsystemng` database
3. Click "Import" tab
4. Choose `database.sql` file
5. Click "Go" to import

### STEP 5: Configure Database Connection
1. Open `config.php` in File Manager
2. Update these lines with YOUR database details:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gitsystemng');         // Your database name
   define('DB_USER', 'your_db_username');   // Your database username
   define('DB_PASS', 'your_db_password');   // Your database password
   ```
3. Update site URL:
   ```php
   define('SITE_URL', 'https://yourdomain.com');
   ```
4. Save the file

### STEP 6: Set File Permissions
1. In File Manager, right-click `uploads` folder
2. Click "Permissions"
3. Set to `755` or `775`
4. Apply to all subdirectories

### STEP 7: Test Your Installation
1. Visit: `https://yourdomain.com` - See your landing page
2. Visit: `https://yourdomain.com/admin/login.php`
3. Login with default credentials:
   - **Username:** admin
   - **Password:** admin123
4. **IMPORTANT:** Change the password immediately after login!

---

## 🔐 Security Setup (IMPORTANT!)

### 1. Change Default Admin Password
After first login:
1. You'll need to create a password change page OR
2. Update directly in database using phpMyAdmin:
   ```sql
   UPDATE admin_users 
   SET password = '$2y$10$YOUR_NEW_HASHED_PASSWORD' 
   WHERE username = 'admin';
   ```
   Use an online bcrypt generator to hash your new password

### 2. Secure config.php
Add this to top of config.php:
```php
<?php
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
```

### 3. Protect Admin Directory (Optional but Recommended)
Create `.htaccess` in `/admin` folder:
```apache
# Protect admin area
AuthType Basic
AuthName "Admin Area"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

---

## 📝 How to Use the Admin Panel

### Adding Products:
1. Login to admin panel
2. Click "Products" in sidebar
3. Click "+ Add New Product"
4. Fill in all details:
   - Name, Category, Description
   - Price (in Naira)
   - Upload images (multiple allowed)
   - Set as Featured (optional)
   - Add badge (New/Sale/Popular)
5. Click "Add Product"

### Managing Inquiries:
1. Click "Inquiries" in sidebar
2. View all customer messages
3. Update status (New → Contacted → Resolved)
4. Filter by status

### Categories:
- Categories are pre-loaded: Laptops, Accessories, Networking, Components, Peripherals
- You can add more via database or create a categories page

---

## 🔄 Updating the Frontend to Use Real Products

The landing page is currently showing sample products. Here's how to connect it to your database:

### Option 1: Simple JavaScript Update (Recommended)
Replace the products section in `index.html` with this:

```html
<script>
// Fetch products from API
fetch('/api.php?action=products')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProducts(data.data);
        }
    });

function displayProducts(products) {
    const grid = document.querySelector('.products-grid');
    grid.innerHTML = ''; // Clear sample products
    
    products.forEach(product => {
        const card = `
            <div class="product-card" data-category="${product.category}">
                ${product.badge ? `<div class="product-badge">${product.badge}</div>` : ''}
                <div class="product-image">
                    ${product.image ? `<img src="${product.image}" alt="${product.name}">` : '<div class="placeholder-image">📦</div>'}
                </div>
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p class="product-category">${product.category}</p>
                    <p class="product-desc">${product.short_description || ''}</p>
                    <div class="product-footer">
                        <span class="product-price">₦${product.price.toLocaleString()}</span>
                        <button class="btn-add-cart" onclick="inquireProduct(${product.id})">Inquire</button>
                    </div>
                </div>
            </div>
        `;
        grid.innerHTML += card;
    });
}

function inquireProduct(productId) {
    // Scroll to contact form and pre-fill product
    document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
    // You can add logic to pre-fill the form
}
</script>
```

### Option 2: Convert to PHP
Rename `index.html` to `index.php` and use PHP to fetch products directly.

---

## 📧 Setting Up Contact Form

The contact form submits to `api.php`. To receive emails:

1. Install PHPMailer (optional) or use PHP's mail() function
2. Add email sending to `api.php` in the submit_inquiry section
3. Or use a service like SendGrid, Mailgun, or SMTP

Example email code to add in api.php:
```php
// After saving inquiry to database
$to = "your-email@gitsystemng.com";
$subject = "New Inquiry from $name";
$body = "Name: $name\nEmail: $email\nMessage: $message";
$headers = "From: noreply@gitsystemng.com";
mail($to, $subject, $body, $headers);
```

---

## 🎨 Customization

### Update Contact Details:
Edit these in the landing page (index.html):
- Phone number
- Email address
- Physical address
- Social media links

### Change Colors:
Edit CSS variables in both files:
```css
:root {
    --primary: #00ff88;      /* Main green color */
    --accent: #ff3366;       /* Accent pink */
    --dark: #050810;         /* Background */
}
```

### Add More Product Images:
When adding products in admin:
- Select multiple files in the image uploader
- First image becomes the primary image
- All images are saved and can be used in product galleries

---

## 🐛 Troubleshooting

### "Database Connection Failed"
- Check config.php has correct database credentials
- Verify database exists in phpMyAdmin
- Check database user has privileges

### "Permission Denied" on Image Upload
- Check `/uploads` folder exists
- Set permissions to 755 or 775
- Ensure web server can write to folder

### Products Not Showing on Frontend
- Check API: visit `yourdomain.com/api.php?action=products`
- Should return JSON with products
- If empty, add products in admin panel first

### Admin Panel Not Loading CSS
- Check file paths in header.php
- Ensure all admin files are in /admin folder
- Clear browser cache

---

## 📱 Contact & Support

For issues with GITSYSTEMNG:
- Email: info@gitsystemng.com
- Update contact details in Settings page (when you create it)

---

## 🔄 Next Steps / Enhancements

Optional features you can add:
1. **Shopping Cart** - Add cart functionality
2. **Online Payments** - Integrate Paystack or Flutterwave
3. **User Accounts** - Customer registration and order history
4. **Email Notifications** - Auto-emails for inquiries
5. **Product Search** - Search functionality
6. **Reviews & Ratings** - Customer product reviews
7. **Blog Section** - Tech news and tips
8. **Newsletter** - Email collection and campaigns

---

## 📄 File Structure

```
GITSYSTEMNG/
├── README.md (this file)
├── database.sql
├── config.php
├── api.php
├── index.html (landing page)
├── admin/
│   ├── login.php
│   ├── index.php
│   ├── products.php
│   ├── inquiries.php
│   ├── logout.php
│   └── includes/
│       ├── header.php
│       └── footer.php
└── uploads/ (create this)
    └── products/ (create this)
```

---

## 🎉 You're All Set!

Your GITSYSTEMNG website is now ready to go live. Start adding your products, updating content, and growing your business!

**Remember:**
- Change default admin password
- Add your actual products
- Update contact information
- Test everything before going live
- Keep regular backups of your database

Good luck with your tech business! 🚀
