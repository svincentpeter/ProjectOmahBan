<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #065f46;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .label {
            font-weight: 500;
            color: #374151;
        }
        .value {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .indent {
            padding-left: 30px;
        }
        .subtotal-row td {
            background: #f0fdf4;
            border-top: 2px solid #10b981;
            border-bottom: 2px solid #10b981;
        }
        .subtotal-row .label {
            font-weight: bold;
            color: #065f46;
        }
        .subtotal-row .value {
            font-weight: bold;
            color: #059669;
            font-size: 14px;
        }
        .total-row td {
            background: #065f46;
            color: white;
        }
        .total-row .label {
            font-weight: bold;
            color: white;
            font-size: 14px;
        }
        .total-row .value {
            font-weight: bold;
            color: white;
            font-size: 16px;
        }
        .negative {
            color: #dc2626;
        }
        .section-title {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 15px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .margin-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .margin-box h4 {
            margin: 0 0 10px;
            font-size: 12px;
            color: #374151;
        }
        .margin-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
    </div>

    <table>
        <tr>
            <td colspan="2" class="section-title">Pendapatan</td>
        </tr>
        <tr>
            <td class="label indent">Pendapatan Penjualan</td>
            <td class="value">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="section-title">Harga Pokok Penjualan</td>
        </tr>
        <tr>
            <td class="label indent">HPP / COGS</td>
            <td class="value negative">(Rp {{ number_format($cogs, 0, ',', '.') }})</td>
        </tr>
        <tr class="subtotal-row">
            <td class="label">LABA KOTOR</td>
            <td class="value">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="section-title">Beban Operasional</td>
        </tr>
        <tr>
            <td class="label indent">Total Beban Operasional</td>
            <td class="value negative">(Rp {{ number_format($expenses, 0, ',', '.') }})</td>
        </tr>
        <tr class="total-row">
            <td class="label">LABA BERSIH SEBELUM PAJAK</td>
            <td class="value">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
        </tr>
    </table>

    @php
        $grossMargin = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0;
        $netMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;
    @endphp

    <div class="margin-box">
        <h4>Rasio Profitabilitas</h4>
        <table style="margin-bottom: 0;">
            <tr>
                <td class="label">Gross Profit Margin</td>
                <td class="value">{{ $grossMargin }}%</td>
            </tr>
            <tr>
                <td class="label">Net Profit Margin</td>
                <td class="value">{{ $netMargin }}%</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d M Y H:i') }} | Omah Ban - Sistem Manajemen Toko
    </div>
</body>
</html>
