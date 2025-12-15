<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>500 Error Diagnosis</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        pre { background: #252526; padding: 15px; border-radius: 4px; border-left: 3px solid #007acc; white-space: pre-wrap; }
        h2 { color: #569cd6; border-bottom: 2px solid #569cd6; padding-bottom: 5px; }
        .section { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔍 500 Error Complete Diagnosis</h1>
    
<?php

echo "<div class='section'><h2>Step 1: Check Laravel Logs</h2>";
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "<div class='success'>✅ Log file exists</div>";
    echo "<pre>Size: " . filesize($logFile) . " bytes</pre>";
    
    // Read last 100 lines
    $lines = file($logFile);
    $lastLines = array_slice($lines, -100);
    echo "<h3>Last 100 lines:</h3>";
    echo "<pre style='max-height: 400px; overflow-y: auto;'>";
    echo htmlspecialchars(implode('', $lastLines));
    echo "</pre>";
} else {
    echo "<div class='warning'>⚠️ No log file found at: $logFile</div>";
}
echo "</div>";

echo "<div class='section'><h2>Step 2: Try to Bootstrap Laravel</h2>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "<div class='success'>✅ Autoload successful</div>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Autoload failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "<div class='success'>✅ App bootstrapped</div>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Bootstrap failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<div class='success'>✅ Kernel created</div>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Kernel creation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}
echo "</div>";

echo "<div class='section'><h2>Step 3: Test Dashboard Route</h2>";
try {
    $request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
    echo "<div class='success'>✅ Request created</div>";
    
    $response = $kernel->handle($request);
    echo "<div class='success'>✅ Response generated</div>";
    echo "<pre>";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content Length: " . strlen($response->getContent()) . " bytes\n";
    echo "</pre>";
    
    if ($response->getStatusCode() == 500) {
        echo "<div class='error'>❌ Route returned 500 error</div>";
        echo "<h3>Response Content (first 2000 chars):</h3>";
        echo "<pre>" . htmlspecialchars(substr($response->getContent(), 0, 2000)) . "</pre>";
    }
    
} catch (Throwable $e) {
    echo "<div class='error'>❌ Request handling failed</div>";
    echo "<pre>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n\n";
    echo "Stack Trace:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
echo "</div>";

echo "<div class='section'><h2>Step 4: Check Database Connection</h2>";
try {
    $db = $app->make('db');
    echo "<div class='success'>✅ Database service available</div>";
    
    $pdo = $db->connection()->getPdo();
    echo "<div class='success'>✅ PDO connection established</div>";
    
    $dbName = $db->select('SELECT DATABASE() as db')[0]->db;
    echo "<pre>Connected to: $dbName</pre>";
    
    // Test employees table
    $count = $db->table('employees')->count();
    echo "<div class='success'>✅ Employees table accessible: $count records</div>";
    
} catch (Throwable $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
echo "</div>";

echo "<div class='section'><h2>Step 5: Check View Files</h2>";
$viewFile = __DIR__ . '/../resources/views/admin/dashboard.blade.php';
if (file_exists($viewFile)) {
    echo "<div class='success'>✅ Dashboard view exists</div>";
    echo "<pre>Size: " . filesize($viewFile) . " bytes</pre>";
} else {
    echo "<div class='error'>❌ Dashboard view NOT FOUND: $viewFile</div>";
}

$layoutFile = __DIR__ . '/../resources/views/layouts/app.blade.php';
if (file_exists($layoutFile)) {
    echo "<div class='success'>✅ Layout view exists</div>";
    echo "<pre>Size: " . filesize($layoutFile) . " bytes</pre>";
} else {
    echo "<div class='error'>❌ Layout view NOT FOUND: $layoutFile</div>";
}
echo "</div>";

echo "<div class='section'><h2>Step 6: Test View Rendering</h2>";
try {
    $view = $app->make('view');
    echo "<div class='success'>✅ View factory available</div>";
    
    $rendered = $view->make('admin.dashboard')->render();
    echo "<div class='success'>✅ Dashboard view rendered successfully!</div>";
    echo "<pre>Rendered size: " . strlen($rendered) . " bytes</pre>";
    
} catch (Throwable $e) {
    echo "<div class='error'>❌ View rendering failed</div>";
    echo "<pre>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n\n";
    echo "Stack Trace:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
echo "</div>";

echo "<div class='section'><h2>Step 7: Check .env Configuration</h2>";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<div class='success'>✅ .env file exists</div>";
    $env = file_get_contents($envFile);
    $lines = explode("\n", $env);
    echo "<pre>";
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#')) {
            if (str_contains($line, 'PASSWORD') || str_contains($line, 'KEY') || str_contains($line, 'SECRET')) {
                echo htmlspecialchars(explode('=', $line)[0]) . "=***\n";
            } else {
                echo htmlspecialchars($line) . "\n";
            }
        }
    }
    echo "</pre>";
} else {
    echo "<div class='error'>❌ .env file NOT FOUND</div>";
}
echo "</div>";

echo "<div class='section'><h2>✅ Diagnosis Complete</h2>";
echo "<p>Check the errors above to identify the issue.</p>";
echo "</div>";

?>
</body>
</html>
