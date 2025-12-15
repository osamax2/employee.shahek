# Employee Tracking Server

## Overview

PHP Laravel backend server for employee location tracking with JWT authentication, MySQL database, and real-time dashboard.

## Tech Stack

- **PHP 8.1+**
- **Laravel 10**
- **MySQL/MariaDB**
- **JWT Authentication** (tymon/jwt-auth)
- **Leaflet + OpenStreetMap** (map visualization)

## Architecture Decision: Laravel

**Why Laravel?**
- Built-in security features (CSRF, SQL injection prevention, XSS protection)
- Eloquent ORM with prepared statements
- JWT authentication via tymon/jwt-auth
- Rate limiting out of the box
- Easy API development with resource controllers
- Blade templating for dashboard
- Migration system for database versioning

## Features

✅ **JWT Authentication** - Access + refresh token flow  
✅ **Location API** - POST endpoint with validation  
✅ **Rate Limiting** - 60 requests/minute per employee  
✅ **Admin Dashboard** - Real-time map with Leaflet/OSM  
✅ **Auto-refresh** - Updates every 30 seconds (configurable)  
✅ **Employee List** - Online/offline status  
✅ **Secure** - Password hashing, prepared statements, HTTPS ready  
✅ **Data Minimization** - Only essential fields stored  

## Database Schema

### `employees`
```sql
- id (primary key)
- name
- email (unique)
- password (hashed)
- is_active (boolean)
- device_id (unique, nullable)
- last_seen_at (timestamp)
- created_at, updated_at
```

### `employee_locations`
```sql
- id (primary key)
- employee_id (foreign key)
- lat, lng (decimal)
- accuracy, speed, heading (decimal, nullable)
- battery (integer, nullable)
- device_os, device_version (string, nullable)
- recorded_at (timestamp from device)
- received_at (timestamp on server)
- ip, user_agent (string)
- created_at, updated_at
```

### `tracking_sessions`
```sql
- id (primary key)
- employee_id (foreign key)
- started_at, ended_at (timestamp)
- location_count (integer)
- created_at, updated_at
```

## Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or MariaDB
- Web server (Apache/Nginx) or `php artisan serve`

### Installation

1. **Install dependencies:**
   ```bash
   cd server
   composer install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env`:
   ```
   DB_DATABASE=employee_tracking
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

3. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

4. **Generate JWT secret:**
   ```bash
   php artisan jwt:secret
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Seed database (optional):**
   ```bash
   php artisan db:seed
   ```

7. **Start development server:**
   ```bash
   php artisan serve
   ```
   
   Server will be available at: `http://localhost:8000`

### Production Deployment

1. **Set environment:**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. **Configure web server:**
   - Point document root to `/public`
   - Enable HTTPS (required for location tracking)
   - Configure virtual host

3. **Optimize Laravel:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Set up SSL certificate:**
   - Use Let's Encrypt or commercial SSL
   - Force HTTPS in Laravel (add to middleware)

## API Endpoints

### Authentication

**POST** `/api/auth/login`
```json
Request:
{
  "email": "employee@example.com",
  "password": "password123"
}

Response:
{
  "success": true,
  "access_token": "eyJ0eXAiOiJKV1...",
  "refresh_token": "base64_encoded_token",
  "token_type": "bearer",
  "expires_in": 3600,
  "employee": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "is_active": true
  }
}
```

**POST** `/api/auth/refresh`
```json
Request:
{
  "refresh_token": "base64_encoded_token"
}

Response:
{
  "success": true,
  "access_token": "new_jwt_token",
  "token_type": "bearer",
  "expires_in": 3600
}
```

**GET** `/api/employees/me`  
Headers: `Authorization: Bearer {token}`
```json
Response:
{
  "success": true,
  "employee": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "is_active": true,
    "last_seen_at": "2025-12-14T10:30:00.000000Z"
  }
}
```

### Location Tracking

**POST** `/api/location`  
Headers: `Authorization: Bearer {token}`
```json
Request:
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

Response:
{
  "success": true,
  "message": "Location recorded successfully",
  "data": {
    "id": 123,
    "recorded_at": "2025-12-14T10:30:00.000000Z"
  }
}
```

**GET** `/api/location`  
Headers: `Authorization: Bearer {token}`
```json
Response:
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 123,
        "lat": "37.7749000",
        "lng": "-122.4194000",
        "accuracy": "10.00",
        "battery": 85,
        "recorded_at": "2025-12-14T10:30:00.000000Z"
      }
    ],
    "per_page": 50,
    "total": 150
  }
}
```

### Admin API

**GET** `/api/admin/locations/latest?active_only=0&employee_id=1`  
Returns latest location for each employee.

**GET** `/api/admin/employees/{id}/history?hours=24`  
Returns location history for specific employee.

**GET** `/api/admin/stats`  
Returns dashboard statistics.

## Admin Dashboard

Access at: `http://localhost:8000/admin/dashboard`

**Features:**
- Real-time map with employee markers
- Color-coded online/offline status (green/red)
- Auto-refresh every 30 seconds
- Employee list with battery levels
- Click employee to focus on map
- Filter: "Show Active Only"
- Stats: Total employees, online, offline, locations today

**Map Controls:**
- Zoom in/out
- Pan
- Click marker for popup with details

## Security Features

✅ **Password Hashing** - Bcrypt with salt  
✅ **JWT Authentication** - Signed tokens with expiration  
✅ **Rate Limiting** - 60 requests/minute per employee  
✅ **SQL Injection Prevention** - Eloquent ORM with prepared statements  
✅ **XSS Protection** - Laravel's built-in escaping  
✅ **CSRF Protection** - For web routes  
✅ **CORS Configuration** - Controlled access  
✅ **Input Validation** - All inputs validated  
✅ **HTTPS Ready** - Designed for secure transport  

## Configuration

Edit `.env`:

```bash
# Rate limiting (requests per minute)
RATE_LIMIT_PER_MINUTE=60

# Map auto-refresh (seconds)
MAP_AUTO_REFRESH_SECONDS=30

# Employee online threshold (minutes)
EMPLOYEE_ONLINE_THRESHOLD_MINUTES=10

# JWT token lifetime (minutes)
JWT_TTL=60

# JWT refresh token lifetime (minutes, default 2 weeks)
JWT_REFRESH_TTL=20160
```

## Testing

### Manual Testing

1. **Test authentication:**
   ```bash
   curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"test@example.com","password":"password"}'
   ```

2. **Test location posting:**
   ```bash
   curl -X POST http://localhost:8000/api/location \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"lat":37.7749,"lng":-122.4194,"accuracy":10,"timestamp":"2025-12-14T10:30:00Z"}'
   ```

3. **Test dashboard:**
   - Open browser: `http://localhost:8000/admin/dashboard`
   - Check map loads
   - Verify auto-refresh countdown

### Unit Tests

```bash
php artisan test
```

## Troubleshooting

**JWT secret not set:**
```bash
php artisan jwt:secret
```

**Database connection error:**
- Check MySQL is running
- Verify credentials in `.env`
- Create database: `CREATE DATABASE employee_tracking;`

**Migrations fail:**
```bash
php artisan migrate:fresh
```

**CORS errors from mobile app:**
- Update `config/cors.php` with allowed origins
- Or set `'allowed_origins' => ['*']` for development

**Rate limit too strict:**
- Increase `RATE_LIMIT_PER_MINUTE` in `.env`

## Project Structure

```
server/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   └── LocationController.php
│   │   │   └── Admin/
│   │   │       └── DashboardController.php
│   │   └── Middleware/
│   │       └── ThrottleRequests.php
│   └── Models/
│       ├── Employee.php
│       ├── EmployeeLocation.php
│       └── TrackingSession.php
├── config/
│   ├── auth.php
│   ├── jwt.php
│   ├── cors.php
│   └── tracking.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── admin/
│           └── dashboard.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── .env.example
├── composer.json
└── README.md
```

## Production Considerations

1. **Use HTTPS** - Absolutely required for location tracking
2. **Set APP_DEBUG=false** - Never show errors in production
3. **Configure firewall** - Only allow ports 80/443
4. **Set up monitoring** - Track API errors and performance
5. **Implement proper refresh tokens** - Current implementation is simplified
6. **Add admin authentication** - Dashboard is currently unprotected
7. **Set up backups** - Regular database backups
8. **Use queue workers** - For any background processing
9. **Configure logging** - Monitor suspicious activity
10. **Add IP whitelisting** - For admin dashboard (optional)

## Compliance

- ✅ Employee consent required (handled by HR/legal)
- ✅ Data minimization implemented
- ✅ Secure storage (encrypted database recommended)
- ✅ Access control (JWT authentication)
- ✅ Audit trail (IP + user agent stored)
- ✅ Right to deletion (implement data purge endpoint)

## Support

For issues or questions, contact: dev-team@yourcompany.com

## License

Proprietary - Internal use only
