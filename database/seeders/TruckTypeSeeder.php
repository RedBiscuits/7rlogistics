<?php

namespace Database\Seeders;

use App\Models\TruckType;
use Illuminate\Database\Seeder;

class TruckTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TruckType::firstOrCreate([
            'name' => 'Dababah',
            'capacity' => 2000,
            'weight' => 2000,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        TruckType::firstOrCreate([
            'name' => 'Jumpo',
            'capacity' => 3200,
            'weight' => 3200,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        TruckType::firstOrCreate([
            'name' => 'Dababah Box',
            'capacity' => 2000,
            'weight' => 2000,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        TruckType::firstOrCreate([
            'name' => 'Jumbo Box',
            'capacity' => 3200,
            'weight' => 3200,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        TruckType::firstOrCreate([
            'name' => 'Dababah Refrigerated',
            'capacity' => 2000,
            'weight' => 2000,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        TruckType::firstOrCreate([
            'name' => 'Jumbo Refrigerated',
            'capacity' => 3200,
            'weight' => 3200,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);
    }
}
