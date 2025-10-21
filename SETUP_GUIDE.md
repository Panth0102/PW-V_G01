# SkillSwap Setup Guide

This guide will help you set up and run your SkillSwap project successfully.

## Prerequisites

1. **MAMP** (or XAMPP) installed and running
2. **PHP 7.4+** (included with MAMP)
3. **MySQL** (included with MAMP)

## Step-by-Step Setup

### 1. Start MAMP
- Open MAMP application
- Click "Start Servers" to start Apache and MySQL
- Verify both servers are running (green lights)

### 2. Place Project Files
- Copy your SkillSwap project folder to MAMP's `htdocs` directory
- Default location: `/Applications/MAMP/htdocs/` (Mac) or `C:\MAMP\htdocs\` (Windows)

### 3. Configure Database Settings
- Open `config/config.php`
- Verify these settings match your MAMP configuration:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', 'root'); // MAMP default
  define('DB_NAME', 'skillswap');
  ```

### 4. Create Database
**Option A: Using the setup script (Recommended)**
1. Open your browser
2. Go to `http://localhost/your-project-folder/setup_database.php`
3. Follow the on-screen instructions
4. Note the test user credentials provided

**Option B: Manual setup**
1. Open phpMyAdmin (`http://localhost/phpMyAdmin`)
2. Create a new database named `skillswap`
3. Import the SQL file from `database/skillswap_db.sql`

### 5. Test Your Setup
1. Run the debug helper: `http://localhost/your-project-folder/debug_helper.php`
2. Check all green checkmarks
3. Fix any red X issues shown

### 6. Test Database Connection
1. Run: `http://localhost/your-project-folder/test_db_connection.php`
2. Verify all tests pass
3. Note the test user credentials

### 7. Access Your Application
1. Go to: `http://localhost/your-project-folder/`
2. Try logging in with test credentials:
   - Email: `admin@skillswap.com`
   - Password: `admin123`

## Common Issues & Solutions

### "Connection failed" Error
- **Check MAMP is running**: Both Apache and MySQL should show green
- **Verify port**: MAMP usually uses port 8889 for MySQL
- **Update config**: If using port 8889, change DB_HOST to `localhost:8889`
- **Check credentials**: Ensure username/password match MAMP settings

### "Database doesn't exist" Error
- Run `setup_database.php` to create the database
- Or manually create database named `skillswap` in phpMyAdmin

### "Page not found" Error
- Ensure you're accessing via `http://localhost/your-project-folder/`
- Check that Apache is running in MAMP
- Verify project is in the correct htdocs folder

### Login/Signup Not Working
- Run database setup first
- Check if Users table exists and has data
- Clear browser cache and cookies
- Check PHP error logs in MAMP

### Styling Issues
- Verify `assets/css/style.css` exists and is accessible
- Check browser console for 404 errors
- Clear browser cache

## File Structure Check
Your project should have this structure:
```
your-project/
├── config/
│   ├── config.php
│   └── connect.php
├── includes/
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── images/
├── database/
│   └── skillswap_db.sql
├── index.php (login page)
├── dashboard.php
├── signup.php
├── offer_skill.php
└── other PHP files...
```

## Security Notes
After setup is complete:
1. Delete `setup_database.php`
2. Delete `debug_helper.php`
3. Delete `test_db_connection.php`
4. Set `DEBUG_MODE` to `false` in `config/config.php`

## Testing Your Application

### Test User Account
- Email: `admin@skillswap.com`
- Password: `admin123`

### Test Flow
1. Login with test account
2. Navigate to dashboard
3. Try posting a skill
4. Test search and filtering
5. Create a new user account

## Getting Help
If you're still having issues:
1. Run `debug_helper.php` and check all items
2. Check MAMP error logs
3. Verify all files are in the correct locations
4. Ensure MAMP ports match your configuration

## Next Steps
Once everything is working:
1. Create your own user account
2. Customize the styling in `assets/css/style.css`
3. Add more features as needed
4. Consider adding proper error logging
5. Implement additional security measures

Good luck with your project! 🚀