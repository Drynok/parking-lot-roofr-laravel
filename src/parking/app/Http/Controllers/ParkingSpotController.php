<?php
namespace App\Http\Controllers;

use \Cache;
use App\Models\ParkingSpot;
use \Validator;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class ParkingSpotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/parking-spots/{id}/park",
     *     summary="Park a vehicle in a parking spot",
     *     tags={"Parking Spots"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the parking spot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_type",
     *         in="query",
     *         description="Type of the vehicle",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="parking_lot_id",
     *         in="query",
     *         description="ID of the parking lot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle parked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Vehicle parked successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parking spot ID",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid parking spot ID"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No available spot found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No available spot found"
     *             )
     *         )
     *     )
     * )
     */
    public function park(Request $request, $id)
    {
        // Validate the parking spot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_spots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking spot ID'], 400);
        }

        $spot = ParkingSpot::where('id', $id)
            ->where('parking_lot_id', $request->parking_lot_id)
            ->first();

        if (!$spot) {
            return response()->json(['message' => 'Parking spot not found'], 404);
        }

        if ($spot->occupied) {
            return response()->json(['message' => 'This spot is occupied'], 404);
        }

        $vehicle = Vehicle::where('name', $request->vehicle_type)->firstOrFail();
        $plate = rand(100000, 999999);

        if ($vehicle->space_occupied > 1) {
            // If Van - check if next 2 spots are free.
            $currentSpotId = $spot->id;
            $spots_around = ParkingSpot::where(function ($query) use ($currentSpotId) {
                $query->where('id', $currentSpotId - 1)
                    ->orWhere('id', $currentSpotId + 1);
            })
                ->where('occupied', false)
                ->get();

            if (!$spots_around) {
                return response()->json(['message' => 'Not enough space to park here']);
            }

            foreach ($spots_around as $next_spot) {
                $next_spot->occupied = true;
                $next_spot->vehicle_id = $vehicle->id;
                $next_spot->plate_number = $plate;
                $next_spot->save();
            }
        }

        $spot->occupied = true;
        $spot->plate_number = $plate; // Generate a random number.
        $spot->vehicle_id = $vehicle->id;
        $spot->save();

        Cache::forget('parking_lot_' . $spot->parking_lot_id);
        Cache::forget('parking_lot_' . $spot->parking_lot_id . '_availability');
        Cache::forget('parking_lots');

        return response()->json(['message' => 'Vehicle parked successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/parking-spots/{id}/unpark",
     *     summary="Unpark a vehicle from a parking spot",
     *     tags={"Parking Spots"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the parking spot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="parking_lot_id",
     *         in="query",
     *         description="ID of the parking lot",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle unparked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Vehicle unparked successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parking spot ID or spot is already free",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example={
     *                     "Invalid parking spot ID",
     *                     "Spot is already free"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking spot not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Parking spot not found"
     *             )
     *         )
     *     )
     * )
     */
    public function unpark(Request $request, $id)
    {
        // Validate the parking spot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_spots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking spot ID'], 400);
        }

        $spot = ParkingSpot::where('id', $id)
            ->where('parking_lot_id', $request->parking_lot_id)
            ->first();

        if (!$spot) {
            return response()->json(['message' => 'Parking spot not found'], 404);
        }

        if (!$spot->occupied) {
            return response()->json(['message' => 'Spot is already free'], 400);
        }

        // Free other spots.
        $other_spots = ParkingSpot::where('plate_number', $spot->plate_number)->get();

        if ($other_spots) {
            foreach ($other_spots as $other_spot) {
                $other_spot->occupied = false;
                $other_spot->vehicle_id = null;
                $other_spot->plate_number = null;
                $other_spot->save();
            }
        }

        $spot->vehicle_id = null;
        $spot->plate_number = null;
        $spot->occupied = false;
        $spot->save();

        Cache::forget('parking_lot_' . $spot->parking_lot_id);
        Cache::forget('parking_lot_' . $spot->parking_lot_id . '_availability');
        Cache::forget('parking_lots');

        return response()->json(['message' => 'Vehicle unparked successfully']);
    }
}


