#!/bin/bash

# Employee Tracking - Einfacher Cloud APK Build
# Der einfachste Weg zur APK - nutzt Expo Cloud Build

echo "════════════════════════════════════════════════════════════"
echo "  Employee Tracking - Cloud APK Build (Empfohlen)"
echo "════════════════════════════════════════════════════════════"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

cd "$(dirname "$0")"

echo -e "${BLUE}Dieser Build läuft in der Expo Cloud (keine lokalen Requirements!)${NC}"
echo ""
echo -e "${YELLOW}📋 Voraussetzungen:${NC}"
echo "   • Expo Account (kostenlos): https://expo.dev/signup"
echo "   • Internet-Verbindung"
echo ""
read -p "Hast du einen Expo Account? (j/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[JjYy]$ ]]; then
    echo ""
    echo -e "${BLUE}🔗 Erstelle einen Account:${NC}"
    echo "   1. Gehe zu: https://expo.dev/signup"
    echo "   2. Registriere dich (kostenlos)"
    echo "   3. Führe dieses Script erneut aus"
    echo ""
    exit 0
fi

echo ""
echo -e "${BLUE}📦 Installiere Dependencies...${NC}"
npm install

echo ""
echo -e "${BLUE}🔧 Installiere EAS CLI...${NC}"
# Prüfe ob EAS bereits installiert ist
if command -v eas &> /dev/null; then
    echo -e "${GREEN}✅ EAS CLI bereits installiert${NC}"
else
    sudo npm install -g eas-cli
fi

echo ""
echo -e "${BLUE}🔐 Prüfe Expo Login...${NC}"
# Prüfe ob bereits eingeloggt
if eas whoami &> /dev/null; then
    EAS_USER=$(eas whoami 2>/dev/null | tail -n 1)
    echo -e "${GREEN}✅ Bereits eingeloggt als: $EAS_USER${NC}"
else
    eas login
fi

echo ""
echo -e "${BLUE}🏗️  Starte Cloud Build...${NC}"
echo -e "${YELLOW}⏱️  Der Build dauert ca. 5-10 Minuten${NC}"
echo ""

# Erstelle .easignore um Trash und andere Probleme zu vermeiden
cat > .easignore << 'EOFIGNORE'
# Build outputs
build/
dist/
android/
ios/

# Dependencies
node_modules/

# Expo
.expo/
.expo-shared/

# Caches
.cache/
*.cache

# IDE
.vscode/
.idea/

# System files
.DS_Store
**/.DS_Store
*.swp
*.swo
*~

# macOS specific
.Trash/
.Spotlight-V100/
.fseventsd/
.TemporaryItems/
EOFIGNORE

echo -e "${GREEN}✅ .easignore erstellt (verhindert Upload-Fehler)${NC}"
echo ""

# Starte Build und warte auf Fertigstellung
eas build --platform android --profile preview

echo ""
echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ Build abgeschlossen!${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}📥 APK herunterladen:${NC}"
echo ""
echo "   Option 1 (direkt auf Android):"
echo "   • Öffne den obigen Link auf deinem Android-Gerät"
echo "   • Lade die APK herunter"
echo "   • Installiere sie"
echo ""
echo "   Option 2 (über Computer):"
echo "   • Lade APK vom obigen Link herunter"
echo "   • Übertrage per USB auf Android-Gerät"
echo "   • Installiere die APK"
echo ""
echo -e "${BLUE}🔗 Alle deine Builds:${NC}"
echo "   https://expo.dev/accounts/[dein-account]/projects/employee-tracking-mobile/builds"
echo ""
echo -e "${GREEN}🎉 Fertig! Installiere die APK auf deinem Android-Gerät.${NC}"
echo ""
