<?php

namespace Tests\Feature;

use App\Models\ParkingSpot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingSpotControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testPark()
    {
        $parkingSpot = ParkingSpot::factory()->create(['occupied' => false]);

        $response = $this->post('/api/parking-spots/' . $parkingSpot->id . '/park', [
            'vehicle_type' => 'car'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Vehicle parked successfully']);
        $this->assertTrue($parkingSpot->refresh()->occupied);
    }

    public function testUnpark()
    {
        $parkingSpot = ParkingSpot::factory()->create(['occupied' => true]);

        $response = $this->post('/api/parking-spots/' . $parkingSpot->id . '/unpark');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Vehicle unparked successfully']);
        $this->assertFalse($parkingSpot->refresh()->occupied);
    }
}
