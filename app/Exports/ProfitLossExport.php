<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProfitLossExport implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected string $startDate;
    protected string $endDate;
    protected int $revenue;
    protected int $cogs;
    protected int $grossProfit;
    protected int $expenses;
    protected int $netProfit;

    public function __construct(
        string $startDate,
        string $endDate,
        int $revenue,
        int $cogs,
        int $grossProfit,
        int $expenses,
        int $netProfit
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->revenue = $revenue;
        $this->cogs = $cogs;
        $this->grossProfit = $grossProfit;
        $this->expenses = $expenses;
        $this->netProfit = $netProfit;
    }

    public function array(): array
    {
        $rows = [];
        
        // Rows 1-3: Header Info
        $rows[] = ['LAPORAN LABA/RUGI'];
        $rows[] = ['Periode: ' . $this->startDate . ' s/d ' . $this->endDate];
        $rows[] = ['']; // Spacer
        
        // Row 4: Table Header
        $rows[] = ['KOMPONEN', 'NOMINAL (IDR)'];
        
        // Row 5: Revenue
        $rows[] = ['Pendapatan (Revenue)', $this->revenue];
        
        // Row 6: COGS (Negative for display preference in accounting, or handled by format)
        // Usually we export positive number but display in brackets if expense. 
        // Let's keep consistency with controller logic: expenses are positive integers, profit = rev - exp.
        // For standard P&L presentation, we usually show expenses in parens.
        $rows[] = ['Harga Pokok Penjualan (HPP)', $this->cogs * -1];
        
        // Row 7: Spacer
        $rows[] = ['', ''];
        
        // Row 8: Gross Profit
        $rows[] = ['LABA KOTOR', $this->grossProfit];
        
        // Row 9: Spacer
        $rows[] = ['', ''];
        
        // Row 10: Expenses
        $rows[] = ['Beban Operasional', $this->expenses * -1];
        
        // Row 11: Spacer
        $rows[] = ['', ''];
        
        // Row 12: Net Profit
        $rows[] = ['LABA BERSIH', $this->netProfit];
        
        // Margin calculation logic
        $grossMargin = $this->revenue > 0 ? round(($this->grossProfit / $this->revenue) * 100, 1) : 0;
        $netMargin = $this->revenue > 0 ? round(($this->netProfit / $this->revenue) * 100, 1) : 0;
        
        // Row 13: Spacer
        $rows[] = ['', ''];
        
        // Row 14: Ratios Header
        $rows[] = ['ANALIAS RASIO'];
        
        // Row 15-16: Ratios
        // We pass string for percentages to avoid auto-formatting issues, or use formatting
        $rows[] = ['Gross Profit Margin', $grossMargin / 100]; // Export as decimal like 0.22
        $rows[] = ['Net Profit Margin', $netMargin / 100];
        
        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            // Format Column B (Nominal) as Accounting
            'B5:B12' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)', 
            // Format Percentages
            'B15:B16' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function title(): string
    {
        return 'Laba Rugi';
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Set Title Style
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '2563EB']], // Blue Header
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // 2. Table Header Style (Row 4)
        $sheet->getStyle('A4:B4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '4B5563']], // Dark Gray
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // 3. Highlight Total Rows (Gross Profit & Net Profit)
        // Row 8 = Gross Profit, Row 12 = Net Profit
        $sheet->getStyle('A8:B8')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'DBEAFE']], // Light Blue
            'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('A12:B12')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D1FAE5']], // Light Green
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE], 'bottom' => ['borderStyle' => Border::BORDER_DOUBLE]],
        ]);

        // 4. Ratio Section Header
        $sheet->mergeCells('A14:B14');
        $sheet->getStyle('A14')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F3F4F6']],
        ]);

        // general border for data
        $sheet->getStyle('A4:B12')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 25,
        ];
    }
}
