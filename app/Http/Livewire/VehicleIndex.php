<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Vehicle;
use App\Models\User;
use Livewire\WithPagination;

class VehicleIndex extends Component
{
    use WithPagination;

    // Use Bootstrap's theme for pagination
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $statusFilter = '';

    // Modal properties
    public $showModal = false;
    public $vehicleId;
    public $model, $type, $number_plate, $status, $driver_id;
    
    // For the driver dropdown
    public $drivers = [];

    // Validation rules
    public function rules()
    {
        return [
            'model' => 'required|string|min:2',
            'type' => 'required|string',
            'number_plate' => 'required|string|unique:vehicles,number_plate,' . $this->vehicleId,
            'status' => 'required|in:active,in_maintenance,inactive',
            'driver_id' => 'nullable|exists:users,id',
        ];
    }

    // Load available drivers when the component mounts
    public function mount()
    {
        // $this->drivers = User::where('type', 'driver')->where('status', 'active')->get();
         $this->drivers = User::where([
            ['type', '=', 'driver'],
            ['status', '=', 'active']
        ])->get();
    }
    
    // Reset pagination when a filter is updated
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function create()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function edit(Vehicle $vehicle)
    {
        $this->vehicleId = $vehicle->id;
        $this->model = $vehicle->model;
        $this->type = $vehicle->type;
        $this->number_plate = $vehicle->number_plate;
        $this->status = $vehicle->status;
        $this->driver_id = $vehicle->driver_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'model' => $this->model,
            'type' => $this->type,
            'number_plate' => $this->number_plate,
            'status' => $this->status,
            'driver_id' => $this->driver_id,
        ]);

        session()->flash('message', $this->vehicleId ? 'Vehicle updated successfully.' : 'Vehicle created successfully.');
        $this->showModal = false;
    }

    public function delete(Vehicle $vehicle)
    {
        $vehicle->delete();
        session()->flash('message', 'Vehicle deleted successfully.');
    }

    private function resetInput()
    {
        $this->vehicleId = null;
        $this->model = '';
        $this->type = '';
        $this->number_plate = '';
        $this->status = 'active';
        $this->driver_id = null;
    }

    public function render()
    {
        $vehicles = Vehicle::with('driver') // Eager load the driver relationship
            ->when($this->search, fn($q) => $q->where('model', 'like', "%{$this->search}%")->orWhere('number_plate', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
            
        return view('livewire.vehicle-index', compact('vehicles'));
    }
}