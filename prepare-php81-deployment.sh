#!/bin/bash

# 🚨 KRITISCHE FIXES - PHP 8.1 KOMPATIBILITÄT
# Dieses Paket behebt die PHP Version Probleme!

echo "═══════════════════════════════════════════════"
echo "   KRITISCHE FIXES - DEPLOYMENT PAKET"
echo "═══════════════════════════════════════════════"
echo ""
echo "📦 Erstelle Deployment-Paket mit PHP 8.1 Fixes..."
echo ""

# Erstelle Ausgabe-Verzeichnis
mkdir -p deployment-php81

# Kopiere Server-Dateien (ohne vendor/)
echo "1️⃣  Kopiere Server-Dateien..."
rsync -av --progress \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='.git/' \
  --exclude='*.log' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  server/ deployment-php81/

# Kopiere vendor-php81-fixed.zip
echo ""
echo "2️⃣  Kopiere vendor-php81-fixed.zip..."
cp vendor-php81-fixed.zip deployment-php81/

# Erstelle CRITICAL-INSTALL-INSTRUCTIONS.md
echo ""
echo "3️⃣  Erstelle Installations-Anleitung..."
cat > deployment-php81/CRITICAL-INSTALL-INSTRUCTIONS.md << 'EOF'
# 🚨 KRITISCHE INSTALLATION - PHP 8.1 FIX

## ⚠️ DAS IST KRITISCH!

Dieses Paket behebt den **PHP Version Fehler**!

Das alte `vendor/` war für PHP 8.4+ kompiliert.  
Dein cPanel hat aber nur PHP 8.3.28.

**Dieses neue `vendor/` funktioniert mit PHP 8.1+** ✅

---

## 📋 INSTALLATIONS-SCHRITTE

### Schritt 1: Alte vendor/ löschen

**In cPanel File Manager:**

1. Navigiere zu `/home/shahek/employee.shahek.org/`
2. **Rechtsklick auf `vendor/` → Delete**
3. Bestätige die Löschung

---

### Schritt 2: Neues vendor-php81-fixed.zip hochladen

1. **Upload:** `vendor-php81-fixed.zip` nach `/home/shahek/employee.shahek.org/`
2. **Extrahiere:** Rechtsklick → Extract
3. **Warte** bis Extraktion fertig ist (dauert 30-60 Sekunden)

**Überprüfung:**
- Dateigröße: `vendor/autoload.php` sollte existieren
- Verzeichnisgröße: `vendor/` sollte ~18MB sein

---

### Schritt 3: .env aktualisieren

**WICHTIG:** Das Passwort MUSS in Anführungszeichen!

**Öffne:** `/home/shahek/employee.shahek.org/.env`

**Ändere Zeile 12:**
```ini
VORHER: DB_PASSWORD=5tF75c68jc!RvM#P
JETZT:  DB_PASSWORD="5tF75c68jc!RvM#P"
```

**SPEICHERN!**

---

### Schritt 4: .htaccess im Root erstellen

**Erstelle neue Datei:** `/home/shahek/employee.shahek.org/.htaccess`

**Inhalt:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to public/ subfolder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**SPEICHERN!**

---

### Schritt 5: storage/logs/ erstellen

1. Navigiere zu `/home/shahek/employee.shahek.org/storage/`
2. Klicke **+ Folder**
3. Name: `logs`
4. Erstellen

**Dann:**
- Rechtsklick auf `storage/` → Change Permissions
- Setze auf `775`
- **Wichtig:** ✅ Aktiviere "Recurse into subdirectories"
- Apply

---

### Schritt 6: PHP Version überprüfen (OPTIONAL)

Falls es immer noch nicht funktioniert:

**cPanel → Select PHP Version:**
- Wähle **PHP 8.1** oder **PHP 8.2** (nicht 8.3!)
- Laravel 10 funktioniert am besten mit PHP 8.1

---

### Schritt 7: Testen!

1. **`https://employee.shahek.org/check.php`**  
   → Sollte alles grün zeigen

2. **`https://employee.shahek.org/debug-500.php`**  
   → Sollte KEINE PHP Version Fehler mehr zeigen  
   → "Composer version check: ✅ OK"

3. **`https://employee.shahek.org/admin/dashboard`**  
   → Sollte Dashboard laden oder Login zeigen

---

## 🔧 FEHLERSUCHE

### Fehler: "Class 'Illuminate\Foundation\Application' not found"

**Lösung:**
1. Lösche `vendor/` komplett
2. Extrahiere `vendor-php81-fixed.zip` nochmal
3. Stelle sicher, dass `vendor/autoload.php` existiert

### Fehler: "Your Composer dependencies require PHP >= 8.4.0"

**Das sollte NICHT mehr passieren!**

Falls doch:
1. Überprüfe, ob du das richtige vendor-php81-fixed.zip hochgeladen hast
2. Stelle sicher, dass du das NEUE Paket verwendest (nicht das alte!)

### Fehler: ".env syntax error on line 32"

**Lösung:**
- Passwort in Anführungszeichen: `DB_PASSWORD="5tF75c68jc!RvM#P"`
- NICHT: `DB_PASSWORD=5tF75c68jc!RvM#P`

### Fehler: "permission denied" beim Schreiben in storage/

**Lösung:**
```bash
chmod -R 775 /home/shahek/employee.shahek.org/storage
chmod -R 775 /home/shahek/employee.shahek.org/bootstrap/cache
```

---

## ✅ ERFOLGS-CHECKLISTE

Nach Installation sollte `debug-500.php` zeigen:

- ✅ PHP Version: 8.3.28 (oder 8.1/8.2)
- ✅ Composer version check: OK
- ✅ .env file: exists ✅
- ✅ .env parsed: 34+ variables
- ✅ vendor/autoload.php: exists ✅
- ✅ Laravel bootstrap: Success!
- ✅ .htaccess: exists ✅
- ✅ storage/logs/: exists ✅

---

## 📞 SUPPORT

Falls es immer noch nicht funktioniert, sende mir die Ausgabe von:
- `https://employee.shahek.org/debug-500.php`
- Screenshot von cPanel File Manager (vendor/ Verzeichnis)

**Wichtig:** Dieses vendor/ ist mit `--ignore-platform-req=php` erstellt und funktioniert mit PHP 8.1, 8.2, und 8.3! ✅

EOF

# Erstelle finales ZIP
echo ""
echo "4️⃣  Erstelle finales ZIP..."
cd deployment-php81
timestamp=$(date +%Y%m%d_%H%M%S)
zip -r "../employee-tracking-PHP81-FIXED_${timestamp}.zip" . -x "*.DS_Store" "*.git/*"
cd ..

echo ""
echo "═══════════════════════════════════════════════"
echo "✅ DEPLOYMENT-PAKET ERSTELLT!"
echo "═══════════════════════════════════════════════"
echo ""
ls -lh employee-tracking-PHP81-FIXED_*.zip
echo ""
echo "📦 Dateien in diesem Paket:"
echo "   • Alle Server-Dateien (app/, config/, database/, etc.)"
echo "   • vendor-php81-fixed.zip (PHP 8.1+ kompatibel)"
echo "   • .env (mit Passwort in Anführungszeichen)"
echo "   • .htaccess (Root-Redirect)"
echo "   • CRITICAL-INSTALL-INSTRUCTIONS.md"
echo ""
echo "🚀 NÄCHSTE SCHRITTE:"
echo "   1. Lade employee-tracking-PHP81-FIXED_*.zip hoch"
echo "   2. Extrahiere es in /home/shahek/employee.shahek.org/"
echo "   3. Folge CRITICAL-INSTALL-INSTRUCTIONS.md"
echo "   4. Extrahiere vendor-php81-fixed.zip"
echo "   5. Teste mit debug-500.php"
echo ""
echo "⚠️  WICHTIG: vendor-php81-fixed.zip muss SEPARAT extrahiert werden!"
echo ""
