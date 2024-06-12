<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use Illuminate\Database\Seeder;

class ParkingLotSeeder extends Seeder
{
    public function run()
    {
        ParkingLot::create([
            'name' => 'Main Street Parking',
            'address' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'FL',
            'zip' => '12345',
            'capacity' => 100,
        ]);

        ParkingLot::create([
            'name' => 'Downtown Parking Garage',
            'address' => '456 Elm St',
            'city' => 'Anytown',
            'state' => 'FL',
            'zip' => '12345',
            'capacity' => 500,
        ]);
    }
}
