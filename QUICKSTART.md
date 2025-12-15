# Quick Start Guide

## Prerequisites

- Node.js 18+ and npm (for mobile app)
- PHP 8.1+ and Composer (for server)
- MySQL/MariaDB
- Git

## Mobile App (5 minutes)

```bash
cd mobile
npm install
cp .env.example .env
```

Edit `.env`:
```
API_BASE_URL=http://10.0.2.2:8000/api  # For Android emulator
# OR
API_BASE_URL=http://localhost:8000/api  # For iOS simulator
```

Start the app:
```bash
npm start
# Then press 'a' for Android or 'i' for iOS
```

## Build APK/IPA (Production)

### Build Android APK
```bash
cd mobile

# Install EAS CLI (one-time)
npm install -g eas-cli

# Login to Expo
eas login

# Build APK
./build-android.sh
# Or: npm run build:android
```

### Build iOS App
```bash
cd mobile
./build-ios.sh
# Or: npm run build:ios
```

**See [mobile/BUILD.md](mobile/BUILD.md) for complete build instructions!**

## Server (5 minutes)

```bash
cd server
composer install
cp .env.example .env
```

Edit `.env`:
```
DB_DATABASE=employee_tracking
DB_USERNAME=root
DB_PASSWORD=your_password
```

Setup:
```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed  # Optional: adds test data
php artisan serve
```

Dashboard: http://localhost:8000/admin/dashboard

## First Test

1. **Mobile:** Open app → Grant permissions → See "Coming Soon" page
2. **Server:** Check dashboard → See employee marker on map
3. **Move:** Change location in simulator → Wait 30 seconds → Verify update

## Database Setup (Alternative)

If Laravel migrations don't work:
```bash
mysql -u root -p < server/database/setup.sql
```

## Troubleshooting

**Mobile app can't connect:**
- Android emulator: Use `10.0.2.2` instead of `localhost`
- iOS simulator: Use `localhost`
- Check server is running: `php artisan serve`

**JWT errors:**
```bash
php artisan jwt:secret
```

**Database errors:**
```bash
# Create database
mysql -u root -p
CREATE DATABASE employee_tracking;
exit

# Run migrations
php artisan migrate
```

**Permissions errors (server):**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Default Credentials

Admin (for future admin login):
- Email: admin@company.com
- Password: admin123

## What to Customize

1. **Mobile app:**
   - Replace logo in `assets/` folder
   - Update `COMPANY_NAME` in `.env`
   - Customize colors in `App.js` styles

2. **Server:**
   - Update `APP_NAME` in `.env`
   - Configure email/SMS notifications (optional)
   - Adjust rate limits and refresh intervals

## Production Checklist

Before deploying to production:

- [ ] Implement proper user authentication (replace device ID)
- [ ] Add admin dashboard login
- [ ] Set up HTTPS (required)
- [ ] Configure proper CORS origins
- [ ] Enable error tracking (Sentry, etc.)
- [ ] Set APP_DEBUG=false
- [ ] Create backup strategy
- [ ] Document consent procedures
- [ ] Train employees on privacy rights
- [ ] Set up monitoring/alerts

## Need Help?

See detailed documentation:
- [Main README](README.md)
- [Mobile README](mobile/README.md)
- [Server README](server/README.md)
- [Test Plan](TEST_PLAN.md)
