<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    // KPI Cards
    public $totalBookings;
    public $totalRevenue;
    public $activeUsers;
    public $totalVehicles;

    // Status Tiles
    public $pendingTrips;
    public $ongoingTrips;
    public $completedTrips;
    public $canceledTrips;

    // Graph Properties
    public $bookingPeriod = 'daily'; // Default period
    public $bookingLabels = [];
    public $bookingData = [];

    public function mount()
    {
        // Calculate KPI Cards
        $this->totalBookings = Trip::count();
        $this->totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $this->activeUsers = User::where('status', 'active')->count();
        $this->totalVehicles = Vehicle::count();

        // Calculate Status Tiles
        $this->pendingTrips = Trip::whereIn('status', ['pending', 'driver_assigned'])->count();
        $this->ongoingTrips = Trip::where('status', 'ongoing')->count();
        $this->completedTrips = Trip::where('status', 'completed')->count();
        $this->canceledTrips = Trip::where('status', 'canceled')->count();
        
        // Load initial chart data
        $this->updateBookingChartData();
    }

    public function updatedBookingPeriod()
    {
        $this->updateBookingChartData();
    }

    public function updateBookingChartData()
    {
        $query = Trip::query();
        $format = '';

        switch ($this->bookingPeriod) {
            case 'weekly':
                $startDate = Carbon::now()->subWeeks(12)->startOfWeek();
                $query->select(DB::raw('YEARWEEK(created_at) as period'), DB::raw('count(*) as count'));
                break;
            case 'monthly':
                $startDate = Carbon::now()->subMonths(12)->startOfMonth();
                $query->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'), DB::raw('count(*) as count'));
                break;
            case 'daily':
            default:
                $startDate = Carbon::now()->subDays(30);
                $query->select(DB::raw('DATE(created_at) as period'), DB::raw('count(*) as count'));
                break;
        }

        $bookings = $query->where('created_at', '>=', $startDate)
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();
            
        $this->bookingLabels = $bookings->pluck('period')->map(function ($period) {
            if ($this->bookingPeriod == 'weekly') {
                return "Week " . substr($period, 4, 2) . ", " . substr($period, 0, 4);
            }
            if ($this->bookingPeriod == 'monthly') {
                return Carbon::parse($period)->format('M Y');
            }
            return Carbon::parse($period)->format('M d');
        })->toArray();
        
        $this->bookingData = $bookings->pluck('count')->toArray();

        // Dispatch browser event to update the chart
        $this->dispatch('chartDataUpdated', ['labels' => $this->bookingLabels, 'data' => $this->bookingData]);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}