<?php

namespace Database\Seeders;

use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $parkingSpots = ParkingSpot::inRandomOrder()->limit(50)->get();

        foreach ($parkingSpots as $parkingSpot) {
            Vehicle::create([
                'type' => $parkingSpot->type,
                'parking_spot_id' => $parkingSpot->id,
                'plate_number' => fake()->regexify('[A-Z]{3}-[0-9]{3}'),
            ]);

            $parkingSpot->occupied = true;
            $parkingSpot->save();
        }
    }
}
