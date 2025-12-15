#!/bin/bash

# Prepare Laravel for cPanel Deployment
# Run this script before uploading to cPanel

set -e

echo "📦 Preparing Laravel App for cPanel Deployment"
echo "=============================================="
echo ""

# Check if we're in the server directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found. Run this script from the server directory"
    exit 1
fi

# Check if artisan exists, if not, we'll create a basic one
if [ ! -f "artisan" ]; then
    echo "⚠️  artisan file not found, creating it..."
    cat > artisan << 'EOF'
#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);
EOF
    chmod +x artisan
    echo "   ✅ artisan file created"
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "📚 Installing production dependencies..."
    if command -v composer &> /dev/null; then
        composer install --optimize-autoloader --no-dev
    else
        echo "   ⚠️  Composer not found. You'll need to run 'composer install' on cPanel"
        echo "   Or upload vendor folder separately"
    fi
else
    echo "📚 Dependencies already installed"
fi

# Create production .env if it doesn't exist
if [ ! -f ".env" ]; then
    echo "⚙️  Creating .env file..."
    cp .env.example .env
    echo "   ⚠️  Remember to edit .env with your cPanel database credentials!"
else
    echo "   .env already exists"
fi

# Clear all caches (if vendor exists)
if [ -d "vendor" ]; then
    echo ""
    echo "🧹 Clearing caches..."
    php artisan cache:clear 2>/dev/null || echo "   ⚠️  Cache clear skipped (normal for fresh install)"
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
else
    echo ""
    echo "⏭️  Skipping cache clear (vendor not installed yet)"
fi

# Set proper permissions
echo ""
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Create deployment package
echo ""
echo "📦 Creating deployment package..."
cd ..
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
ZIPFILE="employee-tracking-server_${TIMESTAMP}.zip"

# Exclude unnecessary files
zip -r "$ZIPFILE" server/ \
    -x "*.git*" \
    -x "*node_modules*" \
    -x "*/storage/logs/*" \
    -x "*/storage/framework/cache/data/*" \
    -x "*/storage/framework/sessions/*" \
    -x "*/storage/framework/views/*" \
    -x "*.DS_Store" \
    -x "*/tests/*"

echo ""
echo "✅ Deployment package created: $ZIPFILE"
echo ""
echo "📋 Next steps:"
echo "   1. Login to your cPanel"
echo "   2. Go to File Manager"
echo "   3. Navigate to public_html/"
echo "   4. Upload $ZIPFILE"
echo "   5. Extract the ZIP file"
echo "   6. Follow the guide in CPANEL_DEPLOY.md"
echo ""
echo "⚠️  Important: Before uploading, edit server/.env with your cPanel database credentials!"
echo ""
echo "📖 Full guide: server/CPANEL_DEPLOY.md"
echo ""
