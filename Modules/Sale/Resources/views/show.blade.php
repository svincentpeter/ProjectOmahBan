@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('third_party_stylesheets')
    <style>
        :root {
            --thead-bg: #e2e8f0;
            /* slate-200, lebih gelap dari sebelumnya */
            --thead-text: #1e293b;
            /* slate-800, lebih tegas */
            --muted: #475569;
            /* slate-600 */

            /* BADGES - warnanya lebih kontras */
            --soft-warning-bg: #fff4cc;
            --soft-warning-border: #ecc94b;
            --soft-warning-text: #835800;

            --soft-danger-bg: #ffe0e0;
            --soft-danger-border: #f87171;
            --soft-danger-text: #b91c1c;

            --soft-success-bg: #d1fae5;
            --soft-success-border: #34d399;
            --soft-success-text: #065f46;

            --soft-secondary-bg: #f1f5f9;
            --soft-secondary-border: #cbd5e1;
            --soft-secondary-text: #334155;
        }

        .table thead.thead-light th {
            background: var(--thead-bg);
            color: var(--thead-text);
            border-bottom-color: #cbd5e1;
        }

        .table-hover tbody tr:hover {
            background-color: #f0f9ff;
        }

        .badge-soft-warning {
            background: var(--soft-warning-bg);
            color: var(--soft-warning-text);
            border: 1px solid var(--soft-warning-border);
            font-weight: 600;
        }

        .badge-soft-danger {
            background: var(--soft-danger-bg);
            color: var(--soft-danger-text);
            border: 1px solid var(--soft-danger-border);
            font-weight: 600;
        }

        .badge-soft-success {
            background: var(--soft-success-bg);
            color: var(--soft-success-text);
            border: 1px solid var(--soft-success-border);
            font-weight: 600;
        }

        .badge-soft-secondary {
            background: var(--soft-secondary-bg);
            color: var(--soft-secondary-text);
            border: 1px solid var(--soft-secondary-border);
            font-weight: 600;
        }

        .discount-card {
            background: var(--soft-warning-bg);
            border: 2px solid var(--soft-warning-border);
            color: var(--soft-warning-text);
            border-radius: .5rem;
            padding: .5rem .6rem;
            text-align: left;
            font-size: .87em;
            box-shadow: 0 1px 4px rgba(235, 190, 40, 0.08);
        }

        .discount-card .text-muted {
            color: var(--muted) !important;
        }

        .price-old {
            color: var(--muted);
            text-decoration: line-through;
        }

        .price-new {
            color: #d1001f;
            font-weight: 700;
        }

        .table tfoot {
            background: #f1f5f9;
        }

        .table-success th,
        .table-success td {
            background: #d1fae5 !important;
        }

        .table-danger th,
        .table-danger td {
            background: #ffe0e0 !important;
        }

        .table-warning th,
        .table-warning td {
            background: #fff9db !important;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Detail #{{ $sale->reference ?? $sale->id }}</li>
    </ol>
@endsection

@section('content')
    @php
        // Mapping badge jenis item -> pakai "soft" badges
        $jenisMap = [
            'new' => ['Baru', 'soft-success'],
            'second' => ['Bekas', 'soft-warning'],
            'manual' => ['Manual', 'soft-secondary'],
        ];

        $details = $sale->saleDetails ?? ($sale->details ?? collect());
        $adjustedDetails =
            $details instanceof \Illuminate\Support\Collection ? $details->where('is_price_adjusted', 1) : collect();
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
                            <a href="{{ route('sales.pdf', $sale->id) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- ===== INFO HEADER RINCI ===== --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}<br>
                                <strong>Customer:</strong> {{ $sale->customer_name ?: '-' }}<br>
                                <strong>Kasir:</strong> {{ optional($sale->user)->name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong> @include('sale::partials.status', ['data' => $sale])<br>
                                <strong>Pembayaran:</strong> @include('sale::partials.payment-status', ['data' => $sale])<br>
                                <strong>Metode:</strong> {{ $sale->payment_method ?? '-' }}
                                @if ($sale->bank_name)
                                    ({{ $sale->bank_name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <h3 class="text-success mb-0">{{ format_currency($sale->total_amount) }}</h3>
                                <small class="text-muted">Grand Total</small>

                                {{-- ✅ BADGE TOTAL DISKON ITEM (jika ada) --}}
                                @if ($sale->has_price_adjustment)
                                    @php
                                        $totalDiscount =
                                            $details instanceof \Illuminate\Support\Collection
                                                ? (int) $details
                                                    ->where('is_price_adjusted', 1)
                                                    ->sum('price_adjustment_amount')
                                                : 0;
                                    @endphp
                                    <div class="mt-2">
                                        <span class="badge badge-soft-warning badge-lg">
                                            <i class="bi bi-tag-fill"></i> Ada Diskon:
                                            {{ format_currency($totalDiscount) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ===== Header Ringkas (badge indikator) ===== --}}
                        <div class="mb-3">
                            <span class="badge badge-soft-secondary">
                                <i class="bi bi-person"></i> Kasir: {{ optional($sale->user)->name ?? '—' }}
                            </span>

                            @if ((int) ($sale->has_manual_input ?? 0) === 1)
                                <span class="badge badge-soft-warning ml-1">
                                    <i class="bi bi-pencil-square"></i>
                                    {{ (int) ($sale->manual_input_count ?? 0) }} item manual
                                </span>
                            @endif

                            @if ((int) ($sale->has_price_adjustment ?? 0) === 1)
                                <span class="badge badge-soft-danger ml-1">
                                    <i class="bi bi-tag-fill"></i> Ada edit harga
                                </span>
                            @endif
                        </div>

                        {{-- ===== Item dengan Edit Harga ===== --}}
                        @if (($adjustedDetails->count() ?? 0) > 0)
                            <div class="card mb-3">
                                <div class="card-header">Item dengan Edit Harga</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-right">Harga Asli</th>
                                                    <th class="text-right">Harga Baru</th>
                                                    <th class="text-right">Potongan</th>
                                                    <th>Alasan</th>
                                                    <th>Diubah oleh</th>
                                                    <th class="text-center">Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($adjustedDetails as $d)
                                                    @php
                                                        $orig =
                                                            (int) ($d->original_price ??
                                                                $d->price +
                                                                    max(0, (int) ($d->price_adjustment_amount ?? 0)));
                                                        $new = (int) ($d->price ?? 0);
                                                        $disc =
                                                            (int) ($d->price_adjustment_amount ?? max(0, $orig - $new));
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $d->product_name }}</td>
                                                        <td class="text-center">{{ (int) $d->quantity }}</td>
                                                        <td class="text-right">{{ format_currency($orig) }}</td>
                                                        <td class="text-right">{{ format_currency($new) }}</td>
                                                        <td class="text-right">
                                                            @if ($disc > 0)
                                                                <span
                                                                    class="badge badge-soft-danger">{{ format_currency($disc) }}</span>
                                                            @elseif($disc < 0)
                                                                <span
                                                                    class="badge badge-soft-success">+{{ format_currency(abs($disc)) }}</span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $d->price_adjustment_note ?? '—' }}</td>
                                                        <td>{{ optional($d->adjuster)->name ?? '—' }}</td>
                                                        <td class="text-center">
                                                            {{ optional(\Carbon\Carbon::parse($d->adjusted_at ?? null))->format('d/m/Y H:i') ?? '—' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ===== Ringkasan Pembayaran ===== --}}
                        @if (($sale->salePayments->count() ?? 0) > 0)
                            <div class="card mb-3">
                                <div class="card-header">Pembayaran</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Metode</th>
                                                    <th>Bank</th>
                                                    <th class="text-right">Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sale->salePayments()->orderByDesc('date')->orderByDesc('id')->get() as $p)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $p->payment_method ?? '—' }}</td>
                                                        <td>{{ $p->bank_name ?? '—' }}</td>
                                                        <td class="text-right">{{ format_currency((int) $p->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3 text-right">
                                        <div>Total: <strong>{{ format_currency((int) $sale->total_amount) }}</strong></div>
                                        <div>Dibayar: <strong>{{ format_currency((int) $sale->paid_amount) }}</strong>
                                        </div>
                                        <div>Kurang:
                                            <strong>{{ format_currency(max((int) $sale->due_amount, 0)) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ===== TABEL DETAIL ITEMS (lengkap) ===== --}}
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
                                        <th width="10%" class="text-right">Harga Jual</th>
                                        <th width="5%" class="text-center">Qty</th>
                                        <th width="8%" class="text-right">Diskon Item</th>
                                        <th width="8%" class="text-right">Pajak Item</th>
                                        <th width="10%" class="text-right">Subtotal</th>
                                        <th width="13%" class="text-center">Info Diskon</th>
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

                                    @forelse($details as $detail)
                                        @php
                                            $name = $detail->product_name;
                                            $code = $detail->product_code;
                                            $qty = (int) $detail->quantity;
                                            $unitPrice = (int) $detail->price;
                                            $subTotal = (int) $detail->sub_total;
                                            $diskon = (int) ($detail->product_discount_amount ?? 0);
                                            $pajak = (int) ($detail->product_tax_amount ?? 0);

                                            // HPP
                                            $hppUnit = (int) ($detail->hpp ?? 0);
                                            if (
                                                ($detail->source_type ?? null) === 'manual' &&
                                                ($detail->manual_kind ?? null) === 'goods'
                                            ) {
                                                $hppUnit = (int) ($detail->manual_hpp ?? 0);
                                            }
                                            $hppTotal = $hppUnit * $qty;

                                            // Profit
                                            $labaItem = $subTotal - $hppTotal;

                                            // Badge jenis
                                            $jenis = $detail->source_type ?? 'new';
                                            [$jenisText, $jenisBadge] = $jenisMap[$jenis] ?? [
                                                'Unknown',
                                                'soft-secondary',
                                            ];
                                            if ($jenis === 'manual') {
                                                $jenisText = $detail->manual_kind === 'service' ? 'Jasa' : 'Barang';
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
                                            <td><span class="badge badge-{{ $jenisBadge }}">{{ $jenisText }}</span>
                                            </td>
                                            <td class="text-right">{{ format_currency($hppUnit) }}</td>

                                            {{-- Harga jual + indikator jika ada edit harga --}}
                                            <td class="text-right">
                                                @if ($detail->is_price_adjusted ?? false)
                                                    <small class="d-block price-old">
                                                        {{ format_currency((int) ($detail->original_price ?? 0)) }}
                                                    </small>
                                                    <strong class="price-new">{{ format_currency($unitPrice) }}</strong>
                                                @else
                                                    {{ format_currency($unitPrice) }}
                                                @endif
                                            </td>

                                            <td class="text-center">{{ $qty }}</td>
                                            <td class="text-right">
                                                @if (($detail->product_discount_amount ?? 0) > 0)
                                                    <span
                                                        class="badge badge-soft-danger">{{ format_currency($diskon) }}</span>
                                                @else
                                                    {{ format_currency(0) }}
                                                @endif
                                            </td>
                                            <td class="text-right">{{ format_currency($pajak) }}</td>
                                            <td class="text-right"><strong>{{ format_currency($subTotal) }}</strong></td>

                                            {{-- Info diskon per item --}}
                                            <td class="text-center">
                                                @if ($detail->is_price_adjusted ?? false)
                                                    @php
                                                        $adjAmt = (int) ($detail->price_adjustment_amount ?? 0);
                                                        $orig =
                                                            (int) ($detail->original_price ??
                                                                $unitPrice + max(0, $adjAmt));
                                                        $pct = $orig > 0 ? round(($adjAmt / $orig) * 100, 1) : 0;
                                                    @endphp
                                                    <div class="discount-card mb-0">
                                                        <div class="mb-1">
                                                            <i class="bi bi-tag-fill"></i>
                                                            <strong>-{{ format_currency($adjAmt) }}</strong>
                                                            <small
                                                                class="text-muted">({{ number_format($pct, 1) }}%)</small>
                                                        </div>

                                                        @if (!empty($detail->price_adjustment_note))
                                                            <div class="border-top pt-1 mt-1">
                                                                <strong>Alasan:</strong><br>
                                                                <em
                                                                    style="white-space: pre-wrap">{{ $detail->price_adjustment_note }}</em>
                                                            </div>
                                                        @endif

                                                        @if (!empty($detail->adjusted_at) || !empty($detail->adjuster))
                                                            <div class="border-top pt-1 mt-1">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-person"></i>
                                                                    {{ optional($detail->adjuster)->name ?? '—' }}<br>
                                                                    <i class="bi bi-clock"></i>
                                                                    {{ optional(\Carbon\Carbon::parse($detail->adjusted_at ?? null))->format('d/m/Y H:i') ?? '—' }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge badge-soft-secondary">-</span>
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
                                @if (($details->count() ?? 0) > 0)
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL:</td>
                                            <td class="text-center">{{ $totalQty }}</td>
                                            <td colspan="2"></td>
                                            <td class="text-right">{{ format_currency($totalJual) }}</td>
                                            <td class="text-center">
                                                @if ($sale->has_price_adjustment)
                                                    @php
                                                        $sumDiscount =
                                                            $details instanceof \Illuminate\Support\Collection
                                                                ? (int) $details
                                                                    ->where('is_price_adjusted', 1)
                                                                    ->sum('price_adjustment_amount')
                                                                : 0;
                                                    @endphp
                                                    <span class="badge badge-soft-warning">
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
                                
                                        <tr class="table-success">
                                            <th>Grand Total</th>
                                            <th class="text-right">{{ format_currency((int) $sale->total_amount) }}</th>
                                        </tr>
                                        <tr>
                                            <th>Total HPP</th>
                                            <td class="text-right">{{ format_currency((int) ($sale->total_hpp ?? 0)) }}
                                            </td>
                                        </tr>
                                        @php $grandProfit = (int) ($sale->total_profit ?? ($totalJual - $totalHpp)); @endphp
                                        <tr class="{{ $grandProfit >= 0 ? 'table-success' : 'table-danger' }}">
                                            <th>Total Laba</th>
                                            <td class="text-right"><strong>{{ format_currency($grandProfit) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Dibayar</th>
                                            <td class="text-right">{{ format_currency((int) $sale->paid_amount) }}</td>
                                        </tr>
                                        <tr class="{{ (int) $sale->due_amount > 0 ? 'table-warning' : '' }}">
                                            <th>Kurang Bayar</th>
                                            <td class="text-right">{{ format_currency(max((int) $sale->due_amount, 0)) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- NOTE --}}
                        @if (!empty($sale->note))
                            <div class="alert alert-info mt-3">
                                <strong><i class="bi bi-chat-left-text"></i> Catatan:</strong><br>
                                {{ $sale->note }}
                            </div>
                        @endif
                    </div> {{-- /card-body --}}
                </div>
            </div>
        </div>
    </div>
@endsection
