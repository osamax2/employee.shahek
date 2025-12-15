# 🚀 Deployment Configuration Complete

## ✅ Your System is Ready for Deployment

All configuration files have been updated with your production settings.

---

## 📊 Database Configuration

```
Database: shahek_employee
Username: shahek_employee
Password: 5tF75c68jc!RvM#P
Host:     localhost
```

---

## 🌐 Domain Configuration

```
Production URL: https://employee.shahek.org
API Endpoint:   https://employee.shahek.org/api
Dashboard:      https://employee.shahek.org/admin/dashboard
```

---

## 📦 Step 1: Create Deployment Package

Run this from your local machine:

```bash
cd server
./prepare-cpanel.sh
```

This will create: `employee-tracking-server_[timestamp].zip`

---

## 📤 Step 2: Upload to cPanel

### Option A: File Manager (Easiest)

1. **Login to cPanel:**
   - Go to: `https://employee.shahek.org:2083`
   - Or: `https://employee.shahek.org/cpanel`

2. **Navigate to File Manager:**
   - cPanel Dashboard → Files → File Manager

3. **Go to public_html:**
   - Click on `public_html` folder

4. **Upload ZIP file:**
   - Click "Upload" button
   - Select the `employee-tracking-server_[timestamp].zip`
   - Wait for upload (may take 5-10 minutes)

5. **Extract ZIP:**
   - Right-click on ZIP file
   - Select "Extract"
   - Extract to current directory
   - You should now have: `/public_html/server/`

### Option B: Using SSH (Faster)

```bash
# From your local machine
scp employee-tracking-server_*.zip cpanel_user@employee.shahek.org:~/public_html/

# SSH into server
ssh cpanel_user@employee.shahek.org

# Extract
cd ~/public_html
unzip employee-tracking-server_*.zip
```

---

## ⚙️ Step 3: Configure PHP in cPanel

1. **Go to:** Software → Select PHP Version

2. **Select:** PHP 8.1 or higher

3. **Enable Extensions:**
   - ✅ bcmath
   - ✅ ctype
   - ✅ fileinfo
   - ✅ json
   - ✅ mbstring
   - ✅ openssl
   - ✅ pdo
   - ✅ pdo_mysql
   - ✅ tokenizer
   - ✅ xml
   - ✅ zip

4. **Save Changes**

---

## 🗄️ Step 4: Verify Database

1. **Go to:** Databases → MySQL® Databases

2. **Verify database exists:**
   - Database: `shahek_employee` ✅

3. **Verify user exists:**
   - User: `shahek_employee` ✅

4. **Verify privileges:**
   - User should have ALL PRIVILEGES on database ✅

---

## 🔑 Step 5: Generate Application Keys

### Method A: With SSH Access

```bash
ssh cpanel_user@employee.shahek.org
cd ~/public_html/server
php artisan key:generate
php artisan jwt:secret
```

### Method B: Without SSH (Browser)

1. **Upload helper file:** Already included in your deployment package!
2. **Visit:** `https://employee.shahek.org/generate-keys.php`
3. **Copy both keys** (APP_KEY and JWT_SECRET)
4. **Edit .env file** via cPanel File Manager:
   - Navigate to `/public_html/server/.env`
   - Paste the keys
   - Save file
5. **Delete helper file:** Remove `generate-keys.php` immediately!

---

## 📊 Step 6: Run Database Migrations

### Method A: With SSH

```bash
cd ~/public_html/server
php artisan migrate --force
php artisan db:seed --force  # Creates admin user and test data
```

### Method B: Without SSH (Browser)

1. **Visit:** `https://employee.shahek.org/run-migrations.php`
2. **Click:** "Run Migrations" button
3. **Click:** "Seed Database" button (creates admin + test employees)
4. **Delete file:** Remove `run-migrations.php` immediately!

### Method C: Using phpMyAdmin (Manual)

1. **Go to:** Databases → phpMyAdmin
2. **Select:** `shahek_employee`
3. **Import:** `/server/database/setup.sql`

---

## 🌍 Step 7: Configure Domain

Since you're using the main domain, Laravel's public folder needs to be the root.

### Recommended Setup:

1. **In cPanel File Manager:**
   - Navigate to `/public_html/`
   - You should have the `server` folder

2. **Create/Edit .htaccess in /public_html/:**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ server/public/$1 [L]
</IfModule>
```

This redirects all requests to `/public_html/server/public/`

**Your API will be accessible at:**
- `https://employee.shahek.org/api/auth/login`
- `https://employee.shahek.org/admin/dashboard`

---

## 🔒 Step 8: Enable SSL/HTTPS

1. **Go to:** Security → SSL/TLS Status

2. **Select:** employee.shahek.org

3. **Click:** "Run AutoSSL"

4. **Wait:** Certificate will be issued in 2-5 minutes

5. **Test:** Visit `https://employee.shahek.org`

---

## 🔐 Step 9: Set File Permissions

### Via SSH:

```bash
cd ~/public_html/server
chmod -R 755 storage
chmod -R 755 bootstrap/cache
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

### Via cPanel File Manager:

1. Right-click on `storage` folder → Permissions → Set to 755
2. Right-click on `bootstrap/cache` → Permissions → Set to 755

---

## ✅ Step 10: Test Your Deployment

### Test 1: API Endpoint

Visit: `https://employee.shahek.org/api/auth/login`

**Expected:** JSON error response (this is good!)
```json
{
  "success": false,
  "message": "Validation error"
}
```

### Test 2: Dashboard

Visit: `https://employee.shahek.org/admin/dashboard`

**Expected:** Map dashboard with login prompt

### Test 3: Login with Test Credentials

After seeding database:
```
Email: admin@company.com
Password: admin123
```

### Test 4: API Login via curl

```bash
curl -X POST https://employee.shahek.org/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@company.com","password":"admin123"}'
```

**Expected:** JWT token response

---

## 📱 Step 11: Update & Build Mobile App

Your mobile app is already configured with the correct API URL!

```bash
cd mobile
npm run build:android
```

Or for iOS:
```bash
npm run build:ios
```

The APK will be built with API pointing to: `https://employee.shahek.org/api`

---

## 🎯 Default Admin Credentials

After running the seeder (`php artisan db:seed`):

**Admin Account:**
- Email: `admin@company.com`
- Password: `admin123`

**Test Employees:**
- `employee1@company.com` / `password123`
- `employee2@company.com` / `password123`
- `employee3@company.com` / `password123`

⚠️ **IMPORTANT:** Change these passwords in production!

---

## 🔧 Troubleshooting

### Error: 500 Internal Server Error

**Check logs:**
```bash
# Via SSH
tail -f ~/public_html/server/storage/logs/laravel.log

# Via cPanel
File Manager → server/storage/logs/laravel.log
```

**Common fixes:**
1. Generate APP_KEY: `php artisan key:generate`
2. Check .env file exists and has correct values
3. Verify file permissions (755 for folders, 644 for files)

### Error: Database Connection Failed

**Verify:**
1. Database `shahek_employee` exists in phpMyAdmin
2. User `shahek_employee` has privileges
3. Password in .env matches: `5tF75c68jc!RvM#P`
4. DB_HOST is set to `localhost`

### Error: 404 Not Found

**Check:**
1. .htaccess file exists in `/public_html/`
2. .htaccess file exists in `/public_html/server/public/`
3. mod_rewrite is enabled (usually is on cPanel)

---

## 📋 Quick Checklist

Before going live:

- [ ] Server files uploaded to `/public_html/server/`
- [ ] PHP 8.1+ selected with all extensions enabled
- [ ] Database `shahek_employee` exists and configured
- [ ] APP_KEY generated
- [ ] JWT_SECRET generated
- [ ] Database migrations run
- [ ] Test data seeded (optional)
- [ ] SSL certificate installed
- [ ] File permissions set (755/644)
- [ ] .htaccess configured for domain routing
- [ ] API endpoint tested
- [ ] Dashboard accessible
- [ ] Test login successful
- [ ] Helper files deleted (generate-keys.php, run-migrations.php)
- [ ] APP_DEBUG=false in .env
- [ ] Default passwords changed

---

## 🎉 Success!

Once all checks pass, your system is live at:

**🌐 API:** https://employee.shahek.org/api  
**📊 Dashboard:** https://employee.shahek.org/admin/dashboard  
**📱 Mobile App:** Ready to build with correct API endpoint

---

## 📚 Documentation

- **Full cPanel Guide:** [CPANEL_DEPLOY.md](server/CPANEL_DEPLOY.md)
- **Database Config:** [DATABASE_CONFIG.md](server/DATABASE_CONFIG.md)
- **Main README:** [README.md](README.md)
- **Quick Start:** [QUICKSTART.md](QUICKSTART.md)
- **Test Plan:** [TEST_PLAN.md](TEST_PLAN.md)

---

## 🆘 Support

If you encounter issues:

1. Check Laravel logs: `server/storage/logs/laravel.log`
2. Check cPanel error logs: Metrics → Errors
3. Review this guide step-by-step
4. Contact your hosting provider for server-specific issues

---

**Deployment Date:** December 15, 2025  
**Domain:** https://employee.shahek.org  
**Status:** ✅ Ready for Deployment
