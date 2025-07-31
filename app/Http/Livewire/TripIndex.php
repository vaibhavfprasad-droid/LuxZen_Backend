<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TripIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $statusFilter = '';

    // Modal properties
    public $showAssignModal = false;
    public $tripToAssign;
    public $driver_id, $vehicle_id;
    public $availableDrivers = [];

    public function rules() {
        return [
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ];
    }
    
    public function updatingStatusFilter() { 
        $this->resetPage(); 
    }

    public function openAssignModal(Trip $trip)
    {
        $this->tripToAssign = $trip;
        $this->driver_id = $trip->driver_id;
        $this->vehicle_id = $trip->vehicle_id;

        $startTime = $trip->scheduled_at->clone();
        $estimatedEndTime = $trip->scheduled_at->clone()->addHours(2);

        $this->availableDrivers = User::where('type', 'driver')
            ->where('status', 'active')
            ->where(function ($query) use ($startTime, $estimatedEndTime, $trip) {
                $query->whereDoesntHave('tripsAsDriver', function ($subQuery) use ($startTime, $estimatedEndTime, $trip) {
                    $subQuery->where('id', '!=', $trip->id) 
                             ->whereNotIn('status', ['completed', 'canceled'])
                             ->where(function ($q) use ($startTime, $estimatedEndTime) {
                                $q->where('scheduled_at', '<', $estimatedEndTime)
                                  ->where(DB::raw("DATE_ADD(scheduled_at, INTERVAL 2 HOUR)"), '>', $startTime);
                             });
                })
                ->orWhere('id', $trip->driver_id);
            })
            ->get();

        $this->showAssignModal = true;
    }

    public function updatedDriverId($driverId)
    {
        $driver = User::find($driverId);
        if ($driver && $driver->vehicle) {
            $this->vehicle_id = $driver->vehicle->id;
        } else {
            $this->vehicle_id = null;
        }
    }

    public function assignDriver()
    {
        $this->validate();

        $updateData = [
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
        ];

        if ($this->tripToAssign->status === 'completed') {
            $updateData['status'] = 'driver_assigned';
            $updateData['completed_at'] = null;
            session()->flash('message', 'Trip details updated and status reverted to "Driver Assigned".');
        } else {
            $updateData['status'] = 'driver_assigned';
            session()->flash('message', 'Driver successfully assigned to trip.');
        }

        $this->tripToAssign->update($updateData);

        $this->showAssignModal = false;
    }

    public function markAsCompleted(Trip $trip)
    {
        $trip->update(['status' => 'completed', 'completed_at' => Carbon::now()]);
        session()->flash('message', 'Trip marked as completed.');
    }

    public function cancelTrip(Trip $trip)
    {
        $trip->update(['status' => 'canceled', 'canceled_at' => Carbon::now()]);
        session()->flash('message', 'Trip has been canceled.');
    }
    
    public function render()
    {
        $trips = Trip::with(['customer', 'driver', 'vehicle'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.trip-index', compact('trips'));
    }
}