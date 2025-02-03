<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        UPDATE destinations
        JOIN trucks ON destinations.truck_id = trucks.id
        SET destinations.truck_type_id = trucks.truck_type_id
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
