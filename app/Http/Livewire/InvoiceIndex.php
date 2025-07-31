<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use Livewire\WithPagination;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $statusFilter = '';
    public $customerSearch = '';
    public
 
$dateFrom = '';
    public $dateTo = '';
    public $tripIdFilter = '';

    // Handles ?trip_id=123 in the URL
    protected $queryString = [
        'tripIdFilter' => ['except' => '', 'as' => 'trip_id'],
    ];

    public function updating($property)
    {
        if (in_array($property, ['statusFilter', 'customerSearch', 'dateFrom', 'dateTo', 'tripIdFilter'])) {
            $this->resetPage();
        }
    }
    
    protected function getFilteredQuery()
    {
        // Eager load all relationships needed for the view, Excel, AND PDF
        return Invoice::with(['trip.customer', 'trip.driver'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->customerSearch, fn($q) => $q->whereHas('trip.customer', function($subQuery) {
                $subQuery->where('name', 'like', "%{$this->customerSearch}%");
            }))
            ->when($this->dateFrom, fn($q) => $q->whereDate('issued_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('issued_at', '<=', $this->dateTo))
            ->when($this->tripIdFilter, fn($q) => $q->where('trip_id', $this->tripIdFilter));
    }

    public function exportExcel()
    {
        $query = $this->getFilteredQuery();
        $fileName = 'invoices-export-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new InvoicesExport($query), $fileName);
    }

    public function exportPdf($invoiceId)
    {
        // Find the specific invoice and ensure its relationships are loaded
        $invoice = Invoice::with(['trip.customer'])->findOrFail($invoiceId);
        
        // Load the PDF view with the invoice data
        $pdf = PDF::loadView('pdf.invoice-receipt', ['invoice' => $invoice]);
        
        // Use streamDownload for better browser compatibility
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function render()
    {
        $invoices = $this->getFilteredQuery()->latest('issued_at')->paginate(15);
        
        return view('livewire.invoice-index', [
            'invoices' => $invoices
        ]);
    }
}