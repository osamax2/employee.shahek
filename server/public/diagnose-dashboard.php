<?php
// Dashboard Route Diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Dashboard Route Diagnosis\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);

try {
    require_once $basePath . '/vendor/autoload.php';
    echo "✅ Autoload successful\n";
    
    $app = require_once $basePath . '/bootstrap/app.php';
    echo "✅ App bootstrapped\n";
    
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Kernel bootstrapped\n";
    
    // Check User model
    echo "\nStep 1: Check User Model\n";
    echo str_repeat("-", 80) . "\n";
    
    if (class_exists('App\Models\User')) {
        echo "✅ User model exists\n";
        
        $adminEmail = env('ADMIN_EMAIL', 'admin@company.com');
        echo "Admin email from .env: $adminEmail\n";
        
        try {
            $admin = App\Models\User::where('email', $adminEmail)->first();
            if ($admin) {
                echo "✅ Admin user found: " . $admin->name . " (ID: " . $admin->id . ")\n";
            } else {
                echo "❌ Admin user NOT FOUND for email: $adminEmail\n";
                echo "Available users:\n";
                $users = App\Models\User::all();
                foreach ($users as $user) {
                    echo "  - {$user->email} (ID: {$user->id}, Name: {$user->name})\n";
                }
            }
        } catch (\Exception $e) {
            echo "❌ Error querying users: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ User model NOT FOUND\n";
    }
    
    // Test dashboard route directly
    echo "\nStep 2: Test Dashboard Route\n";
    echo str_repeat("-", 80) . "\n";
    
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    $request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
    
    try {
        $response = $kernel->handle($request);
        echo "Status: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() === 200) {
            echo "✅ Dashboard route successful\n";
        } else {
            echo "❌ Dashboard route failed\n";
            echo "Content (first 1000 chars):\n";
            echo substr($response->getContent(), 0, 1000) . "\n";
        }
        
        $kernel->terminate($request, $response);
        
    } catch (\Exception $e) {
        echo "❌ EXCEPTION\n";
        echo "Type: " . get_class($e) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
        
        echo "\nStack Trace (first 15 lines):\n";
        $trace = explode("\n", $e->getTraceAsString());
        foreach (array_slice($trace, 0, 15) as $line) {
            echo $line . "\n";
        }
    }
    
    // Check routes
    echo "\nStep 3: Check Registered Routes\n";
    echo str_repeat("-", 80) . "\n";
    
    $routes = app('router')->getRoutes();
    $adminRoutes = 0;
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin') !== false) {
            echo "  - " . implode('|', $route->methods()) . " /$uri\n";
            $adminRoutes++;
        }
    }
    echo "Found $adminRoutes admin routes\n";
    
    // Check latest Laravel log
    echo "\nStep 4: Latest Laravel Log Entries\n";
    echo str_repeat("-", 80) . "\n";
    
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        $lines = file($logPath);
        $lastLines = array_slice($lines, -20);
        foreach ($lastLines as $line) {
            echo $line;
        }
    } else {
        echo "❌ Log file not found\n";
    }
    
} catch (\Exception $e) {
    echo "❌ FATAL ERROR\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ Diagnosis Complete\n";
