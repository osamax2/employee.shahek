<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Service Provider Debug</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        pre { background: #252526; padding: 15px; border-radius: 4px; border-left: 3px solid #007acc; white-space: pre-wrap; }
        h2 { color: #569cd6; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🔍 Service Provider Debug</h1>
    
<?php

echo "<h2>1️⃣ Load Bootstrap</h2>";
try {
    require __DIR__ . '/../bootstrap/app.php';
    echo "<div class='success'>✅ Bootstrap loaded</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Bootstrap failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

echo "<h2>2️⃣ Create Kernel</h2>";
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<div class='success'>✅ Kernel created: " . get_class($kernel) . "</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Kernel failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

echo "<h2>3️⃣ Handle Dummy Request (This triggers provider loading)</h2>";
try {
    $request = Illuminate\Http\Request::create('/test', 'GET');
    $response = $kernel->handle($request);
    echo "<div class='success'>✅ Request handled</div>";
    echo "<pre>Response Status: " . $response->getStatusCode() . "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Request failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>4️⃣ Check Registered Service Providers</h2>";
try {
    $providers = $app->getLoadedProviders();
    if (empty($providers)) {
        echo "<div class='warning'>⚠️ No providers loaded yet - trying to boot...</div>";
        $app->boot();
        $providers = $app->getLoadedProviders();
    }
    
    echo "<div class='success'>✅ Loaded " . count($providers) . " providers</div>";
    echo "<pre>";
    foreach ($providers as $provider => $loaded) {
        echo ($loaded ? "✅" : "❌") . " " . htmlspecialchars($provider) . "\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Provider check failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h2>5️⃣ Check Key Services</h2>";

$services = [
    'db' => 'Database',
    'view' => 'View Factory',
    'config' => 'Config',
    'cache' => 'Cache',
    'events' => 'Events',
    'files' => 'Filesystem',
];

foreach ($services as $abstract => $name) {
    try {
        $service = $app->make($abstract);
        echo "<div class='success'>✅ $name ($abstract): " . get_class($service) . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ $name ($abstract): " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

echo "<h2>6️⃣ Test Database Connection</h2>";
try {
    $db = $app->make('db');
    $pdo = $db->connection()->getPdo();
    echo "<div class='success'>✅ Database connected!</div>";
    echo "<pre>Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "</pre>";
    
    // Test query
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "<pre>Database: " . $result[0]->db_name . "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h2>7️⃣ Test View</h2>";
try {
    $view = $app->make('view');
    echo "<div class='success'>✅ View factory available: " . get_class($view) . "</div>";
    
    // Check view paths
    $paths = $view->getFinder()->getPaths();
    echo "<pre>View paths:\n";
    foreach ($paths as $path) {
        echo "  - $path\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ View error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h2>8️⃣ Test Routes</h2>";
try {
    $routes = $app->make('router')->getRoutes();
    echo "<div class='success'>✅ Routes loaded: " . $routes->count() . " routes</div>";
    echo "<pre>";
    foreach ($routes as $route) {
        $methods = implode('|', $route->methods());
        echo "$methods " . $route->uri() . "\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Routes error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h2>9️⃣ Config Check</h2>";
try {
    $config = $app->make('config');
    echo "<div class='success'>✅ Config available</div>";
    echo "<pre>";
    echo "APP_NAME: " . $config->get('app.name') . "\n";
    echo "APP_ENV: " . $config->get('app.env') . "\n";
    echo "APP_DEBUG: " . ($config->get('app.debug') ? 'true' : 'false') . "\n";
    echo "DB_CONNECTION: " . $config->get('database.default') . "\n";
    echo "DB_DATABASE: " . $config->get('database.connections.mysql.database') . "\n";
    echo "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Config error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

?>

</body>
</html>
