<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\Expense\Entities\Expense;
use App\Models\User;

class DailyReport extends Component
{
    public string $date;
    public ?int $cashierId = null;
    public ?string $paymentMethod = null;
    public ?string $bankName = null;

    public $cashiers = [];
    public array $methodOptions = ['Cash', 'Credit Card', 'Bank Transfer', 'Cheque', 'Other'];

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

    public function render()
    {
        $this->validate();

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

        return view('livewire.reports.daily-report', [
            'omzet'               => $omzet,
            'pengeluaran'         => $pengeluaran,
            'incomeBersih'        => $incomeBersih,
            'ringkasanPembayaran' => $ringkasanPembayaran,
            'transaksi'           => $transaksi,
        ]);
    }

    /** EXPORT CSV: dipanggil dari tombol di view */
    public function exportCsv()
    {
        $this->validate();

        $date = $this->date;

        $omzet = (int) Sale::query()
            ->completed()
            ->whereDate('date', $date)
            ->when($this->cashierId, fn($q) => $q->where('user_id', $this->cashierId))
            ->sum('total_amount');

        $pengeluaran = (int) Expense::query()
            ->whereDate('date', $date)
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->bankName, fn($q) => $q->where('bank_name', $this->bankName))
            ->sum('amount');

        $incomeBersih = $omzet - $pengeluaran;

        $ringkasan = SalePayment::query()
            ->whereDate('date', $date)
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->bankName, fn($q) => $q->where('bank_name', $this->bankName))
            ->selectRaw('payment_method, COALESCE(bank_name, "-") AS bank_name, COUNT(*) as trx_count, SUM(amount) as total_amount')
            ->groupBy('payment_method', 'bank_name')
            ->orderBy('payment_method')
            ->orderBy('bank_name')
            ->get();

        $filename = "daily_report_{$date}.csv";

        return response()->streamDownload(function () use ($date, $omzet, $pengeluaran, $incomeBersih, $ringkasan) {
            $out = fopen('php://output', 'w');
            // Header
            fputcsv($out, ['Laporan Kas Harian']);
            fputcsv($out, ['Tanggal', $date]);
            fputcsv($out, []);
            // KPI
            fputcsv($out, ['KPI', 'Nominal (IDR)']);
            fputcsv($out, ['Omzet', $omzet]);
            fputcsv($out, ['Pengeluaran', $pengeluaran]);
            fputcsv($out, ['Income Bersih', $incomeBersih]);
            fputcsv($out, []);
            // Ringkasan
            fputcsv($out, ['Ringkasan Penerimaan']);
            fputcsv($out, ['Metode', 'Bank', 'Jumlah Trx', 'Total (IDR)']);
            foreach ($ringkasan as $r) {
                fputcsv($out, [$r->payment_method, $r->bank_name, $r->trx_count, $r->total_amount]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
