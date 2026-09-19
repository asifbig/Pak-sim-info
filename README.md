# PAK SIM DATABASE - Premium Golden Edition
## InfinityFree Deployment Guide

This is a complete, production-ready PAK SIM Database application with **real database integration** for authentic statistics.

---

## 📋 Features

✅ **Real Database Integration** - Total Users and Total Checks are stored in a real MySQL database (not fake numbers)
✅ **Premium Golden Design** - Beautiful, responsive interface with gold accents
✅ **Search Functionality** - Search by phone number or CNIC
✅ **Real-Time Statistics** - Live user count and search count from database
✅ **Mobile Responsive** - Works perfectly on all devices
✅ **WhatsApp Integration** - Direct link to WhatsApp channel
✅ **Copy & Share** - Copy results or share via WhatsApp

---

## 🚀 Deployment Steps

### Step 1: Upload Files to InfinityFree

1. Go to your InfinityFree account dashboard
2. Click **File Manager**
3. Navigate to the **public_html** folder
4. Upload all these files:
   - `index.php`
   - `config.php`
   - `css.php`
   - `style.php`
   - `setup.php`
   - `README.md`

### Step 2: Get Your Database Credentials

1. In InfinityFree dashboard, go to **MySQL Databases**
2. Create a new database (if not already created)
3. Note down:
   - **Database Name** (e.g., `if0_xxxxx_simdb`)
   - **Database User** (e.g., `if0_xxxxx`)
   - **Database Password**
   - **Host** (usually `localhost`)

### Step 3: Update config.php

1. Open `config.php` in your file manager
2. Replace these lines with your actual credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'if0_xxxxx');      // Your database user
define('DB_PASS', 'your_password');  // Your database password
define('DB_NAME', 'if0_xxxxx_simdb'); // Your database name
```

3. Save the file

### Step 4: Initialize the Database

1. Open your browser and visit:
   ```
   https://yoursite.infinityfree.com/setup.php
   ```
   (Replace `yoursite` with your actual domain)

2. You should see a success message: **"✓ Database Setup Successful!"**

3. **IMPORTANT:** Delete `setup.php` from your file manager after setup is complete (for security)

### Step 5: Access Your Website

Visit: `https://yoursite.infinityfree.com/index.php`

Your website is now live! 🎉

---

## 📊 How Statistics Work

The application tracks:
- **Total Users**: Unique IP addresses that have performed searches
- **Total Checks**: Total number of searches performed
- **Live Status**: Shows "LIVE" when the server is running

All data is stored in the `search_logs` table in your MySQL database.

---

## 🔧 File Descriptions

| File | Purpose |
|------|---------|
| `index.php` | Main application file with search interface |
| `config.php` | Database configuration and functions |
| `css.php` | Stylesheet (serves as CSS file) |
| `style.php` | CSS wrapper file |
| `setup.php` | Database initialization (delete after use) |
| `README.md` | This file |

---

## ⚠️ Security Notes

1. **Delete setup.php** after database setup is complete
2. **Keep config.php secure** - it contains database credentials
3. **Use HTTPS** - InfinityFree provides free SSL certificates
4. **Backup regularly** - Keep backups of your database

---

## 🐛 Troubleshooting

### "Connection failed" Error
- Check if database credentials in `config.php` are correct
- Verify database user has proper permissions
- Ensure MySQL database is created

### "Page Unavailable" Error
- Check if all files are uploaded to `public_html`
- Verify file permissions (should be 644 for PHP files)
- Check InfinityFree server status

### Statistics Not Updating
- Ensure database tables were created successfully via `setup.php`
- Verify database connection in `config.php`
- Check if search_logs table exists in database

---

## 📞 Support

For issues or questions:
- Check InfinityFree documentation: https://www.infinityfree.net/
- Review the code comments in each PHP file
- Test the setup.php page to verify database connection

---

## 🎨 Customization

You can customize:
- **Colors**: Edit the CSS variables in `css.php`
- **API Endpoint**: Change the API URL in `index.php` (line 30)
- **WhatsApp Link**: Update the WhatsApp channel link in `index.php`

---

## 📝 License

Developed by OLD-STUDIO
Premium Golden Edition - 2026

---

**Ready to deploy? Follow the steps above and your website will be live in minutes!** ✨
