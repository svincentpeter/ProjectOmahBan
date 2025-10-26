<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penyesuaian Stok - {{ $adjustment->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        
        .info-table td:nth-child(2) {
            width: 20px;
            color: #999;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .products-table thead {
            background: #34495e;
            color: white;
        }
        
        .products-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #2c3e50;
        }
        
        .products-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }
        
        .products-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Product Code Badge Style */
        .product-code {
            background: #f5f5f5;
            padding: 3px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            display: inline-block;
            border: 1px solid #ddd;
        }
        
        /* Type Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-add {
            background: #27ae60;
            color: white;
        }
        
        .badge-sub {
            background: #e74c3c;
            color: white;
        }
        
        .badge-tipe {
            background: #3498db;
            color: white;
        }
        
        .summary {
            margin-top: 30px;
            padding: 15px;
            background: #ecf0f1;
            border-left: 4px solid #3498db;
        }
        
        .summary h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .summary-row {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .summary-item {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        
        .summary-item:first-child {
            margin-right: 10px;
        }
        
        .summary-item .label {
            font-size: 9px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }
        
        .signature-table {
            width: 100%;
        }
        
        .signature-box {
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        
        .print-info {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        
        .strong-text {
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Penyesuaian Stok</h1>
        <h2>{{ config('app.name', 'Sistem Inventory') }}</h2>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Nomor Referensi</td>
                <td>:</td>
                <td><strong style="font-size: 13px; color: #2c3e50;">{{ $adjustment->reference }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Penyesuaian</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($adjustment->date)->isoFormat('dddd, D MMMM Y') }}</td>
            </tr>
            <tr>
                <td>Dibuat Pada</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($adjustment->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
            </tr>
            @if($adjustment->note)
            <tr>
                <td>Catatan</td>
                <td>:</td>
                <td>{{ $adjustment->note }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Products Table --}}
    <h3 style="margin-bottom: 15px; color: #2c3e50; font-size: 14px;">Detail Produk</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="25%">NAMA PRODUK</th>
                <th width="15%">KODE</th>
                <th width="15%">KATEGORI</th>
                <th width="10%" class="text-center">JUMLAH</th>
                <th width="15%" class="text-center">TIPE</th>
                <th width="15%" class="text-center">STOK AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAdd = 0;
                $totalSub = 0;
            @endphp
            @foreach($adjustment->adjustedProducts as $index => $item)
            @php
                if ($item->type == 'add') {
                    $totalAdd += $item->quantity;
                } else {
                    $totalSub += $item->quantity;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item->product->product_name }}</strong></td>
                <td>
                    <span class="product-code">{{ $item->product->product_code }}</span>
                </td>
                <td>
                    {{ $item->product->category->category_name ?? '-' }}
                </td>
                <td class="text-center">
                    <span class="strong-text">{{ $item->quantity }} {{ $item->product->product_unit }}</span>
                </td>
                <td class="text-center">
                    @if($item->type == 'add')
                        <span class="badge badge-add">+ TAMBAH</span>
                    @else
                        <span class="badge badge-sub">- KURANG</span>
                    @endif
                </td>
                <td class="text-center">
                    <strong>{{ $item->product->product_quantity }} {{ $item->product->product_unit }}</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <h3>RINGKASAN PENYESUAIAN</h3>
        <div class="summary-row">
            <div class="summary-item">
                <div class="label">Total Penambahan</div>
                <div class="value" style="color: #27ae60;">
                    {{ $adjustment->adjustedProducts->where('type', 'add')->count() }} Produk
                </div>
            </div>
            <div class="summary-item">
                <div class="label">Total Pengurangan</div>
                <div class="value" style="color: #e74c3c;">
                    {{ $adjustment->adjustedProducts->where('type', 'sub')->count() }} Produk
                </div>
            </div>
        </div>
    </div>

    {{-- Footer / Signature --}}
    <div class="footer">
        <table class="signature-table">
            <tr>
                <td class="signature-box" style="text-align: left;">
                    <div>Dibuat Oleh,</div>
                    <div class="signature-line"></div>
                    <div><strong>(____________________)</strong></div>
                </td>
                <td class="signature-box" style="text-align: right;">
                    <div>Disetujui Oleh,</div>
                    <div class="signature-line"></div>
                    <div><strong>(____________________)</strong></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Print Info --}}
    <div class="print-info">
        Dokumen ini dicetak secara otomatis pada {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y - HH:mm:ss') }} WIB
    </div>
</body>
</html>
