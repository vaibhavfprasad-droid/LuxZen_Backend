<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        // Example: fetch only active vehicles; you can add filters e.g., by location if needed
        $vehicles = Vehicle::where('status', 'active')->get();

        return response()->json(['vehicles' => $vehicles]);
    }
}
