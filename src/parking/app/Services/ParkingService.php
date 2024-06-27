<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Repositories\Interfaces\ParkingSpotRepositoryInterface;

class ParkingService
{
    private $parkingSpotRepository;

    public function __construct(ParkingSpotRepositoryInterface $parkingSpotRepository)
    {
        $this->parkingSpotRepository = $parkingSpotRepository;
    }

    public function parkVehicle(int $spotId, array $data): array
    {
        $spot = $this->parkingSpotRepository->findById($spotId, $data['parking_lot_id']);

        if (!$spot) {
            return ['success' => false, 'message' => 'Parking spot not found', 'status' => 404];
        }

        if ($spot->occupied) {
            return ['success' => false, 'message' => 'This spot is occupied', 'status' => 400];
        }

        $vehicle = Vehicle::where('name', $data['vehicle_type'])->firstOrFail();
        $plateNumber = rand(100000, 999999);

        if ($vehicle->space_occupied > 1) {
            $availableSpots = $this->parkingSpotRepository->findAvailableSpots($spot->id, $vehicle->space_occupied - 1);

            if (count($availableSpots) < $vehicle->space_occupied - 1) {
                return ['success' => false, 'message' => 'Not enough space to park here', 'status' => 400];
            }

            foreach ($availableSpots as $availableSpot) {
                $this->parkingSpotRepository->occupySpot($availableSpot, $vehicle->id, $plateNumber);
            }
        }

        $this->parkingSpotRepository->occupySpot($spot, $vehicle->id, $plateNumber);

        return ['success' => true];
    }

    public function unparkVehicle(int $spotId, array $data): array
    {
        $spot = $this->parkingSpotRepository->findById($spotId, $data['parking_lot_id']);

        if (!$spot) {
            return ['success' => false, 'message' => 'Parking spot not found', 'status' => 404];
        }

        if (!$spot->occupied) {
            return ['success' => false, 'message' => 'Spot is already free', 'status' => 400];
        }

        $this->parkingSpotRepository->freeSpotsByPlateNumber($spot->plate_number);

        return ['success' => true];
    }
}
