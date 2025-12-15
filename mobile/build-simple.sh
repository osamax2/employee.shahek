#!/bin/bash

# Simple Local Android Build
# Builds a standalone APK without EAS

set -e

echo "🚀 Building Android APK Locally"
echo "================================"
echo ""

cd "$(dirname "$0")"

# Check if expo is installed
if ! command -v npx &> /dev/null; then
    echo "❌ Error: npm/npx not found. Please install Node.js"
    exit 1
fi

# Install dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Create a simple build using expo export and turtle-cli would be complex
# Instead, let's use EAS with proper ignore

echo "📱 Building with EAS Build..."
echo ""
echo "Note: If you get permission errors, run from project root:"
echo "  cd ~/shahek/mobile && npm run build:android:preview"
echo ""

# Try EAS build from current directory only
npx eas-cli build --platform android --profile preview --local --output ./employee-tracking.apk

echo ""
echo "✅ Build complete!"
echo "APK location: ./employee-tracking.apk"
