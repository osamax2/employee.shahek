#!/bin/bash

# Start Expo with increased file limit
echo "🚀 Starting Expo Development Server"
echo "===================================="
echo ""

cd "$(dirname "$0")"

# Increase file descriptor limit
ulimit -n 10000

echo "✅ File limit increased to 10000"
echo "📱 Starting Expo..."
echo ""

npm start
