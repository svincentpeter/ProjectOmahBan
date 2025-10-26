@extends('layouts.app')

@section('title', 'Daftar Produk Bekas')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Produk Bekas</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                {{-- Total Produk Bekas --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon">
                            <i class="cil-recycle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Produk</div>
                            <div class="stats-value">
                                {{ $products->total() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tersedia --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon">
                            <i class="cil-check-circle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Tersedia</div>
                            <div class="stats-value">
                                {{ $products->where('status', 'available')->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Terjual --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-danger">
                        <div class="stats-icon">
                            <i class="cil-x-circle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Terjual</div>
                            <div class="stats-value">
                                {{ $products->where('status', 'sold')->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="card shadow-sm">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Daftar Produk Bekas
                            </h5>
                            <small class="text-muted">Kelola produk ban & velg bekas</small>
                        </div>

                        <a href="{{ route('products_second.create') }}" class="btn btn-primary">
                            <i class="cil-plus mr-2"></i> Tambah Produk Bekas
                        </a>
                    </div>
                </div>

                {{-- Quick Filters --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>

                        {{-- Quick Filter Pills --}}
                        <div class="quick-filters mb-3">
                            <a href="{{ route('products_second.index') }}"
                                class="filter-pill {{ !request('status') ? 'active' : '' }}">
                                <i class="cil-apps"></i>
                                <span>Semua</span>
                            </a>

                            <a href="{{ route('products_second.index', ['status' => 'available']) }}"
                                class="filter-pill {{ request('status') == 'available' ? 'active' : '' }}">
                                <i class="cil-check-circle"></i>
                                <span>Tersedia</span>
                            </a>

                            <a href="{{ route('products_second.index', ['status' => 'sold']) }}"
                                class="filter-pill {{ request('status') == 'sold' ? 'active' : '' }}">
                                <i class="cil-ban"></i>
                                <span>Terjual</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0">Nama Barang</th>
                                    <th class="border-0">Merk</th>
                                    <th class="border-0">Tahun</th>
                                    <th class="border-0">Ukuran</th>
                                    <th class="border-0">Ring</th>
                                    <th class="border-0 text-right">Modal</th>
                                    <th class="border-0 text-right">Harga Jual</th>
                                    <th class="border-0">Kondisi</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="border-0 text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="font-weight-semibold">{{ $product->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light">
                                                {{ $product->brand->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $product->product_year ?? '-' }}</td>
                                        <td>
                                            <span class="font-weight-semibold">{{ $product->size ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">R{{ $product->ring ?? '-' }}</span>
                                        </td>
                                        <td class="text-right">
                                            <small class="text-muted d-block">Modal</small>
                                            <strong>{{ format_currency($product->purchase_price) }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <small class="text-muted d-block">Jual</small>
                                            <strong
                                                class="text-success">{{ format_currency($product->selling_price) }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($product->condition_notes ?? 'Normal', 20) }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if ($product->status == 'available')
                                                <span class="badge badge-success">
                                                    <i class="cil-check-circle mr-1"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="cil-x-circle mr-1"></i> Terjual
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('products_second.edit', $product->id) }}"
                                                    class="btn btn-outline-warning" data-toggle="tooltip" title="Edit">
                                                    <i class="cil-pencil"></i>
                                                </a>

                                                <button type="button" class="btn btn-outline-danger btn-delete"
                                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    data-toggle="tooltip" title="Hapus">
                                                    <i class="cil-trash"></i>
                                                </button>

                                                <form id="delete-form-{{ $product->id }}"
                                                    action="{{ route('products_second.destroy', $product->id) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                                <p class="mb-0 mt-3 font-weight-semibold">Belum ada produk bekas</p>
                                                <small>Klik tombol "Tambah Produk Bekas" untuk mulai menambah data</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($products->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }}
                                dari {{ $products->total() }} produk
                            </small>
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
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

        /* ========== Statistics Cards ========== */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
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

        .stats-card-purple {
            border-left-color: #4834DF;
        }

        .stats-card-success {
            border-left-color: #2eb85c;
        }

        .stats-card-danger {
            border-left-color: #e55353;
        }

        .stats-card-info {
            border-left-color: #39f;
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

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            color: white;
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%);
            color: white;
        }

        .stats-card-danger .stats-icon {
            background: linear-gradient(135deg, #e55353 0%, #f27474 100%);
            color: white;
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%);
            color: white;
        }

        .stats-content {
            flex: 1;
        }

        .stats-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
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
            padding: 12px 24px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            color: #4f5d73;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
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

        .filter-pill.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-color: #4834DF;
            color: white;
            box-shadow: 0 4px 15px rgba(72, 52, 223, 0.3);
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

        .table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
        }

        /* ========== Badge Enhancements ========== */
        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
        }

        /* ========== Button Group ========== */
        .btn-group .btn {
            transition: all 0.2s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
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
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Delete confirmation
            $('.btn-delete').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Produk Bekas?',
                    html: `Produk <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
                    icon: 'warning',
                    iconColor: '#e55353',
                    showCancelButton: true,
                    confirmButtonColor: '#e55353',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-trash mr-1"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $('#delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush
