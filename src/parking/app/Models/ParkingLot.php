<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ParkingLot",
 *     required={"id", "name", "address", "city", "state", "zip", "capacity"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Main Street Parking"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="city", type="string", example="Anytown"),
 *     @OA\Property(property="state", type="string", example="FL"),
 *     @OA\Property(property="zip", type="string", example="12345"),
 *     @OA\Property(property="capacity", type="integer", example=50)
 * )
 */
class ParkingLot extends Model
{
    protected $fillable = ['name', 'address', 'city', 'state', 'zip', 'capacity'];
    public $timestamps = false;

    public function parkingSpots()
    {
        return $this->hasMany(ParkingSpot::class);
    }
}
