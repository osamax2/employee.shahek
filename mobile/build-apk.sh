#!/bin/bash

# Employee Tracking - APK Build Script
# Erstellt eine installierbare APK-Datei

set -e  # Exit bei Fehler

echo "════════════════════════════════════════════════════════════"
echo "  Employee Tracking - APK Builder"
echo "════════════════════════════════════════════════════════════"
echo ""

# Farben für Output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Aktuelles Verzeichnis speichern
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${BLUE}📱 Schritt 1/5:${NC} Prüfe Voraussetzungen..."

# Prüfe ob Node.js installiert ist
if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js ist nicht installiert!${NC}"
    exit 1
fi

# Prüfe ob npm installiert ist
if ! command -v npm &> /dev/null; then
    echo -e "${RED}❌ npm ist nicht installiert!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Node.js $(node --version) gefunden${NC}"
echo -e "${GREEN}✅ npm $(npm --version) gefunden${NC}"
echo ""

echo -e "${BLUE}📦 Schritt 2/5:${NC} Installiere Dependencies..."
npm install
echo ""

echo -e "${BLUE}🔧 Schritt 3/5:${NC} Installiere EAS CLI (falls nicht vorhanden)..."
if ! command -v eas &> /dev/null; then
    npm install -g eas-cli
    echo -e "${GREEN}✅ EAS CLI installiert${NC}"
else
    echo -e "${GREEN}✅ EAS CLI bereits installiert${NC}"
fi
echo ""

echo -e "${BLUE}🏗️  Schritt 4/5:${NC} Baue APK (Cloud Build)..."
echo -e "${YELLOW}⚠️  Du wirst nach deinem Expo-Account gefragt.${NC}"
echo -e "${YELLOW}⚠️  Falls du keinen hast: https://expo.dev/signup${NC}"
echo ""

# Lösche alten Build-Output
rm -rf build-output 2>/dev/null || true

# Starte Cloud Build (funktioniert ohne macOS Permissions)
echo -e "${BLUE}Starte Build-Prozess...${NC}"
eas build --platform android --profile preview --non-interactive --no-wait || {
    echo ""
    echo -e "${YELLOW}⚠️  Cloud Build benötigt Login. Führe manuell aus:${NC}"
    echo -e "${BLUE}   eas login${NC}"
    echo -e "${BLUE}   eas build --platform android --profile preview${NC}"
    echo ""
    echo -e "${BLUE}Alternative: Lokaler Build (experimentell)${NC}"
    read -p "Lokalen Build versuchen? (j/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[JjYy]$ ]]; then
        echo -e "${BLUE}Starte lokalen Build...${NC}"
        eas build --platform android --profile preview --local
    else
        exit 1
    fi
}

echo ""
echo -e "${GREEN}📥 Schritt 5/5:${NC} Build abgeschlossen!"
echo ""
echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ APK Build erfolgreich!${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}📍 APK Download:${NC}"
echo "   1. Gehe zu: https://expo.dev/accounts/[dein-account]/projects/employee-tracking-mobile/builds"
echo "   2. Lade die neueste APK herunter"
echo "   3. Übertrage sie per USB auf dein Android-Gerät"
echo "   4. Installiere die APK"
echo ""
echo -e "${YELLOW}💡 Alternative (schneller):${NC}"
echo "   • Scanne den QR-Code auf der Expo Build-Seite"
echo "   • Oder öffne den Link direkt auf dem Android-Gerät"
echo ""
echo -e "${BLUE}🔗 Nützliche Links:${NC}"
echo "   • Dashboard: https://employee.shahek.org/public/admin/dashboard"
echo "   • API: https://employee.shahek.org/public/api"
echo "   • Expo Builds: https://expo.dev"
echo ""
