<?php

namespace Database\Seeders;

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
        $users = User::role('Client')->pluck('id');
        foreach ($users as $user) {
            City::factory(10)->create([
                'user_id' => $user,
            ]);
        }
    }
}
