#!/bin/bash

# Zeige Build-Fortschritt

echo "🔍 Prüfe Build-Status..."
echo ""

# Prüfe ob Prozess läuft
if pgrep -f "BUILD-LOCAL.sh" > /dev/null; then
    echo "✅ Build läuft..."
    
    # Zeige was gerade passiert
    if [ -d "node_modules" ]; then
        echo "   ✅ Dependencies installiert"
    else
        echo "   ⏳ Installiere Dependencies..."
    fi
    
    if [ -d "android" ]; then
        echo "   ✅ Android-Projekt generiert"
        
        if [ -d "android/app/build" ]; then
            echo "   ⏳ Gradle baut APK..."
        fi
    else
        echo "   ⏳ Warte auf Prebuild..."
    fi
    
    # Prüfe ob APK schon da ist
    if [ -d "build-output" ]; then
        APK_COUNT=$(find build-output -name "*.apk" 2>/dev/null | wc -l)
        if [ $APK_COUNT -gt 0 ]; then
            echo ""
            echo "🎉 APK FERTIG!"
            ls -lh build-output/*.apk
        fi
    fi
else
    echo "❌ Kein Build läuft"
    
    # Prüfe ob APK existiert
    if [ -d "build-output" ]; then
        APK_COUNT=$(find build-output -name "*.apk" 2>/dev/null | wc -l)
        if [ $APK_COUNT -gt 0 ]; then
            echo ""
            echo "✅ Letzte APKs:"
            ls -lht build-output/*.apk | head -5
        fi
    fi
fi

echo ""
