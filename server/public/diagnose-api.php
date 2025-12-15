<?php
// API Endpoints Diagnostic Tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 API Endpoints Diagnosis\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Test endpoints
$endpoints = [
    ['GET', '/api/admin/locations/latest?active_only=0', 'Locations API'],
    ['GET', '/api/admin/stats', 'Stats API'],
];

foreach ($endpoints as $index => $endpoint) {
    [$method, $uri, $name] = $endpoint;
    
    echo "Test " . ($index + 1) . ": $name\n";
    echo "Endpoint: $method $uri\n";
    echo str_repeat("-", 80) . "\n";
    
    try {
        // Create request
        $request = Illuminate\Http\Request::create($uri, $method);
        $request->headers->set('Accept', 'application/json');
        
        // Handle request
        $response = $kernel->handle($request);
        
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
        
        $content = $response->getContent();
        
        if ($response->getStatusCode() === 200) {
            echo "✅ SUCCESS\n";
            echo "Response (first 500 chars):\n";
            echo substr($content, 0, 500) . "\n";
        } else {
            echo "❌ FAILED\n";
            echo "Response:\n";
            echo substr($content, 0, 2000) . "\n";
            
            // Try to extract error from HTML
            if (preg_match('/<title>(.*?)<\/title>/s', $content, $matches)) {
                echo "\nError Title: " . $matches[1] . "\n";
            }
            
            // Try to find exception message
            if (preg_match('/Exception.*?:(.*?)at/s', $content, $matches)) {
                echo "Exception: " . trim($matches[1]) . "\n";
            }
        }
        
        $kernel->terminate($request, $response);
        
    } catch (\Exception $e) {
        echo "❌ EXCEPTION\n";
        echo "Type: " . get_class($e) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
        echo "\nStack Trace:\n";
        echo $e->getTraceAsString() . "\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
}

// Check if AdminController exists
echo "Controller Check:\n";
echo str_repeat("-", 80) . "\n";

$controllerPath = $basePath . '/app/Http/Controllers/AdminController.php';
if (file_exists($controllerPath)) {
    echo "✅ AdminController.php exists\n";
    echo "Size: " . filesize($controllerPath) . " bytes\n";
    
    // Show first 100 lines
    $lines = file($controllerPath);
    echo "\nFirst 50 lines:\n";
    foreach (array_slice($lines, 0, 50) as $num => $line) {
        echo sprintf("%3d: %s", $num + 1, $line);
    }
} else {
    echo "❌ AdminController.php NOT FOUND\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ API Diagnosis Complete\n";
