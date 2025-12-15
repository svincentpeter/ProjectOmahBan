<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $rows;
    protected string $fromDate;
    protected string $toDate;

    public function __construct($rows, string $fromDate, string $toDate)
    {
        $this->rows = $rows;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function headings(): array
    {
        return ['Nama Kasir', 'Jumlah Transaksi', 'Omset (Rp)', 'Total HPP (Rp)', 'Total Profit (Rp)', 'Margin (%)'];
    }

    public function array(): array
    {
        $exportData = [];
        
        // Header info
        $exportData[] = ['LAPORAN KINERJA KASIR', '', '', '', '', ''];
        $exportData[] = ['Periode: ' . $this->fromDate . ' s/d ' . $this->toDate, '', '', '', '', ''];
        $exportData[] = ['', '', '', '', '', ''];
        $exportData[] = ['KINERJA INDIVIDUAL', '', '', '', '', ''];

        // Table Header (will be overwritten by headings() but good for structure)
        // We skip this as WithHeadings handles the actual header row for the data table
        
        foreach ($this->rows as $r) {
            $p = (int) ($r->total_profit ?? 0);
            $m = ($r->omset ?? 0) > 0 ? round(($p / max($r->omset, 1)) * 100, 1) : 0;
            
            $exportData[] = [
                optional($r->user)->name ?? 'Unknown',
                $r->trx_count,
                (int) $r->omset,
                (int) $r->total_hpp,
                $p,
                $m . '%'
            ];
        }
        
        // Total
        $totTrx = $this->rows->sum('trx_count');
        $totOmset = $this->rows->sum('omset');
        $totHpp = $this->rows->sum('total_hpp');
        $totProfit = $this->rows->sum('total_profit');
        $avgMargin = $totOmset > 0 ? round(($totProfit / max($totOmset, 1)) * 100, 1) : 0;
        
        $exportData[] = ['', '', '', '', '', ''];
        $exportData[] = [
            'TOTAL', 
            $totTrx, 
            (int) $totOmset, 
            (int) $totHpp, 
            (int) $totProfit, 
            $avgMargin . '%'
        ];
        
        return $exportData;
    }

    public function title(): string
    {
        return 'Kinerja Kasir';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 18,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 15,
        ];
    }
}
