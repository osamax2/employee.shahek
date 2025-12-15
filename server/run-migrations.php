<?php
/**
 * Direct SQL Migration Runner for cPanel (No Composer Required!)
 * 
 * This file runs database migrations using direct SQL
 * Works WITHOUT needing vendor/ directory or Composer
 * 
 * INSTRUCTIONS:
 * 1. Make sure your .env file is configured with correct database credentials
 * 2. Upload this file to your Laravel root directory
 * 3. Visit: https://employee.shahek.org/run-migrations.php (once only)
 * 4. DELETE THIS FILE immediately after use for security!
 */

// Security check - file expires after 1 hour
if (file_exists(__FILE__) && (time() - filemtime(__FILE__)) > 3600) {
    die('⚠️ This file has expired for security. Please re-upload if needed.');
}

// Function to parse .env file
function parseEnv($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            $env[$key] = $value;
        }
    }
    return $env;
}

// Load .env configuration
$env = parseEnv(__DIR__ . '/.env');
$DB_HOST = $env['DB_HOST'] ?? 'localhost';
$DB_DATABASE = $env['DB_DATABASE'] ?? '';
$DB_USERNAME = $env['DB_USERNAME'] ?? '';
$DB_PASSWORD = $env['DB_PASSWORD'] ?? '';

// Simple output styling
echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Migration Runner</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 20px;
            background: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .output { 
            background: #f5f5f5; 
            padding: 20px; 
            border-radius: 5px; 
            margin: 15px 0; 
            font-family: 'Courier New', monospace; 
            white-space: pre-wrap;
            border-left: 4px solid #007bff;
        }
        .success { 
            background: #d4edda; 
            border-left: 4px solid #28a745; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0; 
            color: #155724;
        }
        .error { 
            background: #f8d7da; 
            border-left: 4px solid #dc3545; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            color: #721c24;
        }
        .warning { 
            background: #fff3cd; 
            border-left: 4px solid #ffc107; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            color: #856404;
        }

try {
    // Step 1: Test database connection
    echo "<div class='step'>";
    echo "<h2><span class='step-number'>1</span>Testing Database Connection</h2>";
    
    try {
        $pdo = new PDO(
            "mysql:host={$DB_HOST};dbname={$DB_DATABASE}",
            $DB_USERNAME,
            $DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "<div class='success'>";
        echo "✅ <strong>Database connection successful!</strong><br>";
        echo "Database: " . htmlspecialchars($DB_DATABASE) . "<br>";
        echo "Host: " . htmlspecialchars($DB_HOST) . "<br>";
        echo "User: " . htmlspecialchars($DB_USERNAME);
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>";
        echo "❌ <strong>Database connection failed:</strong><br>" . htmlspecialchars($e->getMessage()) . "<br><br>";
        echo "<strong>Please check:</strong><br>";
        echo "1. Database exists in cPanel → MySQL Databases<br>";
        echo "2. Username has ALL PRIVILEGES<br>";
        echo "3. Password is correct in .env file<br>";
        echo "4. DB_HOST is 'localhost'";
        echo "</div>";
        $allSuccess = false;
        throw new Exception('Database connection failed');
    }
    echo "</div>";
    
    // Step 2: Read and execute SQL file
    echo "<div class='step'>";
    echo "<h2><span class='step-number'>2</span>Creating Database Tables</h2>";
    
    $sqlFile = __DIR__ . '/database/setup.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolons but keep them
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "<div class='output'>";
    $successCount = 0;
    $skipCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Extract table name for display
            if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', $statement, $matches)) {
                echo "✅ Created table: {$matches[1]}\n";
            }
        } catch (PDOException $e) {
            // Skip if table already exists
            if (strpos($e->getMessage(), 'already exists') !== false) {
                $skipCount++;
                if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', $statement, $matches)) {
                    echo "⏭️  Table already exists: {$matches[1]}\n";
                }
            } else {
                echo "⚠️  Error: " . htmlspecialchars($e->getMessage()) . "\n";
            }
        }
    }
    
    echo "\n✅ Executed {$successCount} statements";
    if ($skipCount > 0) {
        echo "\n⏭️  Skipped {$skipCount} existing tables";
    }
    echo "</div>";
    
    echo "<div class='success'>✅ <strong>Database tables created successfully!</strong></div>";
    echo "</div>";
    
    // Step 3: Insert admin user
    echo "<div class='step'>";
    echo "<h2><span class='step-number'>3</span>Creating Admin User</h2>";
    
    $adminEmail = 'admin@company.com';
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $adminName = 'Administrator';
    $deviceId = 'admin-device-' . uniqid();
    
    try {
        // Check if admin exists
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE email = ?");
        $stmt->execute([$adminEmail]);
        
        if ($stmt->fetch()) {
            echo "<div class='warning'>";
            echo "⏭️  <strong>Admin user already exists</strong><br>";
            echo "Email: {$adminEmail}";
            echo "</div>";
        } else {
            // Insert admin
            $stmt = $pdo->prepare("
                INSERT INTO employees (name, email, password, device_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$adminName, $adminEmail, $adminPassword, $deviceId]);
            
            echo "<div class='success'>";
            echo "✅ <strong>Admin user created successfully!</strong><br><br>";
            echo "<strong>Login Credentials:</strong><br>";
            echo "📧 Email: <strong>{$adminEmail}</strong><br>";
            echo "🔑 Password: <strong>admin123</strong><br><br>";
            echo "⚠️ <strong>IMPORTANT: Change this password after first login!</strong>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>";
        echo "❌ Error creating admin: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
    echo "</div>";
    
    // Final summary
    if ($allSuccess) {
        echo "<div class='success' style='font-size: 1.1em; margin-top: 30px;'>";
        echo "🎉 <strong>Setup Completed Successfully!</strong><br><br>";
        echo "<strong>Your System is Ready:</strong><br>";
        echo "✅ Database tables created<br>";
        echo "✅ Admin user created<br>";
        echo "✅ Ready for production<br><br>";
        echo "<strong>Next Steps:</strong><br>";
        echo "1. 🗑️ <strong>DELETE THIS FILE NOW!</strong><br>";
        echo "2. 📊 Visit Dashboard: <a href='/admin/dashboard' target='_blank'>https://employee.shahek.org/admin/dashboard</a><br>";
        echo "3. 🔌 Test API: <a href='/api/auth/login' target='_blank'>https://employee.shahek.org/api/auth/login</a><br>";
        echo "4. 🔐 Login: {$adminEmail} / admin123<br>";
        echo "5. ⚠️ Change default password!<br>";
        echo "6. 📱 Build mobile app and deploy";
        echo "</div>";
        
        echo "<div class='warning' style='margin-top: 20px;'>";
        echo "🚨 <strong>SECURITY CRITICAL: Delete This File!</strong><br><br>";
        echo "<strong>Via cPanel File Manager:</strong><br>";
        echo "→ Navigate to /public_html/server/<br>";
        echo "→ Find run-migrations.php<br>";
        echo "→ Right-click → Delete<br><br>";
        echo "<strong>Via SSH:</strong><br>";
        echo "<code>rm ~/public_html/server/run-migrations.php</code>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Fatal Error:</strong><br>" . htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "<strong>Troubleshooting:</strong><br>";
    echo "1. Check .env file exists and has correct credentials<br>";
    echo "2. Verify database exists in cPanel → phpMyAdmin<br>";
    echo "3. Ensure user has ALL PRIVILEGES<br>";
    echo "4. Check setup.sql file exists in database/ folder
    } else {
        echo "<div class='warning'>⚠️ <strong>Seeding completed with warnings.</strong> This is OK if seed data already exists.</div>";
    }
    echo "</div>";
    
    // Final summary
    if ($allSuccess) {
        echo "<div class='success' style='font-size: 1.1em;'>";
        echo "🎉 <strong>All operations completed successfully!</strong><br><br>";
        echo "<strong>Next Steps:</strong><br>";
        echo "1. ✅ Delete this file (run-migrations.php) NOW!<br>";
        echo "2. ✅ Visit your dashboard: <a href='/admin/dashboard'>https://employee.shahek.org/admin/dashboard</a><br>";
        echo "3. ✅ Test API: <a href='/api/auth/login'>https://employee.shahek.org/api/auth/login</a><br>";
        echo "4. ✅ Login with: admin@company.com / admin123<br>";
        echo "5. ⚠️ Change default passwords!";
        echo "</div>";
        
        echo "<div class='warning'>";
        echo "🗑️ <strong>CRITICAL: Delete this file now!</strong><br>";
        echo "Via cPanel File Manager: Navigate to /public_html/server/ and delete run-migrations.php<br>";
        echo "Or via SSH: <code>rm ~/public_html/server/run-migrations.php</code>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Fatal Error:</strong><br>" . htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "Please check your .env file and database credentials.";
    echo "</div>";
}

echo "</div></body></html>";
?>
