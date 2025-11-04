@extends('layouts.app')

@section('title', "History Jasa: {$serviceMaster->service_name}")

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('service-masters.index') }}">Jasa</a></li>
        <li class="breadcrumb-item active">History</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- HEADER SECTION --}}
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        {{-- Tombol Kembali --}}
                        <a href="{{ route('service-masters.index') }}" class="btn btn-secondary"
                            title="Kembali ke daftar jasa">
                            <i class="cil-arrow-left mr-1"></i> Kembali
                        </a>

                        {{-- Header Info --}}
                        <div>
                            <h2 class="mb-1">
                                <i class="cil-history mr-2 text-primary"></i>
                                History Perubahan Harga
                            </h2>
                            <h5 class="text-primary font-weight-bold mb-2">
                                {{ $serviceMaster->service_name }}
                            </h5>
                            <small class="text-muted">
                                Harga saat ini:
                                <strong class="text-success">{{ format_currency($serviceMaster->standard_price) }}</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="row">

                {{-- TIMELINE AUDIT LOG (LEFT SIDE) --}}
                <div class="col-lg-8">
                    @forelse($audits as $audit)
                        <div class="card audit-card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    {{-- ICON --}}
                                    <div class="col-auto text-center">
                                        <div class="timeline-badge rounded-circle">
                                            <i class="cil-pencil text-white fa-lg"></i>
                                        </div>
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="col flex-grow-1">
                                        <h6 class="card-title mb-1 font-weight-bold">
                                            Perubahan Harga
                                            <span class="badge badge-soft-info ml-2" title="Tanggal perubahan">
                                                <i class="cil-clock mr-1"></i>{{ $audit->created_at->format('d M Y H:i') }}
                                            </span>
                                        </h6>

                                        {{-- Price boxes --}}
                                        @php
                                            $change = $audit->new_price - $audit->old_price;
                                            $percent = $audit->old_price > 0 ? ($change / $audit->old_price) * 100 : 0;
                                            $isUp = $change > 0;
                                        @endphp

                                        <div class="mt-3 mb-3">
                                            <div class="row text-center align-items-stretch">
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <div class="price-box price-old">
                                                        <small class="label">Harga Lama</small>
                                                        <div class="value value-danger">
                                                            {{ format_currency($audit->old_price) }}</div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                                                    <div class="arrow-right" aria-hidden="true"><i
                                                            class="cil-arrow-right"></i></div>
                                                </div>

                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <div class="price-box price-new">
                                                        <small class="label">Harga Baru</small>
                                                        <div class="value value-success">
                                                            {{ format_currency($audit->new_price) }}</div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="price-box price-delta {{ $isUp ? 'up' : 'down' }}">
                                                        <small class="label">Perubahan</small>
                                                        <div class="value">
                                                            <i
                                                                class="{{ $isUp ? 'cil-arrow-top' : 'cil-arrow-bottom' }} mr-1"></i>
                                                            {{ $isUp ? '+' : '' }}{{ format_currency($change) }}
                                                        </div>
                                                        <small class="percent">{{ number_format($percent, 1) }}%</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-2">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block mb-1"><i class="cil-user mr-1"></i>Diubah
                                                    oleh:</small>
                                                <strong
                                                    class="text-dark">{{ $audit->changedBy->name ?? 'System' }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block mb-1"><i
                                                        class="cil-info mr-1"></i>Alasan:</small>
                                                <em class="text-muted">{{ $audit->reason ?? '(Tidak ada alasan)' }}</em>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State --}}
                        <div class="alert alert-info alert-icon alert-icon-border rounded-1">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                                        <path d="M12 13v.01" />
                                        <path d="M15 16h.01" />
                                        <path d="M9 16h.01" />
                                    </svg>
                                </div>
                                <div class="ms-3">
                                    <h4 class="alert-title">Belum Ada History</h4>
                                    <div class="text-secondary">
                                        Jasa ini belum pernah diubah. Perubahan harga akan tercatat otomatis di halaman ini.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse

                    {{-- PAGINATION --}}
                    @if ($audits->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $audits->links() }}
                        </div>
                    @endif
                </div>

                {{-- SIDEBAR INFO (RIGHT SIDE) --}}
                <div class="col-lg-4">

                    {{-- Card: Informasi Jasa --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Jasa
                            </h6>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">

                                {{-- Nama Jasa --}}
                                <dt class="col-sm-5 font-weight-semibold">Nama Jasa:</dt>
                                <dd class="col-sm-7 mb-2">
                                    <strong class="text-dark">{{ $serviceMaster->service_name }}</strong>
                                </dd>

                                {{-- Harga Saat Ini --}}
                                <dt class="col-sm-5 font-weight-semibold">Harga Saat Ini:</dt>
                                <dd class="col-sm-7 mb-2">
                                    <strong class="text-success">
                                        {{ format_currency($serviceMaster->standard_price) }}
                                    </strong>
                                </dd>

                                {{-- Kategori --}}
                                <dt class="col-sm-5 font-weight-semibold">Kategori:</dt>
                                <dd class="col-sm-7 mb-2">
                                    @switch($serviceMaster->category)
                                        @case('service')
                                            <span class="badge badge-soft-info"><i class="cil-settings mr-1"></i>Service</span>
                                        @break

                                        @case('goods')
                                            <span class="badge badge-soft-warning"><i class="cil-box mr-1"></i>Goods</span>
                                        @break

                                        @case('custom')
                                            <span class="badge badge-soft-secondary"><i class="cil-star mr-1"></i>Custom</span>
                                        @break
                                    @endswitch
                                </dd>

                                {{-- Status --}}
                                <dt class="col-sm-5 font-weight-semibold">Status:</dt>
                                <dd class="col-sm-7 mb-2">
                                    @if ($serviceMaster->status)
                                        <span class="badge badge-soft-success"><i
                                                class="cil-check-circle mr-1"></i>Aktif</span>
                                    @else
                                        <span class="badge badge-soft-danger"><i
                                                class="cil-x-circle mr-1"></i>Nonaktif</span>
                                    @endif
                                </dd>


                                {{-- Terdaftar Sejak --}}
                                <dt class="col-sm-5 font-weight-semibold">Terdaftar:</dt>
                                <dd class="col-sm-7 mb-2">
                                    <small class="text-muted">
                                        {{ $serviceMaster->created_at->format('d M Y H:i') }}
                                    </small>
                                </dd>

                                {{-- Perubahan Terakhir --}}
                                <dt class="col-sm-5 font-weight-semibold">Ubah Terakhir:</dt>
                                <dd class="col-sm-7">
                                    <small class="text-muted">
                                        {{ $serviceMaster->updated_at->format('d M Y H:i') }}
                                    </small>
                                </dd>

                            </dl>
                        </div>
                    </div>

                    {{-- Card: Statistik --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0">
                                <i class="cil-bar-chart mr-2 text-success"></i>
                                Statistik
                            </h6>
                        </div>
                        <div class="card-body">

                            {{-- Total Perubahan --}}
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="cil-list mr-1"></i>Total Perubahan:
                                </small>
                                <h4 class="mb-0">
                                    <span class="badge bg-success">{{ $audits->total() }} kali</span>
                                </h4>
                            </div>

                            {{-- Terakhir Diubah --}}
                            <div class="mb-0">
                                <small class="text-muted d-block mb-1">
                                    <i class="cil-clock mr-1"></i>Terakhir Diubah:
                                </small>
                                <small class="text-muted">
                                    @if ($serviceMaster->price_updated_at)
                                        <strong>{{ $serviceMaster->price_updated_at->diffForHumans() }}</strong>
                                    @else
                                        <em>(Belum pernah diubah)</em>
                                    @endif
                                </small>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ====== Layout & basics ====== */
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

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem
        }

        .breadcrumb-item.active {
            color: #6c757d
        }

        .gap-3 {
            gap: 1rem
        }

        /* ====== Card accent (tanpa border-4 yang tak ada di BS4) ====== */
        .audit-card {
            position: relative;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: .25s;
            background: #fff
        }

        .audit-card:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 12px 0 0 12px;
            background: linear-gradient(180deg, #6a11cb 0%, #2575fc 100%);
        }

        .audit-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12)
        }

        /* ====== Timeline badge ====== */
        .timeline-badge {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            box-shadow: 0 4px 15px rgba(72, 52, 223, .3)
        }

        @media (max-width:768px) {
            .timeline-badge {
                width: 48px;
                height: 48px
            }
        }

        /* ====== Price boxes ====== */
        .price-box {
            height: 100%;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #e6e6e6;
            background: #fafafa;
            transition: .2s
        }

        .price-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08)
        }

        .price-box .label {
            color: #6c757d;
            font-size: .8rem;
            margin-bottom: .25rem;
            display: block
        }

        .value {
            font-weight: 700;
            font-size: 1.15rem;
            line-height: 1.1;
            font-variant-numeric: tabular-nums
        }

        .value-success {
            color: #1e7e34
        }

        .value-danger {
            color: #c82333
        }

        /* Old/New with soft backgrounds */
        .price-old {
            background: #fff5f5;
            border-color: rgba(220, 53, 69, .35)
        }

        .price-new {
            background: #f3fff5;
            border-color: rgba(40, 167, 69, .35)
        }

        .price-delta {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center
        }

        .price-delta.up {
            background: #fff5f5;
            border-color: rgba(220, 53, 69, .35);
            color: #c82333
        }

        .price-delta.down {
            background: #f3fff5;
            border-color: rgba(40, 167, 69, .35);
            color: #1e7e34
        }

        .price-delta .value {
            font-size: 1.05rem
        }

        .price-delta .percent {
            opacity: .9
        }

        .arrow-right {
            font-size: 1.6rem;
            color: #f0ad4e
        }

        /* kontras tinggi */

        /* ====== Soft badges (BS4 compatible) ====== */
        .badge-soft-primary {
            background: rgba(0, 123, 255, .1);
            color: #0056b3;
            border: 1px solid rgba(0, 123, 255, .25)
        }

        .badge-soft-info {
            background: rgba(23, 162, 184, .12);
            color: #0b7285;
            border: 1px solid rgba(23, 162, 184, .25)
        }

        .badge-soft-success {
            background: rgba(40, 167, 69, .12);
            color: #1e7e34;
            border: 1px solid rgba(40, 167, 69, .25)
        }

        .badge-soft-warning {
            background: rgba(255, 193, 7, .15);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, .35)
        }

        .badge-soft-secondary {
            background: rgba(108, 117, 125, .12);
            color: #495057;
            border: 1px solid rgba(108, 117, 125, .25)
        }

        .badge-soft-danger {
            background: rgba(220, 53, 69, .12);
            color: #c82333;
            border: 1px solid rgba(220, 53, 69, .25)
        }

        .badge {
            border-radius: 8px;
            padding: .35rem .55rem;
            font-weight: 600
        }

        /* ====== Info alert (kontras lebih tinggi) ====== */
        .alert-info {
            background: #e7f3ff;
            border-color: #90caf9;
            color: #0b3c8a
        }

        .alert-info .alert-title {
            color: #0b3c8a
        }

        /* ====== Sidebar dl align ====== */
        dl.row dt {
            color: #6c757d
        }

        dl.row dd strong {
            font-weight: 700
        }
    </style>
@endpush
