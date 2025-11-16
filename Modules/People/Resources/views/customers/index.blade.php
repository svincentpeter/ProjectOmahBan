@extends('layouts.app')

@section('title', 'Daftar Customer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Customer</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                {{-- Total Customer --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon">
                            <i class="cil-people"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Customer</div>
                            <div class="stats-value">
                                {{ \Modules\People\Entities\Customer::count() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer Aktif --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon">
                            <i class="cil-check-circle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Customer Aktif</div>
                            <div class="stats-value">
                                @php
                                    try {
                                        $activeCustomers = \Modules\People\Entities\Customer::whereHas('sales', function($q) { 
                                            $q->where('date', '>=', now()->subMonths(6)); 
                                        })->count();
                                    } catch (\Exception $e) {
                                        \Log::error('Error counting active customers: ' . $e->getMessage());
                                        $activeCustomers = 0;
                                    }
                                @endphp
                                {{ $activeCustomers }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Kota --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon">
                            <i class="cil-location-pin"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Kota</div>
                            <div class="stats-value">
                                {{ \Modules\People\Entities\Customer::distinct('city')->count('city') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Transaksi --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon">
                            <i class="cil-cart"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Transaksi</div>
                            <div class="stats-value">
                                @php
                                    try {
                                        $totalSales = \Modules\Sale\Entities\Sale::count();
                                    } catch (\Exception $e) {
                                        \Log::error('Error counting sales: ' . $e->getMessage());
                                        $totalSales = 0;
                                    }
                                @endphp
                                {{ $totalSales }}
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
                                <i class="cil-people mr-2 text-primary"></i>
                                Daftar Customer
                            </h5>
                            <small class="text-muted">Kelola data customer untuk transaksi penjualan</small>
                        </div>

                        <div class="btn-group" role="group">
                            @can('create_customers')
                                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                    <i class="cil-plus mr-2"></i> Tambah Customer
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card-body py-4 border-bottom" 
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Data</h6>
                        </div>

                        {{-- Filter Form --}}
                        <form method="GET" action="{{ route('customers.index') }}">
                            <div class="row">
                                {{-- Filter by City --}}
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-location-pin mr-1 text-muted"></i> Kota
                                    </label>
                                    <select name="city" id="filter-city" class="form-control">
                                        <option value="">Semua Kota</option>
                                        @php
                                            $cities = \Modules\People\Entities\Customer::distinct('city')
                                                ->pluck('city')
                                                ->filter()
                                                ->sort();
                                        @endphp
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter by Status --}}
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-check mr-1 text-muted"></i> Status
                                    </label>
                                    <select name="status" id="filter-status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                            Tidak Aktif
                                        </option>
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-lg-4 col-md-12 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-settings mr-1 text-muted"></i> Aksi
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="cil-filter mr-1"></i> Terapkan
                                        </button>
                                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                            <i class="cil-reload mr-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'customers-table']) !!}
                        </div>
                    </div>
                </div>
            </div> {{-- end .card --}}
        </div> {{-- end .animated --}}
    </div> {{-- end .container-fluid --}}
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

        /* ========== DataTable Wrapper ========== */
        .datatable-wrapper {
            padding: 1rem;
        }

        /* ========== DataTable Styling ========== */
        #customers-table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #customers-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        #customers-table tbody tr {
            transition: all 0.2s ease;
        }

        #customers-table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
        }

        /* ========== Badge Styling ========== */
        .badge-light-info {
            background-color: #e7f3ff;
            color: #004085;
            padding: 0.35em 0.65em;
            font-weight: 600;
        }

        /* ========== Responsive ========== */
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
    {{-- Script DataTables --}}
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {
            // Delete confirmation
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasSales = $(this).data('has-sales') === 'true';
                const url = '{{ route('customers.destroy', ':id') }}'.replace(':id', id);

                let warningText = hasSales 
                    ? `Customer <strong>"${name}"</strong> memiliki riwayat penjualan dan akan di-arsipkan (soft delete).<br><small class="text-muted">Data masih bisa dikembalikan!</small>`
                    : `Customer <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data tidak dapat dikembalikan!</small>`;

                Swal.fire({
                    title: 'Hapus Customer?',
                    html: warningText,
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
                            'method': 'POST',
                            'action': url
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Tooltip (jika butuh)
            $('body').tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        });
    </script>
@endpush
