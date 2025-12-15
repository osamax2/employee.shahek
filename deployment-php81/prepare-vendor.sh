#!/bin/bash

echo "📦 Creating vendor package for cPanel (no SSH needed)"
echo "===================================================="
echo ""

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found!"
    echo "   Please run this script from the server/ directory"
    exit 1
fi

# Install dependencies locally
echo "📚 Installing Composer dependencies locally..."
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found on your system!"
    echo ""
    echo "Please install Composer first:"
    echo "  macOS: brew install composer"
    echo "  Or visit: https://getcomposer.org/download/"
    exit 1
fi

# Install production dependencies
composer install --no-dev --optimize-autoloader --no-interaction

if [ ! -d "vendor" ]; then
    echo "❌ Failed to create vendor directory"
    exit 1
fi

echo "✅ Dependencies installed"
echo ""

# Create vendor ZIP
echo "📦 Creating vendor.zip..."
cd ..
zip -r vendor.zip server/vendor/ -x "*.git*" > /dev/null 2>&1

if [ -f "vendor.zip" ]; then
    FILESIZE=$(du -h "vendor.zip" | cut -f1)
    echo "✅ Created: vendor.zip (${FILESIZE})"
    echo ""
    echo "📋 Upload Instructions:"
    echo "   1. Login to cPanel File Manager"
    echo "   2. Navigate to: /public_html/server/"
    echo "   3. Upload: vendor.zip"
    echo "   4. Extract the ZIP (right-click → Extract)"
    echo "   5. Delete vendor.zip after extraction"
    echo "   6. Visit: https://employee.shahek.org/admin/dashboard"
    echo ""
    echo "⚠️  File size: ${FILESIZE} - Upload may take a few minutes"
else
    echo "❌ Failed to create vendor.zip"
    exit 1
fi
