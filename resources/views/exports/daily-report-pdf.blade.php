<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Harian - {{ $date }}</title>
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
            color: #1e40af;
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
            width: 33.33%;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        .summary-value.income {
            color: #16a34a;
        }
        .summary-value.expense {
            color: #dc2626;
        }
        .summary-value.net {
            color: #2563eb;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        tfoot td {
            font-weight: bold;
            background: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KAS HARIAN</h1>
        <p>Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Omzet</div>
                <div class="summary-value income">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-value expense">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Income Bersih</div>
                <div class="summary-value net">Rp {{ number_format($incomeBersih, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Rincian per Metode Pembayaran</div>
    <table>
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th>Bank</th>
                <th class="text-center">Jumlah Trx</th>
                <th class="text-right">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ringkasanPembayaran as $row)
            <tr>
                <td>{{ $row->payment_method }}</td>
                <td>{{ $row->bank_name ?: '-' }}</td>
                <td class="text-center">{{ $row->trx_count }}</td>
                <td class="text-right">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($ringkasanPembayaran) > 0)
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="text-center">{{ $ringkasanPembayaran->sum('trx_count') }}</td>
                <td class="text-right">Rp {{ number_format($ringkasanPembayaran->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($transaksi) > 0)
    <div class="section-title">Detail Transaksi</div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Ref No.</th>
                <th>Kasir</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $s)
            <tr>
                <td>{{ \Carbon\Carbon::parse($s->date)->format('H:i') }}</td>
                <td>#{{ $s->reference }}</td>
                <td>{{ $s->user->name ?? 'System' }}</td>
                <td class="text-right">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d M Y H:i') }} | Omah Ban - Sistem Manajemen Toko
    </div>
</body>
</html>
