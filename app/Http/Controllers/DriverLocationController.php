<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverLocation;

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
}
