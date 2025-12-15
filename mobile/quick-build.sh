#!/bin/bash

# SCHNELLER APK BUILD - Mit Debugging

set -e

echo "🚀 Schneller APK Build"
echo ""

cd /Users/osamaalabaji/shahek/mobile

# 1. Cleanup
echo "🧹 Cleanup..."
rm -rf android build-output

# 2. Prebuild
echo "📦 Prebuild (2 Min)..."
npx expo prebuild --platform android --clean

# 3. Build APK
echo "🔨 Build APK (5-10 Min)..."
cd android

# Setze Java Heap größer für schnelleren Build
export GRADLE_OPTS="-Xmx4096m -XX:MaxMetaspaceSize=512m"

# Build mit Progress-Output
./gradlew assembleRelease \
    --no-daemon \
    --stacktrace \
    --warning-mode all \
    2>&1 | while IFS= read -r line; do
        echo "$line"
        # Zeige wichtige Schritte
        echo "$line" | grep -E "^> Task|BUILD SUCCESSFUL|BUILD FAILED" || true
    done

cd ..

# 4. Finde APK
echo ""
echo "🔍 Suche APK..."
APK=$(find android -name "*.apk" -type f 2>/dev/null | head -1)

if [ -n "$APK" ]; then
    mkdir -p build-output
    cp "$APK" build-output/app.apk
    
    echo ""
    echo "✅ APK FERTIG!"
    echo ""
    ls -lh build-output/app.apk
    echo ""
    echo "📱 Installation:"
    echo "   adb install build-output/app.apk"
    
    open build-output
else
    echo "❌ Keine APK gefunden"
    exit 1
fi
