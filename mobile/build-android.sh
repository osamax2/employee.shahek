#!/bin/bash

# Build Android APK for Employee Tracking App
# This script builds a production-ready APK

set -e

echo "🤖 Building Android APK for Employee Tracking"
echo "=============================================="
echo ""

# Check if we're in the mobile directory
if [ ! -f "package.json" ]; then
    echo "❌ Error: Run this script from the mobile directory"
    exit 1
fi

# Check if EAS CLI is installed
if ! command -v eas &> /dev/null; then
    echo "📦 Installing EAS CLI..."
    npm install -g eas-cli
fi

# Check if logged into Expo
echo "🔐 Checking Expo authentication..."
if ! eas whoami &> /dev/null; then
    echo "Please login to Expo:"
    eas login
fi

# Configure project if needed
if [ ! -f "eas.json" ]; then
    echo "⚙️  Configuring EAS Build..."
    eas build:configure
fi

echo ""
echo "📋 Build Options:"
echo "   1. Development Build (debug APK, for testing)"
echo "   2. Preview Build (internal testing APK)"
echo "   3. Production Build (release APK for distribution)"
echo ""
read -p "Select build type (1-3): " BUILD_TYPE

case $BUILD_TYPE in
    1)
        PROFILE="development"
        echo "Building Development APK..."
        ;;
    2)
        PROFILE="preview"
        echo "Building Preview APK..."
        ;;
    3)
        PROFILE="production"
        echo "Building Production APK..."
        ;;
    *)
        echo "❌ Invalid option"
        exit 1
        ;;
esac

echo ""
echo "🚀 Starting build process..."
echo "   This may take 10-20 minutes..."
echo ""

# Build APK
eas build --platform android --profile $PROFILE

echo ""
echo "✅ Build complete!"
echo ""
echo "📥 Download your APK:"
echo "   1. Visit: https://expo.dev/accounts/[your-account]/projects/employee-tracking-mobile/builds"
echo "   2. Or run: eas build:list"
echo ""
echo "📲 Install on device:"
echo "   1. Download APK to your Android device"
echo "   2. Enable 'Install from unknown sources' in Settings"
echo "   3. Open the APK file to install"
echo ""
