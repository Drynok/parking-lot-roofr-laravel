<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Vehicle",
 *     required={"id", "type", "parking_spot_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="Car"),
 *     @OA\Property(property="parking_spot_id", type="integer", example="10"),
 *     @OA\Property(property="plate_number", type="string", example="10"),
 * )
 */
class Vehicle extends Model
{
    protected $fillable = ['type', 'parking_spot_id', 'plate_number'];

    public function parkingSpot()
    {
        return $this->belongsTo(ParkingSpot::class);
    }
}
