# Employee Location Tracking System

## 🎯 Overview

A **compliance-first** employee location tracking system consisting of:

1. **Mobile App** (React Native/Expo) - iOS & Android background location tracking
2. **PHP Server** (Laravel) - API + MySQL database
3. **Web Dashboard** - Real-time map visualization with Leaflet + OpenStreetMap

## ✅ Compliance & Ethics

This system is built with **non-negotiable compliance requirements**:

- ✅ **Explicit consent required** - Employees must be informed in writing
- ✅ **Transparent operation** - Clear privacy notice in app
- ✅ **Secure transport** - HTTPS only
- ✅ **Authentication** - JWT-based access control
- ✅ **Data minimization** - Only essential data collected
- ✅ **Least-privilege permissions** - No access to contacts, SMS, files, etc.
- ✅ **User-visible tracking** - Persistent notification (Android foreground service)

**⚠️ IMPORTANT:** This app must only be used with employees who have:
- Signed a written consent agreement
- Been informed about data collection practices
- Understand their rights regarding data access and deletion

## 📋 Quick Start

### Mobile App Setup

```bash
cd mobile
npm install
cp .env.example .env
# Edit .env with your server URL
npm start
# Run on device: npm run ios / npm run android
```

### Server Setup

```bash
cd server
composer install
cp .env.example .env
# Edit .env with database credentials
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
# Access dashboard: http://localhost:8000/admin/dashboard
```

## 🏗️ Architecture

### Mobile App (React Native + Expo)

**Tech Choice Rationale:**
- **Expo with Dev Client** chosen for reliable background location on both platforms
- Can be ejected to bare React Native if deeper native customization needed
- Provides cross-platform consistency with platform-specific optimizations

**Key Features:**
- Background location tracking (foreground service on Android, background modes on iOS)
- Offline queue with exponential backoff retry
- JWT authentication with automatic token refresh
- Minimal "Coming Soon" UI with privacy notice
- Battery and device info reporting

**Platform-Specific Implementation:**

**Android:**
- Foreground Service with persistent notification: "Company active"
- Permissions: `ACCESS_FINE_LOCATION`, `ACCESS_BACKGROUND_LOCATION`, `FOREGROUND_SERVICE`
- Survives app backgrounding and device reboot

**iOS:**
- Background Modes: location + fetch
- Requires "Always" location permission
- Clear usage descriptions in Info.plist for App Store compliance

### Server (Laravel + MySQL)

**Tech Choice Rationale:**
- **Laravel** chosen for built-in security, ORM, and rapid API development
- **MySQL** for relational data with proper indexing
- **Leaflet + OSM** for free, unlimited map tile access (no API keys needed)

**Key Features:**
- JWT authentication (tymon/jwt-auth)
- Rate limiting (60 req/min per employee)
- SQL injection prevention via Eloquent ORM
- Password hashing with bcrypt
- Real-time dashboard with auto-refresh

**API Endpoints:**
```
POST   /api/auth/login       - Authenticate and get tokens
POST   /api/auth/refresh     - Refresh access token
GET    /api/employees/me     - Get current employee info
POST   /api/location         - Submit location data
GET    /api/admin/locations/latest - Get latest locations (admin)
GET    /api/admin/stats      - Get dashboard stats (admin)
```

### Database Schema

**employees:**
```sql
id, name, email, password, is_active, device_id, last_seen_at
```

**employee_locations:**
```sql
id, employee_id, lat, lng, accuracy, speed, heading, battery, 
device_os, device_version, recorded_at, received_at, ip, user_agent
```

**tracking_sessions:**
```sql
id, employee_id, started_at, ended_at, location_count
```

## 🔒 Security Measures

### Authentication
- **JWT tokens** with 60-minute expiration
- **Refresh tokens** with 2-week expiration
- Secure storage (expo-secure-store on mobile)

### Transport Security
- **HTTPS only** for all API communication
- Certificate pinning recommended for production

### Data Protection
- **Password hashing** with bcrypt
- **Prepared statements** via Eloquent ORM
- **Input validation** on all endpoints
- **Rate limiting** to prevent abuse

### Access Control
- **Per-employee authentication** - Can only access own data
- **Admin dashboard** - Separate authentication (implement in production)
- **CORS configuration** - Controlled cross-origin access

## 📱 Mobile App Details

### Location Tracking Strategy

**Update Frequency:**
- Significant location change (100m distance threshold)
- Periodic heartbeat every 5 minutes
- Configurable via `.env`

**Offline Support:**
- Locations cached locally in AsyncStorage
- Exponential backoff retry (5s → 10s → 20s → ... → 5min)
- Automatic sync when connectivity restored

**Battery Optimization:**
- High accuracy only (not highest)
- Distance-based filtering
- Sensible update intervals

### Permission Handling

**iOS:**
1. Request "While in Use" first
2. Guide user to Settings to enable "Always"
3. Show clear instructions if denied
4. Required Info.plist strings for App Store

**Android:**
1. Request fine location
2. Request background location separately
3. Handle "Don't ask again" scenario
4. Foreground service prevents battery optimization killing

### Data Sent to Server

**Minimal data collection:**
```json
{
  "lat": 37.7749,
  "lng": -122.4194,
  "accuracy": 10,
  "timestamp": "2025-12-14T10:30:00Z",
  "speed": 0,
  "heading": null,
  "battery": 85,
  "device_os": "iOS",
  "device_version": "17.1"
}
```

**Not collected:**
- ❌ Contacts
- ❌ SMS/messages
- ❌ Photos/files
- ❌ Camera/microphone
- ❌ Calendar
- ❌ Browsing history

## 🖥️ Dashboard Details

### Features

**Map View:**
- Leaflet.js with OpenStreetMap tiles (free, no API key)
- Real-time markers for each employee
- Color-coded: 🟢 Online (green) | 🔴 Offline (red)
- Click marker for popup with details
- Auto-fit bounds to show all employees

**Employee List:**
- Sidebar with all active employees
- Online/offline indicators
- Battery levels
- Last seen timestamp
- Click to focus on map

**Auto-Refresh:**
- Updates every 30 seconds (configurable)
- Visual countdown timer
- Flash indicator on refresh

**Stats Dashboard:**
- Total employees
- Online now
- Offline
- Locations received today

**Filters:**
- "Show Active Only" checkbox
- Per-employee filtering

### Configuration

Edit `server/.env`:
```bash
MAP_AUTO_REFRESH_SECONDS=30
EMPLOYEE_ONLINE_THRESHOLD_MINUTES=10
```

## 🧪 Testing & Verification

### Test Plan

#### 1. Initial Setup Test
```
✓ Install mobile app
✓ Grant location permissions (foreground → background)
✓ See "Coming Soon" page with privacy notice
✓ Verify background notification (Android)
✓ Check server receives initial location
```

#### 2. Background Tracking Test
```
✓ Open app → background it
✓ Move location (physical device or simulator)
✓ Wait 5 minutes
✓ Check dashboard shows updated location
✓ Verify employee shows as "online"
```

#### 3. Offline Mode Test
```
✓ Turn off WiFi/cellular data
✓ Move location (wait 5 minutes)
✓ Turn connectivity back on
✓ Verify cached locations are sent to server
✓ Check dashboard receives all missed updates
```

#### 4. Token Expiration Test
```
✓ Wait for token to expire (60 minutes)
✓ Move location
✓ App should auto-refresh token
✓ Location should be sent successfully
✓ No user intervention required
```

#### 5. Device Reboot Test (Android)
```
✓ Restart Android device
✓ Verify tracking resumes automatically
✓ Check foreground notification reappears
✓ Confirm locations continue to be sent
```

#### 6. Dashboard Test
```
✓ Open dashboard in browser
✓ Verify map loads with OSM tiles
✓ Check employee markers appear
✓ Click marker → popup shows details
✓ Verify auto-refresh countdown
✓ Test "Show Active Only" filter
✓ Click employee in list → map focuses
```

#### 7. Multi-Employee Test
```
✓ Install app on multiple devices
✓ Verify each device gets unique ID
✓ Check dashboard shows all employees
✓ Move devices → verify individual updates
```

#### 8. Battery Impact Test
```
✓ Charge device to 100%
✓ Run tracking for 8 hours
✓ Monitor battery drain
✓ Expected: 5-10% additional drain
```

### Manual Testing Commands

**Test authentication:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@device.local","password":"test"}'
```

**Test location submission:**
```bash
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lat": 37.7749,
    "lng": -122.4194,
    "accuracy": 10,
    "timestamp": "2025-12-14T10:30:00Z",
    "battery": 85
  }'
```

**Check dashboard API:**
```bash
curl http://localhost:8000/api/admin/locations/latest
curl http://localhost:8000/api/admin/stats
```

### Simulator Location Testing

**iOS Simulator:**
```
Debug → Location → Custom Location
Enter lat/lng: 37.7749, -122.4194
Or use: Debug → Location → City Run (simulates movement)
```

**Android Emulator:**
```
Extended Controls (⋮ button)
→ Location
→ Enter decimal or search location
→ Send
```

## 🚀 Production Deployment

### Mobile App

**iOS:**
```bash
# Configure certificates in Apple Developer
# Update bundle identifier in app.json
eas build --platform ios --profile production
# Submit to App Store
eas submit --platform ios
```

**Android:**
```bash
# Update package name in app.json
# Generate signing key
keytool -genkey -v -keystore release.keystore \
  -alias release -keyalg RSA -keysize 2048 -validity 10000
  
eas build --platform android --profile production
# Submit to Play Store
eas submit --platform android
```

**App Store Requirements:**
- Clear location usage descriptions
- Privacy policy URL
- Screenshots showing permission prompts
- Explain business justification

### Server

**Environment Setup:**
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tracking.yourcompany.com
```

**Web Server (Nginx):**
```nginx
server {
    listen 443 ssl http2;
    server_name tracking.yourcompany.com;
    root /var/www/server/public;

    ssl_certificate /etc/letsencrypt/live/yourcompany.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourcompany.com/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

**Optimization:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**SSL Certificate:**
```bash
# Using Let's Encrypt
sudo certbot --nginx -d tracking.yourcompany.com
```

## ⚠️ Known Limitations

### iOS Background Location
- System may throttle updates if device is stationary
- "Always" permission requires user action in Settings
- App Store review requires clear justification

### Android Battery Optimization
- Some manufacturers (Xiaomi, Huawei) aggressively kill background apps
- Users may need to disable battery optimization manually
- Foreground service helps but not guaranteed

### Offline Queue
- Limited by device storage
- Very old cached locations may be dropped
- No server-side conflict resolution

### Dashboard
- Not optimized for >1000 employees
- Consider pagination/clustering for scale
- Auto-refresh can be resource-intensive

## 🔧 Troubleshooting

### Mobile App Issues

**iOS: Background tracking stops**
- Verify "Always" permission in Settings → Privacy → Location
- Check Background Modes enabled in Xcode
- Ensure Info.plist strings are descriptive

**Android: Tracking stops after app killed**
- Disable battery optimization for the app
- Check foreground service notification appears
- Verify FOREGROUND_SERVICE permission granted

**Offline queue not processing**
- Check network connectivity
- Review app logs for errors
- Verify exponential backoff is working

**401 Unauthorized errors**
- Token expired → Check refresh token logic
- Invalid credentials → Re-login
- Server clock skew → Sync server time

### Server Issues

**JWT errors**
- Run: `php artisan jwt:secret`
- Check `JWT_SECRET` in `.env`
- Verify JWT config published

**Database connection failed**
- Check MySQL is running: `sudo systemctl status mysql`
- Verify credentials in `.env`
- Create database: `CREATE DATABASE employee_tracking;`

**CORS errors**
- Update `config/cors.php` with mobile app origins
- For dev: `'allowed_origins' => ['*']`
- For prod: Specify exact origins

**Dashboard not loading**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify routes registered: `php artisan route:list`
- Clear cache: `php artisan cache:clear`

## 📞 Support

For technical issues or questions:
- **Email:** dev-team@yourcompany.com
- **Documentation:** See `/mobile/README.md` and `/server/README.md`
- **Logs:** Mobile app console, Server `storage/logs/laravel.log`

## 📄 License

Proprietary - Internal company use only.

## 🙏 Acknowledgments

- **Leaflet** - Open-source map library
- **OpenStreetMap** - Free map tiles
- **Expo** - React Native tooling
- **Laravel** - PHP framework
- **tymon/jwt-auth** - JWT implementation

---

**Built with compliance and employee privacy in mind. 🔒**
