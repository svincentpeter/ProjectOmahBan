<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $sale->reference }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
        }
        .page {
            width: 100%;
            padding: 5mm;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .logo-container { text-align: left; }
        .logo { width: 120px; }
        .company-details p {
            margin: 0;
            font-size: 10px;
            line-height: 1.4;
        }
        .customer-info {
            font-size: 11px;
            padding-left: 20px;
        }
        .customer-info td {
            padding: 1px 2px;
        }
        .nota-label {
            border: 1px solid #000;
            padding: 2px 8px;
            font-weight: bold;
            text-align: center;
        }
        .items-table {
            margin-top: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
        }
        .items-table th { font-weight: bold; }
        .items-table .text-left { text-align: left; }
        .items-table .text-right { text-align: right; }
        .items-table .blank-row td {
            height: 80px; /* Ruang kosong untuk item */
            border-bottom-style: dashed;
        }
        .footer-section {
            margin-top: 10px;
        }
        .signatures { width: 60%; }
        .signatures td {
            text-align: center;
            padding-top: 30px; /* Jarak untuk TTD */
        }
        .total-container { width: 40%; vertical-align: bottom; }
        .total-box {
            border: 1px solid #000;
            padding: 6px;
            font-weight: bold;
            font-size: 14px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="page">
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    <div class="logo-container">
                        {{-- Menggunakan logo dari public path --}}
                        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
                    </div>
                    <div class="company-details">
                        <p>JUAL BELI BAN & VELG RACING</p>
                        <p>Jl. Empu Sendok 2A gedawang, Banyumanik Telp. 085 292 290 382</p>
                        <p>Jl. Grafika Raya No. 7 Banyumanik, Semarang</p>
                    </div>
                </td>
                <td style="width: 40%;">
                    <table class="customer-info">
                        <tr>
                            <td colspan="2">Semarang, {{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Kepada Yth.</td>
                            <td>: ................................</td>
                        </tr>
                        <tr>
                            <td>Jalan</td>
                            <td>: ................................</td>
                        </tr>
                        <tr>
                            <td>Telp.</td>
                            <td>: 085 292 290 382</td>
                        </tr>
                        <tr>
                            <td class="nota-label" colspan="2">NO. NOTA: {{ $sale->reference }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%">Qty</th>
                    <th>Ukuran</th>
                    <th>Merk</th>
                    <th>Motif</th>
                    <th>Nomor Seri</th>
                    <th style="width:18%">Harga Satuan</th>
                    <th style="width:18%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleDetails as $item)
                <tr>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-left">{{ $item->product->product_size ?? '-' }}</td>
                    <td class="text-left">{{ $item->product->brand->name ?? '-' }}</td>
                    <td class="text-left">{{ $item->item_name }}</td>
                    <td>{{ $item->product_code ?? '-' }}</td>
                    <td class="text-right">{{ format_currency($item->price) }}</td>
                    <td class="text-right">{{ format_currency($item->sub_total) }}</td>
                </tr>
                @endforeach
                <tr class="blank-row">
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            </tbody>
        </table>

        <table class="footer-section">
            <tr>
                <td class="signatures">
                    <table>
                        <tr>
                            <td style="padding-bottom: 40px;">Diterima Oleh,</td>
                            <td style="padding-bottom: 40px;">Hormat Kami,</td>
                        </tr>
                        <tr>
                            <td>(....................)</td>
                            <td>(....................)</td>
                        </tr>
                    </table>
                </td>
                <td class="total-container">
                    <table style="width:100%;">
                        <tr><td style="text-align: right; font-weight: bold;">TOTAL</td></tr>
                        <tr><td class="total-box">{{ format_currency($sale->total_amount) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>