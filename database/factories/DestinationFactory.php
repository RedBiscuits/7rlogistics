<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\City;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition()
    {
        $client = User::role('Client')->inRandomOrder()->first();
        $user_id = $client->id ?: User::factory()->create()->id;
        $cities = City::where('user_id', $user_id)->pluck('id')->toArray();
        $from_city = $this->faker->randomElement($cities) ?: City::factory(['user_id' => $user_id])->create()->id;
        $to_city = $this->faker->randomElement($cities) ?: City::factory(['user_id' => $user_id])->create()->id;
        $from_areas = Area::where('city_id', $from_city)->pluck('id')->toArray();
        $to_areas = Area::where('city_id', $to_city)->pluck('id')->toArray();

        $from_area = $this->faker->randomElement($from_areas) ?: Area::factory()->create(['city_id' => $from_city])->id;
        $to_area = $this->faker->randomElement($to_areas) ?: Area::factory()->create(['city_id' => $to_city])->id;

        return [
            'user_id' => $user_id,
            'from_city' => $from_city,
            'to_city' => $to_city,
            'from_area' => $from_area,
            'to_area' => $to_area,
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
