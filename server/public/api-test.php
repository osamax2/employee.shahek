<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        pre { background: #252526; padding: 15px; border-radius: 4px; border-left: 3px solid #007acc; }
    </style>
</head>
<body>
    <h1>🔍 Laravel API Test</h1>
    
<?php
echo "<h2>1️⃣ Check Bootstrap File</h2>";
$bootstrapFile = __DIR__ . '/../bootstrap/app.php';
if (file_exists($bootstrapFile)) {
    echo "<div class='success'>✅ bootstrap/app.php exists</div>";
    echo "<pre>Size: " . filesize($bootstrapFile) . " bytes</pre>";
} else {
    echo "<div class='error'>❌ bootstrap/app.php NOT FOUND</div>";
    exit;
}

echo "<h2>2️⃣ Try to Load Laravel</h2>";
try {
    require $bootstrapFile;
    echo "<div class='success'>✅ bootstrap/app.php loaded</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
} catch (Error $e) {
    echo "<div class='error'>❌ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>3️⃣ Check Application Instance</h2>";
if (isset($app)) {
    echo "<div class='success'>✅ \$app instance exists</div>";
    echo "<pre>Type: " . get_class($app) . "</pre>";
} else {
    echo "<div class='error'>❌ \$app not defined</div>";
    exit;
}

echo "<h2>4️⃣ Create Kernel</h2>";
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<div class='success'>✅ Kernel created</div>";
    echo "<pre>Type: " . get_class($kernel) . "</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Kernel Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
}

echo "<h2>5️⃣ Handle Request</h2>";
try {
    $request = Illuminate\Http\Request::capture();
    echo "<div class='success'>✅ Request captured</div>";
    echo "<pre>URL: " . $request->fullUrl() . "</pre>";
    
    $response = $kernel->handle($request);
    echo "<div class='success'>✅ Response generated</div>";
    echo "<pre>Status: " . $response->getStatusCode() . "</pre>";
    echo "<pre>Content Length: " . strlen($response->getContent()) . " bytes</pre>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Request Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>6️⃣ Test Route</h2>";
try {
    $testRequest = Illuminate\Http\Request::create('/api/admin/employees', 'GET');
    $testResponse = $kernel->handle($testRequest);
    echo "<div class='success'>✅ Route test completed</div>";
    echo "<pre>Status: " . $testResponse->getStatusCode() . "</pre>";
    echo "<pre>Content: " . htmlspecialchars(substr($testResponse->getContent(), 0, 200)) . "...</pre>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Route Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h2>✅ Test Complete</h2>";
?>
</body>
</html>
