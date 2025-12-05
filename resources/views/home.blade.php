{{-- resources/views/home.blade.php / dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">Dashboard</li>
        <li class="breadcrumb-item text-muted">{{ now()->format('d F Y') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- =========================
                 WELCOME BANNER
            ========================== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="welcome-banner gradient-banner shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 font-weight-bold text-white">
                                    <i class="cil-speedometer mr-2"></i>
                                    Selamat Datang, {{ Auth::user()->name }}! 👋
                                </h4>
                                <p class="mb-0 text-white-50">
                                    <i class="cil-calendar mr-1"></i>
                                    {{ now()->isoFormat('dddd, D MMMM Y') }} ·
                                    <i class="cil-clock ml-2 mr-1"></i>
                                    {{ now()->format('H:i') }} WIB
                                </p>
                            </div>
                            <div class="d-none d-md-block">
                                <i class="cil-chart-line" style="font-size: 4rem; opacity: 0.2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================
                 SUMMARY CARDS
            ========================== --}}
            @can('show_total_stats')
                <div class="row mb-4">
                    {{-- TOTAL PENJUALAN --}}
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="stat-icon bg-primary-light text-primary">
                                        <i class="cil-dollar"></i>
                                    </div>
                                    <span class="badge badge-success badge-sm">
                                        <i class="cil-arrow-top mr-1"></i>+12%
                                    </span>
                                </div>
                                <h6 class="text-muted mb-2 font-weight-normal">
                                    <i class="cil-chart-line mr-1"></i> Total Penjualan
                                </h6>
                                <h3 class="mb-0 font-weight-bold text-primary">
                                    {{ format_currency($revenue) }}
                                </h3>
                                <small class="text-muted">
                                    <i class="cil-calendar mr-1"></i>
                                    Periode: {{ now()->format('F Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- KEUNTUNGAN BERSIH --}}
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="stat-icon bg-success-light text-success">
                                        <i class="cil-graph"></i>
                                    </div>
                                    <span class="badge badge-success badge-sm">
                                        <i class="cil-arrow-top mr-1"></i>+8%
                                    </span>
                                </div>
                                <h6 class="text-muted mb-2 font-weight-normal">
                                    <i class="cil-trophy mr-1"></i> Keuntungan Bersih
                                </h6>
                                <h3 class="mb-0 font-weight-bold text-success">
                                    {{ format_currency($profit) }}
                                </h3>
                                <small class="text-muted">
                                    <i class="cil-check-circle mr-1"></i>
                                    Margin:
                                    {{ $revenue > 0 ? number_format(($profit / $revenue) * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- TOTAL PRODUK --}}
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="stat-icon bg-info-light text-info">
                                        <i class="cil-list-rich"></i>
                                    </div>
                                    <span class="badge badge-info badge-sm">
                                        <i class="cil-check mr-1"></i>Aktif
                                    </span>
                                </div>
                                <h6 class="text-muted mb-2 font-weight-normal">
                                    <i class="cil-loop-circular mr-1"></i> Produk Tersimpan
                                </h6>
                                <h3 class="mb-0 font-weight-bold text-info">
                                    {{ $products }}
                                </h3>
                                <small class="text-muted">
                                    <i class="cil-info mr-1"></i>
                                    Total produk di database
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- KATEGORI PRODUK --}}
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="stat-icon bg-warning-light text-warning">
                                        <i class="cil-tags"></i>
                                    </div>
                                    <span class="badge badge-warning badge-sm">
                                        <i class="cil-grid mr-1"></i>Kategori
                                    </span>
                                </div>
                                <h6 class="text-muted mb-2 font-weight-normal">
                                    <i class="cil-folder mr-1"></i> Total Kategori
                                </h6>
                                <h3 class="mb-0 font-weight-bold text-warning">
                                    {{ $categories }}
                                </h3>
                                <small class="text-muted">
                                    <i class="cil-info mr-1"></i>
                                    Kategori produk/layanan
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- =========================
                 CHART SECTION (ROW 1)
            ========================== --}}
            @can('show_weekly_sales_purchases|show_month_overview')
                <div class="row mb-4">
                    {{-- LINE CHART: 7 HARI TERAKHIR --}}
                    @can('show_weekly_sales_purchases')
                        <div class="col-lg-8 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="cil-chart-line mr-2 text-primary"></i>
                                            Penjualan & Pembelian (7 Hari Terakhir)
                                        </h6>
                                        <span class="badge badge-primary badge-sm">Live Data</span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="chart-container" style="position: relative; height: 280px;">
                                        <canvas id="salesPurchasesChart"></canvas>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="cil-info mr-1"></i>
                                        Data ditarik otomatis dari transaksi penjualan & pembelian harian.
                                    </small>
                                </div>
                                <div class="card-footer bg-light border-top py-3">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="legend-dot bg-primary mr-2"></span>
                                                <small class="text-muted font-weight-semibold">Penjualan Ban & Velg</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="legend-dot bg-warning mr-2"></span>
                                                <small class="text-muted font-weight-semibold">Pembelian Stok</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- DOUGHNUT / PIE: RINGKASAN BULAN BERJALAN --}}
                    @can('show_month_overview')
                        <div class="col-lg-4 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="cil-pie-chart mr-2 text-success"></i>
                                        Ringkasan {{ now()->format('F Y') }}
                                    </h6>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                                    <div class="chart-container mb-3"
                                         style="position: relative; height:220px; width:220px">
                                        <canvas id="currentMonthChart"></canvas>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot bg-success mr-2"></span>
                                                <small class="font-weight-semibold">Penjualan</small>
                                            </div>
                                            <small class="font-weight-bold text-success">
                                                {{ format_currency($revenue) }}
                                            </small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot bg-danger mr-2"></span>
                                                <small class="font-weight-semibold">Pembelian</small>
                                            </div>
                                            <small class="font-weight-bold text-danger">
                                                {{ format_currency($profit) }}
                                                {{-- Di sini bisa kamu ganti ke total pembelian kalau variabelnya ada --}}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            @endcan

            {{-- =========================
                 CHART SECTION (ROW 2) – CASH FLOW
            ========================== --}}
            @can('show_monthly_cashflow')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="cil-wallet mr-2 text-info"></i>
                                        Arus Kas Bulanan (Pemasukan & Pengeluaran)
                                    </h6>
                                    <div>
                                        <span class="badge badge-success mr-2">
                                            <i class="cil-arrow-bottom mr-1"></i>Pemasukan
                                        </span>
                                        <span class="badge badge-danger">
                                            <i class="cil-arrow-top mr-1"></i>Pengeluaran
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="chart-container" style="position: relative; height: 280px;">
                                    <canvas id="paymentChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer bg-light border-top py-3">
                                <div class="row text-center">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-muted d-block mb-1">Total Pemasukan</small>
                                        <h6 class="mb-0 font-weight-bold text-success">
                                            <i class="cil-arrow-bottom mr-1"></i>
                                            {{ format_currency($revenue) }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-muted d-block mb-1">Total Pengeluaran</small>
                                        <h6 class="mb-0 font-weight-bold text-danger">
                                            <i class="cil-arrow-top mr-1"></i>
                                            {{ format_currency($profit) }}
                                            {{-- Ganti dengan total pengeluaran kalau sudah ada variabelnya --}}
                                        </h6>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block mb-1">Saldo Bersih</small>
                                        <h6 class="mb-0 font-weight-bold text-primary">
                                            <i class="cil-graph mr-1"></i>
                                            {{ format_currency($profit) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
    </div>
@endsection

{{-- =========================
     THIRD-PARTY SCRIPTS (Chart.js)
========================= --}}
@section('third_party_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js"
            integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

{{-- =========================
     PAGE SCRIPTS (config Chart)
========================= --}}
@push('page_scripts')
    @vite('resources/js/chart-config.js')
@endpush

@push('page_styles')
    <style>
        /* ========== Animations ========== */
        .animated.fadeIn {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ========== Welcome Banner ========== */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .gradient-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.05); }
        }

        /* ========== Stat Cards ========== */
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .bg-primary-light { background-color: rgba(50, 31, 219, 0.1); }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
        .bg-info-light    { background-color: rgba(23, 162, 184, 0.1); }

        /* ========== Card & Shadow ========== */
        .card {
            border-radius: 12px;
            overflow: hidden;
        }
        .shadow-sm {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Chart Legend ========== */
        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        /* ========== Badges ========== */
        .badge-sm {
            font-size: 0.7rem;
            padding: 0.35rem 0.6rem;
            font-weight: 600;
        }

        /* ========== Chart Containers ========== */
        .chart-container {
            position: relative;
            width: 100%;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .welcome-banner {
                padding: 1.5rem;
            }
            .welcome-banner h4 {
                font-size: 1.25rem;
            }
            .stat-card {
                margin-bottom: 1rem;
            }
            .d-none.d-md-block {
                display: none !important;
            }
        }
    </style>
@endpush
