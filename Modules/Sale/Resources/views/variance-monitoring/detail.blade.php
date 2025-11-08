@extends('layouts.app')

@section('title', 'Detail Deviasi Harga')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sale.variance-monitoring.index') }}">Monitoring Deviasi</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- ===== Header Card (seragam dengan halaman Jasa) ===== --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Detail Deviasi Harga
                            </h5>
                            <small class="text-muted">Ringkasan deviasi untuk: <strong
                                    class="text-dark">{{ $varianceLog->item_name }}</strong></small>
                        </div>

                        <div class="btn-group" role="group">
                            <a href="{{ route('sale.variance-monitoring.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-arrow-left mr-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===== Ringkas (chips) ===== --}}
                @php
                    $chg = $varianceLog->variance_amount;
                    $pct = $varianceLog->variance_percent;
                    $up = $pct > 0;
                    $clr = $up ? 'danger' : 'success';
                    $icon = $up ? 'cil-arrow-top' : 'cil-arrow-bottom';
                @endphp
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="stats-card stats-card-success">
                                <div class="stats-icon"><i class="cil-dollar"></i></div>
                                <div class="stats-content">
                                    <div class="stats-label">Harga Master</div>
                                    <div class="stats-value">{{ format_currency($varianceLog->master_price) }}</div>
                                    <small class="text-muted">Harga standar jasa</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="stats-card stats-card-warning">
                                <div class="stats-icon"><i class="cil-cash"></i></div>
                                <div class="stats-content">
                                    <div class="stats-label">Harga Input</div>
                                    <div class="stats-value">{{ format_currency($varianceLog->input_price) }}</div>
                                    <small class="text-muted">Harga yang dimasukkan kasir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="stats-card {{ $up ? 'stats-card-danger' : 'stats-card-success' }}">
                                <div class="stats-icon"><i class="{{ $icon }}"></i></div>
                                <div class="stats-content">
                                    <div class="stats-label">Deviasi</div>
                                    <div class="stats-value">
                                        {{ $up ? '+' : '' }}{{ format_currency($chg) }}
                                    </div>
                                    <small class="text-muted">({{ $pct }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Konten 2 kolom ===== --}}
            <div class="row">
                {{-- Kiri: Detail & Aksi --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0">
                                <i class="cil-clipboard mr-2 text-primary"></i>
                                Informasi Transaksi & Harga
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">Informasi Transaksi</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th width="160">ID Transaksi</th>
                                            <td>{{ $varianceLog->sale_id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Input</th>
                                            <td>{{ $varianceLog->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kasir</th>
                                            <td>{{ $varianceLog->cashier->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Jasa</th>
                                            <td><strong>{{ $varianceLog->item_name }}</strong></td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">Informasi Harga</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th width="160">Harga Master</th>
                                            <td class="text-success font-weight-bold">
                                                {{ format_currency($varianceLog->master_price) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Harga Input</th>
                                            <td class="text-danger font-weight-bold">
                                                {{ format_currency($varianceLog->input_price) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Selisih</th>
                                            <td>
                                                <span class="badge bg-{{ $clr }} px-2 py-1"
                                                    style="font-size:0.95rem">
                                                    {{ $up ? '+' : '' }}{{ format_currency($chg) }}
                                                    ({{ $pct }}%)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Level Deviasi</th>
                                            <td>
                                                @if ($varianceLog->variance_level === 'critical')
                                                    <span class="badge bg-danger">🚨 CRITICAL</span>
                                                @elseif($varianceLog->variance_level === 'warning')
                                                    <span class="badge bg-warning text-dark">⚠️ WARNING</span>
                                                @else
                                                    <span class="badge bg-info">ℹ️ Minor</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <h6 class="border-bottom pb-2 mb-3">Informasi Approval</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="160">Alasan Kasir</th>
                                    <td>
                                        @if ($varianceLog->reason_provided)
                                            <p class="mb-0 p-2 bg-light rounded">{{ $varianceLog->reason_provided }}</p>
                                        @else
                                            <span class="text-muted">Tidak ada alasan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Approval</th>
                                    <td>
                                        @if ($varianceLog->approval_status === 'pending')
                                            <span class="badge bg-warning text-dark">⏳ Pending</span>
                                        @elseif($varianceLog->approval_status === 'approved')
                                            <span class="badge bg-success">✅ Approved</span>
                                        @else
                                            <span class="badge bg-danger">❌ Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($varianceLog->approved_by)
                                    <tr>
                                        <th>Disetujui oleh</th>
                                        <td>{{ $varianceLog->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Approval</th>
                                        <td>{{ $varianceLog->approved_at?->format('d M Y H:i') ?? '-' }}</td>
                                    </tr>
                                @endif
                            </table>

                            {{-- Aksi (hanya saat pending) --}}
                            @if ($varianceLog->approval_status === 'pending')
                                <div class="mt-3 pt-3 border-top">
                                    <form action="{{ route('sale.variance-monitoring.approve', $varianceLog->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Setujui deviasi ini?')">
                                            <i class="cil-check mr-2"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('sale.variance-monitoring.reject', $varianceLog->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Tolak deviasi ini?')">
                                            <i class="cil-x mr-2"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kanan: Sidebar Info --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Level Deviasi
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong class="text-info">Minor:</strong> Deviasi &lt; 30%<br>
                                    <small class="text-muted">Tidak perlu approval khusus</small>
                                </li>
                                <li class="mb-2">
                                    <strong class="text-warning">Warning:</strong> Deviasi 30% – 50%<br>
                                    <small class="text-muted">Perlu alasan dari kasir</small>
                                </li>
                                <li>
                                    <strong class="text-danger">Critical:</strong> Deviasi &gt; 50%<br>
                                    <small class="text-muted">Perlu alasan + PIN Supervisor</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> {{-- /row --}}

        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        .animated.fadeIn {
            animation: fadeIn .3s ease-in
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important
        }

        /* stats-card: disamakan dengan halaman Jasa */
        .stats-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            transition: .3s;
            border-left: 4px solid
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12)
        }

        .stats-card-success {
            border-left-color: #2eb85c
        }

        .stats-card-warning {
            border-left-color: #f9b115
        }

        .stats-card-danger {
            border-left-color: #e55353
        }

        .stats-card-info {
            border-left-color: #39f
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
            color: #fff
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%)
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%)
        }

        .stats-card-danger .stats-icon {
            background: linear-gradient(135deg, #e55353 0%, #ff7b7b 100%)
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%)
        }

        .stats-content {
            flex: 1
        }

        .stats-label {
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: .5px;
            margin-bottom: 4px
        }

        .stats-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1
        }
    </style>
@endpush
