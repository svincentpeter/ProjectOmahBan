{{-- Nota POS A6 Landscape – 1 Halaman, ada padding di tiap sudut --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota {{ $sale->reference ?? '-' }}</title>
    <style>
        /* =========================================================
           KUNCI: jangan andalkan @page margin (kadang "terasa nol")
           -> bikin padding dari wrapper .sheet supaya pasti ada ruang tepi
        ========================================================= */

        @page {
            /* Change to A5 Landscape (larger than A6) to prevent cut-off */
            size: A5 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px; /* Sedikit diperkecil agar muat */
            color: #000;
        }

        .sheet {
            width: 100%;
            padding: 4mm; /* Kurangi padding agar area konten lebih luas */
            page-break-inside: avoid;
        }

        /* Grid header */
        .row { width: 100%; }
        .col {
            display: inline-block;
            vertical-align: top;
        }

        .col-left { width: 60%; }
        .col-right { width: 39%; }

        /* Logo & alamat */
        .logo {
            height: 28px;
            vertical-align: middle;
        }

        .brand {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .tagline {
            font-weight: 700;
            font-size: 10px;
            margin-top: 2px;
        }

        .address {
            margin-top: 6px;
            line-height: 1.30; /* sedikit dipadatkan */
            font-size: 10px;
        }

        /* Blok meta kanan */
        .meta {
            font-size: 10px;
            line-height: 1.45; /* sedikit dipadatkan */
        }

        .meta .nota {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .meta table {
            width: auto;
            border-collapse: collapse;
        }

        .meta td {
            padding: 1px 0;
            white-space: nowrap;
        }

        .meta .label { padding-right: 4px; }
        .meta .colon { padding: 0 4px; }

        /* garis titik untuk isian */
        .meta .dotsline {
            display: inline-block;
            min-width: 38mm;
            border-bottom: 1px dotted #000;
            line-height: 10px;
            height: 10px;
            vertical-align: baseline;
        }

        .meta .dotsline.short { min-width: 28mm; }

        /* Tabel item */
        table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    page-break-inside: avoid;

    table-layout: fixed; /* <-- TAMBAH INI */
}


        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 2px 3px;  /* dipadatkan sedikit agar 1 halaman aman */
            line-height: 1.15;
        }

        table.items th {
            text-transform: uppercase;
            text-align: center;
            font-weight: 700;
        }

        table.items td { vertical-align: top; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Lebar kolom */
        /* Lebar kolom (re-balance supaya kolom kanan lega) */
.col-qty   { width: 7%;  }
.col-name  { width: 30%; }
.col-merk  { width: 12%; }
.col-ukuran{ width: 12%; }
.col-year  { width: 9%;  }
.col-price { width: 14%; }
.col-total { width: 16%; }  /* JUMLAH jadi lebih lebar */


        /* TOTAL: jangan float -> sering bikin tanda tangan "loncat" halaman */
        table.total-box {
            border-collapse: collapse;
            float: none;
            margin-top: 3mm;
            margin-left: auto; /* tetap rata kanan */
            page-break-inside: avoid;
        }

        table.total-box td {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 10px;
        }

        table.total-box .label { font-weight: 700; }

        table.total-box .amount {
            min-width: 28mm;
            text-align: right;
        }

        /* Tanda tangan: hilangkan clear:both dan hemat tinggi */
        .sign {
            clear: none;
            margin-top: 6mm;
            width: 70%;
            page-break-inside: avoid;
            page-break-before: avoid;
        }

        .sign .area {
            width: 46%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .sign .label {
            font-size: 10px;
            margin-bottom: 6mm; /* dipadatkan dari 8mm */
        }

        .sign .line {
            width: 65%;
            margin: 0 auto;
            height: 0;
            border-top: 1px solid #000;
        }
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
                    <div class="nota">
                        No. NOTA: <span class="dotsline short">{{ $sale->reference ?? '-' }}</span>
                    </div>

                    <table>
                        <tr>
                            <td class="label">Semarang</td>
                            <td class="colon">:</td>
                            <td>
                                <span class="dotsline short">
                                    {{ \Carbon\Carbon::parse($sale->date ?? now())->translatedFormat('d F Y') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Kepada Yth.</td>
                            <td class="colon">:</td>
                            <td>
                                <span class="dotsline">
                                    {{ $sale->customer_name ?? (data_get($sale, 'customer.name') ?? '') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Jalan</td>
                            <td class="colon">:</td>
                            <td><span class="dotsline">{{ data_get($sale, 'customer.address') ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Telp.</td>
                            <td class="colon">:</td>
                            <td>
                                <span class="dotsline short">
                                    {{ data_get($sale, 'customer.customer_phone') ?: '085 292 290 382' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

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
                    // Batas maksimal baris agar pasti 1 halaman A6 + padding
                    $maxRows = 6;

                    $details = $sale->saleDetails ?? collect();
                    $slice = $details->take($maxRows);
                    $rowCount = 0;
                @endphp

                @foreach ($slice as $item)
                    @php
                        $qty = (int) $item->quantity;
                        $price = (int) $item->price;
                        $total = (int) ($item->sub_total ?? $qty * $price);

                        $name = $item->product_name ?? ($item->item_name ?? '-');
                        $merk = '-';
                        $ukuran = '-';
                        $tahun = '-';

                        $src = $item->source_type ?? data_get($item, 'options.source_type');
                        if ($src === 'new' && $item->product) {
                            $merk =
                                data_get($item, 'product.brand.name') ??
                                (data_get($item, 'product.brand.brand_name') ?? '-');
                            $size = data_get($item, 'product.product_size') ?? data_get($item, 'product.size');
                            $ring = data_get($item, 'product.ring');
                            $ukuran = $size && $ring ? $size . ' - R' . $ring : ($size ?? '-');
                            $tahun =
                                data_get($item, 'product.product_year') ??
                                (data_get($item, 'product.production_year') ?? '-');
                        } elseif ($src === 'second' && $item->productable) {
                            $name = $item->item_name ?? $name;
                            $name = preg_replace('/\s*\d+\/\d+\s*R\d+/i', '', $name);
                            $name = preg_replace('/\s*\(\d+%\)$/', '', $name);
                            $name = trim($name);

                            $merk =
                                data_get($item, 'productable.brand.name') ??
                                (data_get($item, 'productable.brand') ?? '-');
                            $size = data_get($item, 'productable.size');
                            $ring = data_get($item, 'productable.ring');
                            $ukuran = $size && $ring ? $size . ' - R' . $ring : ($size ?? '-');
                            $tahun =
                                data_get($item, 'productable.product_year') ??
                                (data_get($item, 'productable.year') ?? '-');
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
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
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

        {{-- TANDA TANGAN --}}
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
