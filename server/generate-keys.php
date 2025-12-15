<?php
/**
 * Laravel Key Generator for cPanel
 * 
 * This file generates an APP_KEY for your Laravel application
 * Use this if you don't have SSH access to run artisan commands
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your Laravel root directory (same level as artisan)
 * 2. Visit: https://your-domain.com/generate-keys.php
 * 3. Copy the generated keys to your .env file
 * 4. DELETE THIS FILE immediately after use for security!
 */

// Security check - delete this file if older than 1 hour
if (file_exists(__FILE__) && (time() - filemtime(__FILE__)) > 3600) {
    die('This file has expired for security. Please re-upload if needed.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Key Generator</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .key { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0; font-family: monospace; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px; margin: 20px 0; }
        button { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #c82333; }
    </style>
</head>
<body>
    <h1>🔑 Laravel Key Generator</h1>
    <div class='warning'>
        <strong>⚠️ SECURITY WARNING:</strong><br>
        This file should be deleted immediately after use!<br>
        Do not leave it on your server.
    </div>
";

try {
    // Generate APP_KEY
    $appKey = 'base64:'.base64_encode(random_bytes(32));
    
    // Generate JWT_SECRET
    $jwtSecret = base64_encode(random_bytes(32));
    
    echo "<div class='success'>";
    echo "<h2>✅ Keys Generated Successfully!</h2>";
    
    echo "<h3>APP_KEY:</h3>";
    echo "<div class='key'>APP_KEY={$appKey}</div>";
    
    echo "<h3>JWT_SECRET:</h3>";
    echo "<div class='key'>JWT_SECRET={$jwtSecret}</div>";
    
    echo "</div>";
    
    echo "<h2>📋 Next Steps:</h2>";
    echo "<ol>";
    echo "<li>Copy the keys above to your <code>.env</code> file</li>";
    echo "<li>Save the <code>.env</code> file</li>";
    echo "<li><strong>DELETE THIS FILE (generate-keys.php) NOW!</strong></li>";
    echo "</ol>";
    
    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this file?\");'>";
    echo "<input type='hidden' name='delete' value='1'>";
    echo "<button type='submit'>🗑️ Delete This File</button>";
    echo "</form>";
    
    // Handle deletion
    if (isset($_POST['delete']) && $_POST['delete'] === '1') {
        if (unlink(__FILE__)) {
            echo "<div class='success'><strong>✅ File deleted successfully!</strong><br>You can now close this page.</div>";
            exit;
        } else {
            echo "<div class='warning'><strong>❌ Could not auto-delete.</strong><br>Please delete manually via cPanel File Manager.</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='warning'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</body></html>";
?>
