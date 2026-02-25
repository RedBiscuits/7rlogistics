<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing data: build a Google Maps link from lat/lng or fallback to address text
        $stops = DB::table('shipment_stops')->get();
        foreach ($stops as $stop) {
            $location = null;
            if ($stop->latitude && $stop->longitude) {
                $location = "https://www.google.com/maps?q={$stop->latitude},{$stop->longitude}";
            } elseif ($stop->address) {
                $location = $stop->address;
            }
            DB::table('shipment_stops')->where('id', $stop->id)->update(['address' => $location]);
        }

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->renameColumn('address', 'location');
        });

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['area_id', 'city_id', 'latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->renameColumn('location', 'address');
        });

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('sequence')->constrained('areas')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('area_id')->constrained('cities')->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }
};
