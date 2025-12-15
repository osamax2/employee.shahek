# Project Structure

```
shahek/
├── mobile/                                 # React Native Mobile App
│   ├── src/
│   │   └── services/
│   │       ├── AuthService.js             # JWT authentication logic
│   │       ├── LocationService.js         # Location tracking & offline queue
│   │       ├── StorageService.js          # Local storage & device ID
│   │       └── config.js                  # Environment configuration
│   ├── App.js                             # Main app component (Coming Soon UI)
│   ├── app.json                           # Expo configuration (permissions, etc.)
│   ├── package.json                       # Dependencies
│   ├── .env.example                       # Environment template
│   ├── .gitignore
│   └── README.md                          # Mobile app documentation
│
├── server/                                # Laravel PHP Server
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   │   ├── AuthController.php        # Login, refresh, logout
│   │   │   │   │   └── LocationController.php    # Location submission
│   │   │   │   └── Admin/
│   │   │   │       └── DashboardController.php   # Dashboard API
│   │   │   └── Middleware/
│   │   │       └── ThrottleRequests.php          # Rate limiting
│   │   └── Models/
│   │       ├── Employee.php                      # Employee model (JWT auth)
│   │       ├── EmployeeLocation.php              # Location records
│   │       └── TrackingSession.php               # Tracking sessions
│   ├── config/
│   │   ├── auth.php                       # Authentication configuration
│   │   ├── jwt.php                        # JWT settings
│   │   ├── cors.php                       # CORS configuration
│   │   └── tracking.php                   # Custom tracking settings
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── 2024_01_01_000001_create_employees_table.php
│   │   │   ├── 2024_01_01_000002_create_employee_locations_table.php
│   │   │   └── 2024_01_01_000003_create_tracking_sessions_table.php
│   │   ├── seeders/
│   │   │   └── DatabaseSeeder.php         # Test data seeder
│   │   └── setup.sql                      # Direct SQL setup (alternative)
│   ├── resources/
│   │   └── views/
│   │       ├── layouts/
│   │       │   └── app.blade.php          # Dashboard layout (Leaflet map)
│   │       └── admin/
│   │           └── dashboard.blade.php    # Dashboard page (auto-refresh)
│   ├── routes/
│   │   ├── api.php                        # API routes (JWT protected)
│   │   └── web.php                        # Web routes (dashboard)
│   ├── composer.json                      # PHP dependencies
│   ├── .env.example                       # Environment template
│   ├── setup.sh                           # Quick setup script
│   ├── .gitignore
│   └── README.md                          # Server documentation
│
├── README.md                              # Main project documentation
├── QUICKSTART.md                          # Quick start guide
└── TEST_PLAN.md                           # Comprehensive test plan
```

## Key Files Explained

### Mobile App

**App.js**
- Main component with "Coming Soon" UI
- Initializes location tracking on launch
- Displays privacy notice
- Shows tracking status and battery level

**LocationService.js**
- Handles foreground & background location updates
- Implements offline queue with exponential backoff
- Sends location data to server with JWT auth
- Manages retry logic for failed requests

**AuthService.js**
- JWT authentication (login, refresh, logout)
- Secure token storage via expo-secure-store
- Automatic token refresh on expiration

**StorageService.js**
- Device ID generation and storage
- Offline queue management with AsyncStorage
- Generic key-value storage

**app.json**
- Expo configuration
- iOS: Background modes, location usage descriptions
- Android: Permissions, foreground service config

### Server

**AuthController.php**
- POST /api/auth/login - Authenticate and get tokens
- POST /api/auth/refresh - Refresh access token
- GET /api/employees/me - Get current employee
- Auto-registration for device ID based login

**LocationController.php**
- POST /api/location - Submit location data (JWT protected)
- GET /api/location - Get employee's location history
- Validates coordinates, timestamp, and optional fields

**DashboardController.php**
- GET /api/admin/locations/latest - Latest location per employee
- GET /api/admin/employees/{id}/history - Location history
- GET /api/admin/stats - Dashboard statistics

**Employee.php**
- Eloquent model with JWT implementation
- Relationships: locations, latestLocation, trackingSessions
- isOnline() method (checks last_seen_at)

**dashboard.blade.php**
- Leaflet map with OpenStreetMap tiles
- Auto-refresh every 30 seconds
- Employee list with online/offline status
- Stats cards (total, online, offline, locations today)
- Filter: "Show Active Only"

**Migrations**
- employees: User accounts with device_id
- employee_locations: Location history with metadata
- tracking_sessions: Session tracking (optional)

## Configuration Files

**mobile/.env**
```
API_BASE_URL=https://your-server.com/api
LOCATION_UPDATE_INTERVAL=300000
LOCATION_DISTANCE_FILTER=100
COMPANY_NAME=Your Company Name
```

**server/.env**
```
DB_DATABASE=employee_tracking
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=
JWT_TTL=60
JWT_REFRESH_TTL=20160

RATE_LIMIT_PER_MINUTE=60
MAP_AUTO_REFRESH_SECONDS=30
EMPLOYEE_ONLINE_THRESHOLD_MINUTES=10
```

## API Flow

1. **Mobile App Starts:**
   - Generates/retrieves device ID
   - Calls POST /api/auth/login with device ID
   - Receives JWT access + refresh tokens
   - Stores tokens securely

2. **Location Update:**
   - Gets current location from GPS
   - Adds battery, device info
   - Calls POST /api/location with Authorization header
   - Server validates JWT
   - Server stores location in database
   - Server updates employee.last_seen_at

3. **Token Refresh:**
   - Access token expires (60 minutes)
   - Next API call returns 401
   - App calls POST /api/auth/refresh
   - Receives new access token
   - Retries original API call

4. **Offline Mode:**
   - No network → Locations cached in AsyncStorage
   - Network restored → Process queue
   - Send cached locations with exponential backoff
   - Clear queue on success

5. **Dashboard:**
   - Browser loads /admin/dashboard
   - JavaScript calls GET /api/admin/locations/latest
   - Leaflet renders markers on map
   - Auto-refresh every 30 seconds
   - Click marker → Show employee details

## Security Layers

1. **Authentication:** JWT tokens (access + refresh)
2. **Authorization:** Each employee sees only own data
3. **Transport:** HTTPS only (production)
4. **Rate Limiting:** 60 req/min per employee
5. **Input Validation:** All inputs validated
6. **SQL Injection:** Eloquent ORM with prepared statements
7. **XSS Protection:** Laravel's auto-escaping
8. **Password Hashing:** Bcrypt

## Compliance Features

1. **Data Minimization:** Only essential data collected
2. **Transparency:** Privacy notice in app
3. **Consent:** User-visible tracking (foreground service)
4. **Audit Trail:** IP and user agent logged
5. **Secure Storage:** Database with encryption recommended
6. **Access Control:** JWT-based authentication

## Deployment Targets

**Mobile App:**
- iOS App Store (requires Apple Developer account)
- Google Play Store (requires Google Play Console)
- Enterprise distribution (internal)

**Server:**
- Apache/Nginx + PHP-FPM
- MySQL/MariaDB database
- HTTPS via Let's Encrypt or commercial SSL
- Ubuntu/Debian Linux recommended

## Technology Stack Summary

| Component | Technology | Purpose |
|-----------|-----------|---------|
| Mobile Frontend | React Native + Expo | Cross-platform mobile app |
| Location Tracking | expo-location + task-manager | Background location |
| Mobile Auth | expo-secure-store + axios | Secure token storage |
| Offline Storage | AsyncStorage | Location queue |
| Server Framework | Laravel 10 | PHP API + web server |
| Database | MySQL | Relational data storage |
| Authentication | tymon/jwt-auth | JWT tokens |
| Map Visualization | Leaflet + OpenStreetMap | Free map tiles |
| Frontend (Dashboard) | Blade + vanilla JS | Server-rendered UI |

## Development Workflow

1. **Setup:**
   ```bash
   # Mobile
   cd mobile && npm install
   
   # Server
   cd server && composer install
   php artisan migrate
   ```

2. **Development:**
   ```bash
   # Mobile (terminal 1)
   cd mobile && npm start
   
   # Server (terminal 2)
   cd server && php artisan serve
   ```

3. **Testing:**
   - Mobile: Test on physical device or emulator
   - Server: Use curl or Postman for API testing
   - Dashboard: Open browser to localhost:8000/admin/dashboard

4. **Debugging:**
   - Mobile: React Native debugger or console.log
   - Server: storage/logs/laravel.log
   - Database: MySQL Workbench or phpMyAdmin

## Future Enhancements

- [ ] Admin authentication for dashboard
- [ ] Employee management UI (add/edit/delete)
- [ ] Historical playback of routes
- [ ] Geofencing alerts
- [ ] Push notifications
- [ ] CSV export of location data
- [ ] Data retention policies
- [ ] Right to deletion endpoint
- [ ] Multi-language support
- [ ] Dark mode

## License

Proprietary - Internal company use only
