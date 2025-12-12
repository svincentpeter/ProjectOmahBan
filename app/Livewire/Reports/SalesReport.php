<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Sale\Entities\Sale;

class SalesReport extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customers;
    public $start_date;
    public $end_date;
    public $customer_id;
    public $sale_status;
    public $payment_status;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date'   => 'required|date|after:start_date',
    ];

    public function mount($customers) {
        $this->customers = $customers;
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->customer_id = '';
        $this->sale_status = '';
        $this->payment_status = '';
    }

    public function render() {
        $query = Sale::query()
            ->whereDate('date', '>=', $this->start_date)
            ->whereDate('date', '<=', $this->end_date)
            ->when($this->customer_id, function ($q) {
                return $q->where('customer_id', $this->customer_id);
            })
            ->when($this->sale_status, function ($q) {
                return $q->where('status', $this->sale_status);
            })
            ->when($this->payment_status, function ($q) {
                return $q->where('payment_status', $this->payment_status);
            });

        // Clone for summary to avoid resetting pagination
        $summaryQuery = clone $query;
        $totalOmset = $summaryQuery->sum('total_amount');
        $totalPaid  = $summaryQuery->sum('paid_amount');
        $totalDue   = $summaryQuery->sum('due_amount');
        $count      = $summaryQuery->count();

        $sales = $query->orderBy('date', 'desc')->paginate(10);

        return view('livewire.reports.sales-report', [
            'sales'      => $sales,
            'totalOmset' => $totalOmset,
            'totalPaid'  => $totalPaid,
            'totalDue'   => $totalDue,
            'totalCount' => $count
        ]);
    }

    public function generateReport() {
        $this->validate();
        $this->render();
    }
}
