<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Client']);
        Role::firstOrCreate(['name' => 'Operations']);
        Role::firstOrCreate(['name' => 'Finance']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'hossam.nassef@7rlogistics.net'],
            [
                'name' => 'Hossam Nassef',
                'password' => 'Hossam@01159211171HY',
            ]
        );
        if (! $superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
        }

        $opsUser = User::updateOrCreate(
            ['email' => 'operations@7rlogistics.local'],
            [
                'name' => 'Operations User',
                'password' => 'Operations123!',
            ]
        );
        if (! $opsUser->hasRole('Operations')) {
            $opsUser->assignRole('Operations');
        }

        // User::factory()
        //     ->count(50)
        //     ->create()
        //     ->each(function ($user) {
        //         $user->assignRole('Client');
        //     });

        // User::factory()
        //     ->count(10)
        //     ->create()
        //     ->each(function ($user) {
        //     });

        $this->call([
            TruckSeeder::class,
            //    AreaSeeder::class,
            //    CitySeeder::class,
            //    DestinationSeeder::class,
            //    ShipmentSeeder::class
        ]);

    }
}
