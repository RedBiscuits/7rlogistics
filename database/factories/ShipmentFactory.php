<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition()
    {
        $clients = User::role('Client')->pluck('id')->toArray();
        $client_id = $this->faker->randomElement($clients) ?: User::factory()->create()->id;
        $status = $this->faker->numberBetween(0, 5);

        $destination = Destination::where('user_id', $client_id)->whereNotNull('price')->inRandomOrder()->first() ?: Destination::factory(['user_id' => $client_id, 'price' => 362.48])->create();
        $trucks = Truck::pluck('id')->toArray();

        return [
            'user_id' => $client_id,
            'receiver_name' => $this->faker->name,
            'receiver_phone' => $this->faker->phoneNumber,
            'receiver_address' => $this->faker->address,
            'from_city' => $destination->from_city,
            'from_area' => $destination->from_area,
            'to_city' => $destination->to_city,
            'to_area' => $destination->to_area,
            'status' => $status,
            'price' => $this->faker->randomFloat(2, 50, 500),
            'truck_id' => $this->faker->randomElement($trucks) ?: Truck::factory()->create()->id,
            'driver_id' => $status == 0 || $status == 1 ? null : $this->faker->randomElement(Driver::query()->pluck('id')->toArray()),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'pickup_date' => $this->faker->dateTime('now'),
            'awb' => $this->faker->ean8,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
        ];
    }
}
