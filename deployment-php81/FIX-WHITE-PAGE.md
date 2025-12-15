# 🚨 White Page / Blank Screen Fix

## Problem

You're seeing a **white page** when accessing:
- `https://employee.shahek.org/admin/dashboard`

This means Laravel is trying to load but **Composer dependencies are missing**.

## ✅ Quick Diagnosis

Visit: **`https://employee.shahek.org/check.php`**

This will show you exactly what's missing.

## 🔧 Solution: Install Composer Dependencies

The white page is caused by missing `vendor/` directory. Here's how to fix it:

### Method 1: SSH (Fastest - 2 minutes)

```bash
# SSH into your server
ssh your_cpanel_user@employee.shahek.org

# Navigate to your Laravel directory
cd ~/public_html/server

# Download Composer
curl -sS https://getcomposer.org/installer | php

# Install dependencies
php composer.phar install --no-dev --optimize-autoloader

# Set permissions
chmod -R 775 storage bootstrap/cache

# Done!
```

### Method 2: cPanel Terminal

1. **Login to cPanel**
2. **Go to:** Advanced → Terminal
3. **Run these commands:**

```bash
cd ~/public_html/server
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader
chmod -R 775 storage bootstrap/cache
```

### Method 3: Upload vendor/ Folder (Slowest)

If you don't have SSH or Terminal access:

1. **On your local machine:**
   ```bash
   cd server
   composer install --no-dev
   zip -r vendor.zip vendor/
   ```

2. **Upload `vendor.zip` to cPanel:**
   - File Manager → `/public_html/server/`
   - Upload the ZIP
   - Extract it

3. **Set permissions** via File Manager:
   - Right-click `vendor/` → Permissions → 755

## 🎯 After Installing Composer

Once dependencies are installed:

1. **Test the dashboard:**
   ```
   https://employee.shahek.org/admin/dashboard
   ```

2. **Test the API:**
   ```
   https://employee.shahek.org/api/auth/login
   ```

3. **You should see:**
   - Dashboard: Map interface with login
   - API: JSON response (validation error is expected)

## 📋 Complete Installation Checklist

- [ ] ZIP uploaded to `/public_html/`
- [ ] ZIP extracted (creates `/public_html/server/`)
- [ ] `.htaccess` created in `/public_html/` (redirects to server/public/)
- [ ] **Composer dependencies installed** ← YOU ARE HERE
- [ ] PHP 8.1+ selected
- [ ] Database imported via phpMyAdmin
- [ ] File permissions set
- [ ] SSL enabled

## 🔍 Verify Installation

Run the system check:
```
https://employee.shahek.org/check.php
```

This will show:
- ✅ PHP version
- ✅ PHP extensions
- ✅ Directory structure
- ✅ Composer dependencies
- ✅ .env configuration
- ✅ Database connection
- ✅ File permissions

## 🚨 Common Errors

### Error: "curl: command not found"

Use wget instead:
```bash
wget -O - https://getcomposer.org/installer | php
```

### Error: "composer.phar: command not found"

Make sure you're in the right directory:
```bash
cd ~/public_html/server
ls -la composer.phar  # Should see the file
```

### Error: "memory limit exceeded"

Increase PHP memory:
```bash
php -d memory_limit=512M composer.phar install --no-dev
```

### Error: "Permission denied"

Set correct permissions:
```bash
chmod +x composer.phar
chmod -R 775 storage bootstrap/cache
```

## 📞 Still Having Issues?

### Check Laravel logs:
```
File Manager → server/storage/logs/laravel.log
```

### Check PHP error log:
```
cPanel → Metrics → Errors
```

### Enable debug mode temporarily:
In `.env`:
```
APP_DEBUG=true
```
Then visit the site to see the actual error.
**Remember to set it back to `false` after debugging!**

## ✅ Success Indicators

When everything is working, you should see:

1. **Dashboard loads:** Map with employee markers
2. **API responds:** JSON with validation errors (this is correct!)
3. **No white page:** Content displays properly
4. **No 500 errors:** Laravel is running

---

**Quick Fix (Copy & Paste):**

```bash
cd ~/public_html/server && curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader && chmod -R 775 storage bootstrap/cache
```

Then visit: `https://employee.shahek.org/admin/dashboard` 🎉
