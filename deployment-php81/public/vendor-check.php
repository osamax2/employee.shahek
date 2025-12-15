<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vendor Check</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; font-size: 18px; }
        .error { color: #f48771; font-size: 18px; }
        .warning { color: #dcdcaa; font-size: 16px; }
        pre { background: #252526; padding: 15px; border-radius: 4px; border-left: 3px solid #007acc; overflow-x: auto; }
        h1 { color: #569cd6; }
        h2 { color: #4ec9b0; margin-top: 30px; }
        .box { background: #252526; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px solid #007acc; }
        .command { background: #000; color: #0f0; padding: 10px; border-radius: 4px; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>
    <h1>🔍 Vendor Directory Check</h1>
    
<?php
$rootDir = dirname(__DIR__);

echo "<h2>1️⃣ Check vendor-php81-fixed.zip</h2>";
$zipFile = $rootDir . '/vendor-php81-fixed.zip';
if (file_exists($zipFile)) {
    $size = filesize($zipFile);
    echo "<div class='success'>✅ vendor-php81-fixed.zip exists</div>";
    echo "<pre>Size: " . number_format($size) . " bytes (" . round($size/1024/1024, 2) . " MB)</pre>";
} else {
    echo "<div class='error'>❌ vendor-php81-fixed.zip NOT FOUND!</div>";
    echo "<p class='warning'>Das ZIP-File fehlt im Root-Verzeichnis!</p>";
}

echo "<h2>2️⃣ Check vendor/ directory</h2>";
$vendorDir = $rootDir . '/vendor';
if (is_dir($vendorDir)) {
    echo "<div class='success'>✅ vendor/ directory exists</div>";
    
    // Count subdirectories
    $items = scandir($vendorDir);
    $dirCount = 0;
    foreach ($items as $item) {
        if ($item != '.' && $item != '..' && is_dir($vendorDir . '/' . $item)) {
            $dirCount++;
        }
    }
    echo "<pre>Contains: $dirCount subdirectories</pre>";
    
    // Check for key Laravel packages
    $packages = [
        'illuminate/foundation',
        'illuminate/support',
        'illuminate/database',
        'illuminate/view',
        'laravel/framework'
    ];
    
    echo "<h3>Key Packages:</h3>";
    foreach ($packages as $package) {
        $packagePath = $vendorDir . '/' . $package;
        if (is_dir($packagePath)) {
            echo "<div class='success'>✅ $package</div>";
        } else {
            echo "<div class='error'>❌ $package - MISSING!</div>";
        }
    }
} else {
    echo "<div class='error'>❌ vendor/ directory DOES NOT EXIST!</div>";
    echo "<div class='box'>";
    echo "<h3 style='color: #f48771;'>⚠️ VENDOR FEHLT - ANLEITUNG:</h3>";
    echo "<p>Du musst <strong>vendor-php81-fixed.zip</strong> extrahieren!</p>";
    echo "<p><strong>Im cPanel File Manager:</strong></p>";
    echo "<ol>";
    echo "<li>Gehe zu: /home/shahek/employee.shahek.org/</li>";
    echo "<li>Finde die Datei: <strong>vendor-php81-fixed.zip</strong></li>";
    echo "<li>Rechtsklick → <strong>Extract</strong></li>";
    echo "<li>Wähle: Extract to <strong>/home/shahek/employee.shahek.org/</strong></li>";
    echo "<li>Klicke auf <strong>Extract File(s)</strong></li>";
    echo "<li>Warte ca. 30-60 Sekunden</li>";
    echo "<li>Lade diese Seite neu</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>3️⃣ Check autoload.php</h2>";
$autoloadFile = $vendorDir . '/autoload.php';
if (file_exists($autoloadFile)) {
    echo "<div class='success'>✅ vendor/autoload.php exists</div>";
    echo "<pre>Size: " . filesize($autoloadFile) . " bytes</pre>";
    
    // Try to load it
    echo "<h3>Test Autoload:</h3>";
    try {
        require_once $autoloadFile;
        echo "<div class='success'>✅ Autoload loaded successfully!</div>";
        
        // Check if Illuminate classes are available
        if (class_exists('Illuminate\Foundation\Application')) {
            echo "<div class='success'>✅ Illuminate\Foundation\Application class available!</div>";
        } else {
            echo "<div class='error'>❌ Illuminate\Foundation\Application NOT FOUND after autoload!</div>";
        }
        
        if (class_exists('Illuminate\Support\Facades\Route')) {
            echo "<div class='success'>✅ Illuminate\Support\Facades\Route class available!</div>";
        } else {
            echo "<div class='error'>❌ Route facade not available!</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Autoload failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='error'>❌ vendor/autoload.php NOT FOUND!</div>";
}

echo "<h2>4️⃣ Directory Permissions</h2>";
if (is_dir($vendorDir)) {
    $perms = fileperms($vendorDir);
    $permsOctal = substr(sprintf('%o', $perms), -4);
    echo "<pre>vendor/ permissions: $permsOctal</pre>";
    
    if (is_readable($vendorDir)) {
        echo "<div class='success'>✅ vendor/ is readable</div>";
    } else {
        echo "<div class='error'>❌ vendor/ is NOT readable!</div>";
    }
}

echo "<h2>5️⃣ PHP Info</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . __FILE__ . "\n";
echo "Current Dir: " . getcwd() . "\n";
echo "Root Dir: " . $rootDir . "\n";
echo "</pre>";

if (!is_dir($vendorDir)) {
    echo "<div class='box' style='border-color: #f48771;'>";
    echo "<h2 style='color: #f48771;'>🚨 NÄCHSTER SCHRITT</h2>";
    echo "<p style='font-size: 18px;'><strong>Extrahiere vendor-php81-fixed.zip im cPanel File Manager!</strong></p>";
    echo "<p>Nach dem Extrahieren sollte die Struktur so aussehen:</p>";
    echo "<pre>";
    echo "/home/shahek/employee.shahek.org/\n";
    echo "├── vendor/\n";
    echo "│   ├── autoload.php\n";
    echo "│   ├── composer/\n";
    echo "│   ├── illuminate/\n";
    echo "│   ├── laravel/\n";
    echo "│   └── ... viele weitere Packages\n";
    echo "├── app/\n";
    echo "├── config/\n";
    echo "└── vendor-php81-fixed.zip (kann danach gelöscht werden)\n";
    echo "</pre>";
    echo "</div>";
} else {
    echo "<div class='box' style='border-color: #4ec9b0;'>";
    echo "<h2 style='color: #4ec9b0;'>✅ VENDOR OK!</h2>";
    echo "<p>Jetzt kannst du Laravel testen:</p>";
    echo "<ul>";
    echo "<li><a href='api-test.php' style='color: #569cd6;'>api-test.php</a> - Laravel Bootstrap Test</li>";
    echo "<li><a href='dashboard-test.html' style='color: #569cd6;'>dashboard-test.html</a> - Statisches Dashboard</li>";
    echo "<li><a href='/public/admin/dashboard' style='color: #569cd6;'>/admin/dashboard</a> - Laravel Dashboard</li>";
    echo "</ul>";
    echo "</div>";
}
?>

</body>
</html>
