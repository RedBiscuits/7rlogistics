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
            'name' => 'دبابة',
            'plate_number' => 'ا ب س ١ ٢ ٣',
            'weight' => 2000,
        ]);

        Truck::firstOrCreate([
            'name' => 'جامبو ٥٢٠٠',
            'plate_number' => 'ا ب ص ١ ٢ ٣',
            'weight' => 3200,
        ]);

        Truck::firstOrCreate([
            'name' => 'جامبو ٧٠٠٠',
            'plate_number' => 'ا ب ض ١ ٢ ٣',
            'weight' => 4400,
        ]);

        Truck::firstOrCreate([
            'name' => 'جامبو ٨٠٠٠',
            'plate_number' => 'ا ب د ١ ٢ ٣',
            'weight' => 5200,
        ]);
    }
}
