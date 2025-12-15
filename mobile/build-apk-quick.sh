#!/bin/bash

# Quick APK Build Script (Local - No EAS required)
# Builds APK using Expo's legacy build system

set -e

echo "🚀 Quick Android APK Build (Local)"
echo "===================================="
echo ""

# Check if we're in the mobile directory
if [ ! -f "package.json" ]; then
    echo "❌ Error: Run this script from the mobile directory"
    exit 1
fi

# Check if expo-cli is installed
if ! command -v expo &> /dev/null; then
    echo "📦 Installing Expo CLI..."
    npm install -g expo-cli
fi

# Login to Expo
echo "🔐 Checking Expo authentication..."
if ! expo whoami &> /dev/null; then
    echo "Please login to Expo:"
    expo login
fi

echo ""
echo "⚠️  Note: This uses Expo's legacy build service"
echo "   For production builds, use EAS Build instead (./build-android.sh)"
echo ""

read -p "Continue with legacy build? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 0
fi

echo ""
echo "🏗️  Building APK..."
echo "   This will take 10-20 minutes..."
echo ""

# Build APK
expo build:android -t apk

echo ""
echo "✅ Build queued!"
echo ""
echo "📥 Check build status:"
echo "   expo build:status"
echo ""
echo "📲 When complete, download APK:"
echo "   The build URL will be shown above"
echo "   Or visit: https://expo.dev"
echo ""
