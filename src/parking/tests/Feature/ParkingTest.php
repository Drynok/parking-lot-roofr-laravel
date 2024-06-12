<?php

// tests/Feature/ParkingTest.php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ParkingSpot;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    public function testParkVehicle()
    {
        $spot = ParkingSpot::create(['type' => 'car']);
        $response = $this->postJson('/api/parking-spot/' . $spot->id . '/park', ['type' => 'car']);
        $response->assertStatus(200)->assertJson(['message' => 'Vehicle parked successfully']);
    }

    public function testUnparkVehicle()
    {
        $spot = ParkingSpot::create(['type' => 'car', 'is_occupied' => true]);
        $response = $this->postJson('/api/parking-spot/' . $spot->id . '/unpark');
        $response->assertStatus(200)->assertJson(['message' => 'Vehicle unparked successfully']);
    }

    public function testGetParkingLot()
    {
        $response = $this->getJson('/api/parking-lot');
        $response->assertStatus(200)->assertJsonStructure(['total_spots', 'available_spots']);
    }
}
