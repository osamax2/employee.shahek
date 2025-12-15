# Deploying to cPanel - Complete Guide

## Overview

This guide shows how to deploy the Employee Tracking Laravel server to a cPanel shared hosting environment.

## Prerequisites

- cPanel hosting account with:
  - PHP 8.1 or higher
  - MySQL database
  - SSH access (recommended)
  - Composer access or ability to run PHP scripts

## Step 1: Prepare Your Server Files

### On Your Local Machine

1. **Navigate to server directory:**
   ```bash
   cd server
   ```

2. **Install dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Create production .env file:**
   ```bash
   cp .env.example .env
   ```

4. **Edit .env for production:**
   ```bash
   nano .env
   ```
   
   Set these values:
   ```env
   APP_NAME="Employee Tracking"
   APP_ENV=production
   APP_KEY=  # We'll generate this on server
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_cpanel_db_name
   DB_USERNAME=your_cpanel_db_user
   DB_PASSWORD=your_cpanel_db_password
   
   JWT_SECRET=  # We'll generate this on server
   JWT_TTL=60
   JWT_REFRESH_TTL=20160
   ```

5. **Create a ZIP file of your server folder:**
   ```bash
   cd ..
   zip -r employee-tracking-server.zip server/ -x "*.git*" -x "*node_modules*" -x "*/storage/logs/*" -x "*/storage/framework/cache/*"
   ```

## Step 2: Upload to cPanel

### Method A: Using cPanel File Manager (Easier)

1. **Login to cPanel**
   - Go to: `https://your-domain.com:2083`
   - Or: `https://your-domain.com/cpanel`

2. **Navigate to File Manager**
   - cPanel Dashboard → Files → File Manager

3. **Go to your domain directory**
   - Usually: `/public_html/your-domain/`
   - Or: `/home/username/public_html/`

4. **Upload the ZIP file**
   - Click "Upload" button
   - Select `employee-tracking-server.zip`
   - Wait for upload to complete

5. **Extract the ZIP file**
   - Right-click on the ZIP file
   - Select "Extract"
   - Choose extraction location
   - Click "Extract Files"

6. **Organize the structure**
   - You should have: `/public_html/server/` with all Laravel files

### Method B: Using FTP/SFTP

1. **Connect via FTP client (FileZilla, Cyberduck, etc.)**
   - Host: `ftp.your-domain.com` or your server IP
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload server folder**
   - Navigate to `/public_html/`
   - Upload entire `server/` folder

### Method C: Using SSH (Fastest)

If you have SSH access:

```bash
# On your local machine
scp -r server/ cpanel_user@your-domain.com:~/public_html/

# Or use rsync
rsync -avz --exclude 'node_modules' --exclude '.git' server/ cpanel_user@your-domain.com:~/public_html/server/
```

## Step 3: Configure PHP Version

1. **In cPanel, go to: Software → Select PHP Version**

2. **Select PHP 8.1 or higher**

3. **Enable required extensions:**
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

4. **Save changes**

## Step 4: Create MySQL Database

1. **In cPanel, go to: Databases → MySQL® Databases**

2. **Create a new database:**
   - Database Name: `employee_tracking`
   - Click "Create Database"
   - Note the full database name (usually `cpanel_user_employee_tracking`)

3. **Create a database user:**
   - Username: `tracking_user`
   - Generate a strong password
   - Click "Create User"
   - **Save the password!**

4. **Add user to database:**
   - Select the database and user
   - Grant "ALL PRIVILEGES"
   - Click "Make Changes"

## Step 5: Setup Laravel Application

### Using Terminal (SSH Access)

If you have SSH access:

```bash
# SSH into your server
ssh cpanel_user@your-domain.com

# Navigate to your application
cd public_html/server

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Set permissions
chmod -R 755 storage bootstrap/cache

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Using cPanel Terminal

Some cPanel accounts have Terminal access:

1. **Go to: Advanced → Terminal**
2. **Run the same commands as above**

### Without SSH (Manual Method)

If no SSH access, use cPanel's PHP editor or File Manager:

1. **Generate App Key:**
   
   Create a file `generate-key.php` in your server root:
   ```php
   <?php
   require __DIR__.'/vendor/autoload.php';
   $app = require_once __DIR__.'/bootstrap/app.php';
   $key = 'base64:'.base64_encode(random_bytes(32));
   echo "APP_KEY=$key\n";
   echo "Copy this to your .env file!";
   ?>
   ```
   
   Visit: `https://your-domain.com/generate-key.php`
   Copy the key to `.env`
   Delete the file!

2. **Generate JWT Secret:**
   
   Create `generate-jwt.php`:
   ```php
   <?php
   require __DIR__.'/vendor/autoload.php';
   $secret = base64_encode(random_bytes(32));
   echo "JWT_SECRET=$secret\n";
   echo "Copy this to your .env file!";
   ?>
   ```
   
   Visit, copy, and delete.

3. **Run Migrations:**
   
   Option A: Use phpMyAdmin (next step)
   Option B: Create `migrate.php`:
   ```php
   <?php
   require __DIR__.'/vendor/autoload.php';
   $app = require_once __DIR__.'/bootstrap/app.php';
   $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
   $status = $kernel->call('migrate', ['--force' => true]);
   echo $status === 0 ? 'Migration successful!' : 'Migration failed!';
   ?>
   ```
   
   Visit: `https://your-domain.com/migrate.php`
   Then delete!

## Step 6: Import Database (Alternative to Migrations)

1. **In cPanel, go to: Databases → phpMyAdmin**

2. **Select your database** from the left sidebar

3. **Go to Import tab**

4. **Upload SQL file:**
   - Use `server/database/setup.sql` from your project
   - Or export from your local database
   - Click "Go"

5. **Verify tables were created:**
   - Should see: `employees`, `employee_locations`, `tracking_sessions`

## Step 7: Configure Domain to Point to Laravel

Laravel's entry point is the `public/` folder, not the root.

### Method A: Using Subdomain (Recommended)

1. **Create a subdomain:**
   - cPanel → Domains → Subdomains
   - Subdomain: `api`
   - Domain: `your-domain.com`
   - Document Root: `/public_html/server/public`
   - Click "Create"

2. **Your API will be at:** `https://api.your-domain.com`

### Method B: Using Main Domain

1. **In File Manager, go to your domain's document root**

2. **Edit `.htaccess` in document root:**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ server/public/$1 [L]
   </IfModule>
   ```

3. **Or move Laravel's public content:**
   ```bash
   # Move everything from server/public/ to public_html/
   # Update index.php paths to point to ../server/
   ```

### Method C: Create Symlink

If you have SSH:
```bash
cd ~/public_html
rm -rf public_html  # BE CAREFUL!
ln -s ~/server/public public_html
```

## Step 8: Setup SSL/HTTPS

### Using Let's Encrypt (Free)

1. **In cPanel, go to: Security → SSL/TLS Status**

2. **Select your domain**

3. **Click "Run AutoSSL"**

4. **Wait for certificate to be issued** (few minutes)

5. **Verify:** Visit `https://your-domain.com`

### Force HTTPS

Add to `server/public/.htaccess` (at the top):

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

## Step 9: Set File Permissions

In File Manager or SSH:

```bash
# Set directory permissions
find /home/username/public_html/server -type d -exec chmod 755 {} \;

# Set file permissions
find /home/username/public_html/server -type f -exec chmod 644 {} \;

# Storage and cache need write permissions
chmod -R 775 /home/username/public_html/server/storage
chmod -R 775 /home/username/public_html/server/bootstrap/cache
```

## Step 10: Configure CORS for Mobile App

Edit `server/config/cors.php`:

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Or specify your mobile app origins
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## Step 11: Test the API

### Test Endpoints

1. **Visit your API URL:**
   - `https://api.your-domain.com/api/auth/login`
   - Or: `https://your-domain.com/api/auth/login`

2. **Should see JSON error** (expected - no credentials):
   ```json
   {
     "success": false,
     "message": "Validation error"
   }
   ```
   
   This means the API is working!

3. **Test login:**
   ```bash
   curl -X POST https://api.your-domain.com/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@company.com","password":"admin123"}'
   ```

4. **Visit Dashboard:**
   - `https://api.your-domain.com/admin/dashboard`
   - Should see the map dashboard

## Step 12: Update Mobile App

Update `mobile/.env`:

```env
API_BASE_URL=https://api.your-domain.com/api
```

Rebuild mobile app:
```bash
cd mobile
npm run build:android
```

## Troubleshooting

### Error: 500 Internal Server Error

**Check Laravel logs:**
- File Manager → `server/storage/logs/laravel.log`

**Common causes:**
1. **Missing .env file** → Create from .env.example
2. **APP_KEY not set** → Run `php artisan key:generate`
3. **Wrong permissions** → Check step 9
4. **PHP version < 8.1** → Update in cPanel

### Error: Database Connection Failed

**Check:**
1. Database name format: Usually `cpanel_user_dbname`
2. Database user has privileges
3. DB_HOST is `localhost` in .env
4. Database exists in phpMyAdmin

### Error: 404 Not Found

**Check:**
1. `.htaccess` exists in `public/` folder
2. `mod_rewrite` is enabled
3. Document root points to `public/` folder

### Error: JWT Secret Not Set

Run in terminal or create script:
```php
php artisan jwt:secret
```

Or manually add to .env:
```
JWT_SECRET=your_generated_secret_here
```

### Error: Permission Denied

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Composer Not Found

**Install Composer in cPanel:**

1. **SSH into server:**
   ```bash
   cd ~
   curl -sS https://getcomposer.org/installer | php
   alias composer='php ~/composer.phar'
   ```

2. **Or use cPanel's PHP version:**
   ```bash
   /usr/local/bin/php ~/composer.phar install
   ```

## Step 13: Setup Cron Jobs (Optional)

For scheduled tasks:

1. **In cPanel, go to: Advanced → Cron Jobs**

2. **Add new cron job:**
   ```
   * * * * * cd /home/username/public_html/server && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **This runs Laravel scheduler every minute**

## Step 14: Optimize for Production

### In Terminal/SSH:

```bash
cd ~/public_html/server

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Clear old logs
> storage/logs/laravel.log
```

### Disable Debug Mode

In `.env`:
```
APP_DEBUG=false
APP_ENV=production
```

## Security Checklist

- [x] HTTPS/SSL enabled
- [x] APP_DEBUG=false
- [x] Strong database password
- [x] JWT_SECRET generated
- [x] File permissions correct (755/644)
- [x] Remove test files (generate-key.php, etc.)
- [x] .env file not publicly accessible
- [x] CORS configured properly

## Backup Strategy

### Automated Backups (cPanel)

1. **Go to: Files → Backup Wizard**
2. **Setup automatic backups**
3. **Download backups regularly**

### Manual Database Backup

1. **phpMyAdmin → Export tab**
2. **Select "Quick" export**
3. **Save SQL file**

## Updating Your Application

When you need to deploy updates:

1. **Backup current version**
2. **Upload new files** (overwrite old)
3. **Run migrations** if database changed:
   ```bash
   php artisan migrate --force
   ```
4. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
5. **Re-cache:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Common cPanel Hosting Providers

These providers support Laravel:

- **Bluehost** - PHP 8.1+, MySQL, SSH
- **HostGator** - PHP 8.1+, cPanel
- **SiteGround** - Optimized for Laravel
- **A2 Hosting** - Fast, SSH access
- **InMotion** - Good Laravel support

**Recommended:** Choose a plan with SSH access for easier deployment.

## Alternative: Use Git Deployment

If SSH available:

1. **Initialize git repo:**
   ```bash
   cd ~/public_html
   git clone https://github.com/your-repo/employee-tracking.git server
   ```

2. **Setup deploy script** (`deploy.sh`):
   ```bash
   #!/bin/bash
   cd ~/public_html/server
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Deploy updates:**
   ```bash
   bash deploy.sh
   ```

## Support

If you encounter issues:

1. **Check Laravel logs:** `storage/logs/laravel.log`
2. **Check cPanel error logs:** cPanel → Metrics → Errors
3. **Contact cPanel hosting support** for server-specific issues
4. **See main documentation:** `../README.md`

---

## Quick Reference

```bash
# SSH Commands
ssh cpanel_user@your-domain.com
cd ~/public_html/server

# Generate keys
php artisan key:generate
php artisan jwt:secret

# Run migrations
php artisan migrate --force

# Cache config
php artisan config:cache
php artisan route:cache

# Set permissions
chmod -R 775 storage/ bootstrap/cache/

# View logs
tail -f storage/logs/laravel.log
```

## API Endpoints (Your Production URLs)

Replace `api.your-domain.com` with your actual domain:

```
POST   https://api.your-domain.com/api/auth/login
POST   https://api.your-domain.com/api/auth/refresh
GET    https://api.your-domain.com/api/employees/me
POST   https://api.your-domain.com/api/location
GET    https://api.your-domain.com/admin/dashboard
```

---

**Last Updated:** December 14, 2025  
**Laravel Version:** 10.x  
**PHP Required:** 8.1+
