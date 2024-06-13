<?php
namespace App\Http\Controllers;

use \Cache;
use App\Models\ParkingLot;
use \Validator;

class ParkingLotController extends Controller
{
    /**
     * @OA\Get(
     *     path="/parking-lots",
     *     summary="List parking lots",
     *     description="Retrieve a list of all parking lots with their associated parking spots",
     *     operationId="listParkingLots",
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
     *     path="/parking-lots/{id}",
     *     summary="Get parking lot details",
     *     description="Retrieve the details of a specific parking lot, including its associated parking spots",
     *     operationId="showParkingLot",
     *     tags={"Parking Lots"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the parking lot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ParkingLot")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parking lot ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid parking lot ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking lot not found"
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

    /**
     * @OA\Get(
     *     path="/parking-lots/{id}/availability",
     *     summary="Get parking lot availability",
     *     description="Retrieve the total spots and available spots for a specific parking lot",
     *     operationId="getAvailability",
     *     tags={"Parking Lots"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the parking lot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_spots", type="integer"),
     *             @OA\Property(property="available_spots", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parking lot ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid parking lot ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking lot not found"
     *     )
     * )
     */
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
