<?php

namespace App\Repositories;

use App\Models\ParkingSpot;
use App\Repositories\Interfaces\ParkingSpotRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ParkingSpotRepository implements ParkingSpotRepositoryInterface
{
    public function findById(int $id, int $parkingLotId): ?ParkingSpot
    {
        return ParkingSpot::where('id', $id)
            ->where('parking_lot_id', $parkingLotId)
            ->first();
    }

    public function findAvailableSpots(int $currentSpotId, int $count): array
    {
        return ParkingSpot::where(function ($query) use ($currentSpotId) {
                $query->where('id', $currentSpotId - 1)
                    ->orWhere('id', $currentSpotId + 1);
            })
            ->where('occupied', false)
            ->take($count)
            ->get()
            ->toArray();
    }

    public function occupySpot(ParkingSpot $spot, int $vehicleId, string $plateNumber): void
    {
        $spot->occupied = true;
        $spot->plate_number = $plateNumber;
        $spot->vehicle_id = $vehicleId;
        $spot->save();

        $this->clearCache($spot->parking_lot_id);
    }

    public function freeSpot(ParkingSpot $spot): void
    {
        $spot->occupied = false;
        $spot->vehicle_id = null;
        $spot->plate_number = null;
        $spot->save();

        $this->clearCache($spot->parking_lot_id);
    }

    public function freeSpotsByPlateNumber(string $plateNumber): void
    {
        $spots = ParkingSpot::where('plate_number', $plateNumber)->get();

        foreach ($spots as $spot) {
            $this->freeSpot($spot);
        }
    }

    private function clearCache(int $parkingLotId): void
    {
        Cache::forget('parking_lot_' . $parkingLotId);
        Cache::forget('parking_lot_' . $parkingLotId . '_availability');
        Cache::forget('parking_lots');
    }
}
