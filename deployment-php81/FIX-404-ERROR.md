# 🚨 404 Error - Laravel Not Loading

## Problem

You're getting a 404 error when accessing:
- `https://employee.shahek.org/admin/dashboard`

This means your domain is **not pointing to the Laravel public folder**.

## ✅ Solution: Configure Domain Routing

### Option 1: Upload .htaccess to public_html/ (Recommended)

After extracting your ZIP file, you need to redirect all requests to `server/public/`:

1. **In cPanel File Manager:**
   - Navigate to `/public_html/`
   - Create a new file: `.htaccess`
   - Add this content:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to the server/public directory
    RewriteRule ^(.*)$ server/public/$1 [L]
</IfModule>
```

2. **Save the file**

3. **Test:** Visit `https://employee.shahek.org/admin/dashboard`

### Option 2: Use a Subdomain (Alternative)

If you prefer, create a subdomain:

1. **In cPanel → Domains → Subdomains**
2. **Create subdomain:**
   - Subdomain: `api`
   - Domain: `shahek.org`
   - Document Root: `/public_html/server/public`
3. **Your URLs will be:**
   - API: `https://api.shahek.org/api/auth/login`
   - Dashboard: `https://api.shahek.org/admin/dashboard`

### Option 3: Move Files (Advanced)

If you have SSH access:

```bash
cd ~/public_html
# Backup current setup
mv server server_backup

# Move Laravel public folder to root
mv server_backup/public/* .
mv server_backup/public/.htaccess .

# Update index.php to point to correct paths
sed -i 's|/../|/../server_backup/|g' index.php
```

## 🔍 Quick Check

After configuration, test these URLs:

1. **Root URL:**
   ```
   https://employee.shahek.org/
   ```
   Should show: Laravel app or Coming Soon page

2. **API Endpoint:**
   ```
   https://employee.shahek.org/api/auth/login
   ```
   Should show: JSON error (this is correct!)

3. **Dashboard:**
   ```
   https://employee.shahek.org/admin/dashboard
   ```
   Should show: Map dashboard with login

## 📋 Deployment Checklist

Make sure you've completed these steps:

- [ ] ZIP file uploaded to `/public_html/`
- [ ] ZIP file extracted (creates `/public_html/server/`)
- [ ] **.htaccess created in `/public_html/`** ← YOU ARE HERE
- [ ] PHP 8.1+ selected in cPanel
- [ ] Database imported via phpMyAdmin
- [ ] File permissions set (755/644)

## 🎯 Expected Directory Structure

After proper setup:

```
/public_html/
├── .htaccess                 ← Redirects to server/public/
└── server/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/               ← Laravel entry point
    │   ├── .htaccess
    │   ├── index.php
    │   └── index.html
    ├── resources/
    ├── routes/
    ├── storage/
    └── .env
```

## 🔧 Still Not Working?

### Check 1: Verify mod_rewrite

In cPanel → Software → Select PHP Version → Extensions:
- ✅ Make sure `mod_rewrite` is enabled

### Check 2: Check File Permissions

```bash
chmod 644 /public_html/.htaccess
chmod 644 /public_html/server/public/.htaccess
chmod 644 /public_html/server/public/index.php
```

### Check 3: Check Error Logs

In cPanel → Metrics → Errors:
- Look for recent errors
- Check for "File not found" or "Permission denied"

### Check 4: Test Direct Access

Try accessing directly:
```
https://employee.shahek.org/server/public/index.php
```

If this works, the issue is the .htaccess redirect.

## 📞 Need Help?

The `.htaccess-root` file is included in your deployment package:
- File location: `server/.htaccess-root`
- Copy its contents to `/public_html/.htaccess`

---

**Quick Fix Command (if you have SSH):**

```bash
cd ~/public_html
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ server/public/$1 [L]
</IfModule>
EOF
```

Then test: `https://employee.shahek.org/admin/dashboard`
