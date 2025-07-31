<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Resources\TripResource;

class TripController extends Controller
{
    /**
     * Display paginated list of trips for the authenticated user (as customer).
     */
    public function index(Request $request)
    {
        $trips = $request->user()->tripsAsCustomer()
            ->with(['driver', 'vehicle'])
            ->latest()
            ->paginate(20);

        return TripResource::collection($trips);
    }

    /**
     * Store a newly created trip for the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location'   => 'required|string',
            'pickup_latitude'   => 'required|numeric',
            'pickup_longitude'  => 'required|numeric',
            'dropoff_location'  => 'required|string',
            'dropoff_latitude'  => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'trip_type'         => 'required|in:point_to_point,rental',
            'scheduled_at'      => 'required|date_format:Y-m-d H:i:s',
            'vehicle_id'        => 'required|exists:vehicles,id',
        ]);

        $trip = $request->user()->tripsAsCustomer()->create([
            'pickup_location'   => $validated['pickup_location'],
            'pickup_latitude'   => $validated['pickup_latitude'],
            'pickup_longitude'  => $validated['pickup_longitude'],
            'dropoff_location'  => $validated['dropoff_location'],
            'dropoff_latitude'  => $validated['dropoff_latitude'],
            'dropoff_longitude' => $validated['dropoff_longitude'],
            'trip_type'         => $validated['trip_type'],
            'scheduled_at'      => $validated['scheduled_at'],
            'status'            => 'pending',
            'fare'              => 0.00,
            'vehicle_id'        => $validated['vehicle_id'],
        ]);

        return new TripResource($trip);
    }

    /**
     * Show details of a specific trip (only if owned by this user).
     */
    public function show(Request $request, Trip $trip)
    {
        if ($request->user()->id !== $trip->customer_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $trip->load(['driver', 'vehicle', 'invoice']);

        return new TripResource($trip);
    }

    /**
     * Update (edit) trip (only if owner and not completed/cancelled).
     */
    public function update(Request $request, Trip $trip)
    {
        if ($request->user()->id !== $trip->customer_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (in_array($trip->status, ['cancelled', 'completed'])) {
            return response()->json(['message' => 'Trip cannot be modified'], 400);
        }

        $validated = $request->validate([
            'pickup_location'   => 'required|string',
            'pickup_latitude'   => 'required|numeric',
            'pickup_longitude'  => 'required|numeric',
            'dropoff_location'  => 'required|string',
            'dropoff_latitude'  => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'scheduled_at'      => 'required|date_format:Y-m-d H:i:s',
            'vehicle_id'        => 'nullable|exists:vehicles,id',
            'trip_type'         => 'required|string',
        ]);

        $trip->update($validated);

        return new TripResource($trip);
    }

    /**
     * Cancel a trip (owner only, not already cancelled/completed).
     */
    public function cancel(Request $request, Trip $trip)
    {
        if ($request->user()->id !== $trip->customer_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (in_array($trip->status, ['cancelled', 'completed'])) {
            return response()->json(['message' => 'Trip already cancelled or completed'], 400);
        }

        $trip->status = 'cancelled';      // British spelling - consistent
        $trip->canceled_at = now();        // Track cancellation time
        $trip->save();

        return new TripResource($trip);
    }
}
