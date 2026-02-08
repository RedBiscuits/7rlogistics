<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds driver app status timestamp columns to shipments (safe for production: only adds if missing).
     */
    public function up(): void
    {
        $columns = [
            'driver_acknowledged_at' => 'status',
            'driver_on_way_to_collect_at' => 'driver_acknowledged_at',
            'awaiting_collection_at' => 'driver_on_way_to_collect_at',
        ];

        foreach ($columns as $column => $after) {
            if (Schema::hasColumn('shipments', $column)) {
                continue;
            }
            Schema::table('shipments', function (Blueprint $table) use ($column, $after) {
                $table->timestamp($column)->nullable()->after($after);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $columns = [
                'driver_acknowledged_at',
                'driver_on_way_to_collect_at',
                'awaiting_collection_at',
            ];

            foreach (array_reverse($columns) as $column) {
                if (Schema::hasColumn('shipments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
