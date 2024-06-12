<?php

namespace Tests\Feature;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingLotControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $response = $this->get('/api/parking-lots');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'address',
                'city',
                'state',
                'zip',
                'capacity',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function testShow()
    {
        $parkingLot = ParkingLot::factory()->create();

        $response = $this->get('/api/parking-lots/' . $parkingLot->id);

        $response->assertStatus(200);
        $response->assertJson($parkingLot->toArray());
    }

    public function testGetAvailability()
    {
        $parkingLot = ParkingLot::factory()->create(['capacity' => 100]);
        ParkingSpot::factory()->count(60)->create([
            'parking_lot_id' => $parkingLot->id,
            'occupied' => true
        ]);

        $response = $this->get('/api/parking-lots/' . $parkingLot->id . '/availability');

        $response->assertStatus(200);
        $response->assertJson([
            'total_spots' => 100,
            'occupied_spots' => 60,
            'available_spots' => 40
        ]);
    }
}
