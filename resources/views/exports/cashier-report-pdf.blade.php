<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Kasir</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1d4ed8;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
        }
        .summary-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            width: 25%;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        tfoot td {
            font-weight: bold;
            background: #eff6ff;
            border-top: 2px solid #bfdbfe;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KINERJA KASIR</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}</p>
    </div>

    @php
        $totTrx = $rows->sum('trx_count');
        $totOmset = $rows->sum('omset');
        $totProfit = $rows->sum('total_profit');
        $totHpp = $rows->sum('total_hpp');
    @endphp

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ number_format($totTrx, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Omset</div>
                <div class="summary-value" style="color: #2563eb;">Rp {{ number_format($totOmset, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Profit</div>
                <div class="summary-value" style="color: #16a34a;">Rp {{ number_format($totProfit, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Avg Margin</div>
                <div class="summary-value">{{ $totOmset > 0 ? round(($totProfit / $totOmset) * 100, 1) : 0 }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Kasir</th>
                <th class="text-center">Transaksi</th>
                <th class="text-right">Omset</th>
                <th class="text-right">HPP</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            @php
                $p = (int) ($r->total_profit ?? 0);
                $m = ($r->omset ?? 0) > 0 ? round(($p / max($r->omset, 1)) * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ optional($r->user)->name ?? 'Unknown' }}</td>
                <td class="text-center">{{ number_format($r->trx_count, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($r->omset, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($r->total_hpp, 0, ',', '.') }}</td>
                <td class="text-right {{ $p >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($p, 0, ',', '.') }}</td>
                <td class="text-right">{{ $m }}%</td>
            </tr>
            @endforeach
        </tbody>
        @if(count($rows) > 0)
        <tfoot>
            <tr>
                <td>TOTAL</td>
                <td class="text-center">{{ number_format($totTrx, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totOmset, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totHpp, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totProfit, 0, ',', '.') }}</td>
                <td class="text-right">-</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d M Y H:i') }} | Omah Ban - Sistem Manajemen Toko
    </div>
</body>
</html>
