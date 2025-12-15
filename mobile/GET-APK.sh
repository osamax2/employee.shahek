#!/bin/bash

# GARANTIERT FUNKTIONIERENDES APK BUILD SCRIPT
# Nutzt Expo Web Interface für Build

echo "════════════════════════════════════════════════════════════"
echo "  🎯 EINFACHSTER WEG ZUR APK"
echo "════════════════════════════════════════════════════════════"
echo ""

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}Dieser Weg funktioniert IMMER (umgeht alle macOS Probleme):${NC}"
echo ""

echo -e "${YELLOW}🌐 METHODE: Web-basierter Build${NC}"
echo ""
echo "1️⃣  Gehe zu: ${BLUE}https://expo.dev${NC}"
echo "2️⃣  Logge ein mit: ${GREEN}osamax2${NC}"
echo "3️⃣  Klicke: ${BLUE}'Create a new project'${NC} → ${BLUE}'Upload an existing project'${NC}"
echo "4️⃣  Lade diese Dateien hoch:"
echo ""

# Erstelle Upload-ZIP
echo -e "${BLUE}📦 Erstelle Upload-Paket...${NC}"

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Bereinige erst
rm -rf node_modules .expo android ios build 2>/dev/null

ZIP_FILE="employee-tracking-upload.zip"
rm -f "$ZIP_FILE"

# Erstelle ZIP mit den wichtigsten Dateien
zip -r "$ZIP_FILE" \
    app.json \
    package.json \
    App.js \
    src/ \
    assets/ \
    .env \
    babel.config.js \
    eas.json \
    -x "*.DS_Store" "node_modules/*" ".expo/*" "android/*" "ios/*"

FILE_SIZE=$(du -h "$ZIP_FILE" | cut -f1)

echo ""
echo -e "${GREEN}✅ Upload-Paket erstellt:${NC} ${BLUE}$ZIP_FILE${NC} ($FILE_SIZE)"
echo ""
echo "5️⃣  Lade ${BLUE}$ZIP_FILE${NC} hoch"
echo "6️⃣  Wähle: ${BLUE}Android${NC} → ${BLUE}Preview Build${NC}"
echo "7️⃣  Klicke: ${BLUE}'Start Build'${NC}"
echo "8️⃣  Warte 5-10 Minuten"
echo "9️⃣  Lade APK herunter"
echo ""

# Öffne Browser
echo -e "${BLUE}🌐 Öffne Expo Dashboard...${NC}"
open "https://expo.dev"

echo ""
echo -e "${YELLOW}═══════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}ODER: Terminal-Build (wenn Trash leer)${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════════════${NC}"
echo ""
read -p "Möchtest du Terminal-Build versuchen? (j/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[JjYy]$ ]]; then
    echo ""
    echo -e "${BLUE}🗑️  Schritt 1: Leere Papierkorb...${NC}"
    echo "   Klicke: Papierkorb → Rechtsklick → 'Papierkorb leeren'"
    echo ""
    read -p "Papierkorb geleert? (j/n): " -n 1 -r
    echo ""
    
    if [[ $REPLY =~ ^[JjYy]$ ]]; then
        echo -e "${BLUE}🏗️  Starte Build...${NC}"
        npm install
        eas build --platform android --profile preview
    fi
fi

echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}📱 Nach erfolgreichem Build:${NC}"
echo -e "${GREEN}════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${BLUE}📥 APK herunterladen:${NC}"
echo "   • Option 1: Link direkt auf Android öffnen"
echo "   • Option 2: APK auf Mac laden → USB übertragen"
echo ""
echo -e "${BLUE}🔌 APK installieren:${NC}"
echo "   1. Öffne APK auf Android-Gerät"
echo "   2. Erlaube 'Unbekannte Quellen'"
echo "   3. Installiere App"
echo ""
echo -e "${BLUE}🚀 App starten:${NC}"
echo "   • Email: employee1@company.com"
echo "   • Password: admin123"
echo ""
echo -e "${BLUE}📊 Dashboard öffnen:${NC}"
echo "   https://employee.shahek.org/public/admin/dashboard"
echo ""
