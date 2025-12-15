<?php
// Check employee_locations data
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Employee Locations Data Check\n";
echo str_repeat("=", 80) . "\n\n";

$basePath = dirname(__DIR__);
require_once $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Step 1: Raw employee_locations Data\n";
    echo str_repeat("-", 80) . "\n";
    
    $locations = DB::table('employee_locations')->get();
    echo "Total locations: " . $locations->count() . "\n\n";
    
    foreach ($locations as $loc) {
        echo "ID: {$loc->id}\n";
        echo "  Employee ID: {$loc->employee_id}\n";
        echo "  Lat: '" . ($loc->lat ?? 'NULL') . "' (Type: " . gettype($loc->lat) . ")\n";
        echo "  Lng: '" . ($loc->lng ?? 'NULL') . "' (Type: " . gettype($loc->lng) . ")\n";
        echo "  Accuracy: " . ($loc->accuracy ?? 'NULL') . "\n";
        echo "  Battery: " . ($loc->battery ?? 'NULL') . "\n";
        echo "  Recorded At: {$loc->recorded_at}\n";
        echo "  Received At: {$loc->received_at}\n\n";
    }
    
    echo "Step 2: Check Empty Values\n";
    echo str_repeat("-", 80) . "\n";
    
    $emptyLat = DB::table('employee_locations')
        ->whereNull('lat')
        ->orWhere('lat', '')
        ->count();
    
    $emptyLng = DB::table('employee_locations')
        ->whereNull('lng')
        ->orWhere('lng', '')
        ->count();
    
    echo "Locations with empty/NULL lat: $emptyLat\n";
    echo "Locations with empty/NULL lng: $emptyLng\n";
    
    if ($emptyLat > 0 || $emptyLng > 0) {
        echo "\n⚠️  WARNING: Some locations have empty coordinates!\n";
        echo "Fix: Update with sample coordinates or delete these records\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ Check Complete\n";
