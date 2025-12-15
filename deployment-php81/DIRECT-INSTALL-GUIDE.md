# 🚀 Direct Installation Guide (Files in public_html Root)

## Installation Method: All Files Directly in public_html/

Instead of `/public_html/server/`, you're installing everything directly in `/public_html/`.

---

## 📦 Step 1: Upload Files

### Upload to cPanel:

1. **Login to cPanel → File Manager**
2. **Navigate to `/public_html/`**
3. **Upload both ZIPs:**
   - `employee-tracking-server_20251215_005447.zip`
   - `vendor.zip` (6 MB)

---

## 📂 Step 2: Extract Files

1. **Extract the main ZIP:**
   - Right-click `employee-tracking-server_20251215_005447.zip`
   - Click **Extract**
   - This creates `/public_html/server/` folder

2. **Move all files from server/ to public_html/:**
   - Select ALL files inside `/public_html/server/`
   - Click **Move** button
   - Destination: `/public_html/`
   - Click **Move Files**

3. **Delete the empty server/ folder:**
   - Right-click `/public_html/server/` (now empty)
   - Click **Delete**

4. **Extract vendor.zip:**
   - Right-click `vendor.zip` in `/public_html/`
   - Click **Extract**
   - This creates `/public_html/vendor/`

5. **Delete the ZIPs:**
   - Delete `employee-tracking-server_20251215_005447.zip`
   - Delete `vendor.zip`

---

## 🔧 Step 3: Configure Domain Routing

### Create .htaccess in /public_html/:

1. **In File Manager**, click **+ File**
2. **Name:** `.htaccess` (with the dot!)
3. **Right-click → Edit**
4. **Paste this content:**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to public/ subfolder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

5. **Save and Close**

---

## 📋 Step 4: Verify Directory Structure

Your `/public_html/` should now look like this:

```
/public_html/
├── .htaccess              ← NEW (redirects to public/)
├── .env                   ← Configuration
├── app/                   ← Laravel application code
├── bootstrap/             ← Laravel bootstrap
├── config/                ← Configuration files
├── database/              ← Migrations & SQL files
├── public/                ← Web root (contains index.php)
│   ├── .htaccess         ← Laravel routing
│   └── index.php         ← Laravel entry point
├── resources/             ← Views
├── routes/                ← Route definitions
├── storage/               ← Storage
├── vendor/                ← Composer dependencies (6 MB)
├── artisan                ← Laravel CLI
├── composer.json
├── check.php              ← System check tool
└── other files...
```

**Important:** The `server/` folder should be DELETED after moving all files!

---

## 🗄️ Step 5: Setup Database

### Via phpMyAdmin:

1. **Login to cPanel → phpMyAdmin**
2. **Select database:** `shahek_employee`
3. **Click Import tab**
4. **Choose file:** Browse to `/public_html/database/import-to-phpmyadmin.sql`
5. **Click Go**
6. **Wait for success message**

### Verify Tables Created:

You should see:
- ✅ `employees` (with admin@company.com)
- ✅ `employee_locations`
- ✅ `tracking_sessions`

---

## 🔍 Step 6: Verify Installation

### Run System Check:

Visit: **`https://employee.shahek.org/check.php`**

You should see all green checkmarks:
- ✅ PHP 8.1+
- ✅ PHP Extensions
- ✅ Directory structure
- ✅ **vendor/ exists**
- ✅ Composer autoloader
- ✅ .env configured
- ✅ Database connected
- ✅ File permissions

### If something is RED:

Check the [Troubleshooting](#troubleshooting) section below.

---

## 🎯 Step 7: Test Your Application

### 1. Test Dashboard:

Visit: **`https://employee.shahek.org/admin/dashboard`**

**Expected:** Map interface with login form

**Login credentials:**
- Email: `admin@company.com`
- Password: `admin123`

### 2. Test API:

Visit: **`https://employee.shahek.org/api/auth/login`**

**Expected:** JSON response like:
```json
{
    "message": "The email field is required. (and 1 more error)",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

This is CORRECT! It means the API is working.

---

## 🔒 Step 8: File Permissions

### Set correct permissions:

1. **In File Manager:**
   - Right-click `storage/` folder
   - Click **Permissions**
   - Set to: **775**
   - Check: ✅ **Apply to subdirectories**

2. **Do the same for:**
   - `bootstrap/cache/` → **775**

---

## 🧹 Step 9: Clean Up

### Delete temporary files:

```
✅ Delete: check.php
✅ Delete: install-composer-browser.php (if exists)
✅ Delete: generate-keys.php (already used)
✅ Delete: run-migrations.php (already used)
✅ Delete: CPANEL_DEPLOY.md (optional)
✅ Delete: NO-SSH-INSTALL.md (optional)
✅ Delete: FIX-404-ERROR.md (optional)
✅ Delete: FIX-WHITE-PAGE.md (optional)
```

Keep these files:
- ✅ `.htaccess` (in public_html/ root)
- ✅ `.env`
- ✅ All app/, config/, routes/, etc. folders

---

## 🎉 Success!

Your application is now running at:
- 🌐 **Dashboard:** https://employee.shahek.org/admin/dashboard
- 🔌 **API:** https://employee.shahek.org/api/*

---

## 🆘 Troubleshooting

### Issue: "White Page" or "500 Error"

**Solution:**
1. Visit `https://employee.shahek.org/check.php`
2. Look for RED items
3. Most common: vendor/ missing or .env not configured

### Issue: "404 Not Found"

**Cause:** .htaccess not created or mod_rewrite disabled

**Solution:**
1. Check if `/public_html/.htaccess` exists
2. Check if it has correct content (see Step 3)
3. Contact hosting to enable mod_rewrite

### Issue: "Class not found" errors

**Cause:** vendor/ not uploaded or corrupted

**Solution:**
1. Re-upload `vendor.zip`
2. Extract in `/public_html/`
3. Verify `/public_html/vendor/autoload.php` exists

### Issue: Database connection failed

**Cause:** Wrong credentials in .env

**Solution:**
1. Edit `/public_html/.env`
2. Verify:
   ```
   DB_HOST=localhost
   DB_DATABASE=shahek_employee
   DB_USERNAME=shahek_employee
   DB_PASSWORD=5tF75c68jc!RvM#P
   ```

### Issue: "Permission denied" errors

**Solution:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Or via File Manager:
1. Right-click folder
2. Permissions → 775
3. Apply to subdirectories

---

## 🔧 Configuration

### Environment File (.env):

Located at: `/public_html/.env`

**Critical settings:**
```ini
APP_NAME="Employee Tracking"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://employee.shahek.org
APP_KEY=base64:mcHJOt441KvfjQLeAe/GD4q37/Nr2pe0TMFdbtuluW0=

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=shahek_employee
DB_USERNAME=shahek_employee
DB_PASSWORD=5tF75c68jc!RvM#P

JWT_SECRET=LBJ2gcqYEqJa6AMrTeXQKOzyQz7yMGU9ir5n5sDlwk8=
JWT_TTL=60
```

**Never change:**
- `APP_KEY` (regenerating breaks encrypted data)
- `JWT_SECRET` (regenerating invalidates all tokens)

---

## 📊 Directory Structure Explanation

```
/public_html/                    ← Your domain root
│
├── .htaccess                   ← Redirects everything to public/
│   Example: https://employee.shahek.org/admin
│   → Redirects to: /public_html/public/admin
│   → Laravel routes handle it
│
├── public/                     ← Laravel's web root
│   ├── .htaccess              ← Laravel routing rules
│   ├── index.php              ← Laravel entry point
│   └── index.html             ← Static fallback
│
├── app/                        ← Your application code
│   ├── Http/Controllers/      ← API & Dashboard controllers
│   └── Models/                ← Database models
│
├── routes/                     ← Route definitions
│   ├── web.php                ← Dashboard routes
│   └── api.php                ← API routes
│
├── config/                     ← Configuration files
│   ├── auth.php               ← Authentication
│   ├── jwt.php                ← JWT settings
│   └── cors.php               ← CORS settings
│
├── database/                   ← Database files
│   ├── migrations/            ← Table schemas
│   └── import-to-phpmyadmin.sql ← Complete DB setup
│
├── vendor/                     ← Composer dependencies (DO NOT EDIT!)
│   └── autoload.php           ← Composer autoloader
│
└── storage/                    ← Temporary files, logs, cache
    ├── app/
    ├── framework/
    └── logs/                  ← Error logs here!
```

---

## 🚀 Next Steps

1. **Test the dashboard thoroughly**
2. **Test API endpoints**
3. **Build and deploy mobile app**
4. **Configure SSL (should already be enabled)**
5. **Set up regular database backups**

---

## 🔗 Important URLs

- **Dashboard:** https://employee.shahek.org/admin/dashboard
- **API Login:** https://employee.shahek.org/api/auth/login
- **API Docs:** Coming soon...
- **System Check:** https://employee.shahek.org/check.php (delete after verification)

---

## 📞 Need Help?

If you encounter issues:
1. Check `/public_html/storage/logs/laravel.log`
2. Visit `https://employee.shahek.org/check.php`
3. Verify .env configuration
4. Check file permissions (775 for storage/ and bootstrap/cache/)

---

**Congratulations!** Your Employee Tracking System is now live! 🎉
