<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ParkingSpot",
 *     required={"id", "occupied", "parking_lot_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="occupied", type="string", example="true"),
 *     @OA\Property(property="parking_lot_id", type="integer", example="10"),
 * )
 */
class ParkingSpot extends Model
{
    protected $fillable = ['type', 'occupied', 'parking_lot_id'];

    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class);
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
}

