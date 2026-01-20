<?php

/**
 * Test Scenario Setup Script
 * 
 * This creates a test scenario to replicate the production issue:
 * - Last shipment was ID 16
 * - Creates shipments 17, 18 (normal sequence)  
 * - Skips to IDs 25-32 (test shipments that caused the gap)
 * - Creates some child records (packages) for testing
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Creating Test Scenario ===\n\n";

// Check current max ID
$maxId = DB::table('shipments')->max('id') ?? 0;
echo "Current max shipment ID: $maxId\n\n";

// Get some required foreign keys
$city = DB::table('cities')->first();
$area = DB::table('areas')->first();
$truck = DB::table('trucks')->first();
$user = DB::table('users')->first();
$company = DB::table('companies')->first();

if (!$city || !$area) {
    echo "❌ Error: Need at least one city and area in the database.\n";
    exit(1);
}

echo "Using:\n";
echo "  - City ID: " . ($city->id ?? 'null') . "\n";
echo "  - Area ID: " . ($area->id ?? 'null') . "\n";
echo "  - Truck ID: " . ($truck->id ?? 'null') . "\n";
echo "  - User ID: " . ($user->id ?? 'null') . "\n";
echo "  - Company ID: " . ($company->id ?? 'null') . "\n\n";

try {
    DB::beginTransaction();
    
    // Helper function to create a shipment
    $createShipment = function($overrides = []) use ($city, $area, $truck, $user, $company) {
        $data = array_merge([
            'user_id' => $user->id ?? null,
            'company_id' => $company->id ?? null,
            'receiver_name' => 'Test Receiver ' . rand(1000, 9999),
            'receiver_phone' => '0501234567',
            'receiver_address' => 'Test Address ' . rand(1, 100),
            'from_city' => $city->id ?? 1,
            'from_area' => $area->id ?? 1,
            'to_city' => $city->id ?? 1,
            'to_area' => $area->id ?? 1,
            'status' => 0,
            'price' => rand(100, 500),
            'truck_id' => $truck->id ?? null,
            'driver_id' => null,
            'pickup_date' => now(),
            'awb' => 'AWB' . rand(10000, 99999),
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);
        
        return DB::table('shipments')->insertGetId($data);
    };
    
    // Create shipments 17, 18 (normal sequence)
    echo "Creating normal shipments (should get IDs 17, 18)...\n";
    $id17 = $createShipment(['receiver_name' => 'Normal Shipment 17', 'awb' => 'NORMAL17']);
    $id18 = $createShipment(['receiver_name' => 'Normal Shipment 18', 'awb' => 'NORMAL18']);
    echo "  ✓ Created shipment ID: $id17\n";
    echo "  ✓ Created shipment ID: $id18\n";
    
    // Now manually set auto_increment to skip to 25
    echo "\nSkipping auto_increment to 25 (simulating gap)...\n";
    DB::statement("ALTER TABLE shipments AUTO_INCREMENT = 25");
    
    // Create "test" shipments with IDs 25-32
    echo "\nCreating test shipments (should get IDs 25-32)...\n";
    $testShipmentIds = [];
    for ($i = 0; $i < 8; $i++) {
        $id = $createShipment([
            'receiver_name' => "TEST Receiver $i",
            'awb' => "TEST" . str_pad($i, 4, '0', STR_PAD_LEFT),
        ]);
        $testShipmentIds[] = $id;
        echo "  ✓ Created shipment ID: $id (AWB: TEST" . str_pad($i, 4, '0', STR_PAD_LEFT) . ")\n";
        
        // Create some packages for this shipment
        $numPackages = rand(1, 3);
        for ($j = 0; $j < $numPackages; $j++) {
            DB::table('packages')->insert([
                'shipment_id' => $id,
                'weight' => rand(1, 100),
                'length' => rand(10, 100),
                'width' => rand(10, 100),
                'height' => rand(10, 100),
                'quantity' => rand(1, 5),
                'type' => 'box',
                'description' => "Test package $j for shipment $id",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        echo "    → Created $numPackages package(s)\n";
    }
    
    DB::commit();
    
    echo "\n=== Test Scenario Created Successfully ===\n\n";
    
    // Summary
    echo "Summary:\n";
    echo "  - Shipments $id17, $id18: Normal sequence\n";
    echo "  - Shipments " . implode("-", [min($testShipmentIds), max($testShipmentIds)]) . ": Test shipments (need to be shifted to 19-26)\n";
    echo "  - Gap: IDs 19-24 are missing\n";
    
    echo "\nShipment Overview:\n";
    $allIds = array_merge([$id17, $id18], $testShipmentIds);
    $shipments = DB::table('shipments')
        ->whereIn('id', $allIds)
        ->get(['id', 'awb', 'receiver_name']);
    
    echo str_repeat("-", 60) . "\n";
    echo sprintf("| %-4s | %-15s | %-30s |\n", "ID", "AWB", "Receiver");
    echo str_repeat("-", 60) . "\n";
    foreach ($shipments as $s) {
        echo sprintf("| %-4s | %-15s | %-30s |\n", $s->id, $s->awb, substr($s->receiver_name, 0, 30));
    }
    echo str_repeat("-", 60) . "\n";
    
    // Count child records
    echo "\nChild Records Count:\n";
    foreach ($testShipmentIds as $id) {
        $packageCount = DB::table('packages')->where('shipment_id', $id)->count();
        echo "  Shipment $id: $packageCount packages\n";
    }
    
    echo "\n✅ Now the fix_shipment_ids.php script needs these values:\n";
    echo "   \$sourceIds = [" . implode(", ", $testShipmentIds) . "];\n";
    echo "   \$targetIds = [19, 20, 21, 22, 23, 24, 25, 26];\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
