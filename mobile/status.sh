#!/bin/bash

# BUILD STATUS MONITOR

echo "🔍 Build-Status prüfen..."
echo ""

# Prüfe ob Build läuft
if pgrep -f "BUILD-LOCAL.sh" > /dev/null; then
    echo "✅ BUILD LÄUFT..."
    echo ""
    
    # Zeige letzte Zeilen aus Log
    if [ -f "build.log" ]; then
        echo "📋 Letzte Aktivität:"
        tail -15 build.log | grep -E "✅|⏳|🔧|📦|🔨|Schritt|Installing|Building" || tail -10 build.log
    fi
    
    echo ""
    echo "💡 Live-Log anzeigen:"
    echo "   tail -f build.log"
    
else
    echo "⚠️  Build läuft nicht mehr"
    echo ""
    
    # Prüfe ob APK erstellt wurde
    if [ -d "build-output" ] && [ -n "$(ls -A build-output/*.apk 2>/dev/null)" ]; then
        echo "🎉 APK GEFUNDEN!"
        echo ""
        ls -lh build-output/*.apk
        echo ""
        echo "✅ Installation:"
        echo "   adb install build-output/$(ls build-output/*.apk | head -1 | xargs basename)"
    else
        echo "❌ Keine APK gefunden"
        echo ""
        echo "📋 Letzte Log-Zeilen:"
        if [ -f "build.log" ]; then
            tail -30 build.log | grep -E "ERROR|error|failed|Failed|❌" || tail -20 build.log
        fi
    fi
fi

echo ""
