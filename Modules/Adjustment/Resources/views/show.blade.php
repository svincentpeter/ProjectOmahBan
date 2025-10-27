@extends('layouts.app')

@section('title', 'Detail Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active">{{ $adjustment->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Action Bar --}}
            <div class="action-bar shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 font-weight-bold">
                            <i class="cil-file-alt mr-2 text-primary"></i>
                            Detail Penyesuaian: {{ $adjustment->reference }}
                        </h5>
                        <small class="text-muted">Informasi lengkap penyesuaian stok</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary">
                            <i class="cil-arrow-left mr-1"></i> Kembali
                        </a>
                        <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-warning">
                            <i class="cil-pencil mr-1"></i> Edit
                        </a>
                        <a href="{{ route('adjustments.pdf', $adjustment->id) }}" target="_blank" class="btn btn-info">
                            <i class="cil-print mr-1"></i> Print PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    {{-- Products Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Daftar Produk yang Disesuaikan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="40%">Produk</th>
                                            <th width="15%" class="text-center">Jumlah</th>
                                            <th width="20%" class="text-center">Tipe</th>
                                            <th width="20%" class="text-right">Stok Saat Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($adjustment->adjustedProducts as $index => $item)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-light">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="font-weight-semibold">{{ $item->product->product_name }}</span>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <span class="badge badge-secondary mr-2">
                                                                <i class="cil-barcode mr-1"></i>
                                                                {{ $item->product->product_code }}
                                                            </span>
                                                            @if ($item->product->category)
                                                                <small class="text-muted">
                                                                    <i class="cil-tag mr-1"></i>
                                                                    {{ $item->product->category->category_name }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span
                                                        class="badge badge-{{ $item->type == 'add' ? 'success' : 'danger' }} badge-lg">
                                                        {{ $item->type == 'add' ? '+' : '-' }}{{ $item->quantity }}
                                                        {{ $item->product->product_unit }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if ($item->type == 'add')
                                                        <span class="badge badge-success">
                                                            <i class="cil-plus mr-1"></i> Penambahan
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="cil-minus mr-1"></i> Pengurangan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-right align-middle">
                                                    <span class="badge badge-info badge-lg">
                                                        {{ $item->product->product_quantity }}
                                                        {{ $item->product->product_unit }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="empty-state">
                                                        <i class="cil-inbox" style="font-size: 3rem; color: #e2e8f0;"></i>
                                                        <p class="text-muted mt-2 mb-0">Tidak ada produk yang disesuaikan
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Notes Card (if exists) --}}
                    @if ($adjustment->note)
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-notes mr-2 text-primary"></i>
                                    Catatan
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-0" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-info mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <p class="mb-0">{{ $adjustment->note }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Summary Info Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Penyesuaian
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            {{-- Date --}}
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-calendar text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Tanggal</span>
                                </div>
                                <h6 class="mb-0 ml-4">{{ \Carbon\Carbon::parse($adjustment->date)->format('d F Y') }}</h6>
                            </div>

                            <hr class="my-3">

                            {{-- Reference --}}
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-barcode text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Referensi</span>
                                </div>
                                <h6 class="mb-0 ml-4">
                                    <code class="bg-light px-2 py-1"
                                        style="font-size: 1rem;">{{ $adjustment->reference }}</code>
                                </h6>
                            </div>

                            <hr class="my-3">

                            {{-- Total Products --}}
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-layers text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Total Produk</span>
                                </div>
                                <h6 class="mb-0 ml-4">{{ $adjustment->adjustedProducts->count() }} Produk</h6>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics Cards --}}
                    @if ($adjustment->adjustedProducts->count() > 0)
                        {{-- Addition Stats --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success-light text-success mr-3">
                                        <i class="cil-arrow-circle-top" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-muted small">Penambahan</p>
                                        <h4 class="mb-0 font-weight-bold text-success">
                                            {{ $adjustment->adjustedProducts->where('type', 'add')->count() }} Produk
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Subtraction Stats --}}
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-danger-light text-danger mr-3">
                                        <i class="cil-arrow-circle-bottom" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-muted small">Pengurangan</p>
                                        <h4 class="mb-0 font-weight-bold text-danger">
                                            {{ $adjustment->adjustedProducts->where('type', 'sub')->count() }} Produk
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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

        /* ========== Action Bar ========== */
        .action-bar {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
        }

        /* ========== Cards ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        /* ========== Table Styling ========== */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* ========== Badges ========== */
        .badge-lg {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
        }

        /* ========== Info Items ========== */
        .info-item i {
            opacity: 0.8;
        }

        /* ========== Stat Icons ========== */
        .stat-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* ========== Button Gaps ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Code Badge ========== */
        code {
            border-radius: 6px;
        }

        /* ========== Empty State ========== */
        .empty-state {
            padding: 2rem 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .action-bar .d-flex>div {
                width: 100%;
            }

            .col-lg-4 {
                margin-top: 1rem;
            }
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge-lg {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
        }
    </style>
@endpush
