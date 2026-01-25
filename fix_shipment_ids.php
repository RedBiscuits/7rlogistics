<?php

/**
 * Shipment ID Fix Script
 * 
 * This script updates shipment IDs from 225-232 to 219-226 (shifting by -6)
 * along with all their related child records.
 * 
 * IMPORTANT: 
 * - Run this script from the Laravel project root: php fix_shipment_ids.php
 * - Make a database backup before running!
 * - The script runs in DRY RUN mode by default. Set $dryRun = false to apply changes.
 * 
 * Child tables that reference shipment_id:
 * - packages (shipment_id, cascade)
 * - invoices (shipment_id, nullable)
 * - claims (shipment_id, nullable)
 * - missions (shipment_id, cascade)
 * - driver_notifications (shipment_id, nullable)
 * - media (polymorphic: model_type = 'App\Models\Shipment', model_id)
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ============================================================
// CONFIGURATION
// ============================================================

// Set to false to actually execute the changes
$dryRun = false;

// Source IDs (test shipments to be renumbered)
// PRODUCTION: These are the test shipments that jumped from 219 to 225
$sourceIds = [1000233, 1000234, 1000235, 1000236];

// Target IDs (what they should become)
// PRODUCTION: These should continue the sequence from 219
$targetIds = [227, 220, 221, 222, 223, 224, 225, 226];

// The shift amount (target - source for first pair)
$shift = $targetIds[0] - $sourceIds[0]; // -6

// ============================================================
// HELPER FUNCTIONS  
// ============================================================

function printHeader($title) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "$title\n";
    echo str_repeat("=", 60) . "\n";
}

function printStep($step, $message) {
    echo "\n[$step] $message\n";
    echo str_repeat("-", 50) . "\n";
}

function printTable($headers, $rows) {
    if (empty($rows)) {
        echo "  (No records found)\n";
        return;
    }
    
    // Calculate column widths
    $widths = [];
    foreach ($headers as $i => $header) {
        $widths[$i] = strlen($header);
    }
    foreach ($rows as $row) {
        foreach ($row as $i => $cell) {
            $widths[$i] = max($widths[$i] ?? 0, strlen($cell));
        }
    }
    
    // Print header
    $line = "  +";
    foreach ($widths as $w) {
        $line .= str_repeat("-", $w + 2) . "+";
    }
    echo "$line\n  |";
    foreach ($headers as $i => $header) {
        echo " " . str_pad($header, $widths[$i]) . " |";
    }
    echo "\n$line\n";
    
    // Print rows
    foreach ($rows as $row) {
        echo "  |";
        foreach ($row as $i => $cell) {
            echo " " . str_pad($cell, $widths[$i]) . " |";
        }
        echo "\n";
    }
    echo "$line\n";
}

// ============================================================
// MAIN SCRIPT
// ============================================================

printHeader("SHIPMENT ID FIX SCRIPT");

echo "\nConfiguration:\n";
echo "  - Dry Run: " . ($dryRun ? "YES (no changes will be made)" : "NO (changes will be applied!)") . "\n";
echo "  - Source IDs: " . implode(", ", $sourceIds) . "\n";
echo "  - Target IDs: " . implode(", ", $targetIds) . "\n";
echo "  - Shift: $shift\n";

if (!$dryRun) {
    echo "\n⚠️  WARNING: This script will modify your database!\n";
    echo "Press Ctrl+C within 5 seconds to abort...\n";
    sleep(5);
}

// ============================================================
// STEP 1: Verify source shipments exist
// ============================================================
printStep(1, "Verifying source shipments exist");

$existingShipments = DB::table('shipments')
    ->whereIn('id', $sourceIds)
    ->get(['id', 'awb', 'receiver_name', 'created_at'])
    ->keyBy('id');

$rows = [];
foreach ($sourceIds as $id) {
    $shipment = $existingShipments->get($id);
    $rows[] = [
        (string)$id,
        $shipment ? $shipment->awb : 'N/A',
        $shipment ? ($shipment->receiver_name ?? 'N/A') : 'N/A',
        $shipment ? 'EXISTS' : 'MISSING',
    ];
}
printTable(['Source ID', 'AWB', 'Receiver', 'Status'], $rows);

$missingIds = array_diff($sourceIds, $existingShipments->keys()->toArray());
if (!empty($missingIds)) {
    echo "\n⚠️  Warning: Some source IDs don't exist: " . implode(", ", $missingIds) . "\n";
}

// ============================================================
// STEP 2: Verify target IDs are available
// ============================================================
printStep(2, "Verifying target IDs are available");

$conflictingShipments = DB::table('shipments')
    ->whereIn('id', $targetIds)
    ->get(['id', 'awb', 'receiver_name'])
    ->keyBy('id');

$rows = [];
foreach ($targetIds as $id) {
    $shipment = $conflictingShipments->get($id);
    $rows[] = [
        (string)$id,
        $shipment ? $shipment->awb : 'N/A',
        $shipment ? ($shipment->receiver_name ?? 'N/A') : 'N/A',
        $shipment ? 'OCCUPIED' : 'AVAILABLE',
    ];
}
printTable(['Target ID', 'AWB', 'Receiver', 'Status'], $rows);

// Check for conflicts that aren't part of our shift operation
$selfConflicts = array_intersect($targetIds, $sourceIds);
$realConflicts = $conflictingShipments->keys()->diff($selfConflicts);

if ($realConflicts->isNotEmpty()) {
    echo "\n❌ ERROR: Target IDs already exist and are not part of the shift: " . $realConflicts->implode(", ") . "\n";
    echo "These shipments would need to be moved first. Aborting.\n";
    exit(1);
}

if (!empty($selfConflicts)) {
    echo "\n✓ Note: Some target IDs (" . implode(", ", $selfConflicts) . ") overlap with source IDs - will be handled by sequential update.\n";
}

// ============================================================
// STEP 3: Count child records for each shipment
// ============================================================
printStep(3, "Counting child records");

$childTables = [
    'packages' => 'shipment_id',
    'invoices' => 'shipment_id',
    'claims' => 'shipment_id',
    'missions' => 'shipment_id',
    'driver_notifications' => 'shipment_id',
];

$childCounts = [];
foreach ($sourceIds as $id) {
    $childCounts[$id] = [];
    foreach ($childTables as $table => $column) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->where($column, $id)->count();
            $childCounts[$id][$table] = $count;
        } else {
            $childCounts[$id][$table] = 'N/A';
        }
    }
    // Check media (polymorphic)
    if (Schema::hasTable('media')) {
        $mediaCount = DB::table('media')
            ->where('model_type', 'App\\Models\\Shipment')
            ->where('model_id', $id)
            ->count();
        $childCounts[$id]['media'] = $mediaCount;
    }
}

$headers = array_merge(['Shipment ID'], array_keys($childTables), ['media']);
$rows = [];
foreach ($childCounts as $id => $counts) {
    $row = [(string)$id];
    foreach ($counts as $count) {
        $row[] = (string)$count;
    }
    $rows[] = $row;
}
printTable($headers, $rows);

// ============================================================
// STEP 4: Build the ID mapping
// ============================================================
printStep(4, "Building ID mapping");

$idMapping = [];
for ($i = 0; $i < count($sourceIds); $i++) {
    $idMapping[$sourceIds[$i]] = $targetIds[$i];
}

$rows = [];
foreach ($idMapping as $from => $to) {
    $rows[] = [(string)$from, '→', (string)$to];
}
printTable(['From ID', '', 'To ID'], $rows);

// ============================================================
// STEP 5: Execute the updates
// ============================================================
printStep(5, $dryRun ? "Simulating updates (DRY RUN)" : "Executing updates");

if ($dryRun) {
    echo "\n🔍 DRY RUN MODE - No changes will be made\n";
    echo "\nThe following SQL operations would be executed:\n\n";
}

try {
    DB::beginTransaction();
    
    // Disable foreign key checks to allow temporary ID changes
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    echo "\n  ⚙️ Foreign key checks disabled\n";
    
    // TWO-PASS APPROACH to handle overlapping source/target IDs
    // Pass 1: Move ALL source shipments and children to temp IDs
    // Pass 2: Move ALL from temp IDs to final target IDs
    
    echo "\n  === PASS 1: Moving all shipments to temporary IDs ===\n";
    
    foreach ($idMapping as $oldId => $newId) {
        if (!$existingShipments->has($oldId)) {
            echo "  ⏭️  Skipping ID $oldId (doesn't exist)\n";
            continue;
        }
        
        $tempId = $oldId + 1000000;
        echo "\n  📦 Moving Shipment ID: $oldId → temp ID $tempId\n";
        
        // Move children to temp ID
        foreach ($childTables as $table => $column) {
            if (!Schema::hasTable($table)) continue;
            
            $count = DB::table($table)->where($column, $oldId)->count();
            if ($count > 0) {
                if ($dryRun) {
                    echo "    • UPDATE $table SET $column = $tempId WHERE $column = $oldId; ($count rows)\n";
                } else {
                    DB::table($table)
                        ->where($column, $oldId)
                        ->update([$column => $tempId]);
                    echo "    ✓ $table: $count rows → temp\n";
                }
            }
        }
        
        // Handle media (polymorphic)
        if (Schema::hasTable('media')) {
            $mediaCount = DB::table('media')
                ->where('model_type', 'App\\Models\\Shipment')
                ->where('model_id', $oldId)
                ->count();
            if ($mediaCount > 0) {
                if ($dryRun) {
                    echo "    • UPDATE media SET model_id = $tempId WHERE model_type = 'App\\Models\\Shipment' AND model_id = $oldId; ($mediaCount rows)\n";
                } else {
                    DB::table('media')
                        ->where('model_type', 'App\\Models\\Shipment')
                        ->where('model_id', $oldId)
                        ->update(['model_id' => $tempId]);
                    echo "    ✓ media: $mediaCount rows → temp\n";
                }
            }
        }
        
        // Move shipment to temp ID
        if ($dryRun) {
            echo "    • UPDATE shipments SET id = $tempId WHERE id = $oldId;\n";
        } else {
            DB::table('shipments')
                ->where('id', $oldId)
                ->update(['id' => $tempId]);
            echo "    ✓ Shipment moved to temp ID\n";
        }
    }
    
    echo "\n  === PASS 2: Moving all from temp IDs to final target IDs ===\n";
    
    foreach ($idMapping as $oldId => $newId) {
        if (!$existingShipments->has($oldId)) {
            continue;
        }
        
        $tempId = $oldId + 1000000;
        echo "\n  📦 Moving temp ID $tempId → final ID $newId\n";
        
        // Move shipment from temp ID to final ID
        if ($dryRun) {
            echo "    • UPDATE shipments SET id = $newId WHERE id = $tempId;\n";
        } else {
            DB::table('shipments')
                ->where('id', $tempId)
                ->update(['id' => $newId]);
            echo "    ✓ Shipment ID updated\n";
        }
        
        // Move children from temp ID to final ID
        foreach ($childTables as $table => $column) {
            if (!Schema::hasTable($table)) continue;
            
            if ($dryRun) {
                $count = $childCounts[$oldId][$table] ?? 0;
                if ($count > 0) {
                    echo "    • UPDATE $table SET $column = $newId WHERE $column = $tempId; ($count rows)\n";
                }
            } else {
                $affected = DB::table($table)
                    ->where($column, $tempId)
                    ->update([$column => $newId]);
                if ($affected > 0) {
                    echo "    ✓ $table: $affected rows → final\n";
                }
            }
        }
        
        // Handle media
        if (Schema::hasTable('media')) {
            $mediaCount = $childCounts[$oldId]['media'] ?? 0;
            if ($mediaCount > 0) {
                if ($dryRun) {
                    echo "    • UPDATE media SET model_id = $newId WHERE model_type = 'App\\Models\\Shipment' AND model_id = $tempId; ($mediaCount rows)\n";
                } else {
                    DB::table('media')
                        ->where('model_type', 'App\\Models\\Shipment')
                        ->where('model_id', $tempId)
                        ->update(['model_id' => $newId]);
                    echo "    ✓ media: $mediaCount rows → final\n";
                }
            }
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "\n  ⚙️ Foreign key checks re-enabled\n";
    
    if ($dryRun) {
        DB::rollBack();
        echo "\n🔍 DRY RUN complete - No changes were made.\n";
        echo "\n📋 To apply these changes, edit this file and set:\n";
        echo "   \$dryRun = false;\n";
    } else {
        DB::commit();
        echo "\n✅ All changes committed successfully!\n";
    }
    
} catch (\Exception $e) {
    // Re-enable foreign key checks even on error
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// ============================================================
// STEP 6: Verification (only if not dry run)
// ============================================================
if (!$dryRun) {
    printStep(6, "Verifying changes");
    
    $verifyShipments = DB::table('shipments')
        ->whereIn('id', $targetIds)
        ->get(['id', 'awb', 'receiver_name'])
        ->keyBy('id');
    
    $rows = [];
    foreach ($targetIds as $id) {
        $shipment = $verifyShipments->get($id);
        $rows[] = [
            (string)$id,
            $shipment ? $shipment->awb : 'N/A',
            $shipment ? ($shipment->receiver_name ?? 'N/A') : 'N/A',
            $shipment ? '✓ OK' : '✗ MISSING',
        ];
    }
    printTable(['New ID', 'AWB', 'Receiver', 'Status'], $rows);
    
    // Verify old IDs no longer exist
    $oldShipments = DB::table('shipments')
        ->whereIn('id', $sourceIds)
        ->whereNotIn('id', $targetIds) // Exclude overlapping IDs
        ->count();
    
    if ($oldShipments > 0) {
        echo "\n⚠️  Warning: $oldShipments old shipment IDs still exist!\n";
    } else {
        echo "\n✓ Confirmed: All old IDs have been updated.\n";
    }
}

// ============================================================
// STEP 7: Reset auto-increment (optional)
// ============================================================
printStep(7, "Auto-increment reset suggestion");

$maxId = DB::table('shipments')->max('id');
$nextAutoIncrement = $maxId + 1;

echo "Current max shipment ID: $maxId\n";
echo "Suggested auto-increment value: $nextAutoIncrement\n";
echo "\nTo reset auto-increment, run this SQL:\n";
echo " $nextAutoIncrement;\n";

printHeader("SCRIPT COMPLETED");

if ($dryRun) {
    echo "\n⚠️  REMINDER: This was a DRY RUN. No changes were made.\n";
    echo "To apply changes, edit fix_shipment_ids.php and set \$dryRun = false;\n";
}

echo "\n";
