#!/bin/bash

# Employee Tracking Server - Quick Setup Script
# This script sets up the Laravel server environment

set -e  # Exit on error

echo "🚀 Employee Tracking Server Setup"
echo "=================================="
echo ""

# Check PHP version
echo "📋 Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "   PHP version: $PHP_VERSION"

if ! php -v | grep -q "PHP 8"; then
    echo "❌ Error: PHP 8.1+ required"
    exit 1
fi

# Check Composer
echo ""
echo "📋 Checking Composer..."
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer not installed"
    echo "   Install from: https://getcomposer.org/"
    exit 1
fi
echo "   Composer installed ✓"

# Install dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install

# Setup environment
echo ""
echo "⚙️  Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "   Created .env file"
else
    echo "   .env already exists"
fi

# Generate app key
echo ""
echo "🔑 Generating application key..."
php artisan key:generate

# Generate JWT secret
echo ""
echo "🔐 Generating JWT secret..."
php artisan jwt:secret || echo "   Note: Run manually if jwt:secret command fails"

# Database setup
echo ""
echo "💾 Database setup"
read -p "   Have you created the MySQL database? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "   Running migrations..."
    php artisan migrate --force
    
    read -p "   Seed database with test data? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed
        echo "   Database seeded ✓"
    fi
else
    echo "   ⚠️  Skipping migrations. Run manually:"
    echo "      php artisan migrate"
fi

# Create storage link
echo ""
echo "🔗 Creating storage link..."
php artisan storage:link || echo "   Storage link already exists"

# Set permissions
echo ""
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo ""
echo "✅ Setup complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Edit .env and configure your database credentials"
echo "   2. Run: php artisan migrate (if not done above)"
echo "   3. Run: php artisan serve"
echo "   4. Open: http://localhost:8000/admin/dashboard"
echo ""
echo "🔧 Useful commands:"
echo "   php artisan serve              - Start dev server"
echo "   php artisan migrate:fresh      - Reset database"
echo "   php artisan db:seed            - Seed test data"
echo "   php artisan route:list         - View all routes"
echo "   php artisan tinker             - Open REPL"
echo ""
