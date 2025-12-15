#!/bin/bash

echo "🔧 Fixing 'too many open files' issue for Expo"
echo "==============================================="
echo ""

# Check if Homebrew is installed
if ! command -v brew &> /dev/null; then
    echo "📦 Installing Homebrew first..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

# Install Watchman
echo "📦 Installing Watchman (better file watcher for macOS)..."
brew install watchman

echo ""
echo "✅ Watchman installed!"
echo ""
echo "Now try:"
echo "  cd mobile"
echo "  npm start"
echo ""
