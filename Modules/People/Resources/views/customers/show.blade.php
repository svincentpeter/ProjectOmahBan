@extends('layouts.app')

@section('title', 'Detail Customer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customer</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                {{-- LEFT COLUMN: Customer Info --}}
                <div class="col-lg-8">
                    {{-- Customer Information Card --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="cil-people mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Detail Customer</h5>
                                        <small class="text-muted">Informasi lengkap customer</small>
                                    </div>
                                </div>

                                <div class="btn-group" role="group">
                                    @can('edit_customers')
                                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-primary">
                                            <i class="cil-pencil mr-1"></i> Edit
                                        </a>
                                    @endcan
                                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="cil-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Nama Customer --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-user mr-1"></i> Nama Customer
                                    </label>
                                    <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">
                                        {{ $customer->customer_name }}
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-envelope-closed mr-1"></i> Email
                                    </label>
                                    <div class="text-dark">
                                        <a href="mailto:{{ $customer->customer_email }}" class="text-decoration-none">
                                            {{ $customer->customer_email }}
                                        </a>
                                    </div>
                                </div>

                                {{-- No. Telepon --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-phone mr-1"></i> No. Telepon
                                    </label>
                                    <div class="text-dark">
                                        <a href="tel:{{ $customer->customer_phone }}" class="text-decoration-none">
                                            {{ $customer->customer_phone }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Kota --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-location-pin mr-1"></i> Kota
                                    </label>
                                    <div class="text-dark">
                                        <span class="badge badge-light-info">{{ $customer->city }}</span>
                                    </div>
                                </div>

                                {{-- Negara --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-globe-alt mr-1"></i> Negara
                                    </label>
                                    <div class="text-dark">
                                        {{ $customer->country }}
                                    </div>
                                </div>

                                {{-- Tanggal Terdaftar --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-calendar mr-1"></i> Terdaftar
                                    </label>
                                    <div class="text-dark">
                                        {{ $customer->created_at->format('d M Y, H:i') }}
                                        <small class="text-muted">({{ $customer->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>

                                {{-- Alamat Lengkap --}}
                                <div class="col-md-12 mb-0">
                                    <label class="form-label small font-weight-semibold text-muted mb-1">
                                        <i class="cil-home mr-1"></i> Alamat Lengkap
                                    </label>
                                    <div class="text-dark p-3 bg-light rounded">
                                        {{ $customer->address }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sales History Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="cil-cart mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Riwayat Penjualan</h5>
                                    <small class="text-muted">10 transaksi terakhir dari customer ini</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if ($customer->sales->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Tanggal</th>
                                                <th>Reference</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($customer->sales as $index => $sale)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $sale->date->format('d/m/Y') }}</td>
                                                    <td>
                                                        <strong class="text-primary">{{ $sale->reference }}</strong>
                                                    </td>
                                                    <td>{{ format_currency($sale->total_amount) }}</td>
                                                    <td>
                                                        @if ($sale->status == 'Completed')
                                                            <span class="badge badge-info">Completed</span>
                                                        @else
                                                            <span class="badge badge-secondary">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($sale->payment_status == 'Lunas')
                                                            <span class="badge badge-success">Lunas</span>
                                                        @else
                                                            <span class="badge badge-warning">Belum Lunas</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @can('show_sales')
                                                            <a href="{{ route('sales.show', $sale->id) }}"
                                                                class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                                <i class="cil-eye"></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-5 text-center text-muted">
                                    <i class="cil-info" style="font-size: 2.5rem;"></i>
                                    <p class="mt-3 mb-0">Belum ada riwayat penjualan dari customer ini.</p>
                                    <small>Transaksi akan muncul setelah melakukan penjualan.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Statistics & Actions --}}
                <div class="col-lg-4">
                    {{-- Statistics Card --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="cil-chart-line mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Statistik</h5>
                                    <small class="text-muted">Ringkasan transaksi</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Total Penjualan --}}
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="cil-cart text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Total Penjualan</small>
                                        <h4 class="mb-0 font-weight-bold text-primary">{{ $stats['total_sales'] }}</h4>
                                        <small class="text-muted">transaksi</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Nilai --}}
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="cil-credit-card text-success" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Total Nilai</small>
                                        <h5 class="mb-0 font-weight-bold text-success">
                                            {{ format_currency($stats['total_amount']) }}</h5>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Terbayar --}}
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="cil-check-circle text-info" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Terbayar</small>
                                        <h6 class="mb-0 font-weight-bold text-info">
                                            {{ format_currency($stats['total_paid']) }}</h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Sisa Piutang --}}
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="cil-warning text-warning" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Sisa Piutang</small>
                                        <h6 class="mb-0 font-weight-bold text-warning">
                                            {{ format_currency($stats['total_due']) }}</h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Last Sale --}}
                            @if ($stats['last_sale_date'])
                                <div class="p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="cil-history text-muted" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted d-block">Penjualan Terakhir</small>
                                            <strong>{{ \Carbon\Carbon::parse($stats['last_sale_date'])->format('d M Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Status Card --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="cil-check-circle mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Status Customer</h5>
                                    <small class="text-muted">Status aktivitas</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($customer->is_active)
                                <div class="alert alert-success mb-0" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-check-circle mr-2 mt-1" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <strong>Customer Aktif</strong>
                                            <p class="mb-0 mt-1 small">
                                                Customer ini memiliki transaksi dalam 6 bulan terakhir dan dianggap aktif.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-warning mr-2 mt-1" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <strong>Customer Tidak Aktif</strong>
                                            <p class="mb-0 mt-1 small">
                                                Tidak ada transaksi dalam 6 bulan terakhir. Pertimbangkan untuk menghubungi
                                                customer.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="cil-settings mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Aksi Cepat</h5>
                                    <small class="text-muted">Opsi untuk customer ini</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @can('create_sales')
                                    <a href="{{ route('sales.create', ['customer_id' => $customer->id]) }}"
                                        class="btn btn-primary">
                                        <i class="cil-plus mr-1"></i> Buat Penjualan Baru
                                    </a>
                                @endcan

                                @can('edit_customers')
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-primary">
                                        <i class="cil-pencil mr-1"></i> Edit Customer
                                    </a>
                                @endcan

                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                    <i class="cil-arrow-left mr-1"></i> Kembali ke Daftar
                                </a>

                                @can('delete_customers')
                                    <button type="button" class="btn btn-outline-danger delete-customer"
                                        data-id="{{ $customer->id }}" data-name="{{ $customer->customer_name }}"
                                        data-has-sales="{{ $customer->sales->count() > 0 ? 'true' : 'false' }}">
                                        <i class="cil-trash mr-1"></i> Hapus Customer
                                    </button>
                                @endcan
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

        /* ========== Table Styling ========== */
        .table thead th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 12px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 12px 10px;
        }

        .table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
        }

        /* ========== Stats Box ========== */
        .bg-light {
            background-color: #f8f9fa !important;
        }

        /* ========== Badge Light Variants ========== */
        .badge-light-info {
            background-color: #e7f3ff;
            color: #004085;
            padding: 0.35em 0.65em;
            font-weight: 600;
        }

        /* ========== Alert Styling ========== */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* ========== Button Spacing ========== */
        .d-grid.gap-2>* {
            margin-bottom: 0.5rem;
        }

        .d-grid.gap-2>*:last-child {
            margin-bottom: 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Konfirmasi Hapus Customer
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasSales = $(this).data('has-sales') === 'true';
                const url = '{{ route('customers.destroy', ':id') }}'.replace(':id', id);

                let warningText = hasSales ?
                    `Customer <strong>"${name}"</strong> memiliki riwayat penjualan dan akan di-arsipkan (soft delete).<br><small class="text-muted">Data masih bisa dikembalikan!</small>` :
                    `Customer <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data tidak dapat dikembalikan!</small>`;

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
