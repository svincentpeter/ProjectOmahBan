@extends('layouts.app')

@section('title', 'Data Pembelian')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Pembelian</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- ===== Statistik (seragam dengan Pembelian Bekas) ===== --}}
            <div class="row mb-4">
                {{-- Total Pembelian --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon">
                            <i class="cil-storage"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Pembelian</div>
                            <div class="stats-value">
                                {{ $purchases->total() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Nilai Pembelian --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon">
                            <i class="cil-credit-card"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Nilai</div>
                            <div class="stats-value">
                                {{ rupiah($total_amount) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Terbayar --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon">
                            <i class="cil-check-circle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Terbayar</div>
                            <div class="stats-value">
                                {{ rupiah($total_paid) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sisa Hutang --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon">
                            <i class="cil-warning"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Sisa Hutang</div>
                            <div class="stats-value">
                                {{ rupiah($total_due) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Main Card ===== --}}
            <div class="card shadow-sm">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-storage mr-2 text-primary"></i>
                                Daftar Pembelian Stok
                            </h5>
                            <small class="text-muted">Kelola pembelian stok ban & velg dari supplier</small>
                        </div>

                        <div class="btn-group" role="group">
                            @can('create_purchases')
                                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                    <i class="cil-plus mr-2"></i> Tambah Pembelian
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- ===== Filter Cepat + Filter Lanjutan ===== --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>

                        {{-- Quick Filter Pills --}}
                        <div class="quick-filters mb-3">
                            <a href="{{ route('purchases.index', ['quick_filter' => 'all']) }}"
                                class="filter-pill {{ request('quick_filter', 'all') == 'all' ? 'active' : '' }}">
                                <i class="cil-apps"></i>
                                <span>Semua</span>
                            </a>

                            <a href="{{ route('purchases.index', ['quick_filter' => 'yesterday']) }}"
                                class="filter-pill {{ request('quick_filter') == 'yesterday' ? 'active' : '' }}">
                                <i class="cil-history"></i>
                                <span>Kemarin</span>
                            </a>

                            <a href="{{ route('purchases.index', ['quick_filter' => 'this_week']) }}"
                                class="filter-pill {{ request('quick_filter') == 'this_week' ? 'active' : '' }}">
                                <i class="cil-calendar"></i>
                                <span>Minggu Ini</span>
                            </a>

                            <a href="{{ route('purchases.index', ['quick_filter' => 'this_month']) }}"
                                class="filter-pill {{ request('quick_filter') == 'this_month' ? 'active' : '' }}">
                                <i class="cil-chart"></i>
                                <span>Bulan Ini</span>
                            </a>

                            <a href="{{ route('purchases.index', ['quick_filter' => 'last_month']) }}"
                                class="filter-pill {{ request('quick_filter') == 'last_month' ? 'active' : '' }}">
                                <i class="cil-arrow-thick-to-left"></i>
                                <span>Bulan Lalu</span>
                            </a>
                        </div>

                        {{-- Advanced Filters --}}
                        <form action="{{ route('purchases.index') }}" method="GET">
                            <div class="row">
                                {{-- Dari Tanggal --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-calendar mr-1 text-muted"></i> Dari Tanggal
                                    </label>
                                    <input type="date" name="from" class="form-control"
                                        value="{{ request('from', $from) }}">
                                </div>

                                {{-- Sampai Tanggal --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-calendar mr-1 text-muted"></i> Sampai Tanggal
                                    </label>
                                    <input type="date" name="to" class="form-control"
                                        value="{{ request('to', $to) }}">
                                </div>

                                {{-- Supplier --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-industry mr-1 text-muted"></i> Supplier
                                    </label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="">Semua Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Bayar --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-wallet mr-1 text-muted"></i> Status Bayar
                                    </label>
                                    <select name="payment_status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="Lunas"
                                            {{ request('payment_status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="Belum Lunas"
                                            {{ request('payment_status') == 'Belum Lunas' ? 'selected' : '' }}>Belum
                                            Lunas</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="btn-group w-auto" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="cil-filter mr-1"></i> Terapkan
                                        </button>
                                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                                            <i class="cil-reload mr-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ===== Tabel Data ===== --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            <table class="table table-hover mb-0" id="purchases-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Tanggal</th>
                                        <th>Reference</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                        <th>Terbayar</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Status Bayar</th>
                                        <th width="12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $purchase)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + ($purchases->currentPage() - 1) * $purchases->perPage() }}
                                            </td>
                                            <td>{{ $purchase->date->format('d/m/Y') }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $purchase->reference }}</strong>
                                            </td>
                                            <td>{{ $purchase->supplier_name }}</td>
                                            <td>{{ rupiah($purchase->total_amount) }}</td>
                                            <td>{{ rupiah($purchase->paid_amount) }}</td>
                                            <td>{{ rupiah($purchase->due_amount) }}</td>
                                            <td>
                                                @if ($purchase->status == 'Completed')
                                                    <span class="badge badge-info">Completed</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($purchase->payment_status == 'Lunas')
                                                    <span class="badge badge-success">Lunas</span>
                                                @else
                                                    <span class="badge badge-warning">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group dropleft">
                                                    <button type="button" class="btn btn-ghost-primary dropdown rounded"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>

                                                    <div class="dropdown-menu">
                                                        {{-- Cetak Nota PDF --}}
                                                        <a target="_blank"
                                                            href="{{ route('purchases.pdf', $purchase->id) }}"
                                                            class="dropdown-item">
                                                            <i class="bi bi-file-earmark-pdf mr-2 text-success"
                                                                style="line-height: 1;"></i>
                                                            Cetak Nota
                                                        </a>

                                                        @can('show_purchases')
                                                            <a href="{{ route('purchases.show', $purchase) }}"
                                                                class="dropdown-item">
                                                                <i class="bi bi-eye mr-2 text-info"
                                                                    style="line-height: 1;"></i>
                                                                Detail Pembelian
                                                            </a>
                                                        @endcan

                                                        @can('edit_purchases')
                                                            @if ($purchase->status == 'Pending')
                                                                <div class="dropdown-divider"></div>
                                                                <a href="{{ route('purchases.edit', $purchase) }}"
                                                                    class="dropdown-item">
                                                                    <i class="bi bi-pencil-square mr-2 text-primary"
                                                                        style="line-height: 1;"></i>
                                                                    Edit Pembelian
                                                                </a>
                                                            @endif
                                                        @endcan

                                                        @can('delete_purchases')
                                                            <div class="dropdown-divider"></div>
                                                            <button type="button"
                                                                class="dropdown-item text-danger delete-purchase"
                                                                data-id="{{ $purchase->id }}"
                                                                data-reference="{{ $purchase->reference }}">
                                                                <i class="bi bi-trash mr-2" style="line-height: 1;"></i>
                                                                Hapus Pembelian
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="cil-info" style="font-size: 2rem;"></i>
                                                    <p class="mt-2 mb-0">Belum ada data pembelian.</p>
                                                    <small>Klik "Tambah Pembelian" untuk mulai mencatat.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ===== Pagination info ===== --}}
                @if ($purchases->hasPages())
                    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            Menampilkan
                            {{ $purchases->firstItem() ?? 0 }} - {{ $purchases->lastItem() ?? 0 }}
                            dari {{ $purchases->total() }} data
                        </div>
                        <div>
                            {{ $purchases->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif

            </div> {{-- end card --}}
        </div> {{-- end animated --}}
    </div> {{-- end container --}}
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

        .stats-card-warning {
            border-left-color: #f9b115;
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

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%);
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
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
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

        /* ========== DataTable Wrapper (meski bukan Yajra) ========== */
        .datatable-wrapper {
            padding: 1rem;
        }

        #purchases-table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #purchases-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        #purchases-table tbody tr {
            transition: all 0.2s ease;
        }

        #purchases-table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
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

            .datatable-wrapper {
                padding: 0.5rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Konfirmasi hapus (SweetAlert style sama seperti Pembelian Bekas)
            $(document).on('click', '.delete-purchase', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');
                const url = '{{ route('purchases.destroy', ':id') }}'.replace(':id', id);

                Swal.fire({
                    title: 'Hapus Pembelian Stok?',
                    html: `Pembelian <strong>"${reference}"</strong> akan dihapus permanen.<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
