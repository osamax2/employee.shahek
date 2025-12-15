# 🚨 Fixing 500 Internal Server Error

## Problem

You're seeing:
```
GET https://employee.shahek.org/admin/dashboard 
net::ERR_HTTP_RESPONSE_CODE_FAILURE 500 (Internal Server Error)
```

This means Laravel is trying to load but encountering an error.

## 🔍 Quick Diagnosis

**Upload and visit:** `https://employee.shahek.org/debug-500.php`

This will show you:
- ✅ Which files exist
- ✅ What's in .htaccess
- ✅ .env configuration
- ✅ vendor/ status
- ✅ File permissions
- ✅ **Actual error from logs**

## 🔧 Common Causes & Solutions

### 1. Missing or Incomplete vendor/ Directory

**Symptoms:** "Class not found" errors

**Solution:**
```bash
# Make sure vendor.zip was fully uploaded and extracted
# Check that /public_html/vendor/autoload.php exists
```

**Verify:**
- File Manager → `/public_html/vendor/autoload.php` should exist
- vendor/ folder should be about 60-80MB

**Fix:**
1. Re-upload `vendor.zip`
2. Extract in `/public_html/`
3. Verify `vendor/autoload.php` exists

---

### 2. Wrong File Permissions

**Symptoms:** "Permission denied" errors in logs

**Solution via cPanel File Manager:**

1. Right-click `storage/` → **Permissions**
   - Enter: **775**
   - ✅ Check "Apply to subdirectories"
   - Click **Save**

2. Right-click `bootstrap/cache/` → **Permissions**
   - Enter: **775**
   - ✅ Check "Apply to subdirectories"
   - Click **Save**

**Solution via SSH (if available):**
```bash
cd ~/public_html
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### 3. Missing or Invalid .env File

**Symptoms:** Laravel can't connect to database or missing APP_KEY

**Check .env exists:**
```
File Manager → /public_html/.env
```

**Required contents:**
```ini
APP_NAME="Employee Tracking"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:mcHJOt441KvfjQLeAe/GD4q37/Nr2pe0TMFdbtuluW0=
APP_URL=https://employee.shahek.org

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=shahek_employee
DB_USERNAME=shahek_employee
DB_PASSWORD=5tF75c68jc!RvM#P

JWT_SECRET=LBJ2gcqYEqJa6AMrTeXQKOzyQz7yMGU9ir5n5sDlwk8=
JWT_TTL=60
```

**Fix:**
1. Edit `/public_html/.env`
2. Make sure all values are correct
3. **Important:** No spaces around `=` signs!

---

### 4. Missing .htaccess in Root

**Symptoms:** 404 or 500 on dashboard, but `/public/index.php` works directly

**Check if exists:**
```
File Manager → /public_html/.htaccess
```

**Create if missing:**

1. Click **+ File** in File Manager
2. Name: `.htaccess` (with the dot!)
3. Edit and paste:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to public/ subfolder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

4. Save

---

### 5. PHP Version Too Old

**Symptoms:** Syntax errors, "class not found"

**Required:** PHP 8.1 or higher

**Fix in cPanel:**
1. Go to **Select PHP Version**
2. Choose: **PHP 8.1** or higher
3. Click **Apply**

---

### 6. Missing PHP Extensions

**Symptoms:** "Class 'PDO' not found" or similar

**Fix in cPanel:**
1. Go to **Select PHP Version**
2. Click **Extensions** tab
3. Enable these:
   - ✅ pdo
   - ✅ pdo_mysql
   - ✅ mbstring
   - ✅ openssl
   - ✅ tokenizer
   - ✅ json
   - ✅ bcmath
   - ✅ ctype
   - ✅ fileinfo
   - ✅ xml

4. Click **Save**

---

### 7. Database Not Imported

**Symptoms:** "Table not found" errors

**Fix:**
1. cPanel → **phpMyAdmin**
2. Select database: `shahek_employee`
3. Click **Import**
4. Choose: `/public_html/database/import-to-phpmyadmin.sql`
5. Click **Go**

**Verify tables exist:**
- employees
- employee_locations
- tracking_sessions

---

## 🔍 Check Laravel Error Logs

**Location:** `/public_html/storage/logs/laravel.log`

**View in File Manager:**
1. Navigate to `storage/logs/`
2. Right-click `laravel.log` → **Edit**
3. Scroll to bottom for recent errors

**Common errors and solutions:**

### Error: "No application encryption key has been specified"
**Fix:** Add APP_KEY to .env (see above)

### Error: "SQLSTATE[HY000] [1045] Access denied"
**Fix:** Wrong database credentials in .env

### Error: "Class 'Illuminate\Foundation\Application' not found"
**Fix:** vendor/ not installed or corrupted

### Error: "file_put_contents(...): failed to open stream: Permission denied"
**Fix:** Wrong permissions on storage/ (see #2 above)

---

## 📋 Step-by-Step Checklist

Run through this checklist:

- [ ] **vendor.zip uploaded and extracted?**
  - Check: `/public_html/vendor/autoload.php` exists
  
- [ ] **.env file exists and configured?**
  - Check: `/public_html/.env` has all values
  
- [ ] **.htaccess in root created?**
  - Check: `/public_html/.htaccess` redirects to public/
  
- [ ] **Permissions set correctly?**
  - storage/ = 775
  - bootstrap/cache/ = 775
  
- [ ] **Database imported?**
  - Check phpMyAdmin for tables
  
- [ ] **PHP 8.1+ selected?**
  - Check Select PHP Version
  
- [ ] **PHP extensions enabled?**
  - Check Select PHP Version → Extensions

---

## 🎯 Test Each Component

### 1. Test if PHP works:
```
https://employee.shahek.org/check.php
```
Should show system check (all green = good)

### 2. Test direct Laravel access:
```
https://employee.shahek.org/public/index.php
```
- **If this works:** .htaccess routing problem
- **If this fails:** vendor/ or .env problem

### 3. Test API:
```
https://employee.shahek.org/api/auth/login
```
Should return JSON validation error (this is correct!)

### 4. Test Dashboard:
```
https://employee.shahek.org/admin/dashboard
```
Should show map with login form

---

## 🆘 Still Getting 500?

### Enable Debug Mode (TEMPORARILY!)

Edit `.env`:
```ini
APP_DEBUG=true
```

Visit dashboard again - you'll see detailed error message.

**IMPORTANT:** Set back to `false` after debugging!

### Get More Info

1. **Upload debug-500.php**
2. **Visit:** `https://employee.shahek.org/debug-500.php`
3. **Read all sections** - look for RED items
4. **Check Section 8** - Recent Laravel Logs
5. **DELETE debug-500.php** after done!

---

## 📊 Directory Structure (Final)

Make sure your structure looks like this:

```
/public_html/
├── .htaccess              ← MUST EXIST (redirects to public/)
├── .env                   ← MUST EXIST (configuration)
├── app/
├── bootstrap/
│   └── cache/            ← MUST BE WRITABLE (775)
├── config/
├── database/
├── public/
│   ├── .htaccess
│   └── index.php
├── resources/
├── routes/
├── storage/              ← MUST BE WRITABLE (775)
│   └── logs/
│       └── laravel.log   ← Check for errors!
├── vendor/               ← MUST EXIST (from vendor.zip)
│   └── autoload.php      ← MUST EXIST
├── artisan
└── composer.json
```

---

## 🔧 Quick Fix Commands (if you have SSH)

```bash
cd ~/public_html

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Verify vendor exists
ls -la vendor/autoload.php

# Check .env exists
cat .env

# View recent errors
tail -50 storage/logs/laravel.log
```

---

## 💡 Most Common Solution

In 90% of cases, the 500 error is caused by:

1. **Missing vendor/** - Upload vendor.zip
2. **Wrong permissions** - Set storage/ and bootstrap/cache/ to 775
3. **Missing .htaccess in root** - Create it with redirect to public/

---

**After fixing, test:**
1. `https://employee.shahek.org/check.php` - Should be all green
2. `https://employee.shahek.org/admin/dashboard` - Should show dashboard
3. Delete `check.php` and `debug-500.php`

🎉 **Good luck!**
