<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Modules\Sale\Entities\Sale;
use Modules\Expense\Entities\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ProfitLossReport extends Component
{
    public string $startDate;
    public string $endDate;
    public bool $showComparison = false;
    public string $activePeriod = 'this_month';

    public int $revenue = 0;
    public int $cogs = 0;
    public int $grossProfit = 0;
    public int $expenses = 0;
    public int $netProfit = 0;

    // Comparison data
    public int $prevRevenue = 0;
    public int $prevCogs = 0;
    public int $prevGrossProfit = 0;
    public int $prevExpenses = 0;
    public int $prevNetProfit = 0;

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
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate   = $today;
    }

    public function updated($prop): void
    {
        $this->validateOnly($prop);

        if ($prop === 'startDate' || $prop === 'endDate') {
            $this->activePeriod = 'custom';
        }
    }

    public function toggleComparison(): void
    {
        $this->showComparison = !$this->showComparison;
    }

    public function setPeriod(string $period): void
    {
        $this->activePeriod = $period;
        $now = Carbon::now();

        switch ($period) {
            case 'this_month':
                $this->startDate = $now->startOfMonth()->toDateString();
                $this->endDate   = $now->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->startDate = $now->subMonth()->startOfMonth()->toDateString();
                $this->endDate   = $now->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $this->startDate = $now->startOfQuarter()->toDateString();
                $this->endDate   = $now->endOfQuarter()->toDateString();
                break;
            case 'this_year':
                $this->startDate = $now->startOfYear()->toDateString();
                $this->endDate   = $now->endOfYear()->toDateString();
                break;
        }

        $this->generateReport();
    }

    public function render()
    {
        $this->calculate();

        // Calculate previous period for comparison
        if ($this->showComparison) {
            $this->calculatePreviousPeriod();
        }

        return view('livewire.reports.profit-loss-report', [
            'revenue'        => $this->revenue,
            'cogs'           => $this->cogs,
            'grossProfit'    => $this->grossProfit,
            'expenses'       => $this->expenses,
            'netProfit'      => $this->netProfit,
            'showComparison' => $this->showComparison,
            'prevRevenue'    => $this->prevRevenue,
            'prevCogs'       => $this->prevCogs,
            'prevGrossProfit'=> $this->prevGrossProfit,
            'prevExpenses'   => $this->prevExpenses,
            'prevNetProfit'  => $this->prevNetProfit,
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

        $this->revenue = (int) Sale::completed()
            ->whereBetween('date', [$from, $to])
            ->sum('total_amount');

        $this->cogs = (int) Sale::completed()
            ->whereBetween('date', [$from, $to])
            ->sum('total_hpp');

        $this->grossProfit = $this->revenue - $this->cogs;

        $this->expenses = (int) Expense::query()
            ->whereBetween('date', [$from, $to])
            ->sum('amount');

        $this->netProfit = $this->grossProfit - $this->expenses;
    }

    protected function calculatePreviousPeriod(): void
    {
        // Calculate same duration for previous period
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $days = $start->diffInDays($end) + 1;

        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1);

        $this->prevRevenue = (int) Sale::completed()
            ->whereBetween('date', [$prevStart, $prevEnd])
            ->sum('total_amount');

        $this->prevCogs = (int) Sale::completed()
            ->whereBetween('date', [$prevStart, $prevEnd])
            ->sum('total_hpp');

        $this->prevGrossProfit = $this->prevRevenue - $this->prevCogs;

        $this->prevExpenses = (int) Expense::query()
            ->whereBetween('date', [$prevStart, $prevEnd])
            ->sum('amount');

        $this->prevNetProfit = $this->prevGrossProfit - $this->prevExpenses;
    }

    /** EXPORT CSV */
    public function exportCsv()
    {
        $this->validate();
        $this->calculate();

        $from = $this->startDate;
        $to   = $this->endDate;
        $filename = "profit_loss_{$from}_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Laporan Laba/Rugi']);
            fputcsv($out, ['Periode', "{$from} s/d {$to}"]);
            fputcsv($out, []);
            fputcsv($out, ['Pos', 'Nominal (IDR)']);
            fputcsv($out, ['Revenue (Pendapatan)', $this->revenue]);
            fputcsv($out, ['COGS (HPP)', $this->cogs]);
            fputcsv($out, ['Gross Profit (Laba Kotor)', $this->grossProfit]);
            fputcsv($out, ['Operating Expenses (Beban Operasional)', $this->expenses]);
            fputcsv($out, ['Net Profit (Laba Bersih)', $this->netProfit]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /** EXPORT EXCEL */
    public function exportExcel()
    {
        $this->validate();
        $this->calculate();

        return Excel::download(new \App\Exports\ProfitLossExport(
            $this->startDate,
            $this->endDate,
            $this->revenue,
            $this->cogs,
            $this->grossProfit,
            $this->expenses,
            $this->netProfit
        ), "laporan_laba_rugi_{$this->startDate}_{$this->endDate}.xlsx");
    }

    /** EXPORT PDF */
    public function exportPdf()
    {
        $this->validate();
        $this->calculate();

        $pdf = Pdf::loadView('exports.profit-loss-pdf', [
            'startDate'   => $this->startDate,
            'endDate'     => $this->endDate,
            'revenue'     => $this->revenue,
            'cogs'        => $this->cogs,
            'grossProfit' => $this->grossProfit,
            'expenses'    => $this->expenses,
            'netProfit'   => $this->netProfit,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "laporan_laba_rugi_{$this->startDate}_{$this->endDate}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

