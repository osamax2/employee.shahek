# 🚀 Install Without SSH - Complete Guide

You don't have SSH? No problem! Here are 3 methods to get your app running.

## Method 1: Browser-Based Installer (Easiest - 5 minutes)

This method installs Composer through your web browser!

### Steps:

1. **Upload the installer:**
   - Go to cPanel → File Manager
   - Navigate to `/public_html/server/`
   - Upload: `install-composer-browser.php`

2. **Run the installer:**
   - Visit: `https://employee.shahek.org/install-composer-browser.php`
   - Click **"Install Composer & Dependencies"**
   - Wait 5-10 minutes (don't close the page!)
   - You'll see real-time progress

3. **Complete!**
   - When it says "Installation complete!"
   - **DELETE** `install-composer-browser.php`
   - Visit: `https://employee.shahek.org/admin/dashboard`
   - Login: `admin@company.com` / `admin123`

### ✅ This method works if:
- PHP exec() function is enabled
- You have at least 256MB PHP memory
- You have 100MB+ disk space available

---

## Method 2: Upload vendor/ Folder (Most Reliable - 10 minutes)

Create the vendor/ folder on your computer and upload it.

### On Your Computer:

```bash
# Navigate to the server directory
cd /Users/osamaalabaji/shahek/server

# Run the automated script
chmod +x prepare-vendor.sh
./prepare-vendor.sh
```

This will:
- ✅ Install Composer dependencies locally
- ✅ Create `vendor.zip` (about 50-80MB)
- ✅ Tell you exactly what to do next

### Upload to cPanel:

1. **Go to cPanel File Manager**
2. **Navigate to:** `/public_html/server/`
3. **Upload:** `vendor.zip` (will take a few minutes)
4. **Right-click** the ZIP → **Extract**
5. **Delete** `vendor.zip` after extraction
6. **Visit:** `https://employee.shahek.org/admin/dashboard`

### ✅ Success Check:
```
File Manager → /public_html/server/vendor/autoload.php should exist!
```

---

## Method 3: cPanel Terminal (If Available - 3 minutes)

Some cPanel accounts have Terminal access even without SSH.

### Check for Terminal:

1. **Login to cPanel**
2. **Look for:** Advanced → Terminal
3. **If you see it**, click to open

### Run These Commands:

```bash
# Navigate to your app
cd ~/public_html/server

# Download Composer
curl -sS https://getcomposer.org/installer | php

# Install dependencies
php composer.phar install --no-dev --optimize-autoloader

# Set permissions
chmod -R 775 storage bootstrap/cache

# Done!
```

Then visit: `https://employee.shahek.org/admin/dashboard`

---

## 🆘 Troubleshooting

### "Browser installer shows error"

The exec() function might be disabled. Use **Method 2** instead.

### "Upload fails - file too large"

cPanel upload limit is usually 100MB. Try:
1. Split the vendor.zip into parts
2. Or use FTP client (FileZilla) instead
3. Or ask your hosting to increase upload limit temporarily

### "Extract fails - timeout"

Large ZIPs can timeout. Try:
1. cPanel → File Manager → Settings → Increase timeout
2. Or extract via FTP client
3. Or use Method 3 (Terminal)

### "Still shows white page after upload"

Run the check:
```
https://employee.shahek.org/check.php
```

It will show exactly what's missing.

---

## 📋 Quick Comparison

| Method | Time | Difficulty | Requirements |
|--------|------|------------|--------------|
| **Browser Installer** | 5 min | Easy | PHP exec() enabled |
| **Upload vendor/** | 10 min | Medium | Composer on your computer |
| **cPanel Terminal** | 3 min | Easy | Terminal access |

---

## ✅ How to Verify Success

After installation, visit: `https://employee.shahek.org/check.php`

You should see all green checkmarks:
- ✅ PHP 8.1+
- ✅ Required extensions
- ✅ Directory structure
- ✅ **vendor/ directory exists** ← Important!
- ✅ Composer autoloader
- ✅ .env configured
- ✅ Database connected
- ✅ File permissions

---

## 🎯 Recommended Method

I suggest using **Method 1 (Browser Installer)** first:
- Fastest and easiest
- No need to install anything on your computer
- Real-time progress feedback
- Works in 90% of cPanel setups

If it doesn't work, fall back to **Method 2 (Upload vendor/)**.

---

## 📞 Still Need Help?

### Show me these outputs:

1. **From check.php:**
   Visit `https://employee.shahek.org/check.php` and copy the output

2. **From PHP info:**
   Create a file: `info.php` with:
   ```php
   <?php phpinfo(); ?>
   ```
   Upload to `/public_html/`, visit `https://employee.shahek.org/info.php`
   Copy the first section (PHP Version, Configuration, etc.)
   **Then DELETE the file!**

3. **From File Manager:**
   Screenshot of `/public_html/server/` directory showing folders

---

## 🚀 After Installation

Once vendor/ is installed:

1. **Test the dashboard:**
   ```
   https://employee.shahek.org/admin/dashboard
   ```
   Should show the map with login form

2. **Test the API:**
   ```
   https://employee.shahek.org/api/auth/login
   ```
   Should return JSON (validation error is normal)

3. **Login credentials:**
   - Email: `admin@company.com`
   - Password: `admin123`

4. **Clean up:**
   - Delete `check.php`
   - Delete `install-composer-browser.php`
   - Delete `info.php` (if you created it)

---

**Quick Start (Copy & Paste):**

Just run this on your computer:
```bash
cd /Users/osamaalabaji/shahek/server && ./prepare-vendor.sh
```

Then upload the generated `vendor.zip` to cPanel! 🎉
