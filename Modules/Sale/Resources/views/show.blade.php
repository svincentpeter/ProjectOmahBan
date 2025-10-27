@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Detail #{{ $sale->reference ?? $sale->id }}</li>
    </ol>
@endsection

@section('content')
    @php
        // Mapping badge jenis item
        $jenisMap = [
            'new' => ['Baru', 'success'],
            'second' => ['Bekas', 'warning'],
            'manual' => ['Manual', 'secondary'],
        ];
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-receipt"></i> Detail Penjualan
                            <span class="badge badge-info">{{ $sale->reference ?? '#' . $sale->id }}</span>
                        </h4>
                        <div>
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- INFO HEADER --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}<br>
                                <strong>Customer:</strong> {{ $sale->customer_name ?: '-' }}<br>
                                <strong>Kasir:</strong> {{ $sale->user->name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong> @include('sale::partials.status', ['data' => $sale])<br>
                                <strong>Pembayaran:</strong> @include('sale::partials.payment-status', ['data' => $sale])<br>
                                <strong>Metode:</strong> {{ $sale->payment_method ?? '-' }}
                                @if($sale->bank_name)
                                    ({{ $sale->bank_name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <h3 class="text-success mb-0">{{ format_currency($sale->total_amount) }}</h3>
                                <small class="text-muted">Grand Total</small>
                                
                                {{-- ✅ BADGE JIKA ADA DISKON --}}
                                @if($sale->has_price_adjustment)
                                    @php
                                        $totalDiscount = $sale->saleDetails->where('is_price_adjusted', 1)->sum('price_adjustment_amount');
                                    @endphp
                                    <div class="mt-2">
                                        <span class="badge badge-warning badge-lg">
                                            <i class="bi bi-tag-fill"></i> Ada Diskon: {{ format_currency($totalDiscount) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- TABEL DETAIL ITEMS --}}
                        <h5 class="mb-3"><i class="bi bi-list-ul"></i> Detail Item</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="3%">#</th>
                                        <th width="20%">Produk</th>
                                        <th width="8%">Kode</th>
                                        <th width="6%">Jenis</th>
                                        <th width="9%" class="text-right">HPP/Unit</th>
                                        <th width="10%" class="text-right">Harga Jual</th> {{-- ✅ UPDATE LABEL --}}
                                        <th width="5%" class="text-center">Qty</th>
                                        <th width="8%" class="text-right">Diskon Item</th>
                                        <th width="8%" class="text-right">Pajak Item</th>
                                        <th width="10%" class="text-right">Subtotal</th>
                                        <th width="13%" class="text-center">Info Diskon</th> {{-- ✅ KOLOM BARU --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalQty = 0;
                                        $totalJual = 0;
                                        $totalHpp = 0;
                                        $totalLaba = 0;
                                    @endphp

                                    @forelse($sale->saleDetails as $detail)
                                        @php
                                            $name = $detail->product_name;
                                            $code = $detail->product_code;
                                            $qty = (int)$detail->quantity;
                                            $unitPrice = (int)$detail->price;
                                            $subTotal = (int)$detail->sub_total;
                                            $diskon = (int)$detail->product_discount_amount;
                                            $pajak = (int)$detail->product_tax_amount;

                                            // HPP
                                            $hppUnit = (int)($detail->hpp ?? 0);
                                            if ($detail->source_type === 'manual' && $detail->manual_kind === 'goods') {
                                                $hppUnit = (int)($detail->manual_hpp ?? 0);
                                            }
                                            $hppTotal = $hppUnit * $qty;

                                            // Profit
                                            $labaItem = $subTotal - $hppTotal;

                                            // Badge jenis
                                            $jenis = $detail->source_type ?? 'new';
                                            [$jenisText, $jenisBadge] = $jenisMap[$jenis] ?? ['Unknown', 'secondary'];
                                            if ($jenis === 'manual') {
                                                $jenisText = ($detail->manual_kind === 'service') ? 'Jasa' : 'Barang';
                                            }

                                            // Akumulasi
                                            $totalQty += $qty;
                                            $totalJual += $subTotal;
                                            $totalHpp += $hppTotal;
                                            $totalLaba += $labaItem;
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $i++ }}</td>
                                            <td>{{ $name }}</td>
                                            <td>{{ $code ?: '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $jenisBadge }}">{{ $jenisText }}</span>
                                            </td>
                                            <td class="text-right">{{ format_currency($hppUnit) }}</td>
                                            
                                            {{-- ✅ HARGA JUAL (DENGAN INDICATOR JIKA ADA DISKON) --}}
                                            <td class="text-right">
                                                @if($detail->is_price_adjusted)
                                                    <small class="text-muted d-block">
                                                        <del>{{ format_currency($detail->original_price) }}</del>
                                                    </small>
                                                    <strong class="text-danger">{{ format_currency($unitPrice) }}</strong>
                                                @else
                                                    {{ format_currency($unitPrice) }}
                                                @endif
                                            </td>
                                            
                                            <td class="text-center">{{ $qty }}</td>
                                            <td class="text-right">{{ format_currency($diskon) }}</td>
                                            <td class="text-right">{{ format_currency($pajak) }}</td>
                                            <td class="text-right"><strong>{{ format_currency($subTotal) }}</strong></td>
                                            
                                            {{-- ✅ KOLOM INFO DISKON --}}
                                            <td class="text-center">
                                                @if($detail->is_price_adjusted)
                                                    <div class="alert alert-warning mb-0 p-2" style="font-size: 0.85em;">
                                                        <div class="mb-1">
                                                            <i class="bi bi-tag-fill"></i> 
                                                            <strong>-{{ format_currency($detail->price_adjustment_amount) }}</strong>
                                                            <small class="text-muted">
                                                                ({{ number_format(($detail->price_adjustment_amount / $detail->original_price) * 100, 1) }}%)
                                                            </small>
                                                        </div>
                                                        
                                                        @if($detail->price_adjustment_note)
                                                            <div class="border-top pt-1 mt-1">
                                                                <strong>Alasan:</strong><br>
                                                                <em style="white-space: pre-wrap; font-size: 0.9em;">{{ $detail->price_adjustment_note }}</em>
                                                            </div>
                                                        @endif
                                                        
                                                        @if($detail->adjuster)
                                                            <div class="border-top pt-1 mt-1">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-person"></i> {{ $detail->adjuster->name }}<br>
                                                                    <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($detail->adjusted_at)->format('d/m/Y H:i') }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                <p class="mb-0 mt-2">Tidak ada detail item</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                {{-- FOOTER SUMMARY --}}
                                @if($sale->saleDetails->count() > 0)
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL:</td>
                                            <td class="text-center">{{ $totalQty }}</td>
                                            <td colspan="2"></td>
                                            <td class="text-right">{{ format_currency($totalJual) }}</td>
                                            <td class="text-center">
                                                @if($sale->has_price_adjustment)
                                                    @php
                                                        $sumDiscount = $sale->saleDetails->where('is_price_adjusted', 1)->sum('price_adjustment_amount');
                                                    @endphp
                                                    <span class="badge badge-warning">
                                                        Total: -{{ format_currency($sumDiscount) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        {{-- SUMMARY GRAND TOTAL --}}
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th width="50%">Diskon Global ({{ (int)$sale->discount_percentage }}%)</th>
                                            <td class="text-right">{{ format_currency((int)$sale->discount_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pajak ({{ (int)$sale->tax_percentage }}%)</th>
                                            <td class="text-right">{{ format_currency((int)$sale->tax_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Biaya Kirim</th>
                                            <td class="text-right">{{ format_currency((int)$sale->shipping_amount) }}</td>
                                        </tr>
                                        <tr class="table-success">
                                            <th>Grand Total</th>
                                            <th class="text-right">{{ format_currency((int)$sale->total_amount) }}</th>
                                        </tr>
                                        <tr>
                                            <th>Total HPP</th>
                                            <td class="text-right">{{ format_currency((int)($sale->total_hpp ?? $totalHpp)) }}</td>
                                        </tr>
                                        <tr class="{{ ($sale->total_profit ?? $totalLaba) >= 0 ? 'table-success' : 'table-danger' }}">
                                            <th>Total Laba</th>
                                            <td class="text-right">
                                                @php $grandProfit = (int)($sale->total_profit ?? $totalLaba); @endphp
                                                <strong>{{ format_currency($grandProfit) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Dibayar</th>
                                            <td class="text-right">{{ format_currency((int)$sale->paid_amount) }}</td>
                                        </tr>
                                        <tr class="{{ $sale->due_amount > 0 ? 'table-warning' : '' }}">
                                            <th>Kurang Bayar</th>
                                            <td class="text-right">{{ format_currency(max((int)$sale->due_amount, 0)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- NOTE --}}
                        @if($sale->note)
                            <div class="alert alert-info mt-3">
                                <strong><i class="bi bi-chat-left-text"></i> Catatan:</strong><br>
                                {{ $sale->note }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
