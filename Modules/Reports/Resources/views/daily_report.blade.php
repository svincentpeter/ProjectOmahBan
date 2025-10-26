@extends('layouts.app')

@section('title', 'Laporan Kas Harian')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Kas Harian</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        {{-- Filter Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-weight-bold">
                            <i class="cil-calendar mr-2 text-primary"></i>
                            Filter Tanggal
                        </h5>
                        <small class="text-muted">Pilih tanggal untuk melihat laporan kas harian</small>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('reports.daily.generate') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        {{-- Tanggal Laporan --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="report_date" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Tanggal Laporan
                            </label>
                            <input type="date" 
                                   id="report_date" 
                                   name="report_date"
                                   value="{{ old('report_date', $reportDate) }}"
                                   class="form-control @error('report_date') is-invalid @enderror" 
                                   required>
                            @error('report_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-8 col-md-6 mb-3">
                            <label class="form-label font-weight-semibold d-block">&nbsp;</label>
                            <div class="btn-group" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="cil-chart mr-1"></i> Tampilkan Laporan
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="cil-print mr-1"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($generated)
        @php
            $netPos   = ($netIncome ?? 0) >= 0;
            $gpMargin = ($totalOmset ?? 0) > 0 ? round(($netIncome / max($totalOmset,1)) * 100, 1) : 0;
        @endphp

        {{-- Report Header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Laporan Kas Harian</h4>
                        <p class="text-muted mb-0">
                            <i class="cil-calendar mr-1"></i>
                            Tanggal: {{ \Carbon\Carbon::parse($reportDate)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <small class="text-muted d-block">Generated at</small>
                        <strong>{{ now()->translatedFormat('d M Y, H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row mb-4">
            {{-- Total Omset --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-blue">
                    <div class="stats-icon">
                        <i class="cil-wallet"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Omset Kotor</div>
                        <div class="stats-value">{{ format_currency($totalOmset) }}</div>
                        <div class="stats-sub">Semua transaksi hari ini</div>
                    </div>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-warning">
                    <div class="stats-icon">
                        <i class="cil-building"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Pengeluaran</div>
                        <div class="stats-value text-warning">( {{ format_currency($totalPengeluaran) }} )</div>
                        <div class="stats-sub">Biaya operasional hari ini</div>
                    </div>
                </div>
            </div>

            {{-- Pendapatan Bersih --}}
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="stats-card stats-card-success">
                    <div class="stats-icon">
                        <i class="cil-thumb-up"></i>
                    </div>
                    <div class="stats-content w-100">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <div class="stats-label mb-0">Pendapatan Bersih</div>
                            <span class="badge badge-light">Net Ratio: {{ $gpMargin }}%</span>
                        </div>
                        <div class="stats-value {{ $netPos ? 'text-success' : 'text-danger' }} mb-2">
                            {{ format_currency($netIncome) }}
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="progress" style="height: 8px; border-radius: 6px;">
                            <div class="progress-bar {{ $netPos ? 'bg-success' : 'bg-danger' }}"
                                 role="progressbar"
                                 style="width: {{ min(max(abs($gpMargin),0),100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-cart mr-2 text-primary"></i>
                    Daftar Penjualan
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0" width="16%">Reference</th>
                                <th class="border-0" width="12%">Waktu</th>
                                <th class="border-0">Item Terjual</th>
                                <th class="border-0 text-right" width="18%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="font-weight-semibold">{{ $sale->reference }}</td>
                                <td>{{ optional($sale->created_at)->format('H:i') ?? '-' }}</td>
                                <td>
                                    @if ($sale->saleDetails && $sale->saleDetails->count())
                                        <ul class="mb-0 pl-3" style="list-style-type: none; padding-left: 0;">
                                            @foreach ($sale->saleDetails as $d)
                                                @php
                                                    $name  = $d->item_name ?? optional($d->product)->name ?? ($d->product_name ?? 'Item');
                                                    $qty   = (int) ($d->quantity ?? $d->qty ?? 1);
                                                    $price = (int) ($d->unit_price ?? $d->price ?? 0);
                                                    $sub   = (int) ($d->sub_total ?? ($qty * $price));
                                                @endphp
                                                <li class="mb-1">
                                                    <i class="cil-check-circle text-success mr-1"></i>
                                                    <span class="font-weight-semibold">{{ $name }}</span>
                                                    <span class="text-muted">× {{ $qty }}</span>
                                                    <span class="text-muted"> @ {{ format_currency($price) }}</span>
                                                    <span class="ml-2">= <strong>{{ format_currency($sub) }}</strong></span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em class="text-muted">Tidak ada item</em>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold">{{ format_currency($sale->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3">Tidak ada penjualan pada tanggal ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot style="background-color: #f8f9fa;">
                            <tr>
                                <th colspan="3" class="text-right">Total Omset Kotor</th>
                                <th class="text-right font-weight-bold text-primary">{{ format_currency($totalOmset) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Expenses Table --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-money mr-2 text-danger"></i>
                    Daftar Pengeluaran
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0" width="16%">Reference</th>
                                <th class="border-0" width="18%">Kategori</th>
                                <th class="border-0">Detail</th>
                                <th class="border-0 text-right" width="18%">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($expenses as $ex)
                            <tr>
                                <td class="font-weight-semibold">{{ $ex->reference }}</td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ optional($ex->category)->category_name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mb-1">{{ $ex->details ?? '-' }}</div>
                                    <small class="text-muted">
                                        <i class="cil-user mr-1"></i>{{ optional($ex->user)->name ?? '—' }}
                                        <span class="mx-1">•</span>
                                        <i class="cil-clock mr-1"></i>{{ optional($ex->created_at)->format('H:i') ?? '-' }}
                                    </small>
                                </td>
                                <td class="text-right font-weight-bold text-danger">{{ format_currency($ex->amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3">Tidak ada pengeluaran pada tanggal ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot style="background-color: #f8f9fa;">
                            <tr>
                                <th colspan="3" class="text-right">Total Pengeluaran</th>
                                <th class="text-right font-weight-bold text-danger">{{ format_currency($totalPengeluaran) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Summary Card --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-calculator mr-2 text-primary"></i>
                    Ringkasan & Penerimaan
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr>
                            <th style="width:40%">
                                <i class="cil-wallet mr-2 text-primary"></i> Total Omset Kotor
                            </th>
                            <td class="text-right font-weight-bold">{{ format_currency($totalOmset) }}</td>
                        </tr>
                        <tr>
                            <th>
                                <i class="cil-money mr-2 text-danger"></i> Total Pengeluaran
                            </th>
                            <td class="text-right text-danger">({{ format_currency($totalPengeluaran) }})</td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <th>
                                <i class="cil-thumb-up mr-2 text-success"></i> Pendapatan Bersih
                            </th>
                            <td class="text-right font-weight-bold {{ $netPos ? 'text-success' : 'text-danger' }}">
                                {{ format_currency($netIncome) }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="cil-credit-card mr-2 text-info"></i> Rincian Penerimaan per Metode/Bank
                            </th>
                            <td>
                                @include('reports::components.payments-summary', ['receipts' => $receipts])
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('page_styles')
<style>
    /* ========== Animations ========== */
    .animated.fadeIn {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    /* ========== Stats Cards ========== */
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .stats-card-blue { border-left-color: #3b82f6; }
    .stats-card-warning { border-left-color: #f59e0b; }
    .stats-card-success { border-left-color: #22c55e; }

    .stats-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .stats-card-blue .stats-icon {
        background: rgba(59, 130, 246, 0.14);
        color: #3b82f6;
    }

    .stats-card-warning .stats-icon {
        background: rgba(245, 158, 11, 0.22);
        color: #f59e0b;
    }

    .stats-card-success .stats-icon {
        background: rgba(34, 197, 94, 0.14);
        color: #22c55e;
    }

    .stats-content {
        flex: 1;
    }

    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .stats-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .stats-sub {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 4px;
    }

    /* ========== Table Styling ========== */
    .table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
    }

    .table tbody td,
    .table tbody th {
        padding: 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== Badge Styling ========== */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }

    /* ========== Responsive ========== */
    @media (max-width: 992px) {
        .stats-value {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .stats-card {
            flex-direction: column;
            text-align: center;
        }
        
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }
    }

    /* ========== Print Styles ========== */
    @media print {
        .card, 
        .card-body, 
        .stats-card { 
            box-shadow: none !important; 
            page-break-inside: avoid;
        }
        
        .btn, 
        form:first-of-type, 
        nav, 
        .breadcrumb, 
        .btn-group { 
            display: none !important; 
        }
        
        body, .table { 
            font-size: 11pt; 
        }
        
        @page { 
            margin: 15mm; 
        }
        
        .stats-card {
            border: 1px solid #ddd;
        }
    }
</style>
@endpush
