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

        $spot = ParkingSpot::findOrFail($id)->where('parking_lot_id', $request->parking_lot_id)->first();

        if ($spot->occupied) {
            return response()->json(['message' => 'This spot is occupied'], 404);
        }

        // If Van -> 3 sports

        $vehicle = Vehicle::create([
            'type' => $request->vehicle_type,
            'parking_spot_id' => $spot->id,
            'plate_number' => rand(10000, 99999),
        ]);

        $spot->occupied = true;
        $spot->save();

        Cache::forget('parking_lot_' . $spot->parking_lot_id);
        Cache::forget('parking_lots');

        return response()->json(['message' => 'Vehicle parked successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/parking-spots/{id}/unpark",
     *     summary="Unpark a vehicle from a parking spot",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the parking spot",
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

        $spot = ParkingSpot::findOrFail($id)->where('parking_lot_id', $request->parking_lot_id);

        if (!$spot->occupied) {
            return response()->json(['message' => 'Spot is already free'], 400);
        }

        $vehicle = $spot->vehicle;
        $vehicle->delete();

        $spot->occupied = false;
        $spot->save();

        Cache::forget('parking_lot_' . $spot->parking_lot_id);
        Cache::forget('parking_lots');

        return response()->json(['message' => 'Vehicle unparked successfully']);
    }
}


