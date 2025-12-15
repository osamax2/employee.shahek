#!/bin/bash

# Employee Tracking - Lokaler APK Build (ohne Cloud)
# Erstellt APK direkt auf deinem Mac

set -e

echo "════════════════════════════════════════════════════════════"
echo "  Employee Tracking - Lokaler APK Build"
echo "════════════════════════════════════════════════════════════"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${YELLOW}⚠️  WICHTIG: Lokaler Build benötigt:${NC}"
echo "   1. Android SDK installiert"
echo "   2. ANDROID_HOME Umgebungsvariable gesetzt"
echo "   3. Java JDK 11 oder höher"
echo ""
read -p "Fortfahren? (j/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[JjYy]$ ]]; then
    echo -e "${RED}Abgebrochen.${NC}"
    exit 1
fi

echo -e "${BLUE}📦 Schritt 1/6:${NC} Bereinige Projekt..."
rm -rf node_modules package-lock.json .expo android/.gradle build 2>/dev/null || true
echo -e "${GREEN}✅ Bereinigt${NC}"
echo ""

echo -e "${BLUE}📦 Schritt 2/6:${NC} Installiere Dependencies..."
npm install
echo ""

echo -e "${BLUE}🔧 Schritt 3/6:${NC} Installiere EAS CLI..."
npm install -g eas-cli
echo ""

echo -e "${BLUE}🔐 Schritt 4/6:${NC} Terminal Berechtigungen..."
echo -e "${YELLOW}⚠️  Gib Terminal 'Full Disk Access' in den Systemeinstellungen!${NC}"
echo "   Systemeinstellungen → Datenschutz & Sicherheit → Full Disk Access → Terminal"
echo ""
read -p "Berechtigungen erteilt? (j/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[JjYy]$ ]]; then
    echo -e "${YELLOW}⚠️  Build könnte fehlschlagen ohne Berechtigungen${NC}"
fi
echo ""

echo -e "${BLUE}🏗️  Schritt 5/6:${NC} Erstelle Android-Ordner..."
npx expo prebuild --platform android --clean
echo ""

echo -e "${BLUE}🔨 Schritt 6/6:${NC} Baue APK..."
cd android
./gradlew assembleRelease
cd ..

echo ""
echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ APK Build erfolgreich!${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""

# Finde APK-Datei
APK_FILE=$(find android/app/build/outputs/apk -name "*.apk" | head -n 1)

if [ -f "$APK_FILE" ]; then
    APK_SIZE=$(du -h "$APK_FILE" | cut -f1)
    echo -e "${GREEN}📦 APK erstellt:${NC}"
    echo -e "   Datei: ${BLUE}$APK_FILE${NC}"
    echo -e "   Größe: ${BLUE}$APK_SIZE${NC}"
    echo ""
    
    # Kopiere in Build-Output Ordner
    mkdir -p build-output
    cp "$APK_FILE" build-output/employee-tracking.apk
    echo -e "${GREEN}📥 APK kopiert nach:${NC} ${BLUE}build-output/employee-tracking.apk${NC}"
    echo ""
    
    # Öffne Finder
    open build-output
    
    echo -e "${BLUE}🔌 Installation:${NC}"
    echo "   1. Verbinde Android-Gerät per USB"
    echo "   2. Aktiviere USB-Debugging auf dem Gerät"
    echo "   3. Führe aus: adb install build-output/employee-tracking.apk"
    echo ""
    echo "   ODER:"
    echo "   1. Übertrage build-output/employee-tracking.apk per USB"
    echo "   2. Öffne Datei auf Android-Gerät"
    echo "   3. Erlaube Installation aus unbekannten Quellen"
else
    echo -e "${RED}❌ Keine APK gefunden!${NC}"
    echo "   Prüfe Fehler oben im Log"
fi

echo ""
