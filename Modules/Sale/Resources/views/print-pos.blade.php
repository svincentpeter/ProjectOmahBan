<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan {{ $sale->reference }}</title>
    <style>
        /* Menggunakan font sistem yang umum untuk menghindari masalah koneksi & membuat lebih cepat */
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 10px; /* Ukuran font diperkecil agar lebih ringkas */
            line-height: 1.5;
            color: #000;
        }
        body {
            margin: 0;
            padding: 5px;
        }
        .receipt-container {
            max-width: 320px; /* Sedikit lebih kecil untuk printer thermal */
            margin: 0 auto;
            background: #fff;
            padding: 10px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .header p {
            font-size: 9px;
            margin: 2px 0;
        }

        /* Info Section - Diubah menggunakan table agar rapi */
        .invoice-info-table {
            width: 100%;
            margin-bottom: 10px;
            border-top: 1px dashed #555;
            border-bottom: 1px dashed #555;
            padding: 5px 0;
        }
        .invoice-info-table td {
            font-size: 9px;
            padding: 1px 0;
        }
        .invoice-info-table .label {
            color: #333;
        }
        .invoice-info-table .value {
            text-align: right;
            font-weight: bold;
        }


        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table thead th {
            text-align: left;
            padding-bottom: 5px;
            border-bottom: 1px solid #555;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table .text-right { text-align: right; }
        .items-table tbody td {
            padding: 6px 0;
            border-bottom: 1px dashed #ccc;
        }
        .item-name {
            font-weight: bold;
        }
        .item-details {
            font-size: 9px;
            color: #333;
        }

        /* Summary Section */
        .summary-table {
            width: 100%;
            margin-top: 10px;
        }
        .summary-table td {
            padding: 2px 0;
            font-size: 11px;
        }
        .summary-table .label {
            font-weight: normal;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
        }
        .summary-table .grand-total .label,
        .summary-table .grand-total .value {
            font-size: 13px;
            font-weight: bold;
            padding-top: 5px;
            border-top: 1px solid #555;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #555;
        }
        .footer .barcode {
            margin-bottom: 8px;
        }
        .footer p {
            margin: 0;
            font-size: 9px;
            color: #333;
        }

        @media print {
            body { background-color: #fff; padding: 0; }
            .receipt-container { box-shadow: none; border-radius: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <header class="header">
        <h2>{{ settings()->company_name }}</h2>
        <p>{{ settings()->company_address }}</p>
        <p>Email: {{ settings()->company_email }} | Telp: {{ settings()->company_phone }}</p>
    </header>

    <table class="invoice-info-table">
        <tr>
            <td class="label">No. Nota</td>
            <td class="value">{{ $sale->reference }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d/m/y, H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Kasir</td>
            <td class="value">{{ $sale->user->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleDetails as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->item_name ?? ($item->product->product_name ?? 'Item tidak diketahui') }}</div>
                    <div class="item-details">
                        {{ $item->quantity }} x {{ format_currency($item->price) }}
                    </div>
                </td>
                <td class="text-right" style="vertical-align: middle;">{{ format_currency($item->sub_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tbody>
            @if((int) $sale->discount_amount > 0)
            <tr>
                <td class="label">Diskon ({{ $sale->discount_percentage }}%)</td>
                <td class="value">-{{ format_currency($sale->discount_amount) }}</td>
            </tr>
            @endif
            @if((int) $sale->tax_amount > 0)
            <tr>
                <td class="label">Pajak ({{ $sale->tax_percentage }}%)</td>
                <td class="value">{{ format_currency($sale->tax_amount) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td class="label">TOTAL</td>
                <td class="value">{{ format_currency($sale->total_amount) }}</td>
            </tr>
            <tr>
                <td class="label">Bayar ({{ $sale->payment_method }})</td>
                <td class="value">{{ format_currency($sale->paid_amount) }}</td>
            </tr>
            <tr>
                <td class="label">Kembali</td>
                <td class="value">{{ format_currency(max(0, $sale->paid_amount - $sale->total_amount)) }}</td>
            </tr>
        </tbody>
    </table>

    <footer class="footer">
        <div class="barcode">
            {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeSVG($sale->reference, 'C128', 1, 30, 'black', false) !!}
        </div>
        <p>Terima kasih telah berbelanja!</p>
    </footer>
</div>

</body>
</html>