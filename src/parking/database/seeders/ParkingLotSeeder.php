<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use Illuminate\Database\Seeder;

class ParkingLotSeeder extends Seeder
{
    public function run()
    {
        ParkingLot::create([
            'name' => 'Random Street Parking',
            'address' => '10 Random St',
            'city' => 'Anytown',
            'state' => 'FL',
            'zip' => '12345',
            'capacity' => 50,
        ]);

        ParkingLot::create([
            'name' => 'Downtown Parking',
            'address' => '123 Helm St',
            'city' => 'Anytown',
            'state' => 'FL',
            'zip' => '12345',
            'capacity' => 200,
        ]);
    }
}
