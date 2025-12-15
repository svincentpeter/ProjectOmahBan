<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\Expense\Entities\Expense;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class DailyReport extends Component
{
    public string $date;
    public ?int $cashierId = null;
    public ?string $paymentMethod = null;
    public ?string $bankName = null;
    public bool $showComparison = false;

    public $cashiers = [];
    public array $methodOptions = ['Cash', 'Credit Card', 'Bank Transfer', 'Cheque', 'Other'];

    // Comparison data
    public int $prevOmzet = 0;
    public int $prevPengeluaran = 0;
    public int $prevIncomeBersih = 0;

    protected function rules(): array
    {
        return [
            'date'           => ['required', 'date'],
            'cashierId'      => ['nullable', 'integer'],
            'paymentMethod'  => ['nullable', 'string', 'max:50'],
            'bankName'       => ['nullable', 'string', 'max:100'],
        ];
    }

    public function mount(): void
    {
        $this->date = Carbon::now()->toDateString();
        $this->cashiers = User::query()->select('id', 'name')->orderBy('name')->get();
    }

    public function updated($prop): void
    {
        $this->validateOnly($prop);
    }

    public function toggleComparison(): void
    {
        $this->showComparison = !$this->showComparison;
    }

    protected function getReportData(): array
    {
        $omzet = (int) Sale::query()
            ->completed()
            ->whereDate('date', $this->date)
            ->when($this->cashierId, fn($q) => $q->where('user_id', $this->cashierId))
            ->sum('total_amount');

        $pengeluaran = (int) Expense::query()
            ->whereDate('date', $this->date)
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->bankName, fn($q) => $q->where('bank_name', $this->bankName))
            ->sum('amount');

        $incomeBersih = $omzet - $pengeluaran;

        $ringkasanPembayaran = SalePayment::query()
            ->whereDate('date', $this->date)
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->bankName, fn($q) => $q->where('bank_name', $this->bankName))
            ->selectRaw('payment_method, COALESCE(bank_name, "-") AS bank_name, COUNT(*) as trx_count, SUM(amount) as total_amount')
            ->groupBy('payment_method', 'bank_name')
            ->orderBy('payment_method')
            ->orderBy('bank_name')
            ->get();

        $transaksi = Sale::query()
            ->with(['user:id,name'])
            ->completed()
            ->whereDate('date', $this->date)
            ->when($this->cashierId, fn($q) => $q->where('user_id', $this->cashierId))
            ->orderBy('created_at')
            ->get(['id','reference','date','user_id','total_amount','status','payment_status','created_at']);

        // Comparison with yesterday
        if ($this->showComparison) {
            $yesterday = Carbon::parse($this->date)->subDay()->toDateString();
            
            $this->prevOmzet = (int) Sale::query()
                ->completed()
                ->whereDate('date', $yesterday)
                ->when($this->cashierId, fn($q) => $q->where('user_id', $this->cashierId))
                ->sum('total_amount');

            $this->prevPengeluaran = (int) Expense::query()
                ->whereDate('date', $yesterday)
                ->sum('amount');

            $this->prevIncomeBersih = $this->prevOmzet - $this->prevPengeluaran;
        }

        // Chart data for pie chart
        $chartData = $ringkasanPembayaran->map(fn($r) => [
            'label' => $r->payment_method,
            'value' => (int) $r->total_amount,
        ])->values()->toArray();

        return [
            'omzet'               => $omzet,
            'pengeluaran'         => $pengeluaran,
            'incomeBersih'        => $incomeBersih,
            'ringkasanPembayaran' => $ringkasanPembayaran,
            'transaksi'           => $transaksi,
            'chartData'           => $chartData,
        ];
    }

    public function render()
    {
        $this->validate();
        $data = $this->getReportData();

        return view('livewire.reports.daily-report', $data + [
            'showComparison'    => $this->showComparison,
            'prevOmzet'         => $this->prevOmzet,
            'prevPengeluaran'   => $this->prevPengeluaran,
            'prevIncomeBersih'  => $this->prevIncomeBersih,
        ]);
    }

    /** EXPORT CSV */
    public function exportCsv()
    {
        $this->validate();
        $data = $this->getReportData();
        $date = $this->date;

        $filename = "daily_report_{$date}.csv";

        return response()->streamDownload(function () use ($date, $data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Laporan Kas Harian']);
            fputcsv($out, ['Tanggal', $date]);
            fputcsv($out, []);
            fputcsv($out, ['KPI', 'Nominal (IDR)']);
            fputcsv($out, ['Omzet', $data['omzet']]);
            fputcsv($out, ['Pengeluaran', $data['pengeluaran']]);
            fputcsv($out, ['Income Bersih', $data['incomeBersih']]);
            fputcsv($out, []);
            fputcsv($out, ['Ringkasan Penerimaan']);
            fputcsv($out, ['Metode', 'Bank', 'Jumlah Trx', 'Total (IDR)']);
            foreach ($data['ringkasanPembayaran'] as $r) {
                fputcsv($out, [$r->payment_method, $r->bank_name, $r->trx_count, $r->total_amount]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /** EXPORT EXCEL */
    public function exportExcel()
    {
        $this->validate();
        $data = $this->getReportData();
        $date = $this->date;

        return Excel::download(new \App\Exports\DailyReportExport($date, $data), "laporan_kas_harian_{$date}.xlsx");
    }

    /** EXPORT PDF */
    public function exportPdf()
    {
        $this->validate();
        $data = $this->getReportData();
        $date = $this->date;

        $pdf = Pdf::loadView('exports.daily-report-pdf', [
            'date'                => $date,
            'omzet'               => $data['omzet'],
            'pengeluaran'         => $data['pengeluaran'],
            'incomeBersih'        => $data['incomeBersih'],
            'ringkasanPembayaran' => $data['ringkasanPembayaran'],
            'transaksi'           => $data['transaksi'],
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "laporan_kas_harian_{$date}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

