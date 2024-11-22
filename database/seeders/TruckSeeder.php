<?php

namespace Database\Seeders;

use App\Models\Truck;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Truck::firstOrCreate([
            'name' => 'Dababah',
            'plate_number' => 'ا ب س ١ ٢ ٣',
            'weight' => 2000,
        ]);

        Truck::firstOrCreate([
            'name' => 'Jumpo',
            'plate_number' => 'ا ب ص ١ ٢ ٣',
            'weight' => 3200,
        ]);
    }
}
