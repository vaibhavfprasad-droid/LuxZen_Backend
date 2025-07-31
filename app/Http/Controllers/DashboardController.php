<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Cards Data
        $totalBookings = Trip::count();
        
        // --- THIS IS THE LINE TO CHANGE ---
        // Replace 'fare' with the actual name of the cost/price column in your 'trips' table.
        $revenue = Trip::where('status', 'completed')->sum('fare'); 
        
        // IMPORTANT: Also check your 'users' table. Does it have a 'status' column? 
        // If not, you may need to adjust or remove this line.
        $activeUsers = User::where('status', 'active')->count();
        
        $totalVehicles = Vehicle::count();

        // Status Tiles Data
        // NOTE: Make sure the values ('pending', 'ongoing', etc.) match what's in your database.
        $statusCounts = Trip::select('status', DB::raw('count(*) as total'))
                               ->groupBy('status')
                               ->pluck('total', 'status');

        // Bookings Chart Data (Last 30 days)
        $dailyBookings = Trip::select(
                                    DB::raw('DATE(created_at) as date'), 
                                    DB::raw('count(*) as count')
                                 )
                                 ->where('created_at', '>=', now()->subDays(30))
                                 ->groupBy('date')
                                 ->orderBy('date', 'asc')
                                 ->get();
        
        $chartLabels = [];
        $chartData = [];
        $period = new \DatePeriod(
             new \DateTime(now()->subDays(29)),
             new \DateInterval('P1D'),
             new \DateTime(now()->addDay())
        );

        $bookingsLookup = $dailyBookings->keyBy('date');

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $chartLabels[] = $date->format('M d');
            $chartData[] = $bookingsLookup->get($dateString)->count ?? 0;
        }

        return view('dashboard', compact(
            'totalBookings',
            'revenue',
            'activeUsers',
            'totalVehicles',
            'statusCounts',
            'chartLabels',
            'chartData'
        ));
    }
}