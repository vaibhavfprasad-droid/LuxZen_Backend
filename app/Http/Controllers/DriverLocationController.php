<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverLocation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverLocationController extends Controller
{
    public function index()
    {
        $locations = DriverLocation::with('driver')
            ->latest('created_at')
            ->get();

        return view('pages.driver-locations', compact('locations'));
    }
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->type !== 'driver') {
            return response()->json(['error' => 'Only drivers can send location'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Update existing location or create if not exists
        $location = DriverLocation::updateOrCreate(
            ['driver_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json([
            'status' => 'success',
            'location' => $location
        ]);
    }
    public function latest($driverId)
    {
        try {
            // Get latest location by created_at desc
            $location = DriverLocation::where('driver_id', $driverId)
                ->orderByDesc('created_at')
                ->firstOrFail();

            return response()->json([
                'latitude'  => $location->latitude,
                'longitude' => $location->longitude,
                'updated_at'=> $location->created_at->toDateTimeString(),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Driver location not found'], 404);
        }
    }
}
