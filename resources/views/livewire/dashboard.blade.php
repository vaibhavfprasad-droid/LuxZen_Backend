<div>
    <h3 class="mb-4">Dashboard</h3>

    <!-- KPI Cards -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                        <!-- Icon Placeholder -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16"><path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/></svg>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Bookings</h6>
                        <h4 class="card-title mb-0 fw-bold">{{ $totalBookings }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add other KPI cards in the same pattern -->
    </div>

    <!-- Status Tiles -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
        <div class="col"><div class="card h-100 shadow-sm"><div class="card-body"><h5 class="card-title text-warning">Pending Trips</h5><p class="card-text fs-2 fw-bold">{{ $pendingTrips }}</p></div></div></div>
        <div class="col"><div class="card h-100 shadow-sm"><div class="card-body"><h5 class="card-title text-primary">Ongoing Trips</h5><p class="card-text fs-2 fw-bold">{{ $ongoingTrips }}</p></div></div></div>
        <div class="col"><div class="card h-100 shadow-sm"><div class="card-body"><h5 class="card-title text-success">Completed</h5><p class="card-text fs-2 fw-bold">{{ $completedTrips }}</p></div></div></div>
        <div class="col"><div class="card h-100 shadow-sm"><div class="card-body"><h5 class="card-title text-danger">Canceled</h5><p class="card-text fs-2 fw-bold">{{ $canceledTrips }}</p></div></div></div>
    </div>
    
    <!-- Booking Graph -->
    <div class="card shadow-sm">
        <div class="card-header">Bookings (Last 30 Days)</div>
        <div class="card-body">
            <div x-data="{
                labels: @json($bookingLabels),
                values: @json($bookingData),
                init() {
                    new Chart(this.$refs.canvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Bookings',
                                data: this.values,
                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                borderColor: 'rgba(78, 115, 223, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
                    })
                }
            }">
                <canvas x-ref="canvas" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>