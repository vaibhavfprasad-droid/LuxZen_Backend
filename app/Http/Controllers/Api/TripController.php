<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Resources\TripResource;
use App\Http\Resources\UserResource;
use App\Models\User;

class TripController extends Controller
{
    /**
     * List trips for authenticated customer.
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
     * List trips assigned to authenticated driver.
     */
 public function driverTrips(Request $request)
{
    $driver = $request->user();

    // Get the status filter parameter from query params (array or string)
    $statuses = $request->query('status', null);

    $query = $driver->tripsAsDriver()->with(['customer', 'vehicle'])->latest();

    // If status filter provided, apply it
    if ($statuses) {
        // Ensure it's an array (single status can come as string)
        $filterStatuses = is_array($statuses) ? $statuses : [$statuses];
        $query->whereIn('status', $filterStatuses);
    }

    $trips = $query->paginate(20);

    return TripResource::collection($trips);
}


    /**
     * List all pending trips available for drivers to accept.
     */
    public function pendingTrips(Request $request)
    {
        $trips = Trip::where('status', 'pending')
            ->whereNull('driver_id')
            ->with(['customer', 'vehicle'])
            ->latest()
            ->paginate(20);

        return TripResource::collection($trips);
    }

    /**
     * Store a new trip request by customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'dropoff_location' => 'required|string',
            'dropoff_latitude' => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'trip_type' => 'required|in:point_to_point,rental',
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $trip = $request->user()->tripsAsCustomer()->create(array_merge($validated, [
            'status' => 'pending',
            'fare' => 0.00,
        ]));

        // TODO (Optional): Fire event / notifications for new trip

        return new TripResource($trip);
    }

    /**
     * Show specific trip details for customer.
     */
    public function show(Request $request, Trip $trip)
    {
        if ($trip->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $trip->load(['driver', 'vehicle', 'invoice']);

        return new TripResource($trip);
    }

    /**
     * Update trip (customer only).
     */
    public function update(Request $request, Trip $trip)
    {
        if ($trip->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (in_array($trip->status, ['cancelled', 'completed'])) {
            return response()->json(['message' => 'Trip cannot be modified'], 400);
        }

        $validated = $request->validate([
            'pickup_location' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'dropoff_location' => 'required|string',
            'dropoff_latitude' => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'trip_type' => 'required|string',
        ]);

        $trip->update($validated);

        return new TripResource($trip);
    }

    /**
     * Cancel trip (customer only).
     */
    public function cancel(Request $request, Trip $trip)
    {
        if ($trip->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (in_array($trip->status, ['cancelled', 'completed'])) {
            return response()->json(['message' => 'Trip already cancelled or completed'], 400);
        }

        $trip->status = 'canceled';
        $trip->canceled_at = now();
        $trip->save();

        return new TripResource($trip);
    }

    /**
     * Driver accepts trip.
     */
    // In TripController.php

public function accept(Request $request, Trip $trip)
{
    $driver = $request->user();

    // Prevent accepting if driver already has active trips
    $hasActiveTrip = Trip::where('driver_id', $driver->id)
                         ->whereIn('status', ['driver_assigned', 'ongoing'])
                         ->exists();

    if ($hasActiveTrip) {
        return response()->json([
            'message' => 'You already have an active trip. Please complete or cancel it before accepting a new one.'
        ], 400);
    }

    // Allow accept only if trip is pending and unassigned
    if ($trip->status !== 'pending' || $trip->driver_id !== null) {
        return response()->json(['message' => 'Trip cannot be accepted'], 400);
    }
  
    // Generate 6-digit OTP for start verification
    $otp = random_int(100000, 999999);

    $trip->driver_id = $driver->id;
    $trip->status = 'driver_assigned';
    $trip->otp = $otp;
    $trip->save();

    // TODO: Send $otp to user via SMS or push notification

    return response()->json([
        'trip' => new TripResource($trip),
        // Don't send OTP in response for security, send via notification
    ]);
}

public function verifyCompletionOtp(Request $request, Trip $trip)
{
    $request->validate([
        'completion_otp' => 'required|string|size:6',
    ]);

    if ($trip->driver_id !== $request->user()->id) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    if ($trip->status !== 'ongoing') {
        return response()->json(['message' => 'Trip is not ongoing'], 400);
    }

    if ($trip->completion_otp !== $request->completion_otp) {
        return response()->json(['message' => 'Invalid completion OTP'], 400);
    }

    // Mark trip completed and clear completion OTP
    $trip->status = 'completed';
    $trip->completion_otp = null;
    $trip->completed_at = now();
    $trip->save();

    return new TripResource($trip);
}
    /**
     * Driver rejects trip.
     */
    public function reject(Request $request, Trip $trip)
    {
        if ($trip->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Typically, only pending trips can be rejected
        if ($trip->status !== 'pending') {
            return response()->json(['message' => 'Trip cannot be rejected'], 400);
        }

        $trip->status = 'rejected';
        $trip->save();

        // TODO: Assign ride to another driver or notify customer

        return new TripResource($trip);
    }

    /**
 * List all trips for admin users.
 */
public function allTrips(Request $request)
{
    // Check if the user is admin
    if ($request->user()->type !== 'admin') {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $trips = Trip::with(['customer', 'driver', 'vehicle'])
        ->latest()
        ->paginate(20);

    return TripResource::collection($trips);
}
public function verifyOtp(Request $request, Trip $trip)
{
    $request->validate([
        'otp' => 'required|string|size:6',
    ]);

    if ($trip->driver_id !== $request->user()->id) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    if ($trip->status !== 'driver_assigned') {
        return response()->json(['message' => 'Invalid trip status for OTP verification'], 400);
    }

    if ($trip->otp !== $request->otp) {
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    // Generate a new completion OTP for trip completion
    $completionOtp = random_int(100000, 999999);

    $trip->status = 'ongoing';
    $trip->otp = null; // clear initial OTP
    $trip->completion_otp = $completionOtp;
    $trip->started_at = now();
    $trip->save();

    // TODO: Send $completionOtp to the customer via SMS or push notification

    return new TripResource($trip);
}

// In app/Http/Controllers/Api/TripController.php

public function getCustomerDetails($customerId)
{
    $customer = User::find($customerId);

    if (!$customer) {
        return response()->json(['message' => 'Customer not found'], 404);
    }

    return new UserResource($customer);
}


}
