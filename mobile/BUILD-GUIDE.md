# 📱 Employee Tracking - APK Build Anleitungen

## 🎯 Schnellstart (Empfohlen)

```bash
./build-apk-cloud.sh
```

Dieser Befehl baut deine APK in der **Expo Cloud** (einfachste Methode).

---

## 📋 Verfügbare Build-Scripts

### 1. **build-apk-cloud.sh** ⭐ EMPFOHLEN

**Am einfachsten!** Nutzt Expo Cloud Build.

```bash
./build-apk-cloud.sh
```

✅ **Vorteile:**
- Keine lokalen Android SDK Requirements
- Funktioniert auf jedem Mac/PC
- Zuverlässig und stabil
- Dauert 5-10 Minuten

❗ **Benötigt:**
- Expo Account (kostenlos: https://expo.dev/signup)
- Internet-Verbindung

---

### 2. **build-apk-local.sh** (Fortgeschritten)

Baut APK lokal auf deinem Mac.

```bash
./build-apk-local.sh
```

✅ **Vorteile:**
- Vollständige Kontrolle
- Offline-Build möglich
- Schneller (wenn bereits konfiguriert)

❗ **Benötigt:**
- Android SDK installiert
- Java JDK 11+
- ANDROID_HOME Umgebungsvariable
- Terminal Full Disk Access

---

### 3. **build-apk.sh** (Hybrid)

Versucht Cloud Build, fällt zurück auf lokalen Build.

```bash
./build-apk.sh
```

---

## 🚀 Komplette Anleitung (Cloud Build)

### Schritt 1: Expo Account erstellen
```bash
# Öffne im Browser:
https://expo.dev/signup
```

### Schritt 2: Build starten
```bash
cd /Users/osamaalabaji/shahek/mobile
./build-apk-cloud.sh
```

### Schritt 3: Bei Expo einloggen
```
Wenn gefragt:
Email: deine-email@example.com
Password: dein-passwort
```

### Schritt 4: APK herunterladen

Nach 5-10 Minuten bekommst du einen Link:
```
https://expo.dev/artifacts/[...]build.apk
```

**Option A:** Link direkt auf Android-Gerät öffnen → APK installieren
**Option B:** APK auf PC herunterladen → per USB auf Gerät übertragen

---

## 📥 APK auf Android installieren

### Methode 1: Direkter Download (einfachste)
1. Öffne den APK-Link auf deinem Android-Gerät
2. Tippe auf "Herunterladen"
3. Öffne die heruntergeladene Datei
4. Erlaube "Installation aus unbekannten Quellen"
5. Installiere die App

### Methode 2: USB-Transfer
1. Verbinde Android-Gerät per USB mit Mac
2. Kopiere `employee-tracking.apk` auf Gerät
3. Öffne Datei-Manager auf Android
4. Navigiere zu Downloads
5. Tippe auf APK-Datei
6. Installiere die App

### Methode 3: ADB (für Entwickler)
```bash
# Android per USB verbinden, USB-Debugging aktivieren
adb install build-output/employee-tracking.apk
```

---

## 🔧 Troubleshooting

### Problem: "eas: command not found"
```bash
npm install -g eas-cli
```

### Problem: "Not logged in"
```bash
eas login
```

### Problem: Build schlägt fehl (lokaler Build)
→ Nutze **build-apk-cloud.sh** stattdessen (keine lokalen Requirements)

### Problem: "Operation not permitted" (macOS)
1. Öffne: Systemeinstellungen → Datenschutz & Sicherheit
2. Gehe zu: Full Disk Access
3. Füge Terminal hinzu
4. Versuche Build erneut

---

## 📊 Build-Status prüfen

```bash
# Alle deine Builds anzeigen
eas build:list
```

Oder im Browser:
```
https://expo.dev
```

---

## 🎯 Nach erfolgreicher Installation

### App starten:
1. Öffne "Employee Tracking" App
2. Login: `employee1@company.com`
3. Passwort: `admin123`
4. Erlaube Location-Berechtigungen

### Dashboard öffnen:
```
https://employee.shahek.org/public/admin/dashboard
```

---

## 💡 Tipps

- **Erste APK?** → Nutze `build-apk-cloud.sh`
- **Schnelle Updates?** → Cloud Build ist am zuverlässigsten
- **Offline arbeiten?** → Richte lokalen Build ein (komplexer)
- **Build dauert lange?** → Normal! Cloud Builds: 5-10 Min

---

## 🔗 Nützliche Links

- Expo Dashboard: https://expo.dev
- Expo Docs: https://docs.expo.dev
- Server Dashboard: https://employee.shahek.org/public/admin/dashboard
- API Endpoint: https://employee.shahek.org/public/api

---

## 📝 Build-Profile (eas.json)

- **preview**: APK für Testing (empfohlen)
- **production**: Optimierte APK für Release
- **development**: Development Build mit Dev-Tools

Aktuell nutzen alle Scripts das **preview** Profil.

---

## ❓ Hilfe benötigt?

1. Prüfe: `eas build:list` für Build-Status
2. Logs: `eas build:view [BUILD_ID]`
3. Expo Dashboard: https://expo.dev

---

**Viel Erfolg! 🚀**
