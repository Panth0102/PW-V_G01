# 🚀 SkillSwap - 000webhost Deployment Guide

## Step 1: Create 000webhost Account

1. **Go to**: https://www.000webhost.com
2. **Click**: "Get Free Hosting"
3. **Fill out the form**:
   - Email address
   - Password (make it strong)
   - Website name (e.g., `skillswap-demo`)
4. **Verify your email** (check inbox/spam)
5. **Complete account setup**

## Step 2: Create Your Website

1. **Login** to your 000webhost dashboard
2. **Click**: "Create New Website"
3. **Choose**: "Upload Own Website"
4. **Set website name**: `psskillswap`
5. **Your URL will be**: `psskillswap.000webhostapp.com`
6. **Click**: "Create"

## Step 3: Get Database Details

1. **Go to**: "Manage Website" → Your website
2. **Click**: "Database" in the left sidebar
3. **Click**: "New Database"
4. **Fill details**:
   - Database name: `skillswap`
   - Username: (will be auto-generated)
   - Password: (create a strong password)
5. **Click**: "Create Database"
6. **IMPORTANT**: Write down these details:
   ```
   Database Host: localhost
   Database Name: id[numbers]_skillswap
   Username: id[numbers]_username
   Password: [your password]
   ```

## Step 4: Update Your Config File

Update your `config/config.php` with the database details from Step 3:

```php
// Database Configuration for 000webhost
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'id12345678_username'); // Replace with your actual username
define('DB_PASSWORD', 'your_database_password'); // Replace with your actual password
define('DB_NAME', 'id12345678_skillswap'); // Replace with your actual database name

// Update your website URL
define('APP_URL', 'https://psskillswap.000webhostapp.com'); // Your actual URL
```

## Step 5: Upload Your Files

### Method 1: File Manager (Recommended)
1. **Go to**: "File Manager" in your website dashboard
2. **Navigate to**: `public_html` folder
3. **Delete** the default `index.html` file
4. **Upload all your project files**:
   - Select all files from your SkillSwap project
   - Drag and drop OR use "Upload Files"
   - Make sure `index.php` is in the root of `public_html`

### Method 2: FTP (Advanced)
1. **Get FTP details** from "File Manager" → "FTP Details"
2. **Use FTP client** like FileZilla
3. **Upload to**: `/public_html/` directory

## Step 6: Import Database

1. **Go to**: "Database" → "Manage"
2. **Click**: "phpMyAdmin"
3. **Login** with your database credentials
4. **Select** your database (id[numbers]_skillswap)
5. **Click**: "Import" tab
6. **Choose file**: Upload your `complete_database_reset.sql`
7. **Click**: "Go" to import

## Step 7: Test Your Website

1. **Visit**: `https://your-skillswap.000webhostapp.com`
2. **Login with**:
   - Email: `admin@skillswap.com`
   - Password: `admin123`
3. **Test features**:
   - Dashboard access
   - Skill browsing
   - User registration
   - Basic functionality

## Step 8: Troubleshooting

### If you see "Database Connection Error":
1. **Double-check** database credentials in `config/config.php`
2. **Verify** database exists in phpMyAdmin
3. **Check** if database import was successful

### If you see "Page Not Found":
1. **Ensure** `index.php` is in `public_html` root
2. **Check** file permissions (should be 644 for files, 755 for folders)
3. **Verify** all files uploaded correctly

### If login doesn't work:
1. **Check** if database import completed
2. **Verify** Users table has data
3. **Test** with the test script: `your-site.com/test_login.php`

## 📋 File Structure Check

Your `public_html` folder should look like this:
```
public_html/
├── index.php
├── dashboard.php
├── signup.php
├── config/
│   ├── config.php
│   └── connect.php
├── includes/
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   └── js/
└── [other PHP files]
```

## 🔐 Default Login Credentials

- **Admin Email**: admin@skillswap.com
- **Admin Password**: admin123
- **Other Users**: Use any email from database with password: `password`

## ⚡ Performance Tips

1. **Enable compression** in .htaccess:
```apache
# Add this to .htaccess file
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

2. **Optimize images** before uploading
3. **Minimize CSS/JS** files if needed

## 🚨 Important Notes

- **Free hosting limitations**: 1GB storage, 10GB bandwidth/month
- **No SSL by default**: Use HTTP for development, upgrade for HTTPS
- **Daily backups recommended**: Download your files regularly
- **Inactive sites**: May be suspended after 30 days of no visits

## 🎯 Next Steps After Deployment

1. **Change default passwords** for security
2. **Add your own content** and courses
3. **Test all features** thoroughly
4. **Consider upgrading** to premium if you need more resources
5. **Set up regular backups**

## 📞 Need Help?

If you encounter issues:
1. Check 000webhost documentation
2. Use their support chat
3. Verify all steps were followed correctly
4. Test with the debug script: `test_login.php`

---

**🎉 Congratulations!** Your SkillSwap project should now be live at:
`https://your-skillswap.000webhostapp.com`