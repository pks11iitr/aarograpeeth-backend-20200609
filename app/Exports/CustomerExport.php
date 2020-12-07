<?php


namespace App\Exports;


use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerExport implements FromView
{

    public function __construct($customers)
    {
        $this->customers=$customers;
    }

    public function view(): View
    {
        return view('admin.customer.export', [
            'customers' => $this->customers
        ]);
    }
}
