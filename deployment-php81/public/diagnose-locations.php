<?php
// Diagnose locations API endpoint
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Locations API Diagnosis\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use App\Models\EmployeeLocation;

try {
    echo "Step 1: Check Employee Model\n";
    echo str_repeat("-", 80) . "\n";
    
    $employees = Employee::where('is_active', true)->get();
    echo "Active employees: " . $employees->count() . "\n";
    
    foreach ($employees as $emp) {
        echo "\nEmployee ID {$emp->id}: {$emp->name}\n";
        echo "  Email: {$emp->email}\n";
        echo "  Last seen: " . ($emp->last_seen_at ? $emp->last_seen_at->toDateTimeString() : 'Never') . "\n";
        echo "  Is online: " . ($emp->isOnline() ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nStep 2: Check latestLocation Relationship\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($employees as $emp) {
        echo "\nEmployee ID {$emp->id}: {$emp->name}\n";
        
        try {
            $location = $emp->latestLocation;
            
            if ($location) {
                echo "  ✅ Latest location found\n";
                echo "    Lat: {$location->lat}, Lng: {$location->lng}\n";
                echo "    Recorded: {$location->recorded_at->toDateTimeString()}\n";
            } else {
                echo "  ⚠️  No location found\n";
            }
        } catch (\Exception $e) {
            echo "  ❌ Error loading location: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nStep 3: Test getLatestLocations Logic\n";
    echo str_repeat("-", 80) . "\n";
    
    $query = Employee::with(['latestLocation'])->where('is_active', true);
    $employees = $query->get();
    
    echo "Query executed successfully\n";
    echo "Results: " . $employees->count() . " employees\n";
    
    $data = $employees->map(function ($employee) {
        $location = $employee->latestLocation;
        
        if (!$location) {
            return null;
        }
        
        $isOnline = $employee->isOnline();
        
        return [
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'lat' => (float) $location->lat,
            'lng' => (float) $location->lng,
            'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
            'battery' => $location->battery,
            'recorded_at' => $location->recorded_at->toISOString(),
            'received_at' => $location->received_at->toISOString(),
            'is_online' => $isOnline,
            'last_seen' => $employee->last_seen_at ? $employee->last_seen_at->diffForHumans() : null,
        ];
    })->filter()->values();
    
    echo "✅ Data mapped successfully\n";
    echo "Final count: " . $data->count() . " locations\n";
    
    echo "\nStep 4: JSON Output\n";
    echo str_repeat("-", 80) . "\n";
    
    $response = [
        'success' => true,
        'data' => $data,
        'count' => $data->count(),
        'timestamp' => now()->toISOString(),
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ Diagnosis Complete\n";
