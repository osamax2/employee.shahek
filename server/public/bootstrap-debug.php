<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bootstrap Debug</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; font-size: 16px; }
        .error { color: #f48771; font-size: 16px; }
        .warning { color: #dcdcaa; font-size: 16px; }
        pre { background: #252526; padding: 15px; border-radius: 4px; border-left: 3px solid #007acc; white-space: pre-wrap; overflow-x: auto; }
        h2 { color: #569cd6; }
    </style>
</head>
<body>
    <h1>🔍 Step-by-Step Bootstrap Debug</h1>
    
<?php

echo "<h2>Step 1: Check Files</h2>";
$bootstrapFile = __DIR__ . '/../bootstrap/app.php';
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';

echo "Bootstrap file: " . ($bootstrapFile) . "<br>";
if (file_exists($bootstrapFile)) {
    echo "<div class='success'>✅ bootstrap/app.php exists (" . filesize($bootstrapFile) . " bytes)</div>";
} else {
    echo "<div class='error'>❌ bootstrap/app.php NOT FOUND!</div>";
    exit;
}

if (file_exists($vendorAutoload)) {
    echo "<div class='success'>✅ vendor/autoload.php exists</div>";
} else {
    echo "<div class='error'>❌ vendor/autoload.php NOT FOUND!</div>";
    exit;
}

echo "<h2>Step 2: Load Composer Autoloader</h2>";
try {
    require $vendorAutoload;
    echo "<div class='success'>✅ Autoloader loaded</div>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Autoloader failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>Step 3: Check .env File</h2>";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<div class='success'>✅ .env exists (" . filesize($envFile) . " bytes)</div>";
    
    // Parse .env to show key variables
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    $envVars = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            // Mask sensitive values
            if (str_contains($key, 'PASSWORD') || str_contains($key, 'KEY') || str_contains($key, 'SECRET')) {
                $value = '***HIDDEN***';
            }
            $envVars[$key] = $value;
        }
    }
    echo "<pre>";
    foreach ($envVars as $k => $v) {
        echo htmlspecialchars("$k=$v") . "\n";
    }
    echo "</pre>";
} else {
    echo "<div class='warning'>⚠️ .env file NOT FOUND!</div>";
}

echo "<h2>Step 4: Check Required Classes</h2>";
$classes = [
    'Illuminate\Foundation\Application',
    'Illuminate\Contracts\Http\Kernel',
    'App\Http\Kernel',
    'App\Console\Kernel',
    'App\Exceptions\Handler',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "<div class='success'>✅ $class</div>";
    } else {
        echo "<div class='error'>❌ $class - NOT FOUND!</div>";
    }
}

echo "<h2>Step 5: Try to Create Application</h2>";
try {
    $basePath = dirname(__DIR__);
    echo "<pre>Base path: $basePath</pre>";
    
    $app = new Illuminate\Foundation\Application($basePath);
    echo "<div class='success'>✅ Application created</div>";
    echo "<pre>App class: " . get_class($app) . "</pre>";
    echo "<pre>Base path: " . $app->basePath() . "</pre>";
    echo "<pre>Environment: " . $app->environment() . "</pre>";
    
} catch (Throwable $e) {
    echo "<div class='error'>❌ Application creation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>Step 6: Register Kernel Bindings</h2>";
try {
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        App\Http\Kernel::class
    );
    echo "<div class='success'>✅ HTTP Kernel bound</div>";
    
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    echo "<div class='success'>✅ Console Kernel bound</div>";
    
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
    echo "<div class='success'>✅ Exception Handler bound</div>";
    
} catch (Throwable $e) {
    echo "<div class='error'>❌ Binding failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>Step 7: Make HTTP Kernel</h2>";
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<div class='success'>✅ Kernel created: " . get_class($kernel) . "</div>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Kernel creation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>Step 8: Create Test Request</h2>";
try {
    $request = Illuminate\Http\Request::create('/test', 'GET');
    echo "<div class='success'>✅ Request created</div>";
    echo "<pre>Method: " . $request->method() . "</pre>";
    echo "<pre>Path: " . $request->path() . "</pre>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Request creation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

echo "<h2>Step 9: Handle Request (This loads providers!)</h2>";
try {
    $response = $kernel->handle($request);
    echo "<div class='success'>✅ Request handled successfully!</div>";
    echo "<pre>Status: " . $response->getStatusCode() . "</pre>";
    echo "<pre>Content length: " . strlen($response->getContent()) . " bytes</pre>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Request handling failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Continue anyway to check services
}

echo "<h2>Step 10: Test Services After Bootstrap</h2>";
$services = [
    'db' => 'Database',
    'view' => 'View Factory', 
    'config' => 'Config',
    'cache' => 'Cache',
];

foreach ($services as $abstract => $name) {
    try {
        $service = $app->make($abstract);
        echo "<div class='success'>✅ $name: " . get_class($service) . "</div>";
    } catch (Throwable $e) {
        echo "<div class='error'>❌ $name: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

echo "<h2>✅ Debug Complete</h2>";

?>
</body>
</html>
