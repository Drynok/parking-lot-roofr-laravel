<?php

namespace App\Repositories\Interfaces;

use App\Models\ParkingSpot;

interface ParkingSpotRepositoryInterface
{
    public function findById(int $id, int $parkingLotId): ?ParkingSpot;
    public function findAvailableSpots(int $currentSpotId, int $count): array;
    public function occupySpot(ParkingSpot $spot, int $vehicleId, string $plateNumber): void;
    public function freeSpot(ParkingSpot $spot): void;
    public function freeSpotsByPlateNumber(string $plateNumber): void;
}
