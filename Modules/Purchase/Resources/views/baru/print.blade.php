<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Purchase - {{ $purchase->reference }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-left {
            padding-right: 20px;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
            width: 60%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }

        table tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button no-print">
        <i class="bi bi-printer"></i> Print
    </button>

    <div class="container">
        {{-- HEADER --}}
        <div class="header">
            <h1>{{ settings()->company_name ?? 'TOKO BAN' }}</h1>
            <p>{{ settings()->company_address ?? 'Alamat Toko' }}</p>
            <p>Telp: {{ settings()->company_phone ?? '-' }} | Email: {{ settings()->company_email ?? '-' }}</p>
        </div>

        {{-- DOCUMENT TITLE --}}
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 18px;">BUKTI PEMBELIAN</h2>
            <p style="margin: 5px 0;">{{ $purchase->reference }}</p>
        </div>

        {{-- INFO SECTION --}}
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Informasi Pembelian</h3>
                    <div class="info-row">
                        <div class="info-label">Tanggal:</div>
                        <div class="info-value">{{ $purchase->date->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Reference:</div>
                        <div class="info-value">{{ $purchase->reference }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Supplier:</div>
                        <div class="info-value">{{ $purchase->supplier_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            @if ($purchase->status == 'Completed')
                                <span class="badge badge-info">Completed</span>
                            @else
                                <span class="badge badge-secondary">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Metode Bayar:</div>
                        <div class="info-value">{{ $purchase->payment_method }}</div>
                    </div>
                    @if ($purchase->payment_method == 'Transfer')
                        <div class="info-row">
                            <div class="info-label">Bank:</div>
                            <div class="info-value">{{ $purchase->bank_name ?? '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="info-right">
                <div class="info-box">
                    <h3>Ringkasan Pembayaran</h3>
                    <div class="info-row">
                        <div class="info-label">Total:</div>
                        <div class="info-value">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Terbayar:</div>
                        <div class="info-value">Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Sisa:</div>
                        <div class="info-value">Rp {{ number_format($purchase->due_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status Bayar:</div>
                        <div class="info-value">
                            @if ($purchase->payment_status == 'Lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-warning">Belum Lunas</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCTS TABLE --}}
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th>Nama Produk</th>
                    <th width="15%">Kode</th>
                    <th width="8%" class="text-center">Qty</th>
                    <th width="18%" class="text-end">Harga Satuan</th>
                    <th width="20%" class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->purchaseDetails as $detail)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->product_code }}</td>
                        <td class="text-center">{{ $detail->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end">TOTAL PEMBELIAN:</td>
                    <td class="text-end">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- NOTES --}}
        @if ($purchase->note)
            <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                <strong>Catatan:</strong><br>
                {{ $purchase->note }}
            </div>
        @endif

        {{-- FOOTER --}}
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
            <p>Diinput oleh: {{ $purchase->user->name ?? 'System' }}</p>
            <p>{{ settings()->company_name ?? 'Toko Ban' }} © {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        // Auto print saat load (opsional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>
