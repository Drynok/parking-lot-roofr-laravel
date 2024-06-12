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
            for ($i = 1; $i <= $parkingLot->capacity; $i++) {
                ParkingSpot::create([
                    'type' => $i % 4 == 0 ? 'motorcycle' : 'car',
                    'parking_lot_id' => $parkingLot->id,
                ]);
            }
        }
    }
}
