@extends('layouts.app')

@section('title', 'Laporan Ringkasan Harian')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Ringkasan Harian</li>
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
                            <i class="cil-filter mr-2 text-primary"></i>
                            Filter Periode
                        </h5>
                        <small class="text-muted">Pilih rentang tanggal untuk melihat ringkasan</small>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="from" class="form-label font-weight-semibold">
                            <i class="cil-calendar mr-1 text-muted"></i> Dari Tanggal
                        </label>
                        <input type="date" 
                               name="from" 
                               id="from"
                               value="{{ $from }}" 
                               class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="to" class="form-label font-weight-semibold">
                            <i class="cil-calendar mr-1 text-muted"></i> Sampai Tanggal
                        </label>
                        <input type="date" 
                               name="to" 
                               id="to"
                               value="{{ $to }}" 
                               class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold d-block">
                            <i class="cil-options mr-1 text-muted"></i> Filter Status
                        </label>
                        <div class="custom-control custom-checkbox mt-2">
                            <input class="custom-control-input" 
                                   type="checkbox" 
                                   name="only_paid" 
                                   id="only_paid" 
                                   value="1"
                                   {{ request('only_paid', 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="only_paid">
                                Hanya Transaksi Lunas
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="cil-filter mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KPI Summary Cards --}}
        <div class="row mb-4">
            {{-- Total Transaksi --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-purple">
                    <div class="stats-icon">
                        <i class="cil-list"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Transaksi</div>
                        <div class="stats-value">{{ number_format($sum['trx_count']) }}</div>
                        <div class="stats-sub">Jumlah penjualan</div>
                    </div>
                </div>
            </div>

            {{-- Total Omset --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-blue">
                    <div class="stats-icon">
                        <i class="cil-wallet"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Omset</div>
                        <div class="stats-value">{{ format_currency($sum['omset']) }}</div>
                        <div class="stats-sub">Pendapatan kotor</div>
                    </div>
                </div>
            </div>

            {{-- Total HPP --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-danger">
                    <div class="stats-icon">
                        <i class="cil-basket"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total HPP</div>
                        <div class="stats-value text-danger">{{ format_currency($sum['total_hpp']) }}</div>
                        <div class="stats-sub">Harga Pokok Penjualan</div>
                    </div>
                </div>
            </div>

            {{-- Total Profit --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-success">
                    <div class="stats-icon">
                        <i class="cil-thumb-up"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Profit</div>
                        <div class="stats-value text-success">{{ format_currency($sum['total_profit']) }}</div>
                        @php
                            $profitMargin = $sum['omset'] > 0 ? round(($sum['total_profit'] / $sum['omset']) * 100, 1) : 0;
                        @endphp
                        <div class="stats-sub">Margin: {{ $profitMargin }}%</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-chart-line mr-2 text-primary"></i>
                    Ringkasan Keuangan
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr>
                            <th style="width:40%">
                                <i class="cil-list mr-2 text-purple"></i> Total Transaksi
                            </th>
                            <td class="text-right font-weight-bold">{{ number_format($sum['trx_count']) }} transaksi</td>
                        </tr>
                        <tr>
                            <th>
                                <i class="cil-wallet mr-2 text-primary"></i> Total Omset
                            </th>
                            <td class="text-right font-weight-bold">{{ format_currency($sum['omset']) }}</td>
                        </tr>
                        <tr>
                            <th>
                                <i class="cil-basket mr-2 text-danger"></i> Total HPP
                            </th>
                            <td class="text-right text-danger">({{ format_currency($sum['total_hpp']) }})</td>
                        </tr>
                        <tr style="background-color: #f0fdf4;">
                            <th>
                                <i class="cil-thumb-up mr-2 text-success"></i> Total Profit
                            </th>
                            <td class="text-right font-weight-bold text-success">{{ format_currency($sum['total_profit']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Method Breakdown --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-credit-card mr-2 text-primary"></i>
                    Rincian per Metode Pembayaran
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0">Metode Pembayaran</th>
                                <th class="border-0 text-center">Jumlah Transaksi</th>
                                <th class="border-0 text-right">Total Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($byMethod as $m)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="method-icon mr-2">
                                            <i class="cil-credit-card"></i>
                                        </div>
                                        <strong>{{ $m->payment_method ?: 'Tidak Diketahui' }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ number_format($m->count) }} transaksi</span>
                                </td>
                                <td class="text-right font-weight-bold">{{ format_currency($m->amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3">Tidak ada data transaksi</p>
                                        <small>Coba ubah filter tanggal atau status</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if(count($byMethod) > 0)
                        <tfoot style="background-color: #f8f9fa;">
                            <tr>
                                <th>Total</th>
                                <th class="text-center">{{ number_format($byMethod->sum('count')) }} transaksi</th>
                                <th class="text-right">{{ format_currency($byMethod->sum('amount')) }}</th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
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

    .stats-card-purple { border-left-color: #8b5cf6; }
    .stats-card-blue { border-left-color: #3b82f6; }
    .stats-card-danger { border-left-color: #ef4444; }
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

    .stats-card-purple .stats-icon {
        background: rgba(139, 92, 246, 0.14);
        color: #8b5cf6;
    }

    .stats-card-blue .stats-icon {
        background: rgba(59, 130, 246, 0.14);
        color: #3b82f6;
    }

    .stats-card-danger .stats-icon {
        background: rgba(239, 68, 68, 0.14);
        color: #ef4444;
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

    /* ========== Method Icon ========== */
    .method-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        flex-shrink: 0;
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

    /* ========== Custom Checkbox ========== */
    .custom-control-label {
        font-weight: 500;
    }

    /* ========== Color Utilities ========== */
    .text-purple { color: #8b5cf6 !important; }

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
        
        .method-icon {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
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
        form, 
        nav, 
        .breadcrumb { 
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
