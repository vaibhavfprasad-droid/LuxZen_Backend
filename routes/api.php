<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Resources\UserResource;
use App\Http\Controllers\DriverLocationController;
// use App\Http\Controllers\Api\UserController;

// Public routes without authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes requiring authentication
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/test', function () {
        return response()->json([
            'message' => 'Hello from Laravel! The connection is working!'
        ]);
    });

    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    // Customer trips routes
    Route::apiResource('trips', TripController::class)->only(['index', 'show', 'store']);
    Route::patch('trips/{trip}', [TripController::class, 'update']);
    Route::post('trips/{trip}/cancel', [TripController::class, 'cancel']);
    Route::post('trips/{trip}/accept', [TripController::class, 'accept']);
    Route::post('trips/{trip}/verify-otp', [TripController::class, 'verifyOtp']);
    Route::post('trips/{trip}/verify-completion-otp', [TripController::class, 'verifyCompletionOtp']);
    // Driver trips route
    Route::get('driver/trips', [TripController::class, 'driverTrips']);
    Route::get('customers/{customerId}', [TripController::class, 'getCustomerDetails']);

    // Vehicles
    Route::get('/vehicles', [VehicleController::class, 'index']);


    Route::get('trips/all', [TripController::class, 'allTrips']);


    Route::get('pending-trips', [TripController::class, 'pendingTrips']);
 
    // Add more routes as needed
    Route::post('/driver/location', [DriverLocationController::class, 'store']);
    // Route::get('/users/{id}', [UserController::class, 'show']);


    Route::get('/drivers/{driverId}/location/latest', [DriverLocationController::class, 'latest']);
});
