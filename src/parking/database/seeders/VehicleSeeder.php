<?php

namespace Database\Seeders;

use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $vehicle = Vehicle::create([
            'name' => "Motorcycle",
            'space_occupied' => 1,
        ]);
        $vehicle->save();

        $vehicle = Vehicle::create([
            'name' => "Car",
            'space_occupied' => 1,
        ]);
        $vehicle->save();

        $vehicle = Vehicle::create([
            'name' => "Van",
            'space_occupied' => 3,
        ]);
        $vehicle->save();
    }
}
