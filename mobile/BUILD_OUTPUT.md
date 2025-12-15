# 📦 Build Output Summary

After successful build, you will receive:

## Android Build Outputs

### APK File (Direct Install)
- **File:** `app-release.apk` or similar
- **Size:** ~30-50 MB
- **Distribution:** Direct install on Android devices
- **Use case:** Internal testing, employee distribution

### AAB File (Play Store)
- **File:** `app-release.aab`
- **Size:** ~25-40 MB
- **Distribution:** Google Play Store only
- **Use case:** Public release, automatic updates

## iOS Build Outputs

### IPA File (TestFlight/Device)
- **File:** `app.ipa`
- **Size:** ~40-60 MB
- **Distribution:** TestFlight, direct install
- **Use case:** Beta testing, enterprise distribution

### App Store Package
- **Format:** IPA with App Store provisioning
- **Distribution:** Apple App Store
- **Use case:** Public release

## Build Artifacts Location

### EAS Build (Cloud)
```bash
# List all builds
eas build:list

# Download URLs shown in output
# Also available at: https://expo.dev
```

### Local Build
- Android: `android/app/build/outputs/apk/release/`
- iOS: Archive in Xcode Organizer

## Version Information

Current version is set in `app.json`:
```json
{
  "version": "1.0.0",
  "android": {
    "versionCode": 1
  },
  "ios": {
    "buildNumber": "1"
  }
}
```

## Build Logs

Logs are saved:
- EAS Build: Available in web dashboard
- Local: Check terminal output
- Errors: Detailed in build logs

## Testing Builds

### Before Distribution

1. **Install on test device**
2. **Grant all permissions**
3. **Test background tracking**
4. **Test offline mode**
5. **Verify API connectivity**
6. **Check battery usage**

### Distribution Checklist

- [ ] Version number incremented
- [ ] API_BASE_URL points to production
- [ ] HTTPS enabled on server
- [ ] Privacy notice reviewed
- [ ] Employee consent obtained
- [ ] Test on multiple devices
- [ ] Document known issues

## File Sizes (Approximate)

| Build Type | Android APK | Android AAB | iOS IPA |
|------------|-------------|-------------|---------|
| Development | 60-80 MB | N/A | 70-90 MB |
| Preview | 35-50 MB | 30-40 MB | 45-65 MB |
| Production | 30-45 MB | 25-38 MB | 40-60 MB |

**Note:** Actual sizes depend on dependencies and assets.

## Distribution Methods

### Android

1. **Direct APK Install**
   - Email/download APK to employees
   - Enable "Unknown sources"
   - Install manually

2. **Internal Testing (Play Store)**
   - Upload to Play Console
   - Create internal testing track
   - Share link with employees

3. **Public Release (Play Store)**
   - Full Play Store submission
   - Available to all Android users

### iOS

1. **TestFlight**
   - Up to 10,000 beta testers
   - 90-day expiration
   - Easiest for internal distribution

2. **Enterprise Distribution**
   - Requires Apple Enterprise account
   - Internal distribution only
   - No App Store review

3. **Public Release (App Store)**
   - Full App Store submission
   - App Review required
   - Available to all iOS users

## Next Build

When you need to release updates:

1. **Update version in app.json:**
   ```json
   "version": "1.0.1",  // Increment
   "android": {
     "versionCode": 2   // Increment
   },
   "ios": {
     "buildNumber": "2"  // Increment
   }
   ```

2. **Rebuild:**
   ```bash
   npm run build:android
   npm run build:ios
   ```

3. **Test new build**

4. **Submit to stores** (if using stores)

## Over-The-Air Updates

For minor updates (JavaScript only):
```bash
eas update --branch production --message "Bug fixes"
```

This updates the app without rebuilding!

**Limitations:**
- JS code only
- No native code changes
- No permission changes
- No config changes
