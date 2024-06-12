<?php
namespace App\Http\Controllers;

class ParkingLotController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/parking-lots",
     *     summary="Get all parking lots",
     *     tags={"Parking Lots"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ParkingLot")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $parkingLots = Cache::remember('parking_lots', 60, function () {
            return ParkingLot::with('parkingSpots')->get();
        });
        return response()->json($parkingLots);
    }

    /**
     * @OA\Get(
     *     path="/api/parking-lots/",
     *     summary="Show a parking lot",
     *     tags={"Parking Lots"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ParkingLot")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        // Validate the parking lot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_lots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking lot ID'], 400);
        }

        $parkingLot = Cache::remember('parking_lot_' . $id, 60, function () use ($id) {
            return ParkingLot::with('parkingSpots')->findOrFail($id);
        });
        return response()->json($parkingLot);
    }


    public function getAvailability($id)
    {
        // Validate the parking lot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_lots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking lot ID'], 400);
        }

        $availability = Cache::remember('parking_lot_' . $id . '_availability', 60, function () use ($id) {
            $parkingLot = ParkingLot::findOrFail($id);
            $totalSpots = $parkingLot->capacity;
            $availableSpots = $parkingLot->parkingSpots()->where('occupied', false)->count();
            return [
                'total_spots' => $totalSpots,
                'available_spots' => $availableSpots
            ];
        });
        return response()->json($availability);
    }
}

