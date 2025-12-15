<?php
// Test View Compilation
header('Content-Type: text/html; charset=utf-8');

echo "<h2>View Compilation Test</h2>";

// Check if Laravel can boot
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "✅ Laravel booted successfully<br>";
    
    // Check view paths
    echo "<h3>View Configuration:</h3>";
    $config = $app['config'];
    $viewPaths = $config->get('view.paths');
    echo "View paths: <pre>" . print_r($viewPaths, true) . "</pre>";
    
    $compiledPath = $config->get('view.compiled');
    echo "Compiled path: " . $compiledPath . "<br>";
    
    // Check if compiled path exists and is writable
    if (file_exists($compiledPath)) {
        echo "✅ Compiled path exists<br>";
        if (is_writable($compiledPath)) {
            echo "✅ Compiled path is writable<br>";
        } else {
            echo "❌ Compiled path is NOT writable<br>";
            echo "Permissions: " . substr(sprintf('%o', fileperms($compiledPath)), -4) . "<br>";
        }
    } else {
        echo "❌ Compiled path does NOT exist<br>";
    }
    
    // Check if view files exist
    echo "<h3>View Files:</h3>";
    $layoutPath = resource_path('views/layouts/app.blade.php');
    echo "Layout: " . $layoutPath . " - ";
    if (file_exists($layoutPath)) {
        echo "✅ EXISTS (" . filesize($layoutPath) . " bytes)<br>";
    } else {
        echo "❌ NOT FOUND<br>";
    }
    
    $dashboardPath = resource_path('views/admin/dashboard.blade.php');
    echo "Dashboard: " . $dashboardPath . " - ";
    if (file_exists($dashboardPath)) {
        echo "✅ EXISTS (" . filesize($dashboardPath) . " bytes)<br>";
    } else {
        echo "❌ NOT FOUND<br>";
    }
    
    // Try to compile a simple view
    echo "<h3>View Compilation Test:</h3>";
    try {
        $view = app('view');
        $test = $view->make('admin.dashboard')->render();
        if (strlen($test) > 0) {
            echo "✅ View compiled successfully (" . strlen($test) . " bytes)<br>";
            echo "<h4>First 500 characters:</h4>";
            echo "<pre>" . htmlspecialchars(substr($test, 0, 500)) . "</pre>";
        } else {
            echo "⚠️ View compiled but output is empty<br>";
        }
    } catch (Exception $e) {
        echo "❌ View compilation failed<br>";
        echo "Error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel failed to boot<br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}

echo "<hr>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Script: " . __FILE__ . "<br>";
