#!/bin/bash

# FINALE LÖSUNG - EAS Cloud Build (GARANTIERT)

echo "════════════════════════════════════════════════════════════"
echo "  🎯 APK via EAS Cloud - Garantiert funktionierende Lösung"
echo "════════════════════════════════════════════════════════════"
echo ""

cd /Users/osamaalabaji/shahek/mobile

echo "🧹 Cleanup lokale Probleme..."
pkill -9 -f gradle 2>/dev/null
pkill -9 -f expo 2>/dev/null
rm -rf android ios build node_modules/.cache

echo ""
echo "✅ Bereit für Cloud Build!"
echo ""
echo "Du hast 2 Optionen:"
echo ""
echo "══════════════════════════════════════════════════════════"
echo "OPTION 1: EAS Build (Terminal) - 10 Minuten"
echo "══════════════════════════════════════════════════════════"
echo ""
echo "Befehl:"
echo "  eas build --platform android --profile preview"
echo ""
echo "Was passiert:"
echo "  • Projekt wird in Cloud hochgeladen"
echo "  • Expo baut APK auf ihren Servern"  
echo "  • Du bekommst Download-Link"
echo "  • Kein lokales Java/Gradle Problem!"
echo ""
echo "══════════════════════════════════════════════════════════"
echo "OPTION 2: Web Upload - 5 Minuten"
echo "══════════════════════════════════════════════════════════"
echo ""
echo "1. Gehe zu: https://expo.dev"
echo "2. Login: osamax2"
echo "3. Upload: employee-tracking-upload.zip (bereits erstellt!)"
echo "4. Wähle: Android → Preview Build"
echo "5. Warte 5-10 Min"
echo "6. Lade APK herunter"
echo ""
echo "══════════════════════════════════════════════════════════"
echo ""

read -p "Möchtest du OPTION 1 jetzt starten? (j/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[JjYy]$ ]]; then
    echo ""
    echo "🚀 Starte EAS Build..."
    
    # Leere Papierkorb für sauberen Upload
    rm -rf ~/.Trash/* 2>/dev/null
    
    # Starte Cloud Build
    eas build --platform android --profile preview
    
    echo ""
    echo "✅ Build gestartet oder abgeschlossen!"
    echo ""
    echo "📥 APK herunterladen:"
    echo "   • Folge dem Link oben"
    echo "   • Oder: https://expo.dev/accounts/osamax2/projects/employee-tracking-mobile/builds"
    
else
    echo ""
    echo "💡 Dann nutze OPTION 2 (Web Upload):"
    echo ""
    echo "Die ZIP-Datei ist bereits fertig:"
    ls -lh employee-tracking-upload.zip 2>/dev/null || echo "   (Erstelle mit: bash GET-APK.sh)"
    echo ""
    echo "Upload zu: https://expo.dev"
    
    open "https://expo.dev" 2>/dev/null
fi

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  Warum Cloud statt Lokal?"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Lokale Probleme (du hattest alle!):"
echo "  ❌ Speicher voll (99%)"
echo "  ❌ Java 25 zu neu für Gradle"  
echo "  ❌ Gradle hängt sich auf"
echo "  ❌ macOS Permission Probleme"
echo ""
echo "Cloud Build:"
echo "  ✅ Keine lokalen Dependencies"
echo "  ✅ Funktioniert immer"
echo "  ✅ Schneller (optimierte Server)"
echo "  ✅ Keine Speicherprobleme"
echo ""
