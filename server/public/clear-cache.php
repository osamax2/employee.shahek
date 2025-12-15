<?php
// Cache Clear & Config Check
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🧹 Cache Clear & Config Verification</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<h3>1. Clear Caches:</h3>";
    
    // Clear config cache
    $configCachePath = $app->bootstrapPath('cache/config.php');
    if (file_exists($configCachePath)) {
        unlink($configCachePath);
        echo "<p>✅ Config cache cleared: $configCachePath</p>";
    } else {
        echo "<p>ℹ️ No config cache found</p>";
    }
    
    // Clear route cache
    $routeCachePath = $app->bootstrapPath('cache/routes-v7.php');
    if (file_exists($routeCachePath)) {
        unlink($routeCachePath);
        echo "<p>✅ Route cache cleared</p>";
    } else {
        echo "<p>ℹ️ No route cache found</p>";
    }
    
    // Clear view cache
    $viewCachePath = $app->storagePath('framework/views');
    if (is_dir($viewCachePath)) {
        $files = glob($viewCachePath . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p>✅ View cache cleared: $count files</p>";
    } else {
        echo "<p>⚠️ View cache directory not found</p>";
    }
    
    echo "<h3>2. Check config/app.php:</h3>";
    
    $configPath = base_path('config/app.php');
    echo "<p>Config file: <code>$configPath</code></p>";
    
    if (file_exists($configPath)) {
        $filesize = filesize($configPath);
        $modified = date('Y-m-d H:i:s', filemtime($configPath));
        echo "<p>✅ File exists: $filesize bytes, modified: $modified</p>";
        
        // Read and check providers
        $content = file_get_contents($configPath);
        
        if (strpos($content, 'DatabaseServiceProvider') !== false) {
            echo "<p>✅ DatabaseServiceProvider found in config</p>";
        } else {
            echo "<p>❌ DatabaseServiceProvider NOT found in config!</p>";
        }
        
        if (strpos($content, 'ViewServiceProvider') !== false) {
            echo "<p>✅ ViewServiceProvider found in config</p>";
        } else {
            echo "<p>❌ ViewServiceProvider NOT found in config!</p>";
        }
        
        if (strpos($content, 'ServiceProvider::defaultProviders') !== false) {
            echo "<p>⚠️ Still using defaultProviders() - OLD CONFIG!</p>";
        } else {
            echo "<p>✅ Using explicit provider array - NEW CONFIG</p>";
        }
        
        // Show first 50 lines of providers section
        echo "<h4>Config excerpt (providers section):</h4>";
        $lines = explode("\n", $content);
        $inProviders = false;
        $providersLines = [];
        foreach ($lines as $i => $line) {
            if (strpos($line, "'providers'") !== false) {
                $inProviders = true;
            }
            if ($inProviders) {
                $providersLines[] = htmlspecialchars($line);
                if (strpos($line, '],') !== false && count($providersLines) > 5) {
                    break;
                }
            }
        }
        echo "<pre>" . implode("\n", array_slice($providersLines, 0, 30)) . "</pre>";
        
    } else {
        echo "<p>❌ Config file NOT found!</p>";
    }
    
    echo "<h3>3. Reload and test:</h3>";
    
    // Force reload config
    echo "<p>🔄 Reloading application...</p>";
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Test database
    try {
        $db = $app->make('db');
        echo "<p>✅ Database service resolved!</p>";
    } catch (Exception $e) {
        echo "<p>❌ Database service failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Test view
    try {
        $view = $app->make('view');
        echo "<p>✅ View service resolved!</p>";
    } catch (Exception $e) {
        echo "<p>❌ View service failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<hr>";
    echo "<p><strong>🔄 After clearing caches, test again:</strong></p>";
    echo "<p><a href='/public/laravel-test.php'>Re-run Laravel Test</a></p>";
    echo "<p><a href='/public/admin/dashboard'>Try Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
