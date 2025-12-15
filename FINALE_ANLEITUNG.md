# 🚀 FINALE DEPLOYMENT ANLEITUNG

## ✅ SERVER IST LIVE UND FUNKTIONIERT!

Dashboard: https://employee.shahek.org/public/admin/dashboard  
API Status: https://employee.shahek.org/public/diagnose-api.php

## 📱 MOBILE APP - 3 OPTIONEN

### Option 1: Expo Go (SCHNELLSTER WEG - 2 Minuten)

**Auf deinem Computer:**
```bash
cd mobile
npm install
npm start
```

Falls "too many open files" Fehler:
```bash
# Erhöhe File Descriptor Limit (macOS)
ulimit -n 4096
npm start
```

**Auf deinem Handy:**
1. Installiere Expo Go:
   - Android: https://play.google.com/store/apps/details?id=host.exp.exponent
   - iOS: https://apps.apple.com/app/expo-go/id982107779
2. Scanne den QR-Code vom Terminal
3. Login: `employee1@company.com` / `admin123` (oder dein ADMIN_PASSWORD)

✅ App verbindet sich direkt mit dem Live-Server!

---

### Option 2: Cloud Build (PRODUCTION APK - 15-20 Minuten)

**Papierkorb leeren, dann:**
```bash
cd mobile
# Leere deinen Mac Papierkorb: Cmd+Shift+Delete
# Oder im Terminal:
rm -rf ~/.Trash/*

npm run build:android:preview
```

✅ APK Download-Link kommt per Email!

---

### Option 3: Ohne Build - Direkter Web-Test

Die API ist öffentlich, du kannst direkt testen:

```bash
# Login Test
curl -X POST https://employee.shahek.org/public/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"employee1@company.com","password":"admin123"}'

# Location senden (mit Token aus Login)
curl -X POST https://employee.shahek.org/public/api/location \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "lat": 52.52,
    "lng": 13.405,
    "accuracy": 10,
    "battery": 85,
    "timestamp": "2025-12-15T12:00:00Z"
  }'
```

---

## 🎯 EMPFEHLUNG: Option 1 (Expo Go)

Am schnellsten und einfachsten zum Testen!

## 📊 Test-Accounts

| Email | Password | Rolle |
|-------|----------|-------|
| admin@company.com | admin123* | Web Dashboard |
| employee1@company.com | admin123* | Mobile App |
| employee2@company.com | admin123* | Mobile App |
| employee3@company.com | admin123* | Mobile App |

*Oder das Passwort aus `server/.env` → `ADMIN_PASSWORD`

## ✅ Was bereits funktioniert:

- ✅ Server deployed auf https://employee.shahek.org
- ✅ Dashboard zeigt 3 Mitarbeiter-Standorte (Berlin)
- ✅ API-Endpunkte: `/api/auth/login`, `/api/location`, `/api/admin/stats`
- ✅ Datenbank: 4 Employees, 3 Locations
- ✅ Mobile App konfiguriert für Production-Server
- ✅ JWT Authentication funktioniert
- ✅ Session Management funktioniert

## 🔧 Nächste Schritte nach App-Start:

1. Starte App mit Expo Go
2. Login mit employee1@company.com
3. Erteile Location-Permissions
4. App sendet alle 5 Minuten Standort
5. Öffne Dashboard → sehe deinen Standort auf der Karte!

🎉 Alles bereit zum Laufen!
