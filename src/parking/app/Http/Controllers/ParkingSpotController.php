<?php
namespace App\Http\Controllers;

use \Cache;

class ParkingSpotController extends Controller
{
    public function park(Request $request, $id)
    {
        // Validate the parking spot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_lots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking spot ID'], 400);
        }

        $spot = ParkingSpot::where('parking_lot_id', $request->parking_lot_id)
            ->where('type', $request->vehicle_type)
            ->where('occupied', false)
            ->first();

        if (!$spot) {
            return response()->json(['message' => 'No available spot found'], 404);
        }

        $vehicle = Vehicle::create([
            'type' => $request->vehicle_type,
            'parking_spot_id' => $spot->id
        ]);

        $spot->occupied = true;
        $spot->save();

        Cache::forget('parking_lot_' . $spot->parking_lot_id);
        Cache::forget('parking_lots');

        return response()->json(['message' => 'Vehicle parked successfully']);
    }

    public function unpark($id)
    {
        // Validate the parking spot ID
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:parking_spots,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Invalid parking spot ID'], 400);
        }

        $spot = ParkingSpot::findOrFail($id);

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


