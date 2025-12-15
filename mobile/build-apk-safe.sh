#!/bin/bash

# Employee Tracking - APK Build (Umgeht macOS Permission Fehler)
# Lädt Projekt temporär in sicheren Ordner

echo "════════════════════════════════════════════════════════════"
echo "  Employee Tracking - Sicherer Cloud Build"
echo "════════════════════════════════════════════════════════════"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo -e "${BLUE}🔐 Prüfe EAS Login...${NC}"
if ! eas whoami &> /dev/null; then
    echo -e "${YELLOW}⚠️  Nicht eingeloggt. Führe Login durch...${NC}"
    eas login
fi

EAS_USER=$(eas whoami 2>/dev/null | tail -n 1)
echo -e "${GREEN}✅ Eingeloggt als: $EAS_USER${NC}"
echo ""

echo -e "${BLUE}📂 Erstelle temporären Build-Ordner (umgeht Trash-Fehler)...${NC}"
TEMP_DIR="/tmp/employee-tracking-build-$(date +%s)"
mkdir -p "$TEMP_DIR"

# Kopiere nur notwendige Dateien
echo -e "${BLUE}📋 Kopiere Projekt-Dateien...${NC}"
cd "$SCRIPT_DIR"

# Kopiere Dateien
rsync -av --progress \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.expo' \
    --exclude='android' \
    --exclude='ios' \
    --exclude='build' \
    --exclude='.DS_Store' \
    ./ "$TEMP_DIR/"

echo -e "${GREEN}✅ Projekt kopiert nach: $TEMP_DIR${NC}"
echo ""

cd "$TEMP_DIR"

echo -e "${BLUE}📦 Installiere Dependencies...${NC}"
npm install
echo ""

echo -e "${BLUE}🏗️  Starte EAS Build...${NC}"
echo -e "${YELLOW}⏱️  Build läuft in der Cloud (5-10 Minuten)...${NC}"
echo ""

# Starte Build
eas build --platform android --profile preview --non-interactive

BUILD_STATUS=$?

if [ $BUILD_STATUS -eq 0 ]; then
    echo ""
    echo "════════════════════════════════════════════════════════════"
    echo -e "${GREEN}✅ Build erfolgreich gestartet!${NC}"
    echo "════════════════════════════════════════════════════════════"
    echo ""
    echo -e "${BLUE}📥 APK Status prüfen:${NC}"
    echo "   eas build:list"
    echo ""
    echo -e "${BLUE}🌐 Oder im Browser:${NC}"
    echo "   https://expo.dev/accounts/$EAS_USER/projects/employee-tracking-mobile/builds"
    echo ""
else
    echo ""
    echo -e "${RED}❌ Build fehlgeschlagen${NC}"
    echo ""
    echo -e "${YELLOW}💡 Alternative: Build direkt auf Expo Website:${NC}"
    echo "   1. Gehe zu: https://expo.dev/accounts/$EAS_USER/projects"
    echo "   2. Wähle: employee-tracking-mobile"
    echo "   3. Klicke: 'Create Build'"
    echo "   4. Wähle: Android → preview"
    echo "   5. Starte Build"
fi

# Cleanup
echo ""
echo -e "${BLUE}🧹 Räume auf...${NC}"
rm -rf "$TEMP_DIR"
echo -e "${GREEN}✅ Temp-Ordner gelöscht${NC}"

echo ""
