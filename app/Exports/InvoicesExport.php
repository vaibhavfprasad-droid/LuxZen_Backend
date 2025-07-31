<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query(): Builder
    {
        return $this->query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // These are the column headers in the Excel file.
        return [
            'Invoice Number',
            'Trip ID',
            'Customer Name',
            'Driver Name', // Added Driver Name
            'Amount',
            'Status',
            'Issued Date',
        ];
    }

    /**
    * @param Invoice $invoice The invoice model instance.
    * @return array
    */
    public function map($invoice): array
    {
        // This maps the data from each invoice model to a row in the Excel sheet.
        // The order MUST match the headings() order.
        return [
            $invoice->invoice_number,
            $invoice->trip_id,
            $invoice->trip->customer->name ?? 'N/A', // Safely access customer name
            $invoice->trip->driver->name ?? 'N/A',   // Safely access driver name
            $invoice->amount,
            ucfirst($invoice->status),
            // Ensure issued_at is formatted correctly. This assumes it's a Carbon instance.
            // If it's a string, Carbon::parse() is safer.
            $invoice->issued_at ? \Carbon\Carbon::parse($invoice->issued_at)->format('Y-m-d') : 'N/A',
        ];
    }
}