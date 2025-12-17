# Offline-Status Fix - Documentation

## Problem
When a user deletes the app from their phone, the admin dashboard continues to show the device as "online" for up to 10 minutes. This is because the system relies on `last_seen_at` timestamps, and without active GPS data being sent, the status isn't updated quickly enough.

## Solution Overview
The solution involves three key changes:

### 1. Reduced Online Threshold (Backend)
**File**: `server/config/tracking.php`

Changed the online threshold from **10 minutes to 3 minutes**:
- Devices are now marked offline much faster when they stop sending data
- Previous: Device could show as online for up to 10 minutes after app deletion
- Current: Device will show as offline after 3 minutes of inactivity

```php
'online_threshold_minutes' => env('EMPLOYEE_ONLINE_THRESHOLD_MINUTES', 3),
```

Also added a new configuration for heartbeat interval:
```php
'heartbeat_interval_seconds' => env('HEARTBEAT_INTERVAL_SECONDS', 90), // 1.5 minutes
```

### 2. Heartbeat Service (Mobile App)
**File**: `mobile/src/services/HeartbeatService.js` (NEW FILE)

Created a new service that:
- Sends periodic "heartbeat" signals to the server every 90 seconds (1.5 minutes)
- Updates the `last_seen_at` timestamp even when no GPS data is being sent
- Automatically stops when the app is closed/uninstalled
- Handles token refresh if authentication expires

**Key Features**:
- Runs in the background while app is active
- Automatically starts on app initialization
- Stops cleanly when app is terminated
- Includes retry logic and error handling

### 3. Enhanced Heartbeat Endpoint (Backend)
**File**: `server/app/Http/Controllers/Api/DeviceController.php`

Updated the `heartbeat()` method to:
- Update both device and employee `last_seen_at` timestamps
- Ensure the device is marked as active
- Return accurate timestamp information

```php
// Update device
$device->update([
    'last_seen_at' => now(),
    'is_active' => true,
]);

// Also update employee's last_seen_at
if ($device->employee_id) {
    $employee = Employee::find($device->employee_id);
    if ($employee) {
        $employee->update([
            'last_seen_at' => now(),
        ]);
    }
}
```

### 4. Integration (Mobile App)
**File**: `mobile/App.js`

Added HeartbeatService to the app initialization:
- Import the service
- Start it during app initialization
- Runs automatically in the background

## How It Works Now

### When App is Running:
1. **GPS Data**: Sent every 5 minutes → Updates `last_seen_at`
2. **Heartbeat**: Sent every 90 seconds → Updates `last_seen_at`
3. **Result**: Device always shows as "online" in dashboard

### When App is Deleted/Closed:
1. **No GPS Data**: No location updates sent
2. **No Heartbeat**: No heartbeat signals sent
3. **After 3 Minutes**: Device automatically marked as "offline" in dashboard
4. **Result**: Accurate offline status displayed

## Timeline Comparison

### Before Fix:
```
App Deleted → 10 minutes pass → Device shown as "offline"
```

### After Fix:
```
App Deleted → 3 minutes pass → Device shown as "offline"
```

## Benefits

1. **Faster Offline Detection**: 3 minutes instead of 10 minutes
2. **More Accurate Status**: Heartbeat ensures active devices always show online
3. **Better User Experience**: Admins see real-time device status
4. **Robust**: Handles network issues, token refresh, and edge cases

## Configuration

You can adjust these settings via environment variables:

### Backend (.env):
```env
# How long before marking device offline (in minutes)
EMPLOYEE_ONLINE_THRESHOLD_MINUTES=3

# Mobile app heartbeat interval (in seconds)
HEARTBEAT_INTERVAL_SECONDS=90
```

### Recommended Values:
- **online_threshold_minutes**: 2-5 minutes
  - Too low: May cause false "offline" during brief network issues
  - Too high: Delays offline detection when app is truly closed
  
- **heartbeat_interval_seconds**: 60-120 seconds
  - Too low: Increased server load and battery drain
  - Too high: May miss the threshold window

## Testing

To test the offline status detection:

1. **Open the mobile app** → Device shows as "online" in dashboard
2. **Close/delete the app** from phone
3. **Wait 3 minutes**
4. **Refresh dashboard** → Device should now show as "offline"

## Technical Details

### Database Schema
Both tables have `last_seen_at` timestamp columns:
- `employees` table: `last_seen_at`
- `devices` table: `last_seen_at`

### API Endpoints
- `POST /api/device/heartbeat`: Receives heartbeat signals
- `POST /api/location`: Receives GPS location data

### Dashboard
The dashboard checks the `isOnline()` method in the Employee model:

```php
public function isOnline(): bool
{
    if (!$this->last_seen_at) {
        return false;
    }
    
    $threshold = config('tracking.online_threshold_minutes', 3);
    return $this->last_seen_at->gt(now()->subMinutes($threshold));
}
```

## Files Modified

1. ✅ `server/config/tracking.php` - Reduced threshold, added heartbeat config
2. ✅ `server/app/Http/Controllers/Api/DeviceController.php` - Enhanced heartbeat endpoint
3. ✅ `mobile/src/services/HeartbeatService.js` - New heartbeat service
4. ✅ `mobile/App.js` - Integrated heartbeat service

## Deployment Notes

### Backend:
1. Deploy updated config and controller files
2. Clear config cache: `php artisan config:clear`
3. No database migrations needed (columns already exist)

### Mobile App:
1. Build new APK/IPA with HeartbeatService
2. Users must install updated app version
3. Old versions will still work but with 10-minute threshold

## Troubleshooting

### Device still shows online after app deletion:
- Wait the full 3 minutes (plus dashboard refresh interval)
- Check server logs for heartbeat requests
- Verify config cache is cleared

### Device shows offline while app is running:
- Check network connectivity
- Verify heartbeat service is running (check app logs)
- Confirm API endpoint is accessible
- Check authentication token validity

## Future Improvements

Potential enhancements:
1. Add admin setting to configure threshold from dashboard
2. Implement push notifications when devices go offline
3. Add heartbeat history/logs for debugging
4. Create alerts for devices that haven't sent data in X time
