<?php
/**
 * 500 Error Debugger
 * Shows detailed error information for Internal Server Error
 * 
 * SECURITY WARNING: DELETE THIS FILE AFTER DEBUGGING!
 * 
 * Upload to: /public_html/
 * Visit: https://employee.shahek.org/debug-500.php
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>
<html>
<head>
    <title>500 Error Debugger</title>
    <style>
        body { 
            font-family: monospace; 
            margin: 20px; 
            background: #1e1e1e; 
            color: #00ff00; 
        }
        .section { 
            background: #2d2d2d; 
            padding: 15px; 
            margin: 10px 0; 
            border-left: 4px solid #00ff00; 
        }
        .error { border-left-color: #ff0000; color: #ff6b6b; }
        .warning { border-left-color: #ffa500; color: #ffa500; }
        .success { border-left-color: #00ff00; }
        h1 { color: #fff; }
        h2 { color: #00ff00; margin-top: 0; }
        pre { 
            background: #000; 
            padding: 10px; 
            overflow-x: auto; 
            color: #fff;
        }
        .delete-warning {
            background: #ff0000;
            color: #fff;
            padding: 20px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
<h1>🔍 500 Error Debugger</h1>
<div class='delete-warning'>
    ⚠️ DELETE THIS FILE AFTER DEBUGGING! ⚠️<br>
    This file exposes sensitive information!
</div>";

// 1. Check current directory
echo "<div class='section'>";
echo "<h2>1. Current Directory</h2>";
echo "<pre>Current Directory: " . __DIR__ . "</pre>";
echo "<pre>Script Path: " . __FILE__ . "</pre>";
echo "</div>";

// 2. Check if Laravel files exist
echo "<div class='section'>";
echo "<h2>2. Laravel Files Check</h2>";
$files = [
    'public/index.php' => 'Laravel entry point',
    '.env' => 'Environment configuration',
    'vendor/autoload.php' => 'Composer autoloader',
    'bootstrap/app.php' => 'Laravel bootstrap',
    'artisan' => 'Laravel CLI',
];

foreach ($files as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $class = $exists ? 'success' : 'error';
    $icon = $exists ? '✅' : '❌';
    echo "<pre class='$class'>$icon $file - $desc</pre>";
}
echo "</div>";

// 3. Check .htaccess
echo "<div class='section'>";
echo "<h2>3. .htaccess Check</h2>";

$htaccess_root = __DIR__ . '/.htaccess';
$htaccess_public = __DIR__ . '/public/.htaccess';

echo "<strong>Root .htaccess:</strong>";
if (file_exists($htaccess_root)) {
    echo "<pre class='success'>✅ Exists at: $htaccess_root</pre>";
    echo "<strong>Content:</strong>";
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccess_root)) . "</pre>";
} else {
    echo "<pre class='error'>❌ NOT FOUND at: $htaccess_root</pre>";
    echo "<pre class='error'>This is required! Create with:</pre>";
    echo "<pre>&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteRule ^(.*)\$ public/\$1 [L]
&lt;/IfModule&gt;</pre>";
}

echo "<strong>Public .htaccess:</strong>";
if (file_exists($htaccess_public)) {
    echo "<pre class='success'>✅ Exists</pre>";
} else {
    echo "<pre class='error'>❌ NOT FOUND</pre>";
}
echo "</div>";

// 4. Check .env
echo "<div class='section'>";
echo "<h2>4. Environment Configuration</h2>";

$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    echo "<pre class='success'>✅ .env file exists</pre>";
    
    $env_content = file_get_contents($env_file);
    $env = parse_ini_string($env_content, false, INI_SCANNER_RAW);
    
    $required = [
        'APP_KEY' => 'Application encryption key',
        'APP_DEBUG' => 'Debug mode (should be false in production)',
        'DB_HOST' => 'Database host',
        'DB_DATABASE' => 'Database name',
        'DB_USERNAME' => 'Database user',
        'DB_PASSWORD' => 'Database password',
        'JWT_SECRET' => 'JWT signing key',
    ];
    
    foreach ($required as $key => $desc) {
        $value = $env[$key] ?? null;
        if (empty($value)) {
            echo "<pre class='error'>❌ $key: NOT SET ($desc)</pre>";
        } else {
            // Mask passwords
            $display = in_array($key, ['DB_PASSWORD', 'JWT_SECRET', 'APP_KEY']) 
                ? '***SET***' 
                : $value;
            echo "<pre class='success'>✅ $key: $display</pre>";
        }
    }
} else {
    echo "<pre class='error'>❌ .env file NOT FOUND!</pre>";
    echo "<pre class='error'>Copy from .env.example and configure it!</pre>";
}
echo "</div>";

// 5. Check vendor/
echo "<div class='section'>";
echo "<h2>5. Composer Dependencies</h2>";

$vendor_dir = __DIR__ . '/vendor';
$autoload = $vendor_dir . '/autoload.php';

if (is_dir($vendor_dir)) {
    echo "<pre class='success'>✅ vendor/ directory exists</pre>";
    
    if (file_exists($autoload)) {
        echo "<pre class='success'>✅ vendor/autoload.php exists</pre>";
        
        // Try to load
        try {
            require $autoload;
            echo "<pre class='success'>✅ Autoloader loads successfully</pre>";
        } catch (Exception $e) {
            echo "<pre class='error'>❌ Error loading autoloader: " . htmlspecialchars($e->getMessage()) . "</pre>";
        }
    } else {
        echo "<pre class='error'>❌ vendor/autoload.php NOT FOUND</pre>";
    }
    
    // Check size
    $size = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($vendor_dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    $sizeMB = round($size / 1024 / 1024, 2);
    
    if ($sizeMB < 10) {
        echo "<pre class='warning'>⚠️ vendor/ size: {$sizeMB}MB (seems too small, might be incomplete)</pre>";
    } else {
        echo "<pre class='success'>✅ vendor/ size: {$sizeMB}MB</pre>";
    }
} else {
    echo "<pre class='error'>❌ vendor/ directory NOT FOUND!</pre>";
    echo "<pre class='error'>Upload vendor.zip and extract it here!</pre>";
}
echo "</div>";

// 6. Check permissions
echo "<div class='section'>";
echo "<h2>6. File Permissions</h2>";

$dirs = [
    'storage' => 'Storage directory',
    'storage/framework' => 'Framework cache',
    'storage/logs' => 'Log files',
    'bootstrap/cache' => 'Bootstrap cache',
];

foreach ($dirs as $dir => $desc) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path);
        $perms = substr(sprintf('%o', fileperms($path)), -3);
        $class = $writable ? 'success' : 'error';
        $icon = $writable ? '✅' : '❌';
        echo "<pre class='$class'>$icon $dir/ ($perms) - $desc</pre>";
        
        if (!$writable) {
            echo "<pre class='error'>   Fix: chmod -R 775 $dir</pre>";
        }
    } else {
        echo "<pre class='error'>❌ $dir/ NOT FOUND</pre>";
    }
}
echo "</div>";

// 7. Try to load Laravel
echo "<div class='section'>";
echo "<h2>7. Laravel Bootstrap Test</h2>";

if (file_exists($autoload)) {
    try {
        require_once $autoload;
        
        $app_file = __DIR__ . '/bootstrap/app.php';
        if (file_exists($app_file)) {
            echo "<pre class='success'>✅ bootstrap/app.php exists</pre>";
            
            try {
                $app = require_once $app_file;
                echo "<pre class='success'>✅ Laravel application created</pre>";
                
                // Try to boot
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                echo "<pre class='success'>✅ Kernel resolved</pre>";
                
            } catch (Exception $e) {
                echo "<pre class='error'>❌ Error creating Laravel app:</pre>";
                echo "<pre class='error'>" . htmlspecialchars($e->getMessage()) . "</pre>";
                echo "<pre class='error'>File: " . htmlspecialchars($e->getFile()) . "</pre>";
                echo "<pre class='error'>Line: " . $e->getLine() . "</pre>";
            }
        } else {
            echo "<pre class='error'>❌ bootstrap/app.php NOT FOUND</pre>";
        }
    } catch (Exception $e) {
        echo "<pre class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</pre>";
    }
}
echo "</div>";

// 8. Check logs
echo "<div class='section'>";
echo "<h2>8. Recent Laravel Logs</h2>";

$log_file = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($log_file)) {
    echo "<pre class='success'>✅ Log file exists</pre>";
    
    // Get last 50 lines
    $lines = file($log_file);
    $recent = array_slice($lines, -50);
    
    echo "<strong>Last 50 log lines:</strong>";
    echo "<pre>" . htmlspecialchars(implode('', $recent)) . "</pre>";
} else {
    echo "<pre class='warning'>⚠️ No log file found yet</pre>";
}
echo "</div>";

// 9. PHP Info
echo "<div class='section'>";
echo "<h2>9. PHP Configuration</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
echo "Error Reporting: " . error_reporting() . "\n";
echo "</pre>";

echo "<strong>Loaded Extensions:</strong><br>";
$extensions = get_loaded_extensions();
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'json', 'bcmath'];
foreach ($required as $ext) {
    $loaded = in_array($ext, $extensions);
    $class = $loaded ? 'success' : 'error';
    $icon = $loaded ? '✅' : '❌';
    echo "<pre class='$class'>$icon $ext</pre>";
}
echo "</div>";

// 10. Direct access test
echo "<div class='section'>";
echo "<h2>10. Direct Laravel Test</h2>";
echo "<pre>Try accessing Laravel directly:</pre>";
echo "<pre><a href='/public/index.php' style='color: #00ff00;'>https://employee.shahek.org/public/index.php</a></pre>";
echo "<pre>If this works but /admin/dashboard doesn't, the problem is .htaccess routing.</pre>";
echo "</div>";

echo "<div class='delete-warning'>
    🗑️ IMPORTANT: DELETE THIS FILE AFTER DEBUGGING! 🗑️<br>
    This file contains sensitive information about your system!
</div>";

echo "</body></html>";
?>
