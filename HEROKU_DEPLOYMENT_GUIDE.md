# 🚀 SkillSwap - Heroku Deployment Guide

## Prerequisites
- Git installed on your computer
- Heroku CLI installed: https://devcenter.heroku.com/articles/heroku-cli
- Heroku account (free): https://signup.heroku.com/

## Step 1: Install Heroku CLI
```bash
# macOS (using Homebrew)
brew tap heroku/brew && brew install heroku

# Or download from: https://devcenter.heroku.com/articles/heroku-cli
```

## Step 2: Login to Heroku
```bash
heroku login
```

## Step 3: Initialize Git Repository (if not already done)
```bash
git init
git add .
git commit -m "Initial commit for Heroku deployment"
```

## Step 4: Create Heroku App
```bash
# Create app with a custom name (optional)
heroku create your-skillswap-app

# Or let Heroku generate a name
heroku create
```

## Step 5: Add ClearDB MySQL Add-on
```bash
# Add free MySQL database (ClearDB Ignite - Free tier)
heroku addons:create cleardb:ignite

# Get your database URL
heroku config:get CLEARDB_DATABASE_URL
```

## Step 6: Set Environment Variables
```bash
# Set your app URL (replace with your actual Heroku app URL)
heroku config:set APP_URL=https://your-skillswap-app.herokuapp.com

# Set debug mode to false for production
heroku config:set DEBUG_MODE=false

# View all config vars
heroku config
```

## Step 7: Import Database to ClearDB

### Method 1: Using Heroku CLI
```bash
# Get database connection details
heroku config:get CLEARDB_DATABASE_URL

# The URL format is: mysql://username:password@hostname/database_name
# Extract the details and use them with mysql command:
mysql -h [hostname] -u [username] -p[password] [database_name] < complete_database_reset.sql
```

### Method 2: Using phpMyAdmin (Easier)
1. Get your ClearDB credentials:
   ```bash
   heroku config:get CLEARDB_DATABASE_URL
   ```
2. Go to: https://www.cleardb.com/developers/connect/paas/heroku
3. Login with your ClearDB credentials
4. Use phpMyAdmin to import your `complete_database_reset.sql` file

## Step 8: Deploy to Heroku
```bash
# Deploy your app
git push heroku main

# If your main branch is named 'master':
git push heroku master
```

## Step 9: Open Your App
```bash
# Open your deployed app in browser
heroku open

# Or visit: https://your-app-name.herokuapp.com
```

## Step 10: View Logs (for troubleshooting)
```bash
# View real-time logs
heroku logs --tail

# View recent logs
heroku logs
```

## 🔧 Troubleshooting

### Database Connection Issues
```bash
# Check if ClearDB is properly configured
heroku config:get CLEARDB_DATABASE_URL

# Test database connection
heroku run php -r "
\$url = parse_url(getenv('CLEARDB_DATABASE_URL'));
\$conn = new mysqli(\$url['host'], \$url['user'], \$url['pass'], substr(\$url['path'], 1));
echo \$conn->connect_error ? 'Connection failed' : 'Connected successfully';
"
```

### App Not Loading
```bash
# Check app status
heroku ps

# Restart app
heroku restart

# Check logs for errors
heroku logs --tail
```

### File Upload Issues
- Heroku has an ephemeral filesystem
- Uploaded files are lost on app restart
- Consider using AWS S3 or Cloudinary for file storage

## 📋 Commands Summary

```bash
# Essential Heroku commands
heroku create                    # Create new app
heroku addons:create cleardb:ignite  # Add MySQL database
heroku config:set KEY=value      # Set environment variable
heroku config                    # View all config vars
git push heroku main            # Deploy app
heroku open                     # Open app in browser
heroku logs --tail              # View logs
heroku restart                  # Restart app
```

## 🎯 Post-Deployment Steps

1. **Test your app** thoroughly
2. **Change default passwords** for security
3. **Set up custom domain** (if needed):
   ```bash
   heroku domains:add www.yourdomain.com
   ```
4. **Enable SSL** (free with custom domains):
   ```bash
   heroku certs:auto:enable
   ```

## 💰 Pricing Notes

- **Heroku Dynos**: Free tier available (sleeps after 30 min of inactivity)
- **ClearDB**: Free tier includes 5MB storage, 4 connections
- **Upgrade options**: 
  - Hobby dyno: $7/month (no sleeping)
  - ClearDB Punch: $9.99/month (1GB storage)

## 🚨 Important Notes

- **Free dynos sleep** after 30 minutes of inactivity
- **Database size limit**: 5MB on free ClearDB plan
- **File uploads**: Use cloud storage for production
- **Environment variables**: Never commit sensitive data to Git

---

**🎉 Your SkillSwap app should now be live on Heroku!**

Visit: `https://your-app-name.herokuapp.com`