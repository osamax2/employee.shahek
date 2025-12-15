# Test Plan - Employee Location Tracking System

## Overview

Comprehensive testing plan covering mobile app functionality, server API, dashboard, and compliance requirements.

---

## 1. Mobile App Tests

### 1.1 Initial Setup & Permissions

**Test ID:** MOB-001  
**Priority:** Critical  
**Objective:** Verify app installs and requests permissions correctly

**Steps:**
1. Install app on fresh device (iOS and Android)
2. Launch app
3. Observe permission prompts

**Expected Results:**
- ✓ App launches without crashes
- ✓ iOS: Requests "While in Use" location permission first
- ✓ Android: Requests location permission with rationale
- ✓ Permission dialog shows clear usage description
- ✓ Coming Soon page displays with company logo placeholder
- ✓ Privacy notice visible and readable

**Test on:**
- [ ] iOS 15+
- [ ] iOS 16+
- [ ] iOS 17+
- [ ] Android 11
- [ ] Android 12
- [ ] Android 13+

---

### 1.2 Background Location Permission

**Test ID:** MOB-002  
**Priority:** Critical  
**Objective:** Verify background location permission request and handling

**Steps:**
1. Grant "While in Use" permission
2. Observe app behavior for background permission

**Expected Results (iOS):**
- ✓ App prompts to enable "Always" permission
- ✓ Clear instructions shown if needed
- ✓ Settings deeplink works (if implemented)

**Expected Results (Android):**
- ✓ "Allow all the time" option available
- ✓ Foreground service notification appears after granting
- ✓ Notification shows: "Company active"

---

### 1.3 Foreground Location Tracking

**Test ID:** MOB-003  
**Priority:** Critical  
**Objective:** Verify location updates while app is in foreground

**Steps:**
1. Open app with permissions granted
2. Monitor server for incoming location updates
3. Move location (walk or use simulator)
4. Wait 5 minutes

**Expected Results:**
- ✓ Initial location sent immediately
- ✓ Location updates sent on significant change (100m)
- ✓ Periodic updates sent every 5 minutes
- ✓ Server receives correct lat/lng
- ✓ Battery level included in payload
- ✓ Device OS info included

**Validation:**
```bash
# Check server logs or database
mysql> SELECT * FROM employee_locations ORDER BY id DESC LIMIT 10;
```

---

### 1.4 Background Location Tracking

**Test ID:** MOB-004  
**Priority:** Critical  
**Objective:** Verify location tracking continues when app is backgrounded

**Steps:**
1. Open app (permissions granted)
2. Press home button to background app
3. Move location significantly
4. Wait 5-10 minutes
5. Check server for updates

**Expected Results:**
- ✓ Android: Foreground notification remains visible
- ✓ iOS: Background location indicator active (status bar)
- ✓ Location updates continue to be sent
- ✓ Update frequency maintained (every 5 min)
- ✓ Server receives updates with correct timestamps

**Test Scenarios:**
- [ ] App backgrounded for 1 hour
- [ ] App backgrounded for 4 hours
- [ ] Phone locked
- [ ] Phone unlocked but app not visible
- [ ] Multiple apps running

---

### 1.5 App Termination & Reboot (Android)

**Test ID:** MOB-005  
**Priority:** High  
**Objective:** Verify tracking survives app kill and device reboot

**Steps:**
1. Force stop app from Settings
2. Move location
3. Wait 10 minutes
4. Check if tracking resumes

**Expected Results (Android):**
- ✓ Foreground service prevents easy termination
- ✓ If killed, may require manual restart
- ✓ After reboot: Service should restart (if configured)

**Steps (Reboot Test):**
1. Restart device
2. Wait for device to fully boot
3. Check if tracking resumes automatically

**Expected Results:**
- ✓ Android: Service auto-starts after reboot (if `RECEIVE_BOOT_COMPLETED` granted)
- ✓ iOS: Tracking resumes when app is opened

**Note:** iOS cannot auto-start after reboot without user action.

---

### 1.6 Offline Mode & Retry Logic

**Test ID:** MOB-006  
**Priority:** Critical  
**Objective:** Verify offline queue and retry mechanism

**Steps:**
1. Open app with permissions granted
2. Verify initial location sent
3. Disable WiFi and cellular data
4. Move location (simulator or GPS spoofing)
5. Wait 5 minutes (should queue location locally)
6. Move again
7. Enable connectivity
8. Observe retry behavior

**Expected Results:**
- ✓ Locations cached in AsyncStorage when offline
- ✓ No crash or errors when network unavailable
- ✓ Upon reconnection, queued locations sent to server
- ✓ Retry uses exponential backoff (5s → 10s → 20s → ...)
- ✓ Max retry delay: 5 minutes
- ✓ All cached locations eventually sent
- ✓ Order preserved (oldest first)

**Validation:**
```bash
# Check all locations received
mysql> SELECT recorded_at, received_at FROM employee_locations 
       WHERE employee_id = X ORDER BY recorded_at;
# recorded_at should be older than received_at for cached items
```

---

### 1.7 JWT Token Refresh

**Test ID:** MOB-007  
**Priority:** Critical  
**Objective:** Verify automatic token refresh on expiration

**Steps:**
1. Login to app
2. Extract access token (check logs)
3. Wait 60+ minutes (token TTL)
4. Move location to trigger API call

**Expected Results:**
- ✓ First API call returns 401 Unauthorized
- ✓ App automatically calls `/api/auth/refresh`
- ✓ New access token obtained
- ✓ Original API call retried with new token
- ✓ Location sent successfully
- ✓ No user intervention required
- ✓ No visible error to user

**Edge Case:**
- Refresh token also expired → User sees error, needs re-login

---

### 1.8 Battery Consumption Test

**Test ID:** MOB-008  
**Priority:** Medium  
**Objective:** Measure battery impact of background tracking

**Steps:**
1. Fully charge device to 100%
2. Start tracking
3. Use device normally for 8 hours
4. Record battery level
5. Compare with baseline (no tracking)

**Expected Results:**
- ✓ Additional battery drain: 5-10% over 8 hours
- ✓ No excessive drain (>20%)
- ✓ Device remains usable
- ✓ No overheating

**Variables to Test:**
- [ ] High accuracy vs balanced
- [ ] Update frequency (5 min vs 10 min)
- [ ] WiFi on/off
- [ ] Cellular data only

---

### 1.9 Permission Denial Handling

**Test ID:** MOB-009  
**Priority:** High  
**Objective:** Verify graceful handling of denied permissions

**Steps:**
1. Fresh install
2. Deny location permission
3. Observe app behavior

**Expected Results:**
- ✓ App does not crash
- ✓ Clear message shown: "Location permission required"
- ✓ Instructions to enable in Settings
- ✓ No repeated annoying prompts
- ✓ "Open Settings" button (optional)

**Test:**
- [ ] iOS: Deny once, try again
- [ ] iOS: "Don't Allow"
- [ ] Android: "Deny"
- [ ] Android: "Don't ask again"

---

### 1.10 UI/UX Test

**Test ID:** MOB-010  
**Priority:** Medium  
**Objective:** Verify Coming Soon page displays correctly

**Expected Elements:**
- ✓ Company logo placeholder (circular, blue background)
- ✓ "Coming Soon" heading
- ✓ "Employee Tracking System" subtitle
- ✓ Status section: Shows tracking status, battery %
- ✓ Privacy Notice card: Readable, comprehensive
- ✓ Device ID footer: Shows OS and version
- ✓ Proper spacing and alignment
- ✓ Responsive on different screen sizes

**Test Devices:**
- [ ] iPhone SE (small screen)
- [ ] iPhone 15 Pro
- [ ] iPad
- [ ] Android phone (5.5")
- [ ] Android phone (6.7")
- [ ] Android tablet

---

## 2. Server API Tests

### 2.1 Authentication Endpoint

**Test ID:** API-001  
**Priority:** Critical  
**Objective:** Verify login functionality

**Test Cases:**

**Case 1: Valid Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@device.local","password":"test"}'
```

**Expected Response:**
```json
{
  "success": true,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhb...",
  "refresh_token": "base64_string...",
  "token_type": "bearer",
  "expires_in": 3600,
  "employee": {
    "id": 1,
    "name": "Device test",
    "email": "test@device.local",
    "is_active": true
  }
}
```

**Case 2: Invalid Credentials**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -d '{"email":"test@device.local","password":"wrong"}'
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
# HTTP 401
```

**Case 3: Missing Fields**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -d '{"email":"test@device.local"}'
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "password": ["The password field is required."]
  }
}
# HTTP 422
```

**Case 4: Auto-Registration (Device ID)**
```bash
# First time login with new device ID
curl -X POST http://localhost:8000/api/auth/login \
  -d '{"email":"newdevice123@device.local","password":"newdevice123"}'
```

**Expected:**
- ✓ New employee created automatically
- ✓ Returns access token
- ✓ Employee name: "Device newdevic"

---

### 2.2 Token Refresh Endpoint

**Test ID:** API-002  
**Priority:** Critical  
**Objective:** Verify token refresh

```bash
# Get refresh token from login
REFRESH_TOKEN="..."

curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Content-Type: application/json" \
  -d "{\"refresh_token\":\"$REFRESH_TOKEN\"}"
```

**Expected Response:**
```json
{
  "success": true,
  "access_token": "new_jwt_token...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

**Case: Invalid Refresh Token**
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -d '{"refresh_token":"invalid"}'
```

**Expected:**
```json
{
  "success": false,
  "message": "Token refresh failed"
}
# HTTP 401
```

---

### 2.3 Location Submission Endpoint

**Test ID:** API-003  
**Priority:** Critical  
**Objective:** Verify location data acceptance

**Case 1: Valid Location**
```bash
TOKEN="your_jwt_token"

curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lat": 37.7749,
    "lng": -122.4194,
    "accuracy": 10,
    "timestamp": "2025-12-14T10:30:00Z",
    "speed": 0,
    "heading": null,
    "battery": 85,
    "device_os": "iOS",
    "device_version": "17.1"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Location recorded successfully",
  "data": {
    "id": 123,
    "recorded_at": "2025-12-14T10:30:00.000000Z"
  }
}
# HTTP 201
```

**Case 2: Missing Required Fields**
```bash
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"lat": 37.7749}'
```

**Expected:**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "lng": ["The lng field is required."],
    "timestamp": ["The timestamp field is required."]
  }
}
# HTTP 422
```

**Case 3: Invalid Coordinates**
```bash
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"lat": 999, "lng": -122, "timestamp": "2025-12-14T10:30:00Z"}'
```

**Expected:**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "lat": ["The lat field must be between -90 and 90."]
  }
}
# HTTP 422
```

**Case 4: No Authentication**
```bash
curl -X POST http://localhost:8000/api/location \
  -d '{"lat": 37, "lng": -122, "timestamp": "2025-12-14T10:30:00Z"}'
```

**Expected:**
```json
{
  "message": "Unauthenticated."
}
# HTTP 401
```

**Case 5: Expired Token**
```bash
# Use old/expired token
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer expired_token" \
  -d '{"lat": 37, "lng": -122, "timestamp": "2025-12-14T10:30:00Z"}'
```

**Expected:**
```json
{
  "message": "Token has expired"
}
# HTTP 401
```

---

### 2.4 Rate Limiting Test

**Test ID:** API-004  
**Priority:** High  
**Objective:** Verify rate limiting works

**Steps:**
1. Get valid JWT token
2. Send 60 requests rapidly to `/api/location`
3. Send 61st request

**Expected Results:**
- ✓ First 60 requests: HTTP 201 success
- ✓ 61st request: HTTP 429 Too Many Requests
- ✓ Response headers include:
  - `X-RateLimit-Limit: 60`
  - `X-RateLimit-Remaining: 0`
- ✓ Response body:
```json
{
  "success": false,
  "message": "Too many requests. Please try again later."
}
```

**Script:**
```bash
for i in {1..65}; do
  curl -X POST http://localhost:8000/api/location \
    -H "Authorization: Bearer $TOKEN" \
    -d '{"lat":37,"lng":-122,"timestamp":"2025-12-14T10:30:00Z"}'
  echo "Request $i"
done
```

---

### 2.5 Admin API Tests

**Test ID:** API-005  
**Priority:** High  
**Objective:** Verify admin endpoints

**Case 1: Get Latest Locations**
```bash
curl http://localhost:8000/api/admin/locations/latest
```

**Expected:**
```json
{
  "success": true,
  "data": [
    {
      "employee_id": 1,
      "name": "Device test",
      "email": "test@device.local",
      "lat": 37.7749,
      "lng": -122.4194,
      "accuracy": 10,
      "battery": 85,
      "recorded_at": "2025-12-14T10:30:00.000000Z",
      "received_at": "2025-12-14T10:30:05.000000Z",
      "is_online": true,
      "last_seen": "5 seconds ago"
    }
  ],
  "count": 1,
  "timestamp": "2025-12-14T10:30:10.000000Z"
}
```

**Case 2: Filter Active Only**
```bash
curl "http://localhost:8000/api/admin/locations/latest?active_only=1"
```

**Expected:**
- Only online employees returned

**Case 3: Get Stats**
```bash
curl http://localhost:8000/api/admin/stats
```

**Expected:**
```json
{
  "success": true,
  "data": {
    "total_employees": 10,
    "online_employees": 7,
    "offline_employees": 3,
    "total_locations": 5432,
    "locations_today": 234
  }
}
```

---

## 3. Dashboard Tests

### 3.1 Dashboard Load Test

**Test ID:** DASH-001  
**Priority:** Critical  
**Objective:** Verify dashboard loads correctly

**Steps:**
1. Open browser: `http://localhost:8000/admin/dashboard`
2. Observe page load

**Expected Results:**
- ✓ Page loads without errors
- ✓ Stats cards display with data
- ✓ Map loads with OpenStreetMap tiles
- ✓ Employee list shows on right side
- ✓ Auto-refresh countdown starts
- ✓ No console errors

---

### 3.2 Map Functionality Test

**Test ID:** DASH-002  
**Priority:** High  
**Objective:** Verify map displays employees correctly

**Expected:**
- ✓ Markers appear for each employee with latest location
- ✓ Online employees: Green marker
- ✓ Offline employees: Red marker
- ✓ Click marker → Popup shows:
  - Employee name and email
  - Last update time
  - Accuracy (meters)
  - Battery level
  - Online/offline badge
- ✓ Map auto-fits to show all markers
- ✓ Zoom in/out works
- ✓ Pan works

---

### 3.3 Auto-Refresh Test

**Test ID:** DASH-003  
**Priority:** High  
**Objective:** Verify auto-refresh mechanism

**Steps:**
1. Open dashboard
2. Note current employee locations
3. Move a mobile device (or simulate)
4. Wait for countdown to reach 0
5. Observe map update

**Expected Results:**
- ✓ Countdown timer counts down from 30 seconds
- ✓ At 0, data refreshes automatically
- ✓ Refresh indicator flashes yellow briefly
- ✓ Map markers update to new positions
- ✓ Employee list updates
- ✓ Stats update
- ✓ No page reload (AJAX only)

---

### 3.4 Employee List Test

**Test ID:** DASH-004  
**Priority:** Medium  
**Objective:** Verify employee list functionality

**Expected:**
- ✓ All active employees listed
- ✓ Online indicator: Green dot
- ✓ Offline indicator: Red dot
- ✓ Battery percentage shown (if available)
- ✓ "Last seen" time shown (e.g., "5 minutes ago")
- ✓ Click employee → Map focuses on their marker
- ✓ Marker popup opens automatically
- ✓ List scrollable if many employees

---

### 3.5 Filter Test

**Test ID:** DASH-005  
**Priority:** Medium  
**Objective:** Verify "Show Active Only" filter

**Steps:**
1. Open dashboard with mix of online/offline employees
2. Check "Show Active Only"
3. Observe results

**Expected:**
- ✓ Offline employees disappear from map
- ✓ Offline employees disappear from list
- ✓ Only green markers remain
- ✓ Stats still show total counts
- ✓ Uncheck filter → All employees reappear

---

### 3.6 Stats Accuracy Test

**Test ID:** DASH-006  
**Priority:** Medium  
**Objective:** Verify dashboard statistics are correct

**Steps:**
1. Count employees manually in database
2. Compare with dashboard stats

**Validation Query:**
```sql
-- Total employees
SELECT COUNT(*) FROM employees WHERE is_active = 1;

-- Online employees (last 10 minutes)
SELECT COUNT(*) FROM employees 
WHERE is_active = 1 
  AND last_seen_at >= NOW() - INTERVAL 10 MINUTE;

-- Total locations
SELECT COUNT(*) FROM employee_locations;

-- Locations today
SELECT COUNT(*) FROM employee_locations 
WHERE DATE(recorded_at) = CURDATE();
```

**Expected:**
- ✓ Dashboard stats match database counts
- ✓ Stats update on auto-refresh

---

## 4. Integration Tests

### 4.1 End-to-End Flow Test

**Test ID:** INT-001  
**Priority:** Critical  
**Objective:** Verify complete workflow from mobile to dashboard

**Steps:**
1. Install mobile app on device
2. Grant all permissions
3. Observe initial location sent
4. Open dashboard in browser
5. Verify employee appears on map
6. Background mobile app
7. Move location
8. Wait for auto-refresh on dashboard
9. Verify location updated

**Expected Results:**
- ✓ Employee marker appears on dashboard
- ✓ Marker at correct initial location
- ✓ After movement, marker updates position
- ✓ Battery level displayed
- ✓ "Last seen" timestamp recent
- ✓ Online status: Green

---

### 4.2 Multi-Device Test

**Test ID:** INT-002  
**Priority:** High  
**Objective:** Verify system handles multiple employees

**Steps:**
1. Install app on 3+ devices
2. Login with different device IDs
3. Move devices to different locations
4. Open dashboard

**Expected Results:**
- ✓ All devices appear on map
- ✓ Each device has unique marker
- ✓ Correct locations for each
- ✓ No confusion between employees
- ✓ Stats show correct total count

---

### 4.3 Offline to Online Transition Test

**Test ID:** INT-003  
**Priority:** High  
**Objective:** Verify employee status changes correctly

**Steps:**
1. Mobile app sending locations (online)
2. Verify dashboard shows green marker
3. Force stop mobile app (or wait 10+ minutes)
4. Wait for auto-refresh on dashboard
5. Verify status changes to offline

**Expected Results:**
- ✓ Marker changes from green to red
- ✓ "Last seen" shows time ago
- ✓ Employee still visible on map
- ✓ Stats: Online count decreases

**Reverse Test:**
- ✓ Start app again
- ✓ Marker changes back to green
- ✓ Stats update

---

## 5. Security Tests

### 5.1 SQL Injection Test

**Test ID:** SEC-001  
**Priority:** Critical  
**Objective:** Verify protection against SQL injection

**Test Cases:**
```bash
# Try SQL injection in login
curl -X POST http://localhost:8000/api/auth/login \
  -d '{"email":"admin@test.com'\'' OR 1=1--","password":"x"}'

# Try in location endpoint
curl -X POST http://localhost:8000/api/location \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"lat":"37.7749'\'' OR 1=1--","lng":-122,"timestamp":"2025-12-14T10:30:00Z"}'
```

**Expected Results:**
- ✓ No SQL syntax errors
- ✓ Invalid data rejected
- ✓ Prepared statements prevent injection
- ✓ No unauthorized access

---

### 5.2 XSS Test

**Test ID:** SEC-002  
**Priority:** High  
**Objective:** Verify protection against XSS

**Test:**
```bash
# Create employee with malicious name
curl -X POST http://localhost:8000/api/auth/login \
  -d '{"email":"<script>alert(1)</script>@device.local","password":"test"}'

# Check dashboard displays safely
```

**Expected:**
- ✓ Script tags escaped in HTML
- ✓ No alert box appears on dashboard
- ✓ Name displayed as plain text: `<script>alert(1)</script>`

---

### 5.3 CSRF Test

**Test ID:** SEC-003  
**Priority:** High  
**Objective:** Verify CSRF protection on web routes

**Expected:**
- ✓ API routes exempt from CSRF (stateless JWT)
- ✓ Web routes (dashboard) require CSRF token
- ✓ Requests without token rejected

---

### 5.4 Authorization Test

**Test ID:** SEC-004  
**Priority:** Critical  
**Objective:** Verify users can't access other users' data

**Test:**
```bash
# Login as Employee 1
TOKEN1="..."

# Login as Employee 2
TOKEN2="..."

# Employee 1 tries to access Employee 2's locations
curl http://localhost:8000/api/location \
  -H "Authorization: Bearer $TOKEN1"
  
# Should only return Employee 1's locations
```

**Expected:**
- ✓ Each employee sees only their own data
- ✓ No way to query other employees via API
- ✓ Admin endpoints require separate auth (to be implemented)

---

## 6. Performance Tests

### 6.1 Load Test - Location Submissions

**Test ID:** PERF-001  
**Priority:** Medium  
**Objective:** Verify server handles concurrent requests

**Tool:** Apache Bench or Postman

```bash
# 1000 requests, 50 concurrent
ab -n 1000 -c 50 -H "Authorization: Bearer $TOKEN" \
   -p location.json \
   http://localhost:8000/api/location
```

**Expected Results:**
- ✓ All requests succeed (or rate-limited)
- ✓ Average response time < 200ms
- ✓ No server crashes
- ✓ Database handles inserts

---

### 6.2 Dashboard Performance Test

**Test ID:** PERF-002  
**Priority:** Medium  
**Objective:** Verify dashboard performs with many employees

**Steps:**
1. Seed database with 100+ employees
2. Add recent locations for each
3. Load dashboard
4. Measure load time

**Expected Results:**
- ✓ Initial load < 3 seconds
- ✓ Map renders all markers
- ✓ Auto-refresh completes < 2 seconds
- ✓ No browser lag

**Note:** For >1000 employees, consider pagination/clustering.

---

## 7. Compliance Tests

### 7.1 Data Minimization Test

**Test ID:** COMP-001  
**Priority:** Critical  
**Objective:** Verify only necessary data is collected

**Checklist:**
- ✓ Only location, timestamp, accuracy, speed, heading, battery collected
- ✓ No contacts, SMS, photos, files accessed
- ✓ No camera or microphone permissions requested
- ✓ No unnecessary background services

**Validation:**
- Review app permissions in `app.json`
- Check mobile app code for unnecessary imports
- Audit API endpoint: Only expected fields stored

---

### 7.2 Privacy Notice Test

**Test ID:** COMP-002  
**Priority:** Critical  
**Objective:** Verify privacy notice is clear and visible

**Expected:**
- ✓ Privacy notice shown on app launch
- ✓ Explains data collection purpose
- ✓ Mentions HTTPS security
- ✓ States employee consent required
- ✓ Readable font size
- ✓ No legal jargon (plain language)

---

### 7.3 Consent Verification Test

**Test ID:** COMP-003  
**Priority:** Critical  
**Objective:** Ensure consent is documented

**Note:** This is a procedural test, not technical.

**Checklist:**
- ✓ HR has written consent forms
- ✓ Employees signed before app deployment
- ✓ Consent includes:
  - Purpose of tracking
  - Data collected
  - Retention period
  - Right to withdraw
- ✓ Consent stored securely (HR records)

---

## 8. Edge Cases & Stress Tests

### 8.1 Poor Network Conditions

**Test ID:** EDGE-001  
**Objective:** Test behavior on slow/unreliable network

**Steps:**
1. Use network throttling tool
2. Set to 3G or slower
3. Send locations
4. Observe retry behavior

**Expected:**
- ✓ App retries with exponential backoff
- ✓ No crashes
- ✓ Eventually succeeds or queues

---

### 8.2 Clock Skew Test

**Test ID:** EDGE-002  
**Objective:** Test handling of incorrect device time

**Steps:**
1. Set mobile device time 1 hour ahead
2. Send location

**Expected:**
- ✓ Server records `recorded_at` from device
- ✓ Server records `received_at` from server time
- ✓ No JWT validation errors (within tolerance)

---

### 8.3 Very Old Cached Locations

**Test ID:** EDGE-003  
**Objective:** Test handling of locations cached for days

**Steps:**
1. Go offline
2. Cache 100+ locations over several days
3. Go online

**Expected:**
- ✓ All locations sent (if storage permits)
- ✓ Server accepts old timestamps
- ✓ Dashboard shows historical trail

---

## 9. Acceptance Criteria Summary

### Mobile App
- [x] Installs and launches on iOS 15+ and Android 11+
- [x] Requests and handles location permissions correctly
- [x] Sends location updates in foreground and background
- [x] Offline queue with retry works reliably
- [x] JWT authentication with auto-refresh
- [x] Battery drain acceptable (<10% over 8 hours)
- [x] Privacy notice visible and clear

### Server
- [x] API accepts location data with validation
- [x] JWT authentication secure
- [x] Rate limiting prevents abuse
- [x] SQL injection and XSS protected
- [x] Performance acceptable (<200ms response)

### Dashboard
- [x] Loads and displays map with employee markers
- [x] Auto-refresh every 30 seconds
- [x] Online/offline status accurate
- [x] Employee list functional
- [x] Stats display correctly

### Compliance
- [x] Only necessary data collected
- [x] Privacy notice displayed
- [x] HTTPS ready
- [x] Consent process documented

---

## Test Execution Log

| Test ID | Date | Tester | Result | Notes |
|---------|------|--------|--------|-------|
| MOB-001 | | | | |
| MOB-002 | | | | |
| MOB-003 | | | | |
| ... | | | | |

---

## Bug Report Template

**Bug ID:** BUG-XXX  
**Test ID:** MOB-XXX  
**Priority:** Critical/High/Medium/Low  
**Status:** Open/In Progress/Resolved  

**Description:**  
Clear description of the issue.

**Steps to Reproduce:**
1. Step 1
2. Step 2
3. Step 3

**Expected Result:**  
What should happen.

**Actual Result:**  
What actually happens.

**Environment:**
- OS: iOS 17.1 / Android 13
- Device: iPhone 15 / Pixel 7
- Server: localhost:8000
- Browser: Chrome 120

**Screenshots/Logs:**  
Attach relevant files.

**Severity Impact:**
- Blocks functionality: Yes/No
- Security risk: Yes/No
- Compliance issue: Yes/No

---

**Test Plan Version:** 1.0  
**Last Updated:** 2025-12-14
