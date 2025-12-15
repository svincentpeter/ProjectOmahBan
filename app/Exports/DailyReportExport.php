<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected string $date;
    protected array $data;

    public function __construct(string $date, array $data)
    {
        $this->date = $date;
        $this->data = $data;
    }

    public function headings(): array
    {
        return ['Metode Pembayaran', 'Bank', 'Jumlah Transaksi', 'Total Nominal (Rp)'];
    }

    public function array(): array
    {
        $rows = [];
        
        // Summary row
        $rows[] = ['LAPORAN KAS HARIAN', '', '', ''];
        $rows[] = ['Tanggal: ' . $this->date, '', '', ''];
        $rows[] = ['', '', '', ''];
        
        // KPI Summary
        $rows[] = ['RINGKASAN', '', '', ''];
        $rows[] = ['Omzet', '', '', number_format($this->data['omzet'], 0, ',', '.')];
        $rows[] = ['Pengeluaran', '', '', number_format($this->data['pengeluaran'], 0, ',', '.')];
        $rows[] = ['Income Bersih', '', '', number_format($this->data['incomeBersih'], 0, ',', '.')];
        $rows[] = ['', '', '', ''];
        
        // Payment breakdown header
        $rows[] = ['RINCIAN PER METODE PEMBAYARAN', '', '', ''];
        
        foreach ($this->data['ringkasanPembayaran'] as $r) {
            $rows[] = [
                $r->payment_method,
                $r->bank_name,
                $r->trx_count,
                number_format($r->total_amount, 0, ',', '.'),
            ];
        }
        
        // Total
        $totalTrx = $this->data['ringkasanPembayaran']->sum('trx_count');
        $totalAmount = $this->data['ringkasanPembayaran']->sum('total_amount');
        $rows[] = ['TOTAL', '', $totalTrx, number_format($totalAmount, 0, ',', '.')];
        
        return $rows;
    }

    public function title(): string
    {
        return 'Laporan Kas ' . $this->date;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 18,
            'D' => 20,
        ];
    }
}
