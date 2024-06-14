<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Vehicle",
 *     required={"id", "type", "parking_spot_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Van"),
 *     @OA\Property(property="space_occupied", type="integer", example="1"),
 * )
 */
class Vehicle extends Model
{
    protected $fillable = ['name', 'space_occupied'];
    public $timestamps = false;
}
