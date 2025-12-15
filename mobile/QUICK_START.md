# Mobile App - Quick Start Guide

## ✅ App ist bereits für Production konfiguriert!

Die `.env` Datei ist auf den Live-Server eingestellt:
```
API_BASE_URL=https://employee.shahek.org/public/api
```

## 🚀 Option 1: Testen mit Expo Go (Empfohlen für schnelle Tests)

### Voraussetzungen:
1. Expo Go App auf deinem Android/iOS Gerät installieren:
   - Android: https://play.google.com/store/apps/details?id=host.exp.exponent
   - iOS: https://apps.apple.com/app/expo-go/id982107779

### Starten:
```bash
cd mobile
npm install
npm start
```

Dann scanne den QR-Code mit:
- Android: Expo Go App
- iOS: Kamera App (öffnet dann Expo Go)

### Test-Login:
- Email: `employee1@company.com`
- Password: (Das ADMIN_PASSWORD aus server/.env - standardmäßig der Wert aus ADMIN_PASSWORD)

## 📱 Option 2: Standalone APK bauen (Für Production)

### Mit EAS Build (Cloud):
```bash
cd mobile

# Vorher: .easignore wurde bereits erstellt um Permission-Fehler zu vermeiden
npm run build:android:preview
```

Der Build läuft in der Cloud und du bekommst einen Download-Link.

### Lokaler Build (Alternative):
```bash
cd mobile
./build-simple.sh
```

Oder manuell mit EAS lokal:
```bash
npx eas-cli build --platform android --profile preview --local
```

## 🔧 Troubleshooting

### "EPERM: operation not permitted" Fehler:
**Gelöst!** Die `.easignore` Datei wurde erstellt und schließt problematische Ordner aus.

### Expo Go zeigt "Network Error":
1. Stelle sicher, dass dein Handy im selben WLAN ist wie dein Computer
2. Prüfe die Server-URL: https://employee.shahek.org/public/api
3. Teste die API direkt: https://employee.shahek.org/public/diagnose-api.php

### App startet nicht:
```bash
cd mobile
rm -rf node_modules
npm install
npm start -- --clear
```

## 📝 Features der App

✅ JWT Authentication mit dem Live-Server  
✅ Background Location Tracking (alle 5 Minuten)  
✅ Battery-aware (reduziert Updates bei niedrigem Akku)  
✅ Offline Queue (speichert Locations wenn offline)  
✅ Automatic Retry mit exponential backoff  

## 🌍 Live-Server Status

Dashboard: https://employee.shahek.org/public/admin/dashboard  
API Test: https://employee.shahek.org/public/diagnose-api.php  
Stats: https://employee.shahek.org/public/api/admin/stats  

## 🔐 Employee Test-Accounts

Die Datenbank hat bereits 4 Employees:
1. Administrator (admin@company.com) - Für Web-Dashboard
2. John Smith (employee1@company.com) - Für Mobile App
3. Sarah Johnson (employee2@company.com) - Für Mobile App  
4. Michael Brown (employee3@company.com) - Für Mobile App

Password für alle: Verwende das `ADMIN_PASSWORD` aus der server `.env`
