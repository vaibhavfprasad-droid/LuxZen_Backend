<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0">Vehicles</h3>
        <button wire:click="create" class="btn btn-primary">Add Vehicle</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search by model or number plate...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="in_maintenance">In Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $vehicle)
                        <tr>
                            <td>
                                <div>{{ $vehicle->model }}</div>
                                <small class="text-muted">{{ $vehicle->number_plate }}</small>
                            </td>
                            <td>{{ $vehicle->driver->name ?? 'Unassigned' }}</td>
                            <td>{{ $vehicle->type }}</td>
                            <td>
                                <span class="badge rounded-pill
                                    @if($vehicle->status == 'active') bg-success @elseif($vehicle->status == 'in_maintenance') bg-warning text-dark @else bg-danger @endif">
                                    {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $vehicle->id }})" class="btn btn-sm btn-outline-primary">Edit</button>
                                <button wire:click="delete({{ $vehicle->id }})" wire:confirm="Are you sure?" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center p-4">No vehicles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehicles->hasPages())
        <div class="card-footer">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>

    <!-- Add/Edit Vehicle Modal -->
    <div class="modal fade @if($showModal) show @endif" style="display: @if($showModal) block @else none @endif;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $vehicleId ? 'Edit Vehicle' : 'Create Vehicle' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="model" class="form-label">Model</label>
                                <input wire:model="model" type="text" class="form-control @error('model') is-invalid @enderror" id="model">
                                @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="number_plate" class="form-label">Number Plate</label>
                                <input wire:model="number_plate" type="text" class="form-control @error('number_plate') is-invalid @enderror" id="number_plate">
                                @error('number_plate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type (e.g., Sedan, SUV)</label>
                                <input wire:model="type" type="text" class="form-control @error('type') is-invalid @enderror" id="type">
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="driver_id" class="form-label">Assign Driver</label>
                                <select wire:model="driver_id" class="form-select @error('driver_id') is-invalid @enderror" id="driver_id">
                                    <option value="">Unassigned</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label for="status" class="form-label">Status</label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror" id="status">
                                    <option value="active">Active</option>
                                    <option value="in_maintenance">In Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Backdrop -->
    @if($showModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>