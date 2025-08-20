{{-- Nota POS A6 Landscape --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $sale->reference ?? '-' }}</title>
    <style>
        @page { margin: 5mm; size: 148mm 105mm; } /* A6 landscape */
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }
        .sheet { width: 100%; }

        .row { width:100%; }
        .col { display:inline-block; vertical-align:top; }
        .col-left  { width: 60%; }
        .col-right { width: 39%; text-align:left; }

        /* --- BRAND/LOGO --- */
        .logo-container { display:flex; align-items:center; gap:8px; }
        .logo { height: 28px; } /* atur bila perlu */
        .tagline { font-size: 10px; margin-top: 2px; }
        .address { margin-top:6px; line-height: 1.35; font-size: 10px; }

        /* --- META (kanan) --- */
        .meta { font-size: 10px; line-height: 1.45; }
        .meta .nota { font-size: 12px; font-weight: 700; margin-bottom: 4px; }
        .meta .line { margin-bottom: 2px; }
        .meta .dots { display:inline-block; min-width: 120px; border-bottom: 1px dotted #000; }

        /* --- TABEL ITEM --- */
        table.items { width:100%; border-collapse: collapse; margin-top: 6px; }
        table.items th, table.items td { border: 1px solid #000; padding: 3px 4px; }
        table.items th { text-transform: uppercase; text-align: center; font-weight: 700; }
        table.items td { vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* lebar kolom */
        .col-qty    { width: 7%;  }
        .col-name   { width: 33%; }
        .col-merk   { width: 14%; }
        .col-ukuran { width: 12%; }
        .col-year   { width: 10%; }
        .col-price  { width: 12%; }
        .col-total  { width: 12%; }

        /* --- KOTAK TOTAL TERPISAH (diperkecil) --- */
        table.total-box { border-collapse: collapse; float: right; margin-top: 3mm; }
        table.total-box td { border:1px solid #000; padding: 3px 6px; font-size: 10px; }
        table.total-box .label { font-weight: 700; }
        table.total-box .amount { min-width: 28mm; text-align: right; }

        /* --- TANDA TANGAN (diperkecil & digeser ke kiri) --- */
        .sign { clear: both; margin-top: 10mm; width: 70%; }            /* 70% lebar halaman, sehingga tidak sampai ke kanan */
        .sign .area { width: 46%; display:inline-block; vertical-align:top; text-align: center; }
        .sign .label { font-size: 9px; margin-bottom: 6mm; }           /* lebih kecil & jarak vertikal dipendekkan */
        .sign .line { border-top:1px solid #000; width: 60%; margin: 0 auto; height: 1px; }

        /* --- WATERMARK DRAFT --- */
        .watermark {
            position: fixed; left: 0; top: 0; right: 0; bottom: 0;
            text-align: center; padding-top: 25mm;
            font-size: 42mm; font-weight: 700; color: rgba(0,0,0,.08);
            transform: rotate(-20deg);
        }
    </style>
</head>
<body>
    <div class="sheet">
        {{-- HEADER --}}
        <div class="row">
            <div class="col col-left">
                <div class="logo-container">
                    {{-- logo dari public path --}}
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
                    <div>
                        <div class="tagline"><strong>JUAL BELI BAN &amp; VELG RACING</strong></div>
                        <div class="address">
                            Jl. Mpu Sendok 2A Gedawang, Banyumanik • Telp. 085 292 290 392<br>
                            Jl. Grafika Raya No.7, Banyumanik, Semarang
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-right">
                <div class="meta">
                    {{-- No. NOTA di atas blok Semarang/Kepada/Jalan/Telp. --}}
                    <div class="nota">No. NOTA: <span class="dots">{{ $sale->reference ?? '-' }}</span></div>
                    <div class="line">Semarang, <span class="dots">{{ \Carbon\Carbon::parse($sale->date ?? now())->format('d/m/Y') }}</span></div>
                    <div class="line">Kepada Yth. <span class="dots">{{ data_get($sale,'customer.name') ?? data_get($sale,'customer_name') ?? '' }}</span></div>
                    <div class="line">Jalan <span class="dots">{{ data_get($sale,'customer.address') ?? '' }}</span></div>
                    <div class="line">Telp. <span class="dots">{{ data_get($sale,'customer.phone') ?? '' }}</span></div>
                </div>
            </div>
        </div>

        {{-- WATERMARK DRAFT --}}
        @if(($sale->payment_status ?? 'Unpaid') !== 'Paid')
            <div class="watermark">DRAFT</div>
        @endif

        {{-- TABEL ITEM --}}
        <table class="items">
            <thead>
            <tr>
                <th class="col-qty">QTY.</th>
                <th class="col-name">NAMA PRODUK</th>
                <th class="col-merk">MERK</th>
                <th class="col-ukuran">UKURAN</th>
                <th class="col-year">TAHUN</th>
                <th class="col-price">HARGA SATUAN</th>
                <th class="col-total">JUMLAH</th>
            </tr>
            </thead>
            <tbody>
            @php $rowCount = 0; @endphp
            @foreach ($sale->saleDetails as $d)
                @php
                    $qty    = (int) $d->quantity;
                    $name   = $d->product_name ?? $d->item_name ?? '-';
                    $merk   = data_get($d,'product.brand.brand_name')
                              ?? data_get($d,'productable.brand')
                              ?? '-';
                    $ukuran = data_get($d,'product.product_size')
                              ?? data_get($d,'product.size')
                              ?? data_get($d,'productable.size')
                              ?? data_get($d,'product_unit')
                              ?? '-';
                    $tahun  = data_get($d,'product.production_year')
                              ?? data_get($d,'product.manufacture_year')
                              ?? data_get($d,'productable.year')
                              ?? data_get($d,'productable.production_year')
                              ?? '';
                    $price  = (int) $d->price;
                    $total  = $qty * $price;
                    $rowCount++;
                @endphp
                <tr>
                    <td class="text-center">{{ $qty }}</td>
                    <td>{{ $name }}</td>
                    <td>{{ $merk }}</td>
                    <td>{{ $ukuran }}</td>
                    <td class="text-center">{{ $tahun }}</td>
                    <td class="text-right">{{ format_currency($price) }}</td>
                    <td class="text-right">{{ format_currency($total) }}</td>
                </tr>
            @endforeach

            {{-- Baris kosong biar rapi seperti nota fisik --}}
            @for ($i = $rowCount; $i < 8; $i++)
                <tr>
                    <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor
            </tbody>
        </table>

        {{-- KOTAK TOTAL (kecil & di kanan) --}}
        <table class="total-box">
            <tr>
                <td class="label">TOTAL</td>
                <td class="amount">{{ format_currency((int) ($sale->total_amount ?? 0)) }}</td>
            </tr>
        </table>

        {{-- TANDA TANGAN (dikecilkan & digeser ke kiri) --}}
        <div class="sign">
            <div class="area">
                <div class="label">Diterima Oleh,</div>
                <div class="line"></div>
            </div>
            <div class="area">
                <div class="label">Hormat Kami,</div>
                <div class="line"></div>
            </div>
        </div>
    </div>
</body>
</html>
