<div wire:key="pl-root">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 font-weight-bold">
                <i class="cil-chart-line mr-2 text-primary"></i>
                Laporan Laba / Rugi
            </h3>
            <p class="text-muted mb-0">Profit & Loss Statement Analysis</p>
        </div>
        <div class="btn-group" role="group">
            <button type="button" wire:click="exportCsv" class="btn btn-outline-primary">
                <i class="cil-cloud-download mr-1"></i> Export
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="cil-print mr-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 font-weight-bold">
                <i class="cil-calendar mr-2 text-primary"></i>
                Pilih Periode Laporan
            </h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="generateReport">
                <div class="row align-items-end">
                    <div class="col-lg-5 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-calendar mr-1 text-muted"></i> Tanggal Mulai
                        </label>
                        <input wire:model="startDate" type="date" class="form-control form-control-lg"
                            style="height: 50px;">
                    </div>

                    <div class="col-lg-5 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-calendar mr-1 text-muted"></i> Tanggal Akhir
                        </label>
                        <input wire:model="endDate" type="date" class="form-control form-control-lg"
                            style="height: 50px;">
                    </div>

                    <div class="col-lg-2 col-md-12 mb-3">
                        <button type="submit"
                            class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center"
                            style="height: 50px;">
                            <span wire:loading.remove wire:target="generateReport">
                                <i class="cil-task mr-1"></i> Generate
                            </span>
                            <span wire:loading wire:target="generateReport"
                                class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </div>

                {{-- Loading Indicator --}}
                <div wire:loading.flex wire:target="generateReport" class="align-items-center mt-3 text-primary">
                    <div class="spinner-border spinner-border-sm mr-2" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span>Menganalisa data keuangan...</span>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Results --}}
    @if ($revenue !== null)
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    {{-- Left: Net Profit Display --}}
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="text-center text-lg-left">
                            <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: 1px;">
                                Laba Bersih Periode Ini
                            </h6>
                            <div class="net-profit-display mb-3">
                                <span class="gradient-text">{{ format_currency($netProfit) }}</span>
                            </div>
                            <div class="profit-breakdown">
                                <div class="breakdown-item">
                                    <span class="breakdown-label">Laba Kotor:</span>
                                    <span class="breakdown-value text-success">
                                        {{ format_currency($grossProfit) }}
                                    </span>
                                </div>
                                <div class="breakdown-item">
                                    <span class="breakdown-label">Beban Operasional:</span>
                                    <span class="breakdown-value text-warning">
                                        {{ format_currency($expenses) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="col-lg-1 d-none d-lg-flex justify-content-center">
                        <div class="vertical-divider"></div>
                    </div>

                    {{-- Right: Financial Details --}}
                    <div class="col-lg-6">
                        <h6 class="text-uppercase text-muted mb-4" style="letter-spacing: 1px;">
                            Rincian Perhitungan
                        </h6>

                        <div class="financial-metrics">
                            {{-- Revenue --}}
                            <div class="metric-item">
                                <div class="metric-icon metric-icon-blue">
                                    <i class="cil-wallet"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Revenue (Pendapatan Bersih)</div>
                                    <div class="metric-value text-primary">
                                        {{ format_currency($revenue) }}
                                    </div>
                                </div>
                            </div>

                            {{-- COGS --}}
                            <div class="metric-item">
                                <div class="metric-icon metric-icon-danger">
                                    <i class="cil-basket"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">COGS (HPP Bersih)</div>
                                    <div class="metric-value text-danger">
                                        ( {{ format_currency($cogs) }} )
                                    </div>
                                </div>
                            </div>

                            {{-- Gross Profit --}}
                            <div class="metric-item">
                                <div class="metric-icon metric-icon-success">
                                    <i class="cil-graph"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Gross Profit (Laba Kotor)</div>
                                    <div class="metric-value text-success">
                                        {{ format_currency($grossProfit) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Expenses --}}
                            <div class="metric-item">
                                <div class="metric-icon metric-icon-warning">
                                    <i class="cil-building"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Operating Expenses (Beban)</div>
                                    <div class="metric-value text-warning">
                                        ( {{ format_currency($expenses) }} )
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Styles --}}
<style>
    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    /* ========== Filter Card Enhancements ========== */
    .form-control-lg {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        background-color: #ffffff;
        border-color: #4834DF;
        box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
    }

    /* ========== Net Profit Display ========== */
    .net-profit-display {
        font-size: clamp(2.5rem, 8vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
    }

    .gradient-text {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ========== Profit Breakdown ========== */
    .profit-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .breakdown-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .breakdown-item:last-child {
        border-bottom: none;
    }

    .breakdown-label {
        font-size: 0.9375rem;
        color: #6c757d;
        font-weight: 500;
    }

    .breakdown-value {
        font-size: 1.125rem;
        font-weight: 700;
    }

    /* ========== Vertical Divider ========== */
    .vertical-divider {
        width: 1px;
        background: linear-gradient(to bottom, transparent, #e9ecef 10%, #e9ecef 90%, transparent);
        height: 100%;
        min-height: 300px;
    }

    /* ========== Financial Metrics ========== */
    .financial-metrics {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .metric-item {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .metric-item:hover {
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateX(4px);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-right: 1rem;
    }

    .metric-icon-blue {
        background: rgba(59, 130, 246, 0.14);
        color: #3b82f6;
    }

    .metric-icon-danger {
        background: rgba(239, 68, 68, 0.14);
        color: #ef4444;
    }

    .metric-icon-success {
        background: rgba(34, 197, 94, 0.14);
        color: #22c55e;
    }

    .metric-icon-warning {
        background: rgba(245, 158, 11, 0.22);
        color: #f59e0b;
    }

    .metric-content {
        flex: 1;
    }

    .metric-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .metric-value {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* ========== Loading Spinner ========== */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }

    /* ========== Responsive ========== */
    @media (max-width: 992px) {
        .net-profit-display {
            font-size: 2.5rem;
        }

        .vertical-divider {
            display: none !important;
        }

        .metric-value {
            font-size: 1.125rem;
        }

        .breakdown-value {
            font-size: 1rem;
        }
    }

    @media (max-width: 768px) {
        .net-profit-display {
            font-size: 2rem;
        }

        .metric-item {
            padding: 1rem;
        }

        .metric-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .profit-breakdown {
            padding: 1rem;
        }
    }

    /* ========== Print Styles ========== */
    @media print {
        body {
            background: #fff !important;
        }

        .btn,
        .btn-group,
        .card-header,
        nav,
        .sidebar {
            display: none !important;
        }

        .card,
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .gradient-text {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            color: #22c55e !important;
        }

        .net-profit-display {
            font-size: 2.5rem;
        }

        @page {
            margin: 15mm;
        }
    }
</style>
