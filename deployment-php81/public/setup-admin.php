<?php
// Check if users table exists and create admin user
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Users Table & Admin Setup\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

try {
    // Check if users table exists
    echo "Step 1: Check users table\n";
    echo str_repeat("-", 80) . "\n";
    
    $hasUsersTable = Schema::hasTable('users');
    
    if ($hasUsersTable) {
        echo "✅ Users table EXISTS\n";
        
        $count = DB::table('users')->count();
        echo "User count: $count\n";
        
        if ($count > 0) {
            echo "\nExisting users:\n";
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                echo "  - ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
            }
        }
    } else {
        echo "❌ Users table DOES NOT EXIST\n";
        echo "Creating users table...\n";
        
        // Create users table
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        
        echo "✅ Users table created\n";
        $hasUsersTable = true;
    }
    
    // Create admin user if table exists
    if ($hasUsersTable) {
        echo "\nStep 2: Setup admin user\n";
        echo str_repeat("-", 80) . "\n";
        
        $adminEmail = env('ADMIN_EMAIL', 'admin@company.com');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $adminName = env('ADMIN_NAME', 'Administrator');
        
        $existingAdmin = DB::table('users')->where('email', $adminEmail)->first();
        
        if ($existingAdmin) {
            echo "✅ Admin user already exists\n";
            echo "Email: {$existingAdmin->email}\n";
            echo "Name: {$existingAdmin->name}\n";
        } else {
            echo "Creating admin user...\n";
            echo "Email: $adminEmail\n";
            echo "Name: $adminName\n";
            
            DB::table('users')->insert([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Admin user created successfully\n";
        }
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "✅ Setup Complete\n";
    echo "\nNext steps:\n";
    echo "1. Visit /admin/dashboard - it should auto-login\n";
    echo "2. The API endpoints should now work\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}
