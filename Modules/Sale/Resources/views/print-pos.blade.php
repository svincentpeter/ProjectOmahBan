{{-- Nota POS A6 Landscape – 1 Halaman, dots & tanda tangan rapi --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $sale->reference ?? '-' }}</title>
    <style>
        @page { margin: 5mm; size: 148mm 105mm; } /* A6 landscape */
        * { box-sizing: border-box; }
        html, body { margin:0; padding:0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10.5px; color:#000; }
        .sheet { width:100%; }

        /* Grid header */
        .row { width:100%; }
        .col { display:inline-block; vertical-align:top; }
        .col-left  { width: 60%; }
        .col-right { width: 39%; }

        /* Logo & alamat */
        .logo { height: 28px; vertical-align:middle; }
        .brand { display:flex; gap:8px; align-items:flex-start; }
        .tagline { font-weight:700; font-size:10px; margin-top:2px; }
        .address { margin-top:6px; line-height:1.35; font-size:10px; }

        /* Blok meta kanan */
        .meta { font-size:10px; line-height:1.5; }
        .meta .nota { font-size:12px; font-weight:700; margin-bottom:4px; }
        .meta table { width:auto; border-collapse:collapse; }
        .meta td { padding:1px 0; white-space:nowrap; }
        .meta .label { padding-right:4px; }
        .meta .colon { padding: 0 4px; }           /* beri jarak setelah ':' */
        .meta .dotsline {
            display:inline-block;
            min-width: 38mm;                        /* lebar garis titik (atur sesuai selera) */
            border-bottom: 1px dotted #000;
            line-height: 10px;
            height: 10px;
            vertical-align: baseline;
        }
        .meta .dotsline.short { min-width: 28mm; }  /* variasi lebih pendek jika perlu */

        /* Tabel item */
        table.items { width:100%; border-collapse:collapse; margin-top:6px; page-break-inside:avoid; }
        table.items th, table.items td { border:1px solid #000; padding:3px 4px; }
        table.items th { text-transform:uppercase; text-align:center; font-weight:700; }
        table.items td { vertical-align:top; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }

        /* Lebar kolom */
        .col-qty    { width: 7%;  }
        .col-name   { width: 33%; }
        .col-merk   { width: 14%; }
        .col-ukuran { width: 12%; }
        .col-year   { width: 10%; }
        .col-price  { width: 12%; }
        .col-total  { width: 12%; }

        /* Kotak total kecil di kanan */
        table.total-box { border-collapse:collapse; float:right; margin-top:3mm; }
        table.total-box td { border:1px solid #000; padding:3px 6px; font-size:10px; }
        table.total-box .label { font-weight:700; }
        table.total-box .amount { min-width:28mm; text-align:right; }

        /* Tanda tangan model "foto 1" */
        .sign { clear:both; margin-top: 9mm; width: 70%; } /* agak ke kiri, tidak mentok kanan */
        .sign .area { width: 46%; display:inline-block; vertical-align:top; text-align:center; }
        .sign .label { font-size:10px; margin-bottom: 8mm; }
        .sign .line {
            width: 65%; margin: 0 auto; height: 0;
            border-top: 1px solid #000;                /* garis lurus, bukan titik-titik */
        }

        /* 👇 HAPUS CSS WATERMARK (tidak dipakai lagi) */
        /* .watermark { ... } */
    </style>
</head>
<body>
<div class="sheet">
    {{-- HEADER --}}
    <div class="row">
        <div class="col col-left">
            <div class="brand">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
                <div>
                    <div class="tagline">JUAL BELI BAN &amp; VELG RACING</div>
                    <div class="address">
                        Jl. Mpu Sendok 2A Gedawang, Banyumanik • Telp. 085 292 290 382<br>
                        Jl. Grafika Raya No.7, Banyumanik, Semarang
                    </div>
                </div>
            </div>
        </div>

        <div class="col col-right">
            <div class="meta">
                {{-- No. NOTA di atas block Semarang/Kepada/Jalan/Telp --}}
                <div class="nota">No. NOTA: <span class="dotsline short">{{ $sale->reference ?? '-' }}</span></div>

                <table>
                    <tr>
                        <td class="label">Semarang</td>
                        <td class="colon">:</td>
                        <td><span class="dotsline short">{{ \Carbon\Carbon::parse($sale->date ?? now())->translatedFormat('d F Y') }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Kepada Yth.</td>
                        <td class="colon">:</td>
                        {{-- 👇 PERBAIKAN: Prioritaskan $sale->customer_name --}}
                        <td><span class="dotsline">{{ $sale->customer_name ?? data_get($sale,'customer.name') ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Jalan</td>
                        <td class="colon">:</td>
                        <td><span class="dotsline">{{ data_get($sale,'customer.address') ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Telp.</td>
                        <td class="colon">:</td>
                        <td><span class="dotsline short">{{ data_get($sale,'customer.phone') ?? '085 292 290 382' }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- 👇 HAPUS WATERMARK (tidak ada kondisi @if payment_status) --}}
    {{-- TIDAK ADA LAGI <div class="watermark">DRAFT</div> --}}

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
        @php
            // Batas maksimal baris agar pasti 1 halaman A6
            $maxRows  = 8;
            $details  = ($sale->saleDetails ?? collect());
            $slice    = $details->take($maxRows);
            $rowCount = 0;
        @endphp

        @foreach ($slice as $item)
            @php
                $qty   = (int) $item->quantity;
                $price = (int) $item->price;
                $total = (int) ($item->sub_total ?? ($qty * $price));

                $name  = $item->product_name ?? $item->item_name ?? '-';
                $merk  = '-'; $ukuran='-'; $tahun='-';

                $src = $item->source_type ?? data_get($item,'options.source_type');
                if ($src === 'new' && $item->product) {
                    $merk   = data_get($item,'product.brand.name') ?? data_get($item,'product.brand.brand_name') ?? '-';
                    $size   = data_get($item,'product.product_size') ?? data_get($item,'product.size');
                    $ring   = data_get($item,'product.ring');
                    $ukuran = $size && $ring ? ($size.' - R'.$ring) : ($size ?? '-');
                    $tahun  = data_get($item,'product.product_year') ?? data_get($item,'product.production_year') ?? '-';
                } elseif ($src === 'second' && $item->productable) {
                    $name = $item->item_name ?? $name;
                    $name = preg_replace('/\s*\d+\/\d+\s*R\d+/i', '', $name);  // buang "265/65 R17"
                    $name = preg_replace('/\s*\(\d+%\)$/', '', $name);        // buang "(80%)"
                    $name = trim($name);

                    $merk   = data_get($item,'productable.brand.name') ?? data_get($item,'productable.brand') ?? '-';
                    $size   = data_get($item,'productable.size');
                    $ring   = data_get($item,'productable.ring');
                    $ukuran = $size && $ring ? ($size.' - R'.$ring) : ($size ?? '-');
                    $tahun  = data_get($item,'productable.product_year') ?? data_get($item,'productable.year') ?? '-';
                }
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

        {{-- Tambah baris kosong supaya rapi --}}
        @for ($i = $rowCount; $i < $maxRows; $i++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        @endfor
        </tbody>
    </table>

    {{-- TOTAL --}}
    <table class="total-box">
        <tr>
            <td class="label">TOTAL</td>
            <td class="amount">{{ format_currency((int) ($sale->total_amount ?? 0)) }}</td>
        </tr>
    </table>

    {{-- TANDA TANGAN seperti foto 1 --}}
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
