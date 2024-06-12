<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingLotController;
use App\Http\Controllers\ParkingSpotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/parking-lots', [ParkingLotController::class, 'index']);
Route::get('/parking-lots/{id}', [ParkingLotController::class, 'show']);
Route::get('/parking-lots/{id}/availability', [ParkingLotController::class, 'getAvailability']);

Route::post('/parking-spots/{id}/park', [ParkingSpotController::class, 'park']);
Route::post('/parking-spots/{id}/unpark', [ParkingSpotController::class, 'unpark']);
