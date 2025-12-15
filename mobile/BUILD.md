# 🚀 Quick Build Instructions

## Build Android APK (Recommended Method)

### Prerequisites (One-time setup)
```bash
# 1. Install EAS CLI
npm install -g eas-cli

# 2. Login to Expo (create free account if needed)
eas login
```

### Build APK
```bash
cd mobile

# Method 1: Use build script (interactive)
./build-android.sh

# Method 2: Use npm script
npm run build:android

# Method 3: Direct command
eas build --platform android --profile production
```

**Build time:** 10-20 minutes (cloud build)

**Download:**
After build completes, you'll get a download link or run:
```bash
eas build:list
```

### Install APK on Device

1. **Download APK** from Expo build page
2. **Transfer to Android device** (USB, email, cloud)
3. **Enable installation:**
   - Settings → Security → Unknown Sources → Enable
4. **Open APK file** on device to install

---

## Build iOS App

### Prerequisites
- **Apple Developer Account** ($99/year)
- **macOS** (for local builds) OR use EAS cloud build

### Build IPA
```bash
cd mobile

# Method 1: Use build script
./build-ios.sh

# Method 2: Use npm script
npm run build:ios

# Method 3: Direct command
eas build --platform ios --profile production
```

### Install on iPhone

**Option 1: TestFlight (Easiest)**
```bash
# After build completes
eas submit --platform ios

# Then:
# 1. Open App Store Connect
# 2. Invite yourself as tester
# 3. Install TestFlight app on iPhone
# 4. Accept invitation and install
```

**Option 2: Direct Install**
- Requires development provisioning profile
- Use Apple Configurator or Xcode

---

## Quick Test Builds

For testing without full production build:

### Android Preview
```bash
npm run build:android:preview
```
- Faster build
- Internal testing only
- No Play Store submission needed

### iOS Simulator
```bash
npm run build:ios:preview
```
- For testing on simulator
- No device needed

---

## Environment Configuration

**IMPORTANT:** Before building, update `.env`:

```bash
cd mobile
cp .env.example .env
nano .env  # or use any text editor
```

Set your production server:
```
API_BASE_URL=https://your-production-server.com/api
COMPANY_NAME=Your Company Name
```

**Note:** Changes to `.env` require a new build.

---

## Check Build Status

```bash
# List all builds
eas build:list

# Check specific build
eas build:view BUILD_ID
```

Or visit: https://expo.dev → Projects → Employee Tracking → Builds

---

## Troubleshooting

### "eas: command not found"
```bash
npm install -g eas-cli
```

### "Not logged in"
```bash
eas login
```

### "Project not configured"
```bash
eas build:configure
```

### Build failed
```bash
# Check logs
eas build:list
# Click on failed build to see error details
```

### APK won't install
- Enable "Unknown sources" in Android settings
- Uninstall old version first if signature mismatch

---

## Build Options

### Development Build (for testing)
```bash
eas build -p android --profile development
```
- Debug mode
- Larger file size
- Faster build

### Preview Build (internal testing)
```bash
eas build -p android --profile preview
```
- Optimized
- Not for store submission

### Production Build (for distribution)
```bash
eas build -p android --profile production
```
- Fully optimized
- Smallest size
- Ready for Play Store/App Store

---

## Store Submission

### Google Play Store
```bash
# 1. Build AAB (required for Play Store)
# Edit eas.json: change "apk" to "app-bundle"

# 2. Build
eas build -p android --profile production

# 3. Submit
eas submit -p android
```

### Apple App Store
```bash
# 1. Build
eas build -p ios --profile production

# 2. Submit
eas submit -p ios
```

---

## Cost

**Expo EAS Build:**
- Free tier: Limited builds per month
- Paid tier: $29/month for unlimited builds

**App Stores:**
- Google Play: $25 one-time registration
- Apple App Store: $99/year

**Alternative:** Use free tier for initial builds, upgrade if needed.

---

## Next Steps After Building

1. **Test thoroughly** on real devices
2. **Update version** in app.json before next build
3. **Document changes** for app store updates
4. **Get consent forms signed** by employees
5. **Deploy API server** to production
6. **Configure HTTPS** on server
7. **Update API_BASE_URL** in mobile app
8. **Rebuild** with production settings
9. **Distribute to employees**

---

## Full Documentation

For complete build guide with troubleshooting:
- See [BUILD_GUIDE.md](BUILD_GUIDE.md)

For project documentation:
- See [README.md](README.md)
- See [../README.md](../README.md) (main project)

---

## Quick Command Reference

```bash
# Install EAS CLI
npm install -g eas-cli

# Login
eas login

# Build Android
eas build -p android

# Build iOS
eas build -p ios

# Check builds
eas build:list

# Submit to stores
eas submit -p android
eas submit -p ios
```

---

**Need help?** Check BUILD_GUIDE.md for detailed instructions!
