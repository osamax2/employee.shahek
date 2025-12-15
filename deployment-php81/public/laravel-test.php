<?php
// Simple Laravel Test - No Views
header('Content-Type: text/html; charset=utf-8');

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<!DOCTYPE html><html><head><title>Laravel Test</title></head><body>";
    echo "<h1>✅ Laravel Boots Successfully</h1>";
    
    // Test database
    try {
        $db = $app->make('db');
        $pdo = $db->connection()->getPdo();
        echo "<p>✅ Database Connected: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Test routes
    try {
        $router = $app->make('router');
        $routes = $router->getRoutes();
        echo "<h2>Registered Routes:</h2><ul>";
        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = implode('|', $route->methods());
            if (strpos($uri, 'admin') !== false || strpos($uri, 'api') !== false) {
                echo "<li><code>$methods $uri</code></li>";
            }
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p>❌ Routes Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Test view compilation
    echo "<h2>View Test:</h2>";
    try {
        $viewFactory = $app->make('view');
        echo "<p>✅ View Factory Created</p>";
        
        $viewPaths = config('view.paths');
        echo "<p>View Paths: <code>" . implode(', ', $viewPaths) . "</code></p>";
        
        $compiledPath = config('view.compiled');
        echo "<p>Compiled Path: <code>$compiledPath</code></p>";
        
        if (!file_exists($compiledPath)) {
            echo "<p>⚠️ Compiled path doesn't exist! Creating...</p>";
            mkdir($compiledPath, 0775, true);
        }
        
        if (!is_writable($compiledPath)) {
            echo "<p>❌ Compiled path NOT writable!</p>";
        } else {
            echo "<p>✅ Compiled path is writable</p>";
        }
        
        // Try to render dashboard view
        echo "<h3>Rendering admin.dashboard view:</h3>";
        try {
            $content = $viewFactory->make('admin.dashboard')->render();
            $length = strlen($content);
            echo "<p>✅ View rendered: $length bytes</p>";
            if ($length > 0) {
                echo "<h4>First 500 characters:</h4>";
                echo "<pre>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";
            } else {
                echo "<p>⚠️ View rendered but OUTPUT IS EMPTY!</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ View Render Failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ View Factory Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html><html><body>";
    echo "<h1>❌ Laravel Boot Failed</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
}
