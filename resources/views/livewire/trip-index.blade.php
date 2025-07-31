<div x-data="{ view: 'table' }">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0">Trips</h3>
        <div class="btn-group" role="group">
            <button type="button" class="btn" :class="view === 'table' ? 'btn-primary' : 'btn-outline-primary'" @click="view = 'table'"><i class="fas fa-list"></i> Table View</button>
            <button type="button" class="btn" :class="view === 'calendar' ? 'btn-primary' : 'btn-outline-primary'" @click="view = 'calendar'"><i class="fas fa-calendar-alt"></i> Calendar View</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table View -->
    <div x-show="view === 'table'">
        <!-- Filter Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <select wire:model.live="statusFilter" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="driver_assigned">Driver Assigned</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trips Table -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover m-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Trip Details</th>
                            <th>Driver/Vehicle</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trips as $trip)
                            <tr>
                                <td>{{ $trip->customer->name ?? 'N/A' }}</td>
                                <td>
                                    <div><small class="text-muted">From:</small> {{ $trip->pickup_location }}</div>
                                    <div><small class="text-muted">To:</small> {{ $trip->dropoff_location }}</div>
                                    <small class="text-muted"><i class="far fa-clock"></i> {{ $trip->scheduled_at->format('M d, Y @ h:i A') }}</small>
                                </td>
                                <td>
                                    <div><i class="fas fa-user"></i> {{ $trip->driver->name ?? 'Unassigned' }}</div>
                                    <small class="text-muted"><i class="fas fa-car"></i> {{ $trip->vehicle->number_plate ?? '' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill text-capitalize
                                        @if($trip->status == 'pending') bg-warning text-dark
                                        @elseif($trip->status == 'driver_assigned') bg-info text-dark
                                        @elseif($trip->status == 'ongoing') bg-primary
                                        @elseif($trip->status == 'completed') bg-success
                                        @elseif($trip->status == 'canceled') bg-danger
                                        @else bg-secondary @endif">
                                        {{ str_replace('_', ' ', $trip->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- ======================= CORRECTED ACTIONS BLOCK ======================= --}}
                                    
                                    @if(in_array($trip->status, ['pending', 'driver_assigned']))
                                        <div class="btn-group">
                                            <button wire:click="openAssignModal({{ $trip->id }})" class="btn btn-sm {{ $trip->driver_id ? 'btn-warning' : 'btn-primary' }}">
                                                @if($trip->driver_id) <i class="fas fa-edit"></i> Change @else <i class="fas fa-user-plus"></i> Assign @endif
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">Toggle Dropdown</span></button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-danger" href="#" wire:click.prevent="cancelTrip({{ $trip->id }})" wire:confirm="Are you sure?">Cancel Trip</a></li>
                                            </ul>
                                        </div>

                                    @elseif($trip->status == 'ongoing')
                                        <button wire:click="markAsCompleted({{ $trip->id }})" wire:confirm="Mark this trip as completed?" class="btn btn-sm btn-success">
                                            <i class="fas fa-check-circle"></i> Complete Trip
                                        </button>

                                    @elseif($trip->status == 'completed')
                                        <div class="btn-group">
                                            <a href="{{ route('invoices.index') }}?trip_id={{$trip->id}}" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-invoice"></i> Invoice
                                            </a>
                                            <button wire:click="openAssignModal({{ $trip->id }})" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>

                                    @else
                                        <span>-</span>
                                    @endif
                                    
                                    {{-- ============================= END OF BLOCK ============================= --}}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center p-4">No trips found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trips->hasPages())
                <div class="card-footer">{{ $trips->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Calendar View -->
    <div x-show="view === 'calendar'" wire:ignore>
        <div class="card shadow-sm">
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <!-- Assign Driver Modal -->
    @if($showAssignModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $tripToAssign->driver_id ? 'Change' : 'Assign' }} Driver for Trip #{{ $tripToAssign->id }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showAssignModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading.remove wire:target="assignDriver">
                        <div class="mb-3">
                            <label for="driver_id" class="form-label">Select Driver</label>
                            <select id="driver_id" class="form-select @error('driver_id') is-invalid @enderror" wire:model.live="driver_id">
                                <option value="">-- Choose a driver --</option>
                                @forelse($availableDrivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @empty
                                    <option value="" disabled>No available drivers found.</option>
                                @endforelse
                            </select>
                            @error('driver_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Assigned Vehicle</label>
                            <input type="text" id="vehicle_id" class="form-control" 
                                   value="{{ $vehicle_id && \App\Models\Vehicle::find($vehicle_id) ? \App\Models\Vehicle::find($vehicle_id)->number_plate . ' (' . \App\Models\Vehicle::find($vehicle_id)->model . ')' : 'No vehicle assigned to driver' }}" disabled>
                            <input type="hidden" wire:model="vehicle_id">
                            @error('vehicle_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div wire:loading wire:target="assignDriver" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Assigning, please wait...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showAssignModal', false)">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="assignDriver" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="assignDriver">{{ $tripToAssign->driver_id ? 'Save Changes' : 'Assign Trip' }}</span>
                        <span wire:loading wire:target="assignDriver">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    function tripCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: (fetchInfo, successCallback, failureCallback) => {
                @this.call('getEvents')
                    .then(response => successCallback(response))
                    .catch(error => {
                        console.error('Error fetching FullCalendar events:', error);
                        failureCallback(error);
                    });
            }
        });
        
        calendar.render();

        document.addEventListener('alpine:initialized', () => {
            let viewData = Alpine.find(calendarEl.closest('[x-data]')).__x.$data;
            Alpine.effect(() => {
                if (viewData.view === 'calendar') {
                    setTimeout(() => calendar.updateSize(), 50);
                }
            });
        });
    }

    document.addEventListener('livewire:initialized', () => {
        tripCalendar();
    });
</script>
@endpush