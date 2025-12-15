# Database Configuration

## ✅ Configured cPanel Database Credentials

Your database is already configured with the following details:

```
Database Name: shahek_employee
Username:      shahek_employee  
Password:      5tF75c68jc!RvM#P
Host:          localhost
Port:          3306
```

## 📋 Files Updated

The following files have been configured with your database credentials:

1. ✅ `server/.env` - Production environment file
2. ✅ `server/.env.example` - Example template
3. ✅ `server/.env.cpanel.example` - cPanel-specific template

## 🚀 Next Steps for cPanel Deployment

### 1. Generate Application Keys

After uploading to cPanel, you need to generate Laravel keys:

**Method A - With SSH Access:**
```bash
ssh your_cpanel_user@your-domain.com
cd public_html/server
php artisan key:generate
php artisan jwt:secret
```

**Method B - Without SSH (Browser Method):**
1. Upload `generate-keys.php` to your server root
2. Visit: `https://employee.shahek.org/generate-keys.php`
3. Copy the generated keys to your `.env` file
4. **Delete the file immediately!**

### 2. Run Database Migrations

**Method A - With SSH:**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**Method B - Without SSH:**
1. Upload `run-migrations.php` to server root
2. Visit: `https://employee.shahek.org/run-migrations.php`
3. Click "Run Migrations"
4. Click "Seed Database" (optional - creates test data)
5. **Delete the file immediately!**

### 3. Verify Database Connection

After uploading to cPanel, test the connection:

1. Login to cPanel
2. Go to **phpMyAdmin**
3. Select database: `shahek_employee`
4. Should see 3 tables after migration:
   - `employees`
   - `employee_locations`
   - `tracking_sessions`

## 🔒 Security Notes

⚠️ **IMPORTANT:**
- The `.env` file contains sensitive credentials
- Never commit `.env` to Git (it's already in .gitignore)
- Change default admin password after first login
- Use HTTPS/SSL in production
- Delete helper PHP files after use (generate-keys.php, run-migrations.php)

## 📱 Update Mobile App

After your API is live, update the mobile app:

```bash
cd mobile
nano .env
```

Change:
```env
API_BASE_URL=https://employee.shahek.org/api
```

Then rebuild:
```bash
npm run build:android
```

## 🧪 Test API Connection

Once deployed, test your endpoints:

```bash
# Test database connection (should return JSON)
curl https://employee.shahek.org/api/auth/login

# Test with credentials (after seeding)
curl -X POST https://employee.shahek.org/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@company.com","password":"admin123"}'
```

## 📊 Default Test Credentials

After running `php artisan db:seed`, you'll have:

**Admin Account:**
- Email: `admin@company.com`
- Password: `admin123`

**Test Employees:**
- `employee1@company.com` / `password123`
- `employee2@company.com` / `password123`
- `employee3@company.com` / `password123`

⚠️ **Change these passwords in production!**

## 🛠️ Troubleshooting

### Can't connect to database
- Verify database exists in cPanel → MySQL Databases
- Check user has ALL PRIVILEGES on the database
- Confirm credentials match exactly (case-sensitive)

### Migrations fail
- Run via phpMyAdmin: Import `database/setup.sql`
- Or manually create tables using SQL

### Keys not generating
- Use the browser method with `generate-keys.php`
- Or manually generate: `base64:` + random 32-byte string

## 📖 Full Documentation

For complete deployment instructions, see:
- [CPANEL_DEPLOY.md](CPANEL_DEPLOY.md) - Complete deployment guide
- [README.md](README.md) - Main project documentation
- [QUICKSTART.md](../QUICKSTART.md) - Quick setup guide

---

**Database Status:** ✅ Configured  
**Ready for Deployment:** ✅ Yes  
**Last Updated:** December 15, 2025
