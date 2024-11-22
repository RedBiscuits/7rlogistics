<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cairo = City::firstOrCreate([
            'name' => 'Cairo',
        ]);
        $sharqia = City::firstOrCreate([
            'name' => 'Sharqia',
        ]);
        $giza = City::firstOrCreate([
            'name' => 'Giza',
        ]);
        $monufia = City::firstOrCreate([
            'name' => 'Monufia',
        ]);
        $alexandria = City::firstOrCreate([
            'name' => 'Alexandria',
        ]);

        $matrouh = City::firstOrCreate([
            'name' => 'Matrouh',
        ]);
        $assiut = City::firstOrCreate([
            'name' => 'Assiut',
        ]);

        Area::firstOrCreate([
            'name' => 'Cairo Airport Cargo Village',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Al-Obour',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Fifth Settlement',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Nasr City',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => '10th of Ramadan',
            'city_id' => $sharqia->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Bashtil',
            'city_id' => $giza->id,
        ]);


        Area::firstOrCreate([
            'name' => 'Inshas',
            'city_id' => $sharqia->id,
        ]);


        Area::firstOrCreate([
            'name' => 'New Administrative Capital',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Fourth Metro Line / Al-Ramaya Square',
            'city_id' => $giza->id,
        ]);

        Area::firstOrCreate([
            'name' => '6th of October',
            'city_id' => $giza->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Sadat City',
            'city_id' => $monufia->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Within Alexandria City Limits',
            'city_id' => $alexandria->id,
        ]);

        Area::firstOrCreate([
            'name' => 'North Coast',
            'city_id' => $matrouh->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Dabaa',
            'city_id' => $matrouh->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Assiut',
            'city_id' => $assiut->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Sheraton',
            'city_id' => $cairo->id,
        ]);

        Area::firstOrCreate([
            'name' => 'Mouassat Al-Zakah',
            'city_id' => $cairo->id,
        ]);
    }
}
