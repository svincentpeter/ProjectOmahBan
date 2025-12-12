<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Modules\Sale\Entities\Sale;
use Modules\Expense\Entities\Expense;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnDetail;
use Illuminate\Support\Facades\Schema;

class ProfitLossReport extends Component
{
    public string $startDate;
    public string $endDate;

    public int $revenue = 0;
    public int $cogs = 0;
    public int $grossProfit = 0;
    public int $expenses = 0;
    public int $netProfit = 0;

    protected function rules(): array
    {
        return [
            'startDate' => ['required', 'date'],
            'endDate'   => ['required', 'date', 'after_or_equal:startDate'],
        ];
    }

    public function mount(): void
    {
        $today = Carbon::now()->toDateString();
        $this->startDate = $today;
        $this->endDate   = $today;
    }

    public function updated($prop): void
    {
        $this->validateOnly($prop);
    }

    public function render()
    {
        $this->calculate();
        return view('livewire.reports.profit-loss-report', [
            'revenue'     => $this->revenue,
            'cogs'        => $this->cogs,
            'grossProfit' => $this->grossProfit,
            'expenses'    => $this->expenses,
            'netProfit'   => $this->netProfit,
        ]);
    }

    public function generateReport(): void
    {
        $this->validate();
        $this->calculate();
    }

    protected function calculate(): void
    {
        $this->validate();

        $from = Carbon::parse($this->startDate)->startOfDay();
        $to   = Carbon::parse($this->endDate)->endOfDay();

        $revenueGross = (int) Sale::completed()
            ->whereBetween('date', [$from, $to])
            ->sum('total_amount');

        // $revenueReturn = (int) SaleReturn::query()
        //     ->whereBetween('date', [$from, $to])
        //     ->sum('total_amount');
        $revenueReturn = 0;

        $this->revenue = $revenueGross - $revenueReturn;

        $cogsGross = (int) Sale::completed()
            ->whereBetween('date', [$from, $to])
            ->sum('total_hpp');

        // $queryBase = SaleReturnDetail::query()
        //     ->whereHas('saleReturn', fn($q) => $q->whereBetween('date', [$from, $to]));

        // if (Schema::hasColumn('sale_return_details', 'hpp')) {
        //     $cogsReturn = (int) $queryBase->clone()->sum('hpp');
        // } elseif (Schema::hasColumn('sale_return_details', 'total_hpp')) {
        //     $cogsReturn = (int) $queryBase->clone()->sum('total_hpp');
        // } elseif (Schema::hasColumn('sale_return_details', 'cost')) {
        //     $cogsReturn = (int) $queryBase->clone()->sum('cost');
        // } elseif (Schema::hasColumn('sale_return_details', 'total_cost')) {
        //     $cogsReturn = (int) $queryBase->clone()->sum('total_cost');
        // } else {
        //     $cogsReturn = 0;
        // }
        $cogsReturn = 0;

        $this->cogs = $cogsGross - $cogsReturn;

        $this->grossProfit = $this->revenue - $this->cogs;

        $this->expenses = (int) Expense::query()
            ->whereBetween('date', [$from, $to])
            ->sum('amount');

        $this->netProfit = $this->grossProfit - $this->expenses;
    }

    /** EXPORT CSV: KPIs */
    public function exportCsv()
    {
        $this->validate();
        $from = $this->startDate;
        $to   = $this->endDate;

        // Pastikan nilai ter-update
        $this->calculate();

        $filename = "profit_loss_{$from}_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Laporan Laba/Rugi']);
            fputcsv($out, ['Periode', "{$from} s/d {$to}"]);
            fputcsv($out, []);
            fputcsv($out, ['Pos', 'Nominal (IDR)']);
            fputcsv($out, ['Revenue (Bersih)', $this->revenue]);
            fputcsv($out, ['COGS (HPP Bersih)', $this->cogs]);
            fputcsv($out, ['Gross Profit', $this->grossProfit]);
            fputcsv($out, ['Expenses', $this->expenses]);
            fputcsv($out, ['Net Profit', $this->netProfit]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
