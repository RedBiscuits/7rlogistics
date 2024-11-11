<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role('Client')->get();
        foreach ($users as $user) {
            $cities = $user->cities;
            foreach ($cities as $city) {
                Area::factory(10)->create([
                    'city_id' => $city->id,
                ]);
            }
        }
    }
}
