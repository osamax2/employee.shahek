#!/bin/bash

# Build iOS App for Employee Tracking
# This script builds an iOS app (IPA file)

set -e

echo "🍎 Building iOS App for Employee Tracking"
echo "=========================================="
echo ""

# Check if we're in the mobile directory
if [ ! -f "package.json" ]; then
    echo "❌ Error: Run this script from the mobile directory"
    exit 1
fi

# Check if on macOS
if [[ "$OSTYPE" != "darwin"* ]]; then
    echo "❌ Error: iOS builds require macOS"
    echo "   Use EAS Build cloud service instead:"
    echo "   eas build --platform ios"
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
echo "   1. Development Build (for testing on device)"
echo "   2. Preview Build (internal testing)"
echo "   3. Production Build (for App Store)"
echo ""
read -p "Select build type (1-3): " BUILD_TYPE

case $BUILD_TYPE in
    1)
        PROFILE="development"
        echo "Building Development IPA..."
        ;;
    2)
        PROFILE="preview"
        echo "Building Preview IPA..."
        ;;
    3)
        PROFILE="production"
        echo "Building Production IPA..."
        ;;
    *)
        echo "❌ Invalid option"
        exit 1
        ;;
esac

echo ""
echo "⚠️  iOS Build Requirements:"
echo "   - Apple Developer Account ($99/year)"
echo "   - Provisioning Profile"
echo "   - Distribution Certificate"
echo ""

read -p "Do you have these set up? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "📚 Setup Instructions:"
    echo "   1. Join Apple Developer Program: https://developer.apple.com/programs/"
    echo "   2. Create App ID in Apple Developer Portal"
    echo "   3. Create Provisioning Profile"
    echo "   4. Run: eas credentials"
    echo ""
    exit 0
fi

echo ""
echo "🚀 Starting build process..."
echo "   This may take 15-30 minutes..."
echo ""

# Build iOS app
eas build --platform ios --profile $PROFILE

echo ""
echo "✅ Build complete!"
echo ""
echo "📥 Download your IPA:"
echo "   1. Visit: https://expo.dev/accounts/[your-account]/projects/employee-tracking-mobile/builds"
echo "   2. Or run: eas build:list"
echo ""
echo "📲 Install on device:"
echo "   Option 1: TestFlight (recommended)"
echo "     - Submit to TestFlight: eas submit --platform ios"
echo "     - Invite testers via App Store Connect"
echo ""
echo "   Option 2: Direct Install"
echo "     - Use Apple Configurator"
echo "     - Or use development provisioning profile"
echo ""
