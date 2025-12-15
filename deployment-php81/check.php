<?php
/**
 * System Check Script for cPanel
 * Tests if everything is configured correctly
 * 
 * Visit: https://employee.shahek.org/check.php
 * DELETE after verification!
 */

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>System Check - Employee Tracking</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 20px;
            background: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 30px; }
        .check { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 System Check - Employee Tracking</h1>
    <p>This script checks if your Laravel application is properly configured.</p>
";

// Check 1: PHP Version
echo "<h2>1. PHP Configuration</h2>";
$phpVersion = phpversion();
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo "<div class='check success'>✅ <strong>PHP Version:</strong> {$phpVersion} (Required: 8.1+)</div>";
} else {
    echo "<div class='check error'>❌ <strong>PHP Version:</strong> {$phpVersion} (Required: 8.1+)<br>Please update PHP in cPanel → Select PHP Version</div>";
}

// Check 2: Required PHP Extensions
echo "<h2>2. PHP Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'json', 'bcmath', 'ctype', 'fileinfo', 'xml'];
$missing = [];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='check success'>✅ <strong>{$ext}</strong>: Loaded</div>";
    } else {
        echo "<div class='check error'>❌ <strong>{$ext}</strong>: Missing</div>";
        $missing[] = $ext;
    }
}

if (!empty($missing)) {
    echo "<div class='check warning'>⚠️ <strong>Missing extensions:</strong> " . implode(', ', $missing) . "<br>Enable them in cPanel → Select PHP Version → Extensions</div>";
}

// Check 3: Directory Structure
echo "<h2>3. Directory Structure</h2>";
$base_dir = __DIR__;
$required_dirs = [
    'app' => 'Application code',
    'bootstrap' => 'Bootstrap files',
    'config' => 'Configuration files',
    'database' => 'Database files',
    'public' => 'Public web directory',
    'resources' => 'Views and assets',
    'routes' => 'Route definitions',
    'storage' => 'Storage directory',
    'vendor' => 'Composer dependencies (REQUIRED!)'
];

foreach ($required_dirs as $dir => $description) {
    $path = $base_dir . '/' . $dir;
    if (is_dir($path)) {
        echo "<div class='check success'>✅ <strong>{$dir}/</strong>: Exists - {$description}</div>";
    } else {
        $is_vendor = ($dir === 'vendor');
        $class = $is_vendor ? 'error' : 'warning';
        echo "<div class='check {$class}'>" . ($is_vendor ? '❌' : '⚠️') . " <strong>{$dir}/</strong>: Missing - {$description}</div>";
    }
}

// Check 4: Vendor Directory (Critical)
echo "<h2>4. Composer Dependencies</h2>";
if (is_dir($base_dir . '/vendor')) {
    $autoload = $base_dir . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        echo "<div class='check success'>✅ <strong>Composer autoloader:</strong> Found</div>";
        
        // Try to load Laravel
        try {
            require $autoload;
            echo "<div class='check success'>✅ <strong>Laravel dependencies:</strong> Loaded successfully</div>";
            
            // Check if Laravel app can be created
            if (file_exists($base_dir . '/bootstrap/app.php')) {
                echo "<div class='check success'>✅ <strong>Laravel bootstrap:</strong> Ready</div>";
            }
        } catch (Exception $e) {
            echo "<div class='check error'>❌ <strong>Error loading Laravel:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='check error'>❌ <strong>Composer autoloader:</strong> Missing</div>";
    }
} else {
    echo "<div class='check error'>";
    echo "❌ <strong>CRITICAL: vendor/ directory missing!</strong><br><br>";
    echo "<strong>This is why you see a white page!</strong><br><br>";
    echo "<strong>Solution - Install Composer dependencies:</strong><br><br>";
    echo "<strong>Method 1: Via SSH (Recommended)</strong><br>";
    echo "<pre>cd ~/public_html/server\ncurl -sS https://getcomposer.org/installer | php\nphp composer.phar install --no-dev --optimize-autoloader</pre>";
    echo "<strong>Method 2: Via cPanel Terminal</strong><br>";
    echo "1. Go to cPanel → Advanced → Terminal<br>";
    echo "2. Run the same commands as above<br><br>";
    echo "<strong>Method 3: Upload vendor/ from local</strong><br>";
    echo "1. On your local machine: <code>cd server && composer install</code><br>";
    echo "2. ZIP the vendor/ folder<br>";
    echo "3. Upload to cPanel and extract in /public_html/server/<br>";
    echo "</div>";
}

// Check 5: .env File
echo "<h2>5. Environment Configuration</h2>";
$env_file = $base_dir . '/.env';
if (file_exists($env_file)) {
    echo "<div class='check success'>✅ <strong>.env file:</strong> Exists</div>";
    
    // Parse .env
    $env = parse_ini_file($env_file, false, INI_SCANNER_RAW);
    
    // Check APP_KEY
    if (!empty($env['APP_KEY'])) {
        echo "<div class='check success'>✅ <strong>APP_KEY:</strong> Set</div>";
    } else {
        echo "<div class='check error'>❌ <strong>APP_KEY:</strong> Not set<br>Visit: https://employee.shahek.org/generate-keys.php</div>";
    }
    
    // Check JWT_SECRET
    if (!empty($env['JWT_SECRET'])) {
        echo "<div class='check success'>✅ <strong>JWT_SECRET:</strong> Set</div>";
    } else {
        echo "<div class='check error'>❌ <strong>JWT_SECRET:</strong> Not set<br>Visit: https://employee.shahek.org/generate-keys.php</div>";
    }
    
    // Check Database
    if (!empty($env['DB_DATABASE']) && !empty($env['DB_USERNAME'])) {
        echo "<div class='check success'>✅ <strong>Database configured:</strong> {$env['DB_DATABASE']}</div>";
        
        // Test connection
        try {
            $pdo = new PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}",
                $env['DB_USERNAME'],
                $env['DB_PASSWORD']
            );
            echo "<div class='check success'>✅ <strong>Database connection:</strong> Successful</div>";
        } catch (PDOException $e) {
            echo "<div class='check error'>❌ <strong>Database connection:</strong> Failed<br>" . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='check error'>❌ <strong>Database:</strong> Not configured</div>";
    }
} else {
    echo "<div class='check error'>❌ <strong>.env file:</strong> Missing<br>Copy from .env.example</div>";
}

// Check 6: File Permissions
echo "<h2>6. File Permissions</h2>";
$storage_writable = is_writable($base_dir . '/storage');
$cache_writable = is_writable($base_dir . '/bootstrap/cache');

if ($storage_writable) {
    echo "<div class='check success'>✅ <strong>storage/:</strong> Writable</div>";
} else {
    echo "<div class='check error'>❌ <strong>storage/:</strong> Not writable<br>Run: chmod -R 775 storage/</div>";
}

if ($cache_writable) {
    echo "<div class='check success'>✅ <strong>bootstrap/cache/:</strong> Writable</div>";
} else {
    echo "<div class='check error'>❌ <strong>bootstrap/cache/:</strong> Not writable<br>Run: chmod -R 775 bootstrap/cache/</div>";
}

// Summary
echo "<h2>📋 Summary</h2>";

$vendor_missing = !is_dir($base_dir . '/vendor');

if ($vendor_missing) {
    echo "<div class='check error'>";
    echo "<h3>🚨 CRITICAL ISSUE: Composer Dependencies Missing</h3>";
    echo "<p><strong>This is why your site shows a white page!</strong></p>";
    echo "<p><strong>Quick Fix (SSH Required):</strong></p>";
    echo "<pre>cd ~/public_html/server
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader</pre>";
    echo "<p>After running this, refresh your dashboard!</p>";
    echo "</div>";
} else {
    echo "<div class='check success'>";
    echo "<h3>✅ System Configuration Complete!</h3>";
    echo "<p>Your application should be working now.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>🗑️ <strong>DELETE THIS FILE (check.php)</strong></li>";
    echo "<li>Visit: <a href='/admin/dashboard'>https://employee.shahek.org/admin/dashboard</a></li>";
    echo "<li>Test API: <a href='/api/auth/login'>https://employee.shahek.org/api/auth/login</a></li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div></body></html>";
?>