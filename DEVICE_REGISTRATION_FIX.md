# Device Registration Error - Fehlerbehebung

## Problem: "Failed to register device with server"

### Mögliche Ursachen:

#### 1. **API_BASE_URL ist falsch konfiguriert**

Die Standard-URL ist `http://localhost:8000/api`, was auf echten Geräten nicht funktioniert.

**Lösung:**
- Öffne [mobile/src/services/config.js](mobile/src/services/config.js)
- Ändere die `API_BASE_URL` zu deiner Server-IP oder Domain:

```javascript
export const API_BASE_URL = 'http://DEINE_SERVER_IP:8000/api';
// oder für Production:
// export const API_BASE_URL = 'https://deine-domain.com/api';
```

**Beispiele:**
- Lokales Netzwerk: `http://192.168.1.100:8000/api`
- cPanel: `https://deine-domain.com/api`

#### 2. **Server läuft nicht oder ist nicht erreichbar**

**Prüfen:**
```bash
# Teste die API mit curl
curl http://DEINE_SERVER_IP:8000/api/auth/login

# Sollte eine JSON-Antwort zurückgeben
```

#### 3. **Datenbank-Tabelle fehlt**

Die `devices`-Tabelle muss existieren.

**Lösung:**
1. Öffne phpMyAdmin
2. Wähle deine Datenbank
3. Importiere: `server/database/create_devices_table.sql`

#### 4. **CORS-Problem**

Der Server muss CORS für deine Mobile-App erlauben.

**Prüfen:** [server/config/cors.php](server/config/cors.php)

```php
'allowed_origins' => ['*'], // Für Development
```

#### 5. **Authentifizierungs-Token fehlt**

**Debug-Schritte:**

1. **Prüfe die Logs in der App:**
   - Öffne die Developer Console
   - Suche nach: "Registering device with server..."
   - Prüfe ob "API URL" und "Device data" geloggt werden

2. **Teste die API manuell:**
```bash
# 1. Login
curl -X POST http://DEINE_SERVER_IP:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@device.local","password":"test123"}'

# Kopiere das "access_token" aus der Antwort

# 2. Device registrieren
curl -X POST http://DEINE_SERVER_IP:8000/api/device/register \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer DEIN_ACCESS_TOKEN" \
  -d '{
    "device_id": "test-device",
    "device_name": "Test Device",
    "os_name": "Android"
  }'
```

## Schnelle Lösung

### Option 1: Config-Datei anpassen

Erstelle/bearbeite [mobile/src/services/config.js](mobile/src/services/config.js):

```javascript
// Für lokales Testen mit Expo auf gleichem Netzwerk
export const API_BASE_URL = 'http://192.168.1.XXX:8000/api'; // Deine IP

// Für Android Emulator
// export const API_BASE_URL = 'http://10.0.2.2:8000/api';

// Für iOS Simulator
// export const API_BASE_URL = 'http://localhost:8000/api';

// Für Production
// export const API_BASE_URL = 'https://deine-domain.com/api';
```

### Option 2: Device-Registrierung optional machen

Die App funktioniert auch ohne Device-Registrierung (nur für Location-Tracking).

Der Code wurde bereits angepasst um weiterzulaufen wenn die Registrierung fehlschlägt.

## Verbesserungen die ich gemacht habe:

1. ✅ **Bessere Fehlerbehandlung** - App zeigt jetzt detaillierte Fehlermeldungen
2. ✅ **Retry-Option** - Benutzer kann Initialisierung wiederholen
3. ✅ **Device-Registrierung optional** - App läuft weiter wenn Registrierung fehlschlägt
4. ✅ **Detailliertes Logging** - API URL und Fehlerdetails werden geloggt
5. ✅ **Token-Verifizierung** - Prüft Token vor Device-Registrierung

## Nächste Schritte:

1. **API_BASE_URL konfigurieren** in `config.js`
2. **SQL-Datei importieren** in phpMyAdmin
3. **Server starten** und erreichbar machen
4. **App neu starten**
