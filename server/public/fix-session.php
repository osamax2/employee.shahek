<?php
echo "🔧 Session Fix Tool\n\n";

// Create session directory
$sessionPath = __DIR__ . '/../storage/framework/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0775, true);
    echo "✅ Created: $sessionPath\n";
} else {
    echo "✅ Exists: $sessionPath\n";
}

// Set permissions
chmod($sessionPath, 0775);
echo "✅ Set permissions: 0775\n";

// Create .gitignore
$gitignore = $sessionPath . '/.gitignore';
file_put_contents($gitignore, "*\n!.gitignore\n");
echo "✅ Created .gitignore\n";

// Test write
$testFile = $sessionPath . '/test_' . time();
if (file_put_contents($testFile, 'test')) {
    unlink($testFile);
    echo "✅ Write test: SUCCESS\n";
} else {
    echo "❌ Write test: FAILED\n";
}

// Check other required directories
$dirs = [
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs',
];

foreach ($dirs as $dir) {
    $path = __DIR__ . '/../' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
        echo "✅ Created: $path\n";
    }
    chmod($path, 0775);
}

echo "\n✅ All storage directories configured!\n";
echo "\nNow test: https://employee.shahek.org/public/admin/dashboard\n";
