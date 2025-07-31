<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Resources\UserResource;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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

    // Trip resource - only these actions
    Route::apiResource('trips', TripController::class)->only(['index', 'show', 'store']);

    // Add Edit & Cancel
    Route::patch('trips/{trip}', [TripController::class, 'update']);
    Route::post('trips/{trip}/cancel', [TripController::class, 'cancel']);

    Route::get('/vehicles', [\App\Http\Controllers\Api\VehicleController::class, 'index']);
});
