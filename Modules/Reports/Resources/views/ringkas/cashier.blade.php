@extends('layouts.app')

@section('title', 'Laporan Kinerja Kasir')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Laporan Tiap Kasir</li>
    </ol>
@endsection

@section('content')
    @php
        // Fallback aman
        $from = $from ?? request('from', now()->startOfMonth()->toDateString());
        $to = $to ?? request('to', now()->toDateString());
        $userId = $userId ?? request('user_id');
        $onlyPaid = isset($onlyPaid) ? $onlyPaid : (bool) request('only_paid', 1);
        $rows = $rows ?? collect();
        $cashiers = $cashiers ?? collect();

        // Agregat
        $totTrx = (int) $rows->sum('trx_count');
        $totOmset = (int) $rows->sum('omset');
        $totHpp = (int) $rows->sum('total_hpp');
        $totProfit = (int) $rows->sum('total_profit');
        $npMargin = $totOmset > 0 ? round(($totProfit / max($totOmset, 1)) * 100, 1) : 0;
    @endphp

    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Filter Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-filter mr-2 text-primary"></i>
                                Filter Laporan
                            </h5>
                            <small class="text-muted">Pilih periode dan kasir untuk analisis kinerja</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('ringkas-report.cashier') }}" class="row align-items-end">
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label for="from" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Dari Tanggal
                            </label>
                            <input type="date" name="from" id="from" value="{{ $from }}"
                                class="form-control">
                        </div>

                        <div class="col-lg-2 col-md-6 mb-3">
                            <label for="to" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Sampai Tanggal
                            </label>
                            <input type="date" name="to" id="to" value="{{ $to }}"
                                class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="user_id" class="form-label font-weight-semibold">
                                <i class="cil-user mr-1 text-muted"></i> Pilih Kasir
                            </label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">— Semua Kasir —</option>
                                @foreach ($cashiers as $c)
                                    <option value="{{ $c->id }}"
                                        {{ (string) $c->id === (string) $userId ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label font-weight-semibold d-block">
                                <i class="cil-options mr-1 text-muted"></i> Filter Status
                            </label>
                            <div class="custom-control custom-checkbox mt-2">
                                <input class="custom-control-input" type="checkbox" name="only_paid" id="only_paid"
                                    value="1" {{ $onlyPaid ? 'checked' : '' }}>
                                <label class="custom-control-label" for="only_paid">
                                    Hanya Lunas
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-12 mb-3">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="cil-filter mr-1"></i> Terapkan
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="cil-print mr-1"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Report Header --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 font-weight-bold">Laporan Kinerja Kasir</h4>
                            <p class="text-muted mb-0">
                                <i class="cil-calendar mr-1"></i>
                                Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }}
                                – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
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
                {{-- Total Transaksi --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-blue">
                        <div class="stats-icon">
                            <i class="cil-user"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Transaksi</div>
                            <div class="stats-value">{{ number_format($totTrx, 0, ',', '.') }}</div>
                            <div class="stats-sub">Semua kasir</div>
                        </div>
                    </div>
                </div>

                {{-- Total Omset --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-indigo">
                        <div class="stats-icon">
                            <i class="cil-wallet"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Omset</div>
                            <div class="stats-value">{{ format_currency($totOmset) }}</div>
                            <div class="stats-sub">Akumulasi penjualan</div>
                        </div>
                    </div>
                </div>

                {{-- Total HPP --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon">
                            <i class="cil-basket"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total HPP</div>
                            <div class="stats-value text-warning">({{ format_currency($totHpp) }})</div>
                            <div class="stats-sub">Harga pokok terjual</div>
                        </div>
                    </div>
                </div>

                {{-- Total Profit --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon">
                            <i class="cil-thumb-up"></i>
                        </div>
                        <div class="stats-content w-100">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <div class="stats-label mb-0">Total Profit</div>
                                <span class="badge badge-light">Net Margin: {{ $npMargin }}%</span>
                            </div>
                            <div class="stats-value {{ $totProfit >= 0 ? 'text-success' : 'text-danger' }} mb-2">
                                {{ format_currency($totProfit) }}
                            </div>

                            {{-- Progress Bar --}}
                            <div class="progress" style="height: 8px; border-radius: 6px;">
                                <div class="progress-bar {{ $totProfit >= 0 ? 'bg-success' : 'bg-danger' }}"
                                    role="progressbar" style="width: {{ min(max(abs($npMargin), 0), 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Table --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="cil-people mr-2 text-primary"></i>
                        Kinerja Individual Kasir
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0" width="30%">Nama Kasir</th>
                                    <th class="border-0 text-center" width="12%">Transaksi</th>
                                    <th class="border-0 text-right" width="18%">Omset</th>
                                    <th class="border-0 text-right" width="18%">HPP</th>
                                    <th class="border-0 text-right" width="22%">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $r)
                                    @php
                                        $p = (int) ($r->total_profit ?? 0);
                                        $m = ($r->omset ?? 0) > 0 ? round(($p / max($r->omset, 1)) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="cashier-avatar mr-2">
                                                    <i class="cil-user"></i>
                                                </div>
                                                <strong>{{ optional($r->user)->name ?? 'Unknown' }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-light">{{ number_format($r->trx_count, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-right font-weight-semibold">{{ format_currency($r->omset) }}</td>
                                        <td class="text-right text-muted">{{ format_currency($r->total_hpp) }}</td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <span
                                                    class="font-weight-bold {{ $p >= 0 ? 'text-success' : 'text-danger' }} mr-2">
                                                    {{ format_currency($p) }}
                                                </span>
                                                <span class="badge badge-light">{{ $m }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                                <p class="mb-0 mt-3">Tidak ada data transaksi</p>
                                                <small>Coba ubah filter periode atau kasir</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (count($rows) > 0)
                                <tfoot style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ number_format($totTrx, 0, ',', '.') }}</th>
                                        <th class="text-right">{{ format_currency($totOmset) }}</th>
                                        <th class="text-right">{{ format_currency($totHpp) }}</th>
                                        <th
                                            class="text-right font-weight-bold {{ $totProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ format_currency($totProfit) }}
                                        </th>
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

        .stats-card-blue {
            border-left-color: #3b82f6;
        }

        .stats-card-indigo {
            border-left-color: #6366f1;
        }

        .stats-card-warning {
            border-left-color: #f59e0b;
        }

        .stats-card-success {
            border-left-color: #22c55e;
        }

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

        .stats-card-indigo .stats-icon {
            background: rgba(99, 102, 241, 0.15);
            color: #6366f1;
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

        /* ========== Cashier Avatar ========== */
        .cashier-avatar {
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

            .cashier-avatar {
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
            .breadcrumb,
            .btn-group {
                display: none !important;
            }

            body,
            .table {
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
