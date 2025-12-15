<?php
// Check employee_locations table structure
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Employee Locations Table Structure\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Step 1: Table Columns\n";
    echo str_repeat("-", 80) . "\n";
    
    $columns = Schema::getColumnListing('employee_locations');
    echo "Columns in employee_locations:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    echo "\nStep 2: Sample Records\n";
    echo str_repeat("-", 80) . "\n";
    
    $records = DB::table('employee_locations')->limit(3)->get();
    
    if ($records->count() > 0) {
        foreach ($records as $idx => $record) {
            echo "\nRecord " . ($idx + 1) . ":\n";
            foreach ($record as $key => $value) {
                $display = $value ?? 'NULL';
                if (is_string($display) && strlen($display) > 50) {
                    $display = substr($display, 0, 50) . '...';
                }
                echo "  $key: $display\n";
            }
        }
    } else {
        echo "No records found\n";
    }
    
    echo "\nStep 3: Add Sample Location Data\n";
    echo str_repeat("-", 80) . "\n";
    
    // Check if we need to add coordinates columns or update existing data
    $hasLatitude = in_array('latitude', $columns);
    $hasLongitude = in_array('longitude', $columns);
    $hasLat = in_array('lat', $columns);
    $hasLng = in_array('lng', $columns);
    
    echo "Has 'latitude' column: " . ($hasLatitude ? 'Yes' : 'No') . "\n";
    echo "Has 'longitude' column: " . ($hasLongitude ? 'Yes' : 'No') . "\n";
    echo "Has 'lat' column: " . ($hasLat ? 'Yes' : 'No') . "\n";
    echo "Has 'lng' column: " . ($hasLng ? 'Yes' : 'No') . "\n";
    
    // Add sample coordinates if possible
    if ($hasLatitude && $hasLongitude) {
        echo "\nAdding sample coordinates (Berlin area)...\n";
        
        $sampleCoords = [
            2 => ['lat' => 52.5200, 'lng' => 13.4050], // Berlin
            3 => ['lat' => 52.5170, 'lng' => 13.3889], // Berlin-Mitte
            4 => ['lat' => 52.5244, 'lng' => 13.4105], // Alexanderplatz
        ];
        
        foreach ($sampleCoords as $empId => $coords) {
            $updated = DB::table('employee_locations')
                ->where('employee_id', $empId)
                ->update([
                    'latitude' => $coords['lat'],
                    'longitude' => $coords['lng'],
                ]);
            
            if ($updated) {
                echo "  ✅ Updated employee $empId location\n";
            }
        }
        
        echo "\n✅ Sample coordinates added!\n";
        echo "Refresh the dashboard to see markers on the map.\n";
    } elseif ($hasLat && $hasLng) {
        echo "\nAdding sample coordinates (Berlin area)...\n";
        
        $sampleCoords = [
            2 => ['lat' => 52.5200, 'lng' => 13.4050],
            3 => ['lat' => 52.5170, 'lng' => 13.3889],
            4 => ['lat' => 52.5244, 'lng' => 13.4105],
        ];
        
        foreach ($sampleCoords as $empId => $coords) {
            $updated = DB::table('employee_locations')
                ->where('employee_id', $empId)
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                ]);
            
            if ($updated) {
                echo "  ✅ Updated employee $empId location\n";
            }
        }
        
        echo "\n✅ Sample coordinates added!\n";
        echo "Refresh the dashboard to see markers on the map.\n";
    } else {
        echo "\n⚠️  No coordinate columns found!\n";
        echo "Cannot add sample data.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ Check Complete\n";
