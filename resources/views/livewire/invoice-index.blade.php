<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0">Invoices & Payments</h3>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filters</h5>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="customerSearch" class="form-label">Customer Name</label>
                    <input id="customerSearch" wire:model.live.debounce.300ms="customerSearch" type="text" class="form-control" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter" class="form-select">
                        <option value="">All</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="overdue">Overdue</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFrom" class="form-label">From Date</label>
                    <input id="dateFrom" wire:model.live="dateFrom" type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="dateTo" class="form-label">To Date</label>
                    <input id="dateTo" wire:model.live="dateTo" type="date" class="form-control">
                </div>
            </div>
            <div class="row mt-3">
                 <div class="col">
                    <button wire:click="exportExcel" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="exportExcel"><i class="fas fa-file-excel"></i> Export to Excel</span>
                        <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span wire:loading wire:target="exportExcel"> Exporting...</span>
                    </button>
                 </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Trip ID</th>
                        <th>Customer</th>
                        <th>Driver</th>
                        <th>Amount</th>
                        <th>Issued Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>
                                <a href="{{ route('trips.index', ['trip_id' => $invoice->trip_id]) }}">{{ $invoice->trip_id }}</a>
                            </td>
                            <td>{{ $invoice->trip->customer->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->trip->driver->name ?? 'N/A' }}</td>
                            <td>${{ number_format($invoice->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->issued_at)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge rounded-pill text-capitalize 
                                    @if($invoice->status == 'paid') bg-success 
                                    @elseif($invoice->status == 'unpaid') bg-warning text-dark 
                                    @else bg-danger @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td>
                                 <button wire:click="exportPdf({{ $invoice->id }})" class="btn btn-sm btn-outline-danger" wire:loading.attr="disabled" wire:target="exportPdf({{ $invoice->id }})">
                                     <span wire:loading.remove wire:target="exportPdf({{ $invoice->id }})"><i class="fas fa-file-pdf"></i> PDF</span>
                                     <span wire:loading wire:target="exportPdf({{ $invoice->id }})">
                                         <span class="spinner-border spinner-border-sm" role="status"></span>
                                     </span>
                                 </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center p-4">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>