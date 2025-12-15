<?php
// Erweiterte Session-Diagnose für Laravel 10
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Session Configuration Deep Dive\n";
echo str_repeat("=", 60) . "\n\n";

// Step 1: Check PHP Session Support
echo "Step 1: PHP Session Support\n";
echo "Session support enabled: " . (function_exists('session_start') ? "✅ YES" : "❌ NO") . "\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Session save handler: " . ini_get('session.save_handler') . "\n\n";

// Step 2: Load Laravel
$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Step 2: Laravel Configuration\n";

// Check config/session.php
$sessionConfigPath = $basePath . '/config/session.php';
echo "Session config file: " . ($exists = file_exists($sessionConfigPath) ? "✅ EXISTS" : "❌ MISSING") . "\n";
if ($exists) {
    echo "File size: " . filesize($sessionConfigPath) . " bytes\n";
    echo "Readable: " . (is_readable($sessionConfigPath) ? "✅ YES" : "❌ NO") . "\n";
}

// Check what config() returns
echo "\nStep 3: Configuration Values\n";
try {
    $driver = config('session.driver');
    echo "session.driver from config(): " . ($driver ?? 'NULL') . "\n";
    
    $connection = config('session.connection');
    echo "session.connection: " . ($connection ?? 'NULL') . "\n";
    
    $table = config('session.table');
    echo "session.table: " . ($table ?? 'NULL') . "\n";
    
    $lifetime = config('session.lifetime');
    echo "session.lifetime: " . ($lifetime ?? 'NULL') . "\n";
    
    $files = config('session.files');
    echo "session.files path: " . ($files ?? 'NULL') . "\n";
} catch (\Exception $e) {
    echo "❌ Error reading config: " . $e->getMessage() . "\n";
}

// Check .env values
echo "\nStep 4: Environment Variables\n";
echo "SESSION_DRIVER from env: " . (env('SESSION_DRIVER') ?? 'NULL') . "\n";
echo "SESSION_LIFETIME from env: " . (env('SESSION_LIFETIME') ?? 'NULL') . "\n";

// Check raw config file content
echo "\nStep 5: Raw Config File Content\n";
if (file_exists($sessionConfigPath)) {
    echo "First 50 lines of config/session.php:\n";
    echo str_repeat("-", 60) . "\n";
    $lines = file($sessionConfigPath);
    foreach (array_slice($lines, 0, 50) as $num => $line) {
        echo sprintf("%3d: %s", $num + 1, $line);
    }
    echo str_repeat("-", 60) . "\n";
}

// Check storage directory
echo "\nStep 6: Storage Directory Status\n";
$storagePath = storage_path('framework/sessions');
echo "Storage path: $storagePath\n";
echo "Directory exists: " . (is_dir($storagePath) ? "✅ YES" : "❌ NO") . "\n";
if (is_dir($storagePath)) {
    echo "Readable: " . (is_readable($storagePath) ? "✅ YES" : "❌ NO") . "\n";
    echo "Writable: " . (is_writable($storagePath) ? "✅ YES" : "❌ NO") . "\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";
}

// Try to get SessionManager
echo "\nStep 7: SessionManager Resolution\n";
try {
    $sessionManager = app('Illuminate\Session\SessionManager');
    echo "✅ SessionManager resolved\n";
    echo "Default driver: " . $sessionManager->getDefaultDriver() . "\n";
} catch (\Exception $e) {
    echo "❌ SessionManager failed: " . $e->getMessage() . "\n";
    echo "Exception type: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

// Check if session service provider is registered
echo "\nStep 8: Service Provider Check\n";
$providers = config('app.providers');
$sessionProvider = null;
foreach ($providers as $provider) {
    if (strpos($provider, 'SessionServiceProvider') !== false) {
        $sessionProvider = $provider;
        break;
    }
}
echo "SessionServiceProvider: " . ($sessionProvider ? "✅ " . $sessionProvider : "❌ NOT FOUND") . "\n";

// Try loading config manually
echo "\nStep 9: Manual Config Load Test\n";
try {
    $manualConfig = include $sessionConfigPath;
    echo "Config file returns: " . gettype($manualConfig) . "\n";
    if (is_array($manualConfig)) {
        echo "Array keys: " . implode(', ', array_keys($manualConfig)) . "\n";
        echo "Driver value: " . ($manualConfig['driver'] ?? 'NULL') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Deep Dive Complete\n";
