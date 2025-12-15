#!/bin/bash

# LOCAL APK BUILD - OHNE CLOUD
# Baut APK direkt auf deinem Mac

echo "════════════════════════════════════════════════════════════"
echo "  📱 Lokaler APK Build (Ohne Cloud)"
echo "════════════════════════════════════════════════════════════"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Prüfe Voraussetzungen
echo -e "${BLUE}🔍 Prüfe Voraussetzungen...${NC}"

# Speicherplatz prüfen (mindestens 3 GB nötig)
AVAILABLE_GB=$(df -g / | tail -1 | awk '{print $4}')
if [ "$AVAILABLE_GB" -lt 3 ]; then
    echo -e "${RED}❌ Zu wenig Speicherplatz!${NC}"
    echo "   Verfügbar: ${AVAILABLE_GB}GB"
    echo "   Benötigt: mindestens 3GB"
    echo ""
    echo -e "${YELLOW}💡 Speicher freigeben:${NC}"
    echo "   rm -rf ~/.gradle ~/.npm ~/.expo"
    echo "   rm -rf ~/Library/Caches/*"
    exit 1
fi
echo -e "${GREEN}✅ Speicherplatz: ${AVAILABLE_GB}GB verfügbar${NC}"

# Java prüfen
if ! command -v java &> /dev/null; then
    echo -e "${RED}❌ Java nicht gefunden!${NC}"
    echo "   Installiere: brew install openjdk@17"
    exit 1
fi
echo -e "${GREEN}✅ Java gefunden: $(java -version 2>&1 | head -n 1)${NC}"

# Node prüfen
if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js nicht gefunden!${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Node.js gefunden: $(node --version)${NC}"

echo ""
echo -e "${BLUE}📦 Schritt 1/5: Bereinige alte Builds...${NC}"
rm -rf node_modules android ios build .expo 2>/dev/null
echo -e "${GREEN}✅ Bereinigt${NC}"

echo ""
echo -e "${BLUE}📦 Schritt 2/5: Installiere Dependencies...${NC}"
npm install
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ npm install fehlgeschlagen${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Dependencies installiert${NC}"

echo ""
echo -e "${BLUE}🔧 Schritt 3/5: Generiere Android-Projekt (prebuild)...${NC}"
npx expo prebuild --platform android --clean
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Prebuild fehlgeschlagen${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Android-Projekt generiert${NC}"

echo ""
echo -e "${BLUE}🔨 Schritt 4/5: Baue APK mit Gradle...${NC}"
cd android

# Verwende gradlew (wrapper) wenn vorhanden
if [ -f "./gradlew" ]; then
    chmod +x gradlew
    echo -e "${BLUE}   Nutze lokalen Gradle Wrapper...${NC}"
    ./gradlew assembleRelease --no-daemon
else
    echo -e "${RED}❌ Gradle Wrapper nicht gefunden${NC}"
    exit 1
fi

BUILD_STATUS=$?
cd ..

if [ $BUILD_STATUS -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Gradle Build fehlgeschlagen${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}📦 Schritt 5/5: Finde und kopiere APK...${NC}"

# Finde die generierte APK
APK_PATH=$(find android/app/build/outputs/apk -name "*.apk" -type f | grep -E 'release|debug' | head -n 1)

if [ -z "$APK_PATH" ]; then
    echo -e "${RED}❌ Keine APK gefunden!${NC}"
    echo "   Prüfe: android/app/build/outputs/apk/"
    exit 1
fi

# Erstelle Output-Ordner
mkdir -p build-output

# Kopiere APK
APK_NAME="employee-tracking-$(date +%Y%m%d-%H%M%S).apk"
cp "$APK_PATH" "build-output/$APK_NAME"

APK_SIZE=$(du -h "build-output/$APK_NAME" | cut -f1)

echo ""
echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ APK BUILD ERFOLGREICH!${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}📦 APK Details:${NC}"
echo "   Datei: ${GREEN}$APK_NAME${NC}"
echo "   Größe: ${GREEN}$APK_SIZE${NC}"
echo "   Pfad:  ${GREEN}$(pwd)/build-output/$APK_NAME${NC}"
echo ""

# Öffne Finder
open build-output

echo -e "${BLUE}🔌 Installation auf Android:${NC}"
echo ""
echo "   ${YELLOW}Methode 1: USB + ADB${NC}"
echo "   1. Verbinde Android-Gerät per USB"
echo "   2. Aktiviere USB-Debugging"
echo "   3. Führe aus:"
echo "      ${GREEN}adb install build-output/$APK_NAME${NC}"
echo ""
echo "   ${YELLOW}Methode 2: Datei-Transfer${NC}"
echo "   1. Kopiere APK per USB auf Gerät"
echo "   2. Öffne Datei-Manager auf Android"
echo "   3. Tippe auf APK-Datei"
echo "   4. Erlaube 'Unbekannte Quellen'"
echo "   5. Installiere"
echo ""
echo "   ${YELLOW}Methode 3: Cloud-Upload${NC}"
echo "   1. Lade APK zu Google Drive / Dropbox"
echo "   2. Öffne Link auf Android"
echo "   3. Installiere"
echo ""

echo -e "${BLUE}🚀 Nach Installation:${NC}"
echo "   Email:    employee1@company.com"
echo "   Password: admin123"
echo ""
echo -e "${BLUE}📊 Dashboard:${NC}"
echo "   https://employee.shahek.org/public/admin/dashboard"
echo ""

echo -e "${GREEN}🎉 Fertig!${NC}"
echo ""
