# Building Mobile Apps - Complete Guide

## Overview

This guide covers building Android APK and iOS IPA files for the Employee Tracking mobile app.

## Build Methods

### Method 1: EAS Build (Recommended)

**Advantages:**
- ✅ Cloud-based building (no local Android Studio/Xcode needed)
- ✅ Consistent builds across all platforms
- ✅ Automatic code signing
- ✅ Works on any OS (Windows, macOS, Linux)

**Requirements:**
- Expo account (free)
- EAS CLI installed

### Method 2: Local Build

**Advantages:**
- ✅ No cloud dependency
- ✅ Faster iteration for development

**Requirements:**
- Android: Android Studio + SDK
- iOS: macOS + Xcode (iOS builds only work on Mac)

---

## 🤖 Building Android APK

### Quick Start (EAS Build)

```bash
cd mobile

# Install EAS CLI (one time)
npm install -g eas-cli

# Login to Expo
eas login

# Build APK
./build-android.sh
```

Or manually:
```bash
# Preview build (for testing)
eas build --platform android --profile preview

# Production build
eas build --platform android --profile production
```

### Build Profiles

**Development:** Debug APK for testing
```bash
eas build -p android --profile development
```

**Preview:** Internal testing APK
```bash
eas build -p android --profile preview
```

**Production:** Release APK for distribution
```bash
eas build -p android --profile production
```

### Download & Install

1. **Check build status:**
   ```bash
   eas build:list
   ```

2. **Download APK:**
   - Visit: https://expo.dev → Projects → Builds
   - Or click the link shown in terminal

3. **Install on Android device:**
   - Transfer APK to device
   - Enable "Install from unknown sources" in Settings
   - Open APK file to install

### Alternative: Quick APK Build (Legacy)

```bash
cd mobile
./build-apk-quick.sh
```

Or manually:
```bash
expo build:android -t apk
expo build:status
```

---

## 🍎 Building iOS App

### Prerequisites

**Apple Developer Account:**
- Sign up at: https://developer.apple.com/programs/
- Cost: $99/year

**Required:**
- Bundle Identifier (e.g., com.yourcompany.employeetracking)
- App ID
- Provisioning Profile
- Distribution Certificate

### Quick Start (EAS Build)

```bash
cd mobile

# Build iOS app
./build-ios.sh
```

Or manually:
```bash
# Setup credentials (first time)
eas credentials

# Build for TestFlight/App Store
eas build --platform ios --profile production

# Build for simulator (testing only)
eas build --platform ios --profile preview
```

### Submitting to TestFlight

```bash
# After successful build
eas submit --platform ios
```

This will:
1. Upload IPA to App Store Connect
2. Create TestFlight build
3. Allow you to invite beta testers

### Installing on Device (Development)

**Option 1: TestFlight (Recommended)**
- Submit build to TestFlight
- Invite yourself as tester
- Install via TestFlight app

**Option 2: Development Profile**
```bash
# Build with development profile
eas build -p ios --profile development

# Install via Apple Configurator or Xcode
```

---

## 📦 Configuration

### App Identifiers

Edit [app.json](app.json):

**iOS:**
```json
"ios": {
  "bundleIdentifier": "com.yourcompany.employeetracking"
}
```

**Android:**
```json
"android": {
  "package": "com.yourcompany.employeetracking"
}
```

### App Name & Version

```json
{
  "name": "Employee Tracking",
  "version": "1.0.0"
}
```

### Environment Variables

Before building, configure [.env](.env):

```bash
API_BASE_URL=https://your-production-server.com/api
COMPANY_NAME=Your Company Name
COMPANY_LOGO_URL=https://your-company.com/logo.png
```

**Note:** Environment variables are baked into the build. Change requires rebuild.

---

## 🔐 Code Signing

### Android

**Automatic (EAS Build):**
EAS handles keystore generation and signing automatically.

**Manual:**
```bash
# Generate keystore
keytool -genkey -v -keystore release.keystore \
  -alias release -keyalg RSA -keysize 2048 -validity 10000

# Upload to EAS
eas credentials
```

### iOS

**Automatic (EAS Build):**
```bash
eas credentials
# Follow prompts to generate certificates and profiles
```

**Manual:**
1. Go to Apple Developer Portal
2. Create App ID
3. Create Distribution Certificate
4. Create Provisioning Profile
5. Upload to EAS

---

## 🏪 Publishing to Stores

### Google Play Store (Android)

1. **Create Google Play Console account** ($25 one-time fee)

2. **Create app listing:**
   - Go to: https://play.google.com/console
   - Create Application
   - Fill in app details, screenshots, description

3. **Build AAB (required for Play Store):**
   ```bash
   # Edit eas.json
   "production": {
     "android": {
       "buildType": "app-bundle"  // Change from "apk" to "app-bundle"
     }
   }
   
   # Build
   eas build -p android --profile production
   ```

4. **Submit:**
   ```bash
   eas submit -p android
   ```

### Apple App Store (iOS)

1. **Create App Store Connect listing:**
   - Go to: https://appstoreconnect.apple.com
   - Create New App
   - Fill in metadata, screenshots

2. **Build and submit:**
   ```bash
   # Build production IPA
   eas build -p ios --profile production
   
   # Submit to App Store
   eas submit -p ios
   ```

3. **App Review:**
   - Provide test account (if login required)
   - Explain location usage clearly
   - Include privacy policy URL
   - Wait for review (2-7 days typically)

---

## 📱 Testing Builds

### Android

**Install APK directly:**
```bash
# Via USB (ADB)
adb install app-release.apk

# Or transfer to device and open
```

**Internal Testing:**
- Upload to Google Play Console → Internal Testing track
- Share link with testers

### iOS

**TestFlight (Recommended):**
```bash
eas submit -p ios
# Then invite testers in App Store Connect
```

**Ad Hoc Distribution:**
- Build with ad hoc provisioning profile
- Add device UDIDs to profile
- Distribute IPA via email/web

---

## 🛠️ Troubleshooting

### Build Failed

**Check logs:**
```bash
eas build:list
# Click on build to see detailed logs
```

**Common issues:**

1. **Invalid bundle identifier:**
   - Ensure it matches Apple Developer Portal
   - Format: com.company.appname (lowercase, no spaces)

2. **Provisioning profile mismatch:**
   ```bash
   eas credentials
   # Re-sync credentials
   ```

3. **Build timeout:**
   - Large dependencies may cause timeout
   - Optimize package.json

### APK Not Installing

1. **Enable unknown sources:**
   - Settings → Security → Unknown Sources → Enable

2. **Signature mismatch:**
   - Uninstall old version first
   - Or use same keystore

### iOS Build Issues

1. **Certificate expired:**
   ```bash
   eas credentials
   # Generate new certificate
   ```

2. **Device not in provisioning profile:**
   - Add device UDID to profile in Apple Developer Portal

### App Store Rejection

**Location Permission:**
- Provide clear explanation in Info.plist
- Include privacy policy
- Demonstrate purpose in screenshots

**Background Location:**
- Explain business justification
- Show user-visible indicator (notification)
- Document consent process

---

## 📊 Build Status

Check build progress:
```bash
# List all builds
eas build:list

# Watch current build
eas build:view [build-id]
```

Web dashboard:
- Visit: https://expo.dev
- Navigate to your project
- Click "Builds" tab

---

## 🔄 Update Strategy

### Over-The-Air (OTA) Updates

For minor updates without rebuilding:

```bash
# Publish update
eas update --branch production --message "Bug fixes"
```

**Note:** Only works for JavaScript code changes, not native changes.

### Full Rebuild Required For:
- Native module changes
- Permission changes
- Config changes (app.json)
- New native dependencies

---

## 📋 Pre-Release Checklist

Before distributing to employees:

**Functionality:**
- [ ] Location tracking works in background
- [ ] Offline mode caches and retries
- [ ] Token refresh works automatically
- [ ] Battery drain acceptable
- [ ] Permissions handled gracefully

**Configuration:**
- [ ] API_BASE_URL points to production server
- [ ] HTTPS enabled
- [ ] Company name/logo updated
- [ ] Version number incremented

**Compliance:**
- [ ] Privacy notice reviewed by legal
- [ ] Employee consent process documented
- [ ] Location usage descriptions clear
- [ ] Data retention policy defined

**App Store:**
- [ ] Screenshots prepared (5-8 per platform)
- [ ] App description written
- [ ] Privacy policy URL ready
- [ ] Support email/website listed

---

## 💡 Tips

**Faster Development:**
```bash
# Use Expo Go for quick testing (no build needed)
npm start
# Scan QR code with Expo Go app
```

**Cache Builds:**
EAS caches dependencies between builds for faster rebuilds.

**Local Development:**
```bash
# iOS simulator (Mac only)
npm run ios

# Android emulator
npm run android
```

**Environment-Specific Builds:**
Create multiple profiles in eas.json:
```json
"staging": {
  "env": {
    "API_BASE_URL": "https://staging-api.com"
  }
},
"production": {
  "env": {
    "API_BASE_URL": "https://api.com"
  }
}
```

---

## 🆘 Support

**Expo Documentation:**
- EAS Build: https://docs.expo.dev/build/introduction/
- Submitting: https://docs.expo.dev/submit/introduction/

**Community:**
- Expo Forums: https://forums.expo.dev
- Discord: https://chat.expo.dev

**This Project:**
- See [README.md](README.md) for full documentation
- Check [TEST_PLAN.md](../TEST_PLAN.md) for testing guide

---

## 📄 Quick Reference

```bash
# Android APK
eas build -p android --profile production

# iOS App
eas build -p ios --profile production

# Submit to stores
eas submit -p android
eas submit -p ios

# Check status
eas build:list

# View logs
eas build:view [build-id]

# Update app (OTA)
eas update --branch production
```

---

**Last Updated:** December 14, 2025  
**Build Tools:** EAS Build + Expo CLI
