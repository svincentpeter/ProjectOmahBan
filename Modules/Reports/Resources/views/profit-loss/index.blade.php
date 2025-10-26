@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Laba Rugi</li>
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
                            Filter Periode
                        </h5>
                        <small class="text-muted">Pilih rentang tanggal untuk melihat laporan</small>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('reports.profit_loss.generate') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        {{-- Tanggal Awal --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="start_date" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Tanggal Awal
                            </label>
                            <input type="date" 
                                   id="start_date" 
                                   name="start_date"
                                   value="{{ old('start_date', $startDate) }}"
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   required>
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Akhir --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="end_date" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Tanggal Akhir
                            </label>
                            <input type="date" 
                                   id="end_date" 
                                   name="end_date"
                                   value="{{ old('end_date', $endDate) }}"
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   required>
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-6 col-md-12 mb-3">
                            <label class="form-label font-weight-semibold d-block">&nbsp;</label>
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="cil-chart mr-1"></i> Tampilkan Laporan
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="cil-print mr-1"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Preset Buttons --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="quick-filters">
                                <a href="{{ route('reports.profit_loss.index', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" 
                                   class="filter-pill">
                                    <i class="cil-calendar-check"></i>
                                    <span>Hari Ini</span>
                                </a>

                                <a href="{{ route('reports.profit_loss.index', ['start_date' => now()->subDays(6)->toDateString(), 'end_date' => now()->toDateString()]) }}" 
                                   class="filter-pill">
                                    <i class="cil-calendar"></i>
                                    <span>7 Hari Terakhir</span>
                                </a>

                                <a href="{{ route('reports.profit_loss.index', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}" 
                                   class="filter-pill">
                                    <i class="cil-calendar"></i>
                                    <span>Bulan Ini</span>
                                </a>

                                <a href="{{ route('reports.profit_loss.index', ['start_date' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'end_date' => now()->subMonthNoOverflow()->endOfMonth()->toDateString()]) }}" 
                                   class="filter-pill">
                                    <i class="cil-calendar"></i>
                                    <span>Bulan Lalu</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($generated))
        @php
            $gpMargin = ($revenue ?? 0) > 0 ? round(($grossProfit / max($revenue,1)) * 100, 1) : 0;
            $npMargin = ($revenue ?? 0) > 0 ? round(($netProfit   / max($revenue,1)) * 100, 1) : 0;
            $gpPos    = ($grossProfit ?? 0) >= 0;
            $npPos    = ($netProfit   ?? 0) >= 0;
        @endphp

        {{-- Report Header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Laporan Laba Rugi</h4>
                        <p class="text-muted mb-0">
                            <i class="cil-calendar mr-1"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                            – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <small class="text-muted d-block">Generated at</small>
                        <strong>{{ now()->translatedFormat('d M Y, H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards Row 1: Revenue, COGS, Gross Profit --}}
        <div class="row mb-4">
            {{-- Revenue --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-blue">
                    <div class="stats-icon">
                        <i class="cil-wallet"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Pendapatan (Revenue)</div>
                        <div class="stats-value">{{ format_currency($revenue) }}</div>
                        <div class="stats-sub">Total penjualan produk</div>
                    </div>
                </div>
            </div>

            {{-- COGS --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-danger">
                    <div class="stats-icon">
                        <i class="cil-basket"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">HPP / COGS</div>
                        <div class="stats-value text-danger">( {{ format_currency($cogs) }} )</div>
                        <div class="stats-sub">Harga Pokok Penjualan</div>
                    </div>
                </div>
            </div>

            {{-- Gross Profit --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-teal">
                    <div class="stats-icon">
                        <i class="cil-graph"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Laba Kotor</div>
                        <div class="stats-value {{ $gpPos ? 'text-success' : 'text-danger' }}">
                            {{ format_currency($grossProfit) }}
                        </div>
                        <div class="stats-sub">
                            Margin Kotor: <strong>{{ $gpMargin }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards Row 2: Operating Expenses, Net Profit --}}
        <div class="row mb-4">
            {{-- Operating Expenses --}}
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card stats-card-warning">
                    <div class="stats-icon">
                        <i class="cil-building"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Beban Operasional</div>
                        <div class="stats-value text-warning">( {{ format_currency($operatingExpenses) }} )</div>
                        <div class="stats-sub">Biaya operasional usaha</div>
                    </div>
                </div>
            </div>

            {{-- Net Profit --}}
            <div class="col-lg-8 col-md-6 mb-3">
                <div class="stats-card stats-card-success">
                    <div class="stats-icon">
                        <i class="cil-thumb-up"></i>
                    </div>
                    <div class="stats-content w-100">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <div class="stats-label mb-0">Laba Bersih Sebelum Pajak</div>
                            <span class="badge badge-light">Net Margin: {{ $npMargin }}%</span>
                        </div>
                        <div class="stats-value {{ $npPos ? 'text-success' : 'text-danger' }} mb-2">
                            {{ format_currency($netProfit) }}
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="progress" style="height: 8px; border-radius: 6px;">
                            <div class="progress-bar {{ $npPos ? 'bg-success' : 'bg-danger' }}"
                                 role="progressbar"
                                 style="width: {{ min(max(abs($npMargin),0),100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-list mr-2 text-primary"></i>
                    Ringkasan Perhitungan
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr>
                            <th class="summary-label">
                                <i class="cil-wallet mr-2 text-primary"></i> Pendapatan (Revenue)
                            </th>
                            <td class="text-right font-weight-bold">{{ format_currency($revenue) }}</td>
                        </tr>
                        <tr>
                            <th class="summary-label">
                                <i class="cil-basket mr-2 text-danger"></i> Harga Pokok Penjualan (HPP / COGS)
                            </th>
                            <td class="text-right text-danger">({{ format_currency($cogs) }})</td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <th class="summary-label">
                                <i class="cil-graph mr-2" style="color: #14b8a6;"></i> Laba Kotor (Gross Profit)
                            </th>
                            <td class="text-right font-weight-bold {{ $gpPos ? 'text-success' : 'text-danger' }}">
                                {{ format_currency($grossProfit) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="summary-label">
                                <i class="cil-building mr-2 text-warning"></i> Beban Operasional
                            </th>
                            <td class="text-right text-warning">({{ format_currency($operatingExpenses) }})</td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <th class="summary-label">
                                <i class="cil-thumb-up mr-2 text-success"></i> Laba Bersih Sebelum Pajak
                            </th>
                            <td class="text-right font-weight-bold {{ $npPos ? 'text-success' : 'text-danger' }}">
                                {{ format_currency($netProfit) }}
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
    .stats-card-danger { border-left-color: #ef4444; }
    .stats-card-teal { border-left-color: #14b8a6; }
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

    .stats-card-danger .stats-icon {
        background: rgba(239, 68, 68, 0.14);
        color: #ef4444;
    }

    .stats-card-teal .stats-icon {
        background: rgba(20, 184, 166, 0.14);
        color: #14b8a6;
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

    /* ========== Quick Filter Pills ========== */
    .quick-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        color: #4f5d73;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .filter-pill i {
        margin-right: 8px;
        font-size: 1rem;
    }

    .filter-pill:hover {
        border-color: #4834DF;
        color: #4834DF;
        background: #f8f7ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 52, 223, 0.15);
        text-decoration: none;
    }

    /* ========== Summary Table ========== */
    .summary-label {
        width: 60%;
        font-weight: 600;
        font-size: 0.9375rem;
        padding: 14px 16px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.02) !important;
    }

    /* ========== Responsive ========== */
    @media (max-width: 992px) {
        .stats-value {
            font-size: 1.25rem;
        }
        
        .quick-filters {
            flex-direction: column;
        }
        
        .filter-pill {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .stats-card {
            flex-direction: column;
            text-align: center;
        }
        
        .summary-label {
            width: auto;
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
        .btn-group,
        .quick-filters,
        .filter-pill { 
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
