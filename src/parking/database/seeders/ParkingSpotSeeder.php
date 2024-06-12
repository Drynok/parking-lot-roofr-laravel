<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    public function run()
    {
        $parkingLots = ParkingLot::all();

        foreach ($parkingLots as $parkingLot) {
            for ($i = 1; $i <= $parkingLot->capacity - 20; $i++) {
                ParkingSpot::create([
                    'occupied' => false,
                    'parking_lot_id' => $parkingLot->id,
                ]);
            }
            for ($i = 1; $i <= 20; $i++) {
                ParkingSpot::create([
                    'occupied' => true,
                    'parking_lot_id' => $parkingLot->id,
                ]);
            }
        }
    }
}
