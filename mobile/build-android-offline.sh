#!/bin/bash

# Android Offline Build Script
# Erstellt APK lokal ohne Cloud-Dienste

set -e

echo "🔨 Android Offline Build gestartet..."
echo ""

# Prüfe ob node_modules existiert
if [ ! -d "node_modules" ]; then
    echo "📦 Installiere Dependencies..."
    npm install
    echo ""
fi

# Bereinige und erstelle Android-Projekt neu
echo "🔧 Bereite Android-Projekt vor..."
if [ -d "android" ]; then
    echo "  → Entferne altes Android-Verzeichnis..."
    rm -rf android
fi

echo "  → Erstelle Android-Projekt mit Expo..."
npx expo prebuild -p android --clean

echo ""
echo "  → Bereinige macOS-Metadaten komplett..."
# Nutze dot_clean zum Entfernen aller macOS Metadaten
dot_clean -m . 2>/dev/null || true

# Starte ZWEI Cleaner-Prozesse für maximale Abdeckung
(
    while true; do
        # Speziell für expo-file-system
        rm -f node_modules/expo-file-system/android/build/intermediates/packaged_res/release/._* 2>/dev/null
        find node_modules/expo-file-system/android/build -type f -name "._*" -delete 2>/dev/null
        sleep 0.1
    done
) &
CLEANER_PID1=$!

(
    while true; do
        # Allgemein für alle node_modules
        find node_modules/*/android/build -type f -name "._*" -delete 2>/dev/null
        find android/build -type f -name "._*" -delete 2>/dev/null
        sleep 0.3
    done
) &
CLEANER_PID2=$!

echo ""

# Funktion zum Extrahieren der Java-Version
get_java_version() {
    local java_bin="$1/bin/java"
    if [ -f "$java_bin" ]; then
        local version=$("$java_bin" -version 2>&1 | head -n 1 | grep -oE '[0-9]+' | head -n 1)
        echo "$version"
    else
        echo "0"
    fi
}

# Suche nach allen installierten Java-Versionen
echo "🔍 Suche nach installierten Java-Versionen..."

JAVA_PATHS=()
JAVA_VERS=()

# Durchsuche /Library/Java/JavaVirtualMachines/
if [ -d "/Library/Java/JavaVirtualMachines" ]; then
    for jdk_dir in /Library/Java/JavaVirtualMachines/*/Contents/Home; do
        if [ -d "$jdk_dir" ]; then
            version=$(get_java_version "$jdk_dir")
            if [ "$version" != "0" ]; then
                JAVA_VERS+=("$version")
                JAVA_PATHS+=("$jdk_dir")
                echo "  ✓ Gefunden: Java $version - $jdk_dir"
            fi
        fi
    done
fi

# Durchsuche Homebrew Installationen
for brew_java in /usr/local/opt/openjdk@* /opt/homebrew/opt/openjdk@*; do
    if [ -d "$brew_java" ]; then
        version=$(get_java_version "$brew_java")
        if [ "$version" != "0" ]; then
            # Prüfe ob Version schon vorhanden
            found=0
            for v in "${JAVA_VERS[@]}"; do
                if [ "$v" == "$version" ]; then
                    found=1
                    break
                fi
            done
            if [ $found -eq 0 ]; then
                JAVA_VERS+=("$version")
                JAVA_PATHS+=("$brew_java")
                echo "  ✓ Gefunden: Java $version - $brew_java"
            fi
        fi
    fi
done

echo ""

# Wähle beste Version aus (Priorität: 17 > 11 > 8 > andere zwischen 8 und 20)
BEST_JAVA=""
BEST_VERSION=0
BEST_INDEX=-1

for i in "${!JAVA_VERS[@]}"; do
    version="${JAVA_VERS[$i]}"
    
    if [ "$version" -eq 17 ]; then
        BEST_JAVA="${JAVA_PATHS[$i]}"
        BEST_VERSION=17
        break
    elif [ "$version" -eq 11 ] && [ "$BEST_VERSION" -ne 17 ]; then
        BEST_JAVA="${JAVA_PATHS[$i]}"
        BEST_VERSION=11
    elif [ "$version" -ge 8 ] && [ "$version" -le 20 ] && [ "$BEST_VERSION" -lt 8 ]; then
        BEST_JAVA="${JAVA_PATHS[$i]}"
        BEST_VERSION=$version
    fi
done

if [ -n "$BEST_JAVA" ]; then
    export JAVA_HOME="$BEST_JAVA"
    echo "✅ Verwende Java $BEST_VERSION"
    echo "📍 JAVA_HOME: $JAVA_HOME"
    echo ""
else
    echo "❌ Keine kompatible Java-Version gefunden!"
    echo "Installiere Java 17 oder 11"
    exit 1
fi

echo "Java Version:"
"${JAVA_HOME}/bin/java" -version 2>&1

echo ""
echo "🧹 Bereinige macOS-Dateien und alte Builds..."

# Beende alle Gradle und Kotlin Daemons
echo "  → Beende laufende Daemons..."
pkill -f '.*GradleDaemon.*' 2>/dev/null || true
pkill -f '.*KotlinCompileDaemon.*' 2>/dev/null || true
sleep 2

# Entferne ALLE ._ Dateien im gesamten Projektverzeichnis
echo "  → Entferne macOS ._ Dateien..."
find . -type f -name "._*" -delete 2>/dev/null || true

# Lösche spezifisch das problematische Build-Verzeichnis
echo "  → Bereinige expo-file-system Build-Verzeichnis..."
rm -rf node_modules/expo-file-system/android/build 2>/dev/null || true
rm -rf node_modules/*/android/build/intermediates 2>/dev/null || true

# Bereinige alle Build-Verzeichnisse
echo "  → Bereinige Build-Verzeichnisse..."
rm -rf android/build 2>/dev/null || true
rm -rf android/app/build 2>/dev/null || true
rm -rf android/.gradle 2>/dev/null || true

# Bereinige Gradle Caches
rm -rf ~/.gradle/daemon 2>/dev/null || true
rm -rf ~/.gradle/caches/transforms-* 2>/dev/null || true
rm -rf ~/.gradle/caches/*/kotlin-dsl 2>/dev/null || true

# Prüfe und konfiguriere Android SDK
echo ""
echo "🔍 Prüfe Android SDK..."

ANDROID_SDK=""
if [ -n "$ANDROID_HOME" ]; then
    ANDROID_SDK="$ANDROID_HOME"
elif [ -n "$ANDROID_SDK_ROOT" ]; then
    ANDROID_SDK="$ANDROID_SDK_ROOT"
elif [ -d "$HOME/Library/Android/sdk" ]; then
    ANDROID_SDK="$HOME/Library/Android/sdk"
fi

if [ -n "$ANDROID_SDK" ]; then
    export ANDROID_HOME="$ANDROID_SDK"
    export ANDROID_SDK_ROOT="$ANDROID_SDK"
    echo "✓ Android SDK: $ANDROID_SDK"
    
    # Prüfe und bereinige alle NDK-Versionen
    if [ -d "$ANDROID_SDK/ndk" ]; then
        echo "  → Prüfe NDK-Versionen..."
        for ndk_dir in "$ANDROID_SDK/ndk"/*; do
            if [ -d "$ndk_dir" ]; then
                ndk_version=$(basename "$ndk_dir")
                if [ ! -f "$ndk_dir/source.properties" ]; then
                    echo "  ⚠️  Entferne beschädigtes NDK: $ndk_version"
                    rm -rf "$ndk_dir"
                else
                    echo "  ✓ NDK OK: $ndk_version"
                fi
            fi
        done
        
        # Zeige verbleibende NDK-Versionen
        REMAINING_NDK=$(ls -1 "$ANDROID_SDK/ndk" 2>/dev/null | tail -1)
        if [ -n "$REMAINING_NDK" ]; then
            echo "✓ Verwende NDK: $REMAINING_NDK"
        else
            echo "⚠️  Keine gültige NDK-Version gefunden"
            echo "   Installiere NDK über Android Studio SDK Manager"
        fi
    else
        echo "⚠️  NDK-Verzeichnis nicht gefunden"
    fi
else
    echo "⚠️  Android SDK nicht gefunden"
    echo "Setze ANDROID_HOME manuell oder installiere Android Studio"
fi

echo ""
echo "🔨 Starte Build-Prozess..."

# Wechsle ins Android-Verzeichnis
cd android

# Baue direkt ohne clean (da wir schon bereinigt haben)
echo "  → Baue Release APK..."
./gradlew assembleRelease --no-daemon --warning-mode all --stacktrace

echo ""
echo "✅ Build erfolgreich abgeschlossen!"
echo ""
echo "📱 APK Dateien:"
find app/build/outputs/apk/release -name "*.apk" -exec ls -lh {} \;

echo ""
echo "📍 APK Speicherort:"
echo "$(pwd)/app/build/outputs/apk/release/"

# Kopiere APK ins Hauptverzeichnis für einfachen Zugriff
APK_FILE=$(find app/build/outputs/apk/release -name "*.apk" | head -1)
if [ -n "$APK_FILE" ]; then
    cp "$APK_FILE" ../employee-app.apk
    echo ""
    echo "✓ APK kopiert nach: $(pwd)/../employee-app.apk"
fi

cd ..
echo ""
echo "🎉 Fertig! Installiere die APK auf deinem Gerät."

# Beende beide Cleaner-Prozesse
kill $CLEANER_PID1 2>/dev/null || true
kill $CLEANER_PID2 2>/dev/null || true
