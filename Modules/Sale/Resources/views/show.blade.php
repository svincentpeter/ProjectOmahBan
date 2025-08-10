@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header d-flex flex-wrap align-items-center">
                    <div>
                        No. Referensi: <strong>{{ $sale->reference }}</strong>
                    </div>
                    <div class="mfs-auto d-print-none">
                        <a target="_blank" class="btn btn-sm btn-secondary mfe-1" href="{{ route('sales.pdf', $sale->id) }}">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info" href="{{ route('sales.pdf', $sale->id) }}">
                            <i class="bi bi-save"></i> Simpan
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Info Perusahaan & Invoice --}}
                    <div class="row mb-4">
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <h5 class="mb-2 border-bottom pb-2">Informasi Perusahaan</h5>
                            <div><strong>{{ settings()->company_name }}</strong></div>
                            <div>{{ settings()->company_address }}</div>
                            <div>Email: {{ settings()->company_email }}</div>
                            <div>Telp: {{ settings()->company_phone }}</div>
                        </div>

                        <div class="col-sm-4 mb-3 mb-md-0">
                            <h5 class="mb-2 border-bottom pb-2">Informasi Invoice</h5>
                            <div>Invoice: <strong>INV/{{ $sale->reference }}</strong></div>
                            <div>Tanggal: {{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</div>

                            <div class="mt-1">
                                Status:
                                {{-- gunakan partial badge yang sudah ada bila tersedia --}}
                                @includeIf('sale::partials.status', ['data' => $sale])
                            </div>

                            <div class="mt-1">
                                Status Pembayaran:
                                @includeIf('sale::partials.payment-status', ['data' => $sale])
                            </div>

                            <div class="mt-1">
                                Metode:
                                <span class="badge badge-pill badge-primary">
                                    {{ $sale->payment_method }}{{ $sale->bank_name ? ' ('.$sale->bank_name.')' : '' }}
                                </span>
                            </div>

                            @if($sale->note)
                                <div class="mt-1">Catatan: {{ $sale->note }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Item --}}
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="align-middle">#</th>
                                <th class="align-middle">Produk</th>
                                <th class="align-middle">Kode</th>
                                <th class="align-middle">Jenis</th>
                                <th class="align-middle text-right">HPP / Unit</th>
                                <th class="align-middle text-right">Harga Jual / Unit</th>
                                <th class="align-middle text-center">Qty</th>
                                <th class="align-middle text-right">Diskon Item</th>
                                <th class="align-middle text-right">Pajak Item</th>
                                <th class="align-middle text-right">Subtotal</th>
                                <th class="align-middle text-right">Total HPP</th>
                                <th class="align-middle text-right">Laba / Item</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                                $totalQty   = 0;
                                $totalJual  = 0;
                                $totalHpp   = 0;
                                $totalLaba  = 0;

                                $jenisMap  = ['new' => ['Baru','success'], 'second' => ['Bekas','warning'], 'manual' => ['Manual','secondary']];
                            @endphp

                            @foreach($sale->saleDetails as $item)
                                @php
                                    $qty       = (int) $item->quantity;
                                    $hppUnit   = (int) $item->hpp;
                                    $hppTotal  = $hppUnit * $qty;

                                    $unitPrice = (int) $item->unit_price;
                                    $subTotal  = (int) $item->sub_total;

                                    $diskon    = (int) $item->product_discount_amount;
                                    $pajak     = (int) $item->product_tax_amount;

                                    $labaItem  = !is_null($item->subtotal_profit) ? (int)$item->subtotal_profit : ($subTotal - $hppTotal);

                                    $totalQty  += $qty;
                                    $totalJual += $subTotal;
                                    $totalHpp  += $hppTotal;
                                    $totalLaba += $labaItem;

                                    [$jenisText,$jenisColor] = $jenisMap[$item->source_type ?? 'manual'] ?? ['-','light'];
                                @endphp

                                <tr>
                                    <td class="align-middle">{{ $i++ }}</td>
                                    <td class="align-middle">{{ $item->product_name ?? $item->item_name }}</td>
                                    <td class="align-middle">
                                        <span class="badge badge-success">{{ $item->product_code ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-{{ $jenisColor }}">{{ $jenisText }}</span>
                                    </td>

                                    <td class="align-middle text-right">{{ format_currency($hppUnit) }}</td>
                                    <td class="align-middle text-right">{{ format_currency($unitPrice) }}</td>

                                    <td class="align-middle text-center">{{ $qty }}</td>

                                    <td class="align-middle text-right">{{ format_currency($diskon) }}</td>
                                    <td class="align-middle text-right">{{ format_currency($pajak) }}</td>

                                    <td class="align-middle text-right">{{ format_currency($subTotal) }}</td>
                                    <td class="align-middle text-right">{{ format_currency($hppTotal) }}</td>
                                    <td class="align-middle text-right">
                                        <span class="badge {{ $labaItem >= 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ format_currency($labaItem) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">TOTAL</td>
                                    <td class="text-center">{{ $totalQty }}</td>
                                    <td class="text-right">—</td>
                                    <td class="text-right">—</td>
                                    <td class="text-right">{{ format_currency($totalJual) }}</td>
                                    <td class="text-right">{{ format_currency($totalHpp) }}</td>
                                    <td class="text-right">{{ format_currency($totalLaba) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Ringkasan Pembayaran --}}
                    <div class="row">
                        <div class="col-lg-5 col-sm-6 ml-md-auto">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="left"><strong>Diskon ({{ $sale->discount_percentage }}%)</strong></td>
                                    <td class="right">{{ format_currency((int)$sale->discount_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Pajak ({{ $sale->tax_percentage }}%)</strong></td>
                                    <td class="right">{{ format_currency((int)$sale->tax_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Biaya Kirim</strong></td>
                                    <td class="right">{{ format_currency((int)$sale->shipping_amount) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="left"><strong>Grand Total</strong></td>
                                    <td class="right"><strong>{{ format_currency((int)$sale->total_amount) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Total HPP</strong></td>
                                    <td class="right">{{ format_currency((int)($sale->total_hpp ?? $totalHpp)) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Total Laba</strong></td>
                                    <td class="right">
                                        <span class="badge {{ ($sale->total_profit ?? $totalLaba) >= 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ format_currency((int)($sale->total_profit ?? $totalLaba)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Dibayar</strong></td>
                                    <td class="right">{{ format_currency((int)$sale->paid_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Kurang</strong></td>
                                    <td class="right">{{ format_currency(max((int)$sale->total_amount - (int)$sale->paid_amount, 0)) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> {{-- /card-body --}}
            </div>
        </div>
    </div>
</div>
@endsection
