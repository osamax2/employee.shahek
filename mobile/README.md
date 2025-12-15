# Employee Tracking Mobile App

## Overview

React Native (Expo) mobile application for compliance-first employee location tracking. This app reports device location to a backend server with full consent and transparency.

## Tech Stack

- **React Native** with Expo (~50.0.0)
- **expo-location** - Background & foreground location tracking
- **expo-task-manager** - Background task management
- **expo-secure-store** - Secure token storage
- **axios** - HTTP client with JWT authentication
- **AsyncStorage** - Offline queue management

## Architecture Decision: Expo Dev Client

**Why Expo?**
- Expo Location + Task Manager provides reliable background location on both iOS and Android
- Built-in foreground service support for Android
- Proper iOS background modes configuration
- Easier deployment and updates
- Can be ejected to bare React Native if needed

**Background Location Strategy:**
- **Android:** Uses Foreground Service with persistent notification ("Company active")
- **iOS:** Uses Background Location + Background Fetch capabilities
- **Offline support:** Queues locations locally and retries with exponential backoff

## Features

✅ **Coming Soon UI** - Logo placeholder + privacy notice  
✅ **Background location tracking** - Continues when app is backgrounded  
✅ **Offline support** - Caches locations and retries when online  
✅ **JWT Authentication** - Access + refresh token flow  
✅ **Data minimization** - Only collects: lat, lng, timestamp, accuracy, speed, battery, device OS  
✅ **HTTPS** - All API calls secured  
✅ **Permission handling** - Guides users through iOS "Always" permission  

## Setup

### Prerequisites

- Node.js 18+ and npm
- Expo CLI: `npm install -g expo-cli`
- iOS: Xcode and CocoaPods (Mac only)
- Android: Android Studio and emulator

### Installation

1. **Install dependencies:**
   ```bash
   cd mobile
   npm install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env`:
   ```
   API_BASE_URL=https://your-server.com/api
   COMPANY_NAME=Your Company Name
   ```

3. **Start development server:**
   ```bash
   npm start
   ```

4. **Run on device/emulator:**
   ```bash
   # iOS (Mac only)
   npm run ios
   
   # Android
   npm run android
   ```

### Building for Production

**iOS:**
```bash
expo build:ios
```
Or use EAS Build:
```bash
eas build --platform ios
```

**Android:**
```bash
expo build:android
```
Or:
```bash
eas build --platform android
```

## Permissions

### iOS (Info.plist)

Already configured in `app.json`:
- `NSLocationAlwaysAndWhenInUseUsageDescription`
- `NSLocationWhenInUseUsageDescription`
- `NSLocationAlwaysUsageDescription`
- `UIBackgroundModes`: location, fetch

### Android (AndroidManifest.xml)

Already configured in `app.json`:
- `ACCESS_FINE_LOCATION`
- `ACCESS_COARSE_LOCATION`
- `ACCESS_BACKGROUND_LOCATION`
- `FOREGROUND_SERVICE`
- `FOREGROUND_SERVICE_LOCATION`
- `RECEIVE_BOOT_COMPLETED`

## API Integration

### Authentication
- Device ID is generated on first launch
- Auto-login uses device ID (for demo; implement proper auth in production)
- Tokens stored securely in `expo-secure-store`

### Location Reporting
```javascript
POST /api/location
Authorization: Bearer {access_token}
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

### Token Refresh
```javascript
POST /api/auth/refresh
{
  "refresh_token": "..."
}
```

## Testing

### Test Scenarios

1. **Initial Setup:**
   - Install app → Grants location permissions → See "Coming Soon" page
   - Verify background notification appears (Android)

2. **Background Tracking:**
   - Open app → Background it → Move location (use simulator)
   - Check server receives updates every 5 minutes

3. **Offline Mode:**
   - Turn off WiFi/data → Move location → Turn back on
   - Verify cached locations are sent

4. **Token Expiration:**
   - Wait for token to expire → Move location
   - App should refresh token automatically

5. **Reboot (Android):**
   - Restart device → Verify tracking resumes

### Simulator Location Testing

**iOS Simulator:**
- Debug → Location → Custom Location or GPX Route

**Android Emulator:**
- Extended Controls (⋮) → Location → Set coordinates

## Compliance & Privacy

- ✅ Explicit consent required (employment agreement)
- ✅ User-visible notification (Android foreground service)
- ✅ Clear privacy notice in app
- ✅ Data minimization (only essential fields)
- ✅ Secure transport (HTTPS only)
- ✅ No access to contacts, SMS, files, camera, etc.

## Project Structure

```
mobile/
├── src/
│   └── services/
│       ├── AuthService.js       # JWT authentication
│       ├── LocationService.js   # Location tracking & queue
│       ├── StorageService.js    # AsyncStorage & device ID
│       └── config.js            # Environment config
├── App.js                       # Main app component
├── app.json                     # Expo configuration
├── package.json                 # Dependencies
└── README.md                    # This file
```

## Troubleshooting

**iOS: Background location not working**
- Ensure "Always" permission is granted in Settings
- Check that `UIBackgroundModes` includes "location"
- Verify Info.plist strings are descriptive enough for App Store

**Android: Tracking stops after app killed**
- Foreground service should prevent this
- Check battery optimization settings (disable for this app)
- Verify `FOREGROUND_SERVICE` permission is granted

**Offline queue not processing**
- Check network connectivity
- Review console logs for retry attempts
- Exponential backoff increases delay up to 5 minutes

**401 Unauthorized errors**
- Token may be expired
- Check `AuthService.refreshAccessToken()` is working
- Verify server `/api/auth/refresh` endpoint

## Production Considerations

1. **Replace device ID auth** with proper email/password or SSO
2. **Add logout functionality** to stop tracking
3. **Implement crash reporting** (Sentry, Bugsnag)
4. **Add analytics** (only privacy-safe metrics)
5. **Test thoroughly** on real devices with poor connectivity
6. **Configure proper App Store/Play Store** descriptions
7. **Set up OTA updates** via Expo Updates or CodePush
8. **Implement certificate pinning** for extra security

## Support

For issues or questions, contact: dev-team@yourcompany.com
