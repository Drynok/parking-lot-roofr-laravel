<?php

namespace Database\Seeders;

use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $parkingSpots = ParkingSpot::where('occupied', false)->take(10)->get();

        foreach ($parkingSpots as $parkingSpot) {
            Vehicle::create([
                'type' => 'Car',
                'parking_spot_id' => $parkingSpot->id,
            ]);

            $parkingSpot->occupied = true;
            $parkingSpot->save();
        }
    }
}
