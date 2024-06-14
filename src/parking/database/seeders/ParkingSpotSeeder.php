<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    public function run()
    {
        $parkingLots = ParkingLot::all();
        $vehicles = Vehicle::all();

        foreach ($parkingLots as $parkingLot) {
            for ($i = 1; $i <= $parkingLot->capacity; $i++) {
                ParkingSpot::create([
                    'parking_lot_id' => $parkingLot->id
                ]);
            }
        }
    }
}
