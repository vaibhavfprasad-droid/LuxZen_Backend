@extends('layouts.app')

@section('title', 'Dashboard - Luxzen Admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- KPI Cards Row -->
    <div class="row">
        <!-- Total Bookings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow h-100 py-2" style="border-left-color: #4e73df;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow h-100 py-2" style="border-left-color: #1cc88a;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($revenue ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow h-100 py-2" style="border-left-color: #36b9cc;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow h-100 py-2" style="border-left-color: #f6c23e;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVehicles ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status Tiles -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info p-3 rounded text-white mb-3 shadow-sm">
                <h3>{{ $statusCounts['pending'] ?? 0 }}</h3>
                <p>Pending Bookings</p>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary p-3 rounded text-white mb-3 shadow-sm">
                <h3>{{ $statusCounts['ongoing'] ?? 0 }}</h3>
                <p>Ongoing Trips</p>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success p-3 rounded text-white mb-3 shadow-sm">
                <h3>{{ $statusCounts['completed'] ?? 0 }}</h3>
                <p>Completed Bookings</p>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger p-3 rounded text-white mb-3 shadow-sm">
                <h3>{{ $statusCounts['cancelled'] ?? 0 }}</h3>
                <p>Cancelled Bookings</p>
            </div>
        </div>
    </div>

    <!-- Bookings Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Bookings Overview (Last 30 Days)</h6>
                    {{-- Note: This is a placeholder for filtering. Full functionality requires AJAX/Livewire --}}
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active">Daily</button>
                        <button type="button" class="btn btn-sm btn-outline-primary">Weekly</button>
                        <button type="button" class="btn btn-sm btn-outline-primary">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- This is the only script block that will be processed by Blade --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Bookings Chart
    const ctx = document.getElementById('bookingsChart').getContext('2d');
    const bookingsChart = new Chart(ctx, {
        type: 'line', // You can change this to 'bar'
        data: {
            // Use the json directive to safely convert the PHP array to a JavaScript array
            labels: @json($chartLabels),
            datasets: [{
                label: 'Bookings',
                data: @json($chartData),
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 4,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) { label += context.parsed.y + ' bookings'; }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush