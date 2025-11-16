<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $purchase->reference }} - Pembelian Produk Bekas</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* ========== Header ========== */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4834DF;
        }

        .header h1 {
            font-size: 24px;
            color: #4834DF;
            margin-bottom: 5px;
        }

        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .header .company-info {
            font-size: 11px;
            color: #666;
        }

        /* ========== Invoice Title ========== */
        .invoice-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .invoice-title h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
        }

        .invoice-title .reference {
            font-size: 14px;
            color: #4834DF;
            font-weight: bold;
        }

        /* ========== Info Boxes ========== */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info-box:first-child {
            margin-right: 15px;
        }

        .info-box h3 {
            font-size: 12px;
            color: #4834DF;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .info-box p {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .info-box strong {
            display: inline-block;
            width: 120px;
            color: #666;
        }

        /* ========== Table ========== */
        .table-wrapper {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #4834DF;
            color: white;
        }

        table thead th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }

        table thead th.text-right {
            text-align: right;
        }

        table thead th.text-center {
            text-align: center;
        }

        table tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }

        table tbody td.text-right {
            text-align: right;
        }

        table tbody td.text-center {
            text-align: center;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* ========== Summary Box ========== */
        .summary-box {
            float: right;
            width: 350px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .summary-row.total {
            border-top: 2px solid #4834DF;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .summary-row.total .value {
            color: #4834DF;
        }

        .summary-row.due {
            color: #e55353;
            font-weight: bold;
        }

        .summary-row.paid {
            color: #2eb85c;
            font-weight: bold;
        }

        /* ========== Payment Status Badge ========== */
        .payment-status {
            clear: both;
            text-align: center;
            padding: 15px;
            background: #d4edda;
            border: 2px solid #2eb85c;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .payment-status.unpaid {
            background: #fff3cd;
            border-color: #f9b115;
        }

        .payment-status h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .payment-status.unpaid h3 {
            color: #856404;
        }

        .payment-status h3 {
            color: #155724;
        }

        /* ========== Notes ========== */
        .notes {
            clear: both;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #4834DF;
            border-radius: 5px;
        }

        .notes h4 {
            font-size: 12px;
            margin-bottom: 8px;
            color: #4834DF;
            text-transform: uppercase;
        }

        .notes p {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }

        /* ========== Footer ========== */
        .footer {
            clear: both;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            margin-top: 30px;
            font-size: 10px;
            color: #999;
        }

        /* ========== Print Styles ========== */
        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 1cm;
            }
        }

        /* ========== Print Button ========== */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #4834DF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.3);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #3a28c4;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(72, 52, 223, 0.4);
        }
    </style>
</head>

<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print / PDF
    </button>

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-name">{{ settings()->company_name ?? 'TOKO BAN & VELG' }}</div>
            <h1>NOTA PEMBELIAN PRODUK BEKAS</h1>
            <div class="company-info">
                {{ settings()->company_address ?? 'Alamat Toko' }}<br>
                Telp: {{ settings()->company_phone ?? '-' }} | Email: {{ settings()->company_email ?? '-' }}
            </div>
        </div>

        {{-- Invoice Title --}}
        <div class="invoice-title">
            <h2>Bukti Pembelian Produk Bekas</h2>
            <div class="reference">{{ $purchase->reference }}</div>
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            <div class="info-box">
                <h3>📋 Informasi Pembelian</h3>
                <p><strong>Tanggal:</strong> {{ $purchase->date->format('d F Y') }}</p>
                <p><strong>Reference:</strong> {{ $purchase->reference }}</p>
                <p><strong>Status:</strong> {{ $purchase->status }}</p>
            </div>

            <div class="info-box">
                <h3>👤 Informasi Customer</h3>
                <p><strong>Nama:</strong> {{ $purchase->customer_name }}</p>
                @if ($purchase->customer_phone)
                    <p><strong>No. HP:</strong> {{ $purchase->customer_phone }}</p>
                @endif
                <p><strong>Metode Bayar:</strong> {{ $purchase->payment_method }}</p>
                @if ($purchase->payment_method == 'Transfer' && $purchase->bank_name)
                    <p><strong>Bank:</strong> {{ $purchase->bank_name }}</p>
                @endif
            </div>
        </div>

        {{-- Products Table --}}
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="35%">Produk</th>
                        <th width="20%">Kode</th>
                        <th width="20%">Kondisi</th>
                        <th width="20%" class="text-right">Harga Beli</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->purchaseSecondDetails as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><strong>{{ $detail->product_name }}</strong></td>
                            <td>{{ $detail->product_code }}</td>
                            <td>{{ $detail->condition_notes ?: '-' }}</td>
                            <td class="text-right">{{ rupiah($detail->unit_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary Box --}}
        <div class="summary-box">
            <div class="summary-row total">
                <span>Total Pembelian:</span>
                <span class="value">{{ rupiah($purchase->total_amount) }}</span>
            </div>

            <div class="summary-row paid">
                <span>Terbayar:</span>
                <span class="value">{{ rupiah($purchase->paid_amount) }}</span>
            </div>

            <div class="summary-row due">
                <span>Sisa Hutang:</span>
                <span class="value">{{ rupiah($purchase->due_amount) }}</span>
            </div>
        </div>

        {{-- Payment Status --}}
        <div class="payment-status {{ $purchase->payment_status == 'Lunas' ? '' : 'unpaid' }}">
            <h3>
                @if ($purchase->payment_status == 'Lunas')
                    ✅ STATUS: LUNAS
                @else
                    ⚠️ STATUS: BELUM LUNAS
                @endif
            </h3>
        </div>

        {{-- Notes --}}
        @if ($purchase->note)
            <div class="notes">
                <h4>📝 Catatan</h4>
                <p>{{ $purchase->note }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
            <p>Dokumen ini adalah bukti sah pembelian produk bekas</p>
            <p>&copy; {{ date('Y') }} {{ settings()->company_name ?? 'TOKO BAN & VELG' }} - All Rights Reserved</p>
        </div>
    </div>

    <script>
        // Auto-focus on print dialog when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>

</html>
