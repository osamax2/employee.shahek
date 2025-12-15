# Android Build Fehler beheben

## Problem: Gradle Build Failed

### Schnelle Lösungen:

#### 1. **Package Name geändert**
- Von `com.yourcompany.employeetracking` → `com.shahek.employeetracking`
- Einheitlich für Android & iOS

#### 2. **Duplikate Permissions entfernt**
- Entfernt: `android.permission.*` Duplikate
- Expo handhabt das automatisch

#### 3. **versionCode hinzugefügt**
- Android benötigt `versionCode` für Builds

#### 4. **Gradle Command explizit gesetzt**
- `gradleCommand: ":app:assembleRelease"` für production/preview

### Jetzt neu builden:

```bash
cd mobile

# Cache löschen
rm -rf node_modules
npm install

# Expo cache löschen
npx expo start -c

# Neu builden mit EAS
eas build --platform android --profile production
```

### Alternative: Lokaler Build

```bash
cd mobile

# Pre-build für native code
npx expo prebuild --clean

# Android build
cd android
./gradlew clean
./gradlew assembleRelease

# APK findest du unter:
# android/app/build/outputs/apk/release/app-release.apk
```

### Häufige Gradle-Fehler:

#### Fehler: "compileSdkVersion"
```bash
# Lösung: Update auf neueste Expo SDK
npm install expo@latest
```

#### Fehler: "AAPT: error"
```bash
# Lösung: Assets prüfen
# Stelle sicher dass icon.png, splash.png, adaptive-icon.png existieren
```

#### Fehler: "Out of memory"
```bash
# Lösung: gradle.properties anpassen
echo "org.gradle.jvmargs=-Xmx4096m -XX:MaxPermSize=512m" >> android/gradle.properties
```

#### Fehler: "Task failed"
```bash
# Lösung: Clean build
cd android
./gradlew clean
./gradlew assembleRelease --stacktrace
```

### Debug-Modus:

```bash
# Detaillierte Logs
eas build --platform android --profile production --local

# Oder mit mehr Info
eas build --platform android --profile production --clear-cache
```

### Was geändert wurde:

1. ✅ Package: `com.shahek.employeetracking`
2. ✅ versionCode: `1` hinzugefügt
3. ✅ Permissions bereinigt (keine Duplikate)
4. ✅ gradleCommand explizit gesetzt
5. ✅ Config für Google Maps vorbereitet

Versuch den Build nochmal! 🚀
