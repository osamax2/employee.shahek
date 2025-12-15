<?php
/**
 * Browser-Based Composer Installer for cPanel
 * 
 * This script installs Composer and dependencies through your web browser.
 * No SSH required!
 * 
 * USAGE:
 * 1. Upload this file to: /public_html/server/
 * 2. Visit: https://employee.shahek.org/install-composer-browser.php
 * 3. Click "Install Composer" button
 * 4. Wait for completion
 * 5. DELETE THIS FILE after success!
 */

set_time_limit(600); // 10 minutes
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

$baseDir = __DIR__;
$composerPhar = $baseDir . '/composer.phar';
$vendorDir = $baseDir . '/vendor';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Install Composer - Employee Tracking</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .step h3 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .success h3 { color: #28a745; }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .error h3 { color: #dc3545; }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .warning h3 { color: #856404; }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }
        button:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        .progress {
            display: none;
            margin-top: 20px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #0056b3);
            width: 0%;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        .log {
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            display: none;
        }
        .log-line {
            margin: 5px 0;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .emoji {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><span class="emoji">📦</span>Composer Installer</h1>
    <p class="subtitle">Install Laravel dependencies without SSH</p>

    <?php
    // Check if already installed
    $composerInstalled = file_exists($composerPhar);
    $vendorInstalled = is_dir($vendorDir) && file_exists($vendorDir . '/autoload.php');

    if ($vendorInstalled) {
        echo '<div class="step success">';
        echo '<h3>✅ Already Installed!</h3>';
        echo '<p>Composer dependencies are already installed.</p>';
        echo '<p><strong>Next steps:</strong></p>';
        echo '<ol>';
        echo '<li>🗑️ <strong>DELETE THIS FILE</strong> (install-composer-browser.php)</li>';
        echo '<li>Visit: <a href="/admin/dashboard">Dashboard</a></li>';
        echo '<li>Login: admin@company.com / admin123</li>';
        echo '</ol>';
        echo '</div>';
        exit;
    }
    ?>

    <div class="step">
        <h3>📋 System Check</h3>
        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?> <?php echo version_compare(phpversion(), '8.1.0', '>=') ? '✅' : '❌ Need 8.1+'; ?></p>
        <p><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
        <p><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</p>
        <p><strong>Composer:</strong> <?php echo $composerInstalled ? '✅ Downloaded' : '⏳ Will download'; ?></p>
        <p><strong>Dependencies:</strong> <?php echo $vendorInstalled ? '✅ Installed' : '⏳ Will install'; ?></p>
    </div>

    <?php if (!function_exists('exec')): ?>
    <div class="step error">
        <h3>❌ exec() Function Disabled</h3>
        <p>This script requires the <code>exec()</code> function to be enabled.</p>
        <p><strong>Alternative:</strong> Upload vendor/ folder manually:</p>
        <ol>
            <li>On your computer, run: <code>composer install --no-dev</code></li>
            <li>ZIP the vendor/ folder</li>
            <li>Upload to cPanel</li>
            <li>Extract in /public_html/server/</li>
        </ol>
    </div>
    <?php exit; endif; ?>

    <div class="step warning">
        <h3>⚠️ Important Notes</h3>
        <ul>
            <li>This process may take 5-10 minutes</li>
            <li>Do not close this page during installation</li>
            <li>Requires ~100MB disk space</li>
            <li>Delete this file after successful installation</li>
        </ul>
    </div>

    <form method="post" id="installForm">
        <button type="submit" name="install" id="installBtn">
            <span class="emoji">🚀</span> Install Composer & Dependencies
        </button>
    </form>

    <div class="progress" id="progress">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill">0%</div>
        </div>
    </div>

    <div class="log" id="log"></div>

    <?php
    if (isset($_POST['install'])) {
        echo '<script>
            document.getElementById("progress").style.display = "block";
            document.getElementById("log").style.display = "block";
            document.getElementById("installBtn").disabled = true;
        </script>';

        flush();
        ob_flush();

        function logMessage($message, $progress = null) {
            echo "<script>
                var log = document.getElementById('log');
                var line = document.createElement('div');
                line.className = 'log-line';
                line.textContent = '" . addslashes($message) . "';
                log.appendChild(line);
                log.scrollTop = log.scrollHeight;";
            
            if ($progress !== null) {
                echo "document.getElementById('progressFill').style.width = '{$progress}%';
                      document.getElementById('progressFill').textContent = '{$progress}%';";
            }
            
            echo "</script>";
            flush();
            ob_flush();
        }

        try {
            logMessage("🚀 Starting installation...", 10);

            // Step 1: Download Composer
            if (!$composerInstalled) {
                logMessage("📥 Downloading Composer installer...", 20);
                
                $installerUrl = 'https://getcomposer.org/installer';
                $installer = file_get_contents($installerUrl);
                
                if ($installer === false) {
                    throw new Exception("Failed to download Composer installer");
                }
                
                logMessage("✅ Composer installer downloaded", 30);
                
                // Save and run installer
                $installerFile = $baseDir . '/composer-setup.php';
                file_put_contents($installerFile, $installer);
                
                logMessage("🔧 Installing Composer...", 40);
                
                ob_start();
                include $installerFile;
                $output = ob_get_clean();
                
                unlink($installerFile);
                
                if (!file_exists($composerPhar)) {
                    throw new Exception("Composer installation failed");
                }
                
                logMessage("✅ Composer installed successfully", 50);
            } else {
                logMessage("✅ Composer already exists", 50);
            }

            // Step 2: Install dependencies
            logMessage("📚 Installing Laravel dependencies...", 60);
            logMessage("⏳ This may take 5-10 minutes, please wait...", 60);
            
            $cmd = "cd " . escapeshellarg($baseDir) . " && php composer.phar install --no-dev --optimize-autoloader --no-interaction 2>&1";
            
            exec($cmd, $output, $returnCode);
            
            logMessage("📦 Composer output:", 70);
            foreach ($output as $line) {
                if (!empty(trim($line))) {
                    logMessage("   " . $line, null);
                }
            }
            
            if ($returnCode !== 0) {
                throw new Exception("Composer install failed with code: {$returnCode}");
            }
            
            // Verify installation
            if (!is_dir($vendorDir) || !file_exists($vendorDir . '/autoload.php')) {
                throw new Exception("vendor/autoload.php not found after installation");
            }
            
            logMessage("✅ Dependencies installed successfully", 90);
            
            // Step 3: Set permissions
            logMessage("🔒 Setting permissions...", 95);
            @chmod($baseDir . '/storage', 0775);
            @chmod($baseDir . '/bootstrap/cache', 0775);
            
            logMessage("✅ Permissions set", 100);
            logMessage("", null);
            logMessage("🎉 Installation complete!", 100);
            logMessage("", null);
            logMessage("📋 Next steps:", null);
            logMessage("   1. 🗑️  DELETE this file (install-composer-browser.php)", null);
            logMessage("   2. 🌐 Visit: https://employee.shahek.org/admin/dashboard", null);
            logMessage("   3. 🔐 Login: admin@company.com / admin123", null);
            
            echo '<script>
                setTimeout(function() {
                    document.getElementById("installBtn").textContent = "✅ Installation Complete!";
                    document.getElementById("installBtn").style.background = "#28a745";
                }, 1000);
            </script>';
            
        } catch (Exception $e) {
            logMessage("❌ ERROR: " . $e->getMessage(), null);
            logMessage("", null);
            logMessage("📞 Troubleshooting:", null);
            logMessage("   1. Check PHP memory limit (needs 256MB+)", null);
            logMessage("   2. Check disk space (needs 100MB+)", null);
            logMessage("   3. Try manual upload method", null);
            
            echo '<script>
                document.getElementById("installBtn").textContent = "❌ Installation Failed";
                document.getElementById("installBtn").style.background = "#dc3545";
                document.getElementById("installBtn").disabled = false;
            </script>';
        }
    }
    ?>
</div>
</body>
</html>
