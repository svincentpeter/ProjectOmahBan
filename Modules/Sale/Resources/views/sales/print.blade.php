<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $sale->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }

        .invoice-info {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }

        .invoice-info div {
            width: 48%;
        }

        .invoice-info table {
            width: 100%;
            font-size: 11pt;
        }

        .invoice-info td {
            padding: 3px 0;
        }

        .invoice-info td:first-child {
            width: 40%;
            font-weight: bold;
        }

        /* 👇 HAPUS WATERMARK STYLE */
        /* TIDAK ADA LAGI .watermark CSS */

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table td.right {
            text-align: right;
        }

        .total-section {
            width: 50%;
            margin-left: auto;
            margin-top: 10px;
        }

        .total-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-section td {
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }

        .total-section .grand-total {
            font-weight: bold;
            font-size: 14pt;
            border-top: 2px solid #000;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 60px;
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10pt;
            color: #666;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    @php($customer = $sale->customer_info)
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ settings()->company_name ?? 'OMAH BAN' }}</h1>
            <p>{{ settings()->company_address ?? 'Jl. Contoh No. 123, Kota' }}</p>
            <p>Telp: {{ settings()->company_phone ?? '0812-3456-7890' }} | Email: {{ settings()->company_email ?? 'info@omahban.com' }}</p>
        </div>

        {{-- Invoice Info --}}
        <div class="invoice-info">
            <div>
                <table>
                    <tr>
                        <td>Invoice No</td>
                        <td>: <strong>{{ $sale->reference }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($sale->date)->format('d F Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Kasir</td>
                        <td>: {{ data_get($sale, 'user.name') ?? 'Admin' }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table>
                    <tr>
                        <td>Metode Bayar</td>
                        <td>: {{ $sale->payment_method ?? 'Tunai' }}</td>
                    </tr>
                    @if(!empty($sale->bank_name))
                    <tr>
                        <td>Bank</td>
                        <td>: {{ $sale->bank_name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Customer Info --}}
        <div style="margin-bottom: 20px;">
            <table style="width: 100%; border: 1px solid #ddd; padding: 10px;">
                <tr>
                    <td style="width: 30%; font-weight: bold;">Customer:</td>
                    <td>{{ $customer['name'] }}</td>
                </tr>
                @if($customer['email'] && $customer['email'] !== '-')
                    <tr>
                        <td style="font-weight: bold;">Email:</td>
                        <td>{{ $customer['email'] }}</td>
                    </tr>
                @endif
                @if($customer['phone'] && $customer['phone'] !== '-')
                    <tr>
                        <td style="font-weight: bold;">Telepon:</td>
                        <td>{{ $customer['phone'] }}</td>
                    </tr>
                @endif
                @if($customer['city'] && $customer['city'] !== '-')
                    <tr>
                        <td style="font-weight: bold;">Kota:</td>
                        <td>{{ $customer['city'] }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- 👇 HAPUS WATERMARK (tidak ada kondisi @if payment_status) --}}
        {{-- TIDAK ADA LAGI <div class="watermark">DRAFT</div> --}}

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Product</th>
                    <th style="width: 15%;">Net Unit Price</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 10%;">Discount</th>
                    <th style="width: 10%;">Tax</th>
                    <th style="width: 15%;">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleDetails as $key => $item)
                    <tr>
                        <td class="center">{{ $key + 1 }}</td>
                        <td>
                            {{ $item->product_name }}
                            @if(!empty($item->product_code))
                                <br><small style="color: #666;">{{ $item->product_code }}</small>
                            @endif
                        </td>
                        <td class="right">{{ format_currency($item->unit_price) }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">{{ format_currency($item->product_discount_amount) }}</td>
                        <td class="right">{{ format_currency($item->product_tax_amount) }}</td>
                        <td class="right">{{ format_currency($item->sub_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Total Section --}}
        <div class="total-section">
            <table>
                @if((int)($sale->discount_amount ?? 0) > 0)
                <tr>
                    <td>Discount ({{ $sale->discount_percentage }}%)</td>
                    <td class="right">{{ format_currency($sale->discount_amount) }}</td>
                </tr>
                @endif
                @if((int)($sale->tax_amount ?? 0) > 0)
                <tr>
                    <td>Tax ({{ $sale->tax_percentage }}%)</td>
                    <td class="right">{{ format_currency($sale->tax_amount) }}</td>
                </tr>
                @endif
                @if((int)($sale->shipping_amount ?? 0) > 0)
                <tr>
                    <td>Shipping</td>
                    <td class="right">{{ format_currency($sale->shipping_amount) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>Grand Total</td>
                    <td class="right">{{ format_currency($sale->total_amount) }}</td>
                </tr>
            </table>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <p>Penjual,</p>
                <div class="line"></div>
                <p style="margin-top: 10px;">(............................)</p>
            </div>
            <div class="signature-box">
                <p>Pembeli,</p>
                <div class="line"></div>
                <p style="margin-top: 10px;">(............................)</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>{{ settings()->company_name ?? 'Omah Ban' }} © {{ date('Y') }}.</p>
            <p>Terima kasih atas kepercayaan Anda!</p>
        </div>
    </div>
</body>
</html>
