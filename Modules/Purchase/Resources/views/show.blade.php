@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Action Buttons --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                            <i class="cil-arrow-left mr-2"></i> Kembali
                        </a>

                        @can('edit_purchases')
                            @if ($purchase->status == 'Pending')
                                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                                    <i class="cil-pencil mr-2"></i> Edit
                                </a>
                            @endif
                        @endcan

                        {{-- Print / PDF --}}
                        <a href="{{ route('purchases.pdf', $purchase->id) }}" target="_blank" class="btn btn-info">
                            <i class="cil-print mr-2"></i> Print/PDF
                        </a>

                        @can('delete_purchases')
                            <button type="button" class="btn btn-danger" id="delete-purchase" data-id="{{ $purchase->id }}"
                                data-reference="{{ $purchase->reference }}">
                                <i class="cil-trash mr-2"></i> Hapus
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- LEFT: Purchase Information + Produk --}}
                <div class="col-lg-8 mb-4">
                    {{-- Purchase Header Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="cil-storage mr-2"></i>
                                {{ $purchase->reference }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Tanggal --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Tanggal</label>
                                    <div class="font-weight-bold">
                                        {{ $purchase->date->format('d F Y') }}
                                    </div>
                                </div>

                                {{-- Reference --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Reference</label>
                                    <div class="font-weight-bold text-primary">
                                        {{ $purchase->reference }}
                                    </div>
                                </div>

                                {{-- Supplier --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Supplier</label>
                                    <div class="font-weight-bold">
                                        {{ $purchase->supplier_name }}
                                        @php
                                            $supplier = $purchase->supplier ?? null;
                                        @endphp
                                        @if ($supplier && !empty($supplier->phone_number ?? $supplier->supplier_phone))
                                            <br>
                                            <small class="text-muted">
                                                <i class="cil-phone mr-1"></i>
                                                {{ $supplier->phone_number ?? $supplier->supplier_phone }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Status</label>
                                    <div>
                                        @if ($purchase->status == 'Completed')
                                            <span class="badge badge-info badge-lg">
                                                <i class="cil-check-circle mr-1"></i> Completed
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-lg">
                                                <i class="cil-clock mr-1"></i> Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Metode Pembayaran --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Metode Pembayaran</label>
                                    <div class="font-weight-bold">
                                        <i class="cil-wallet mr-1"></i>
                                        {{ $purchase->payment_method }}
                                        @if ($purchase->payment_method == 'Transfer' && $purchase->bank_name)
                                            <br>
                                            <small class="text-muted">Bank: {{ $purchase->bank_name }}</small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status Pembayaran --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">Status Pembayaran</label>
                                    <div>
                                        @if ($purchase->payment_status == 'Lunas')
                                            <span class="badge badge-success badge-lg">
                                                <i class="cil-check mr-1"></i> Lunas
                                            </span>
                                        @else
                                            <span class="badge badge-warning badge-lg">
                                                <i class="cil-warning mr-1"></i> Belum Lunas
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                @if ($purchase->note)
                                    <div class="col-12">
                                        <label class="text-muted small font-weight-semibold mb-1">Catatan</label>
                                        <div class="alert alert-light mb-0">
                                            <i class="cil-notes mr-1"></i>
                                            {{ $purchase->note }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Products Table Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="cil-basket mr-2 text-primary"></i>
                                Daftar Produk
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produk</th>
                                            <th width="10%" class="text-center">Qty</th>
                                            <th width="15%" class="text-right">Harga Satuan</th>
                                            <th width="15%" class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->purchaseDetails as $detail)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $detail->product_name }}</strong>
                                                </td>
                                                <td>
                                                    <code class="text-dark">{{ $detail->product_code }}</code>
                                                </td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-right">
                                                    {{ rupiah($detail->unit_price) }}
                                                </td>
                                                <td class="text-right font-weight-bold text-primary">
                                                    {{ rupiah($detail->sub_total) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="5" class="text-right font-weight-semibold">
                                                TOTAL PEMBELIAN:
                                            </th>
                                            <th class="text-right text-primary h6 mb-0">
                                                {{ rupiah($purchase->total_amount) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Payment Summary & Info --}}
                <div class="col-lg-4 mb-4">
                    {{-- Payment Summary Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="cil-calculator mr-2"></i>
                                Ringkasan Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Total Amount --}}
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="font-weight-semibold">Total Pembelian:</span>
                                <span class="h5 mb-0 font-weight-bold text-primary">
                                    {{ rupiah($purchase->total_amount) }}
                                </span>
                            </div>

                            {{-- Paid Amount --}}
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="font-weight-semibold">Terbayar:</span>
                                <span class="h5 mb-0 font-weight-bold text-success">
                                    {{ rupiah($purchase->paid_amount) }}
                                </span>
                            </div>

                            {{-- Due Amount --}}
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="font-weight-semibold">Sisa Hutang:</span>
                                <span
                                    class="h5 mb-0 font-weight-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ rupiah($purchase->due_amount) }}
                                </span>
                            </div>

                            {{-- Payment Status Alert --}}
                            <div
                                class="alert {{ $purchase->payment_status == 'Lunas' ? 'alert-success' : 'alert-warning' }} mb-0">
                                <strong>Status Pembayaran:</strong>
                                <br>
                                <span class="h6 mb-0">
                                    @if ($purchase->payment_status == 'Lunas')
                                        <i class="cil-check-circle mr-1"></i> Lunas
                                    @else
                                        <i class="cil-warning mr-1"></i> Belum Lunas
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Information Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Tambahan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small font-weight-semibold mb-1">
                                    <i class="cil-user mr-1"></i> Diinput Oleh
                                </label>
                                <div class="font-weight-bold">
                                    {{ $purchase->user->name ?? 'System' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small font-weight-semibold mb-1">
                                    <i class="cil-calendar mr-1"></i> Dibuat Pada
                                </label>
                                <div>
                                    {{ $purchase->created_at->format('d F Y, H:i') }} WIB
                                    <br>
                                    <small class="text-muted">
                                        {{ $purchase->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>

                            @if ($purchase->updated_at != $purchase->created_at)
                                <div class="mb-3">
                                    <label class="text-muted small font-weight-semibold mb-1">
                                        <i class="cil-pencil mr-1"></i> Terakhir Diupdate
                                    </label>
                                    <div>
                                        {{ $purchase->updated_at->format('d F Y, H:i') }} WIB
                                        <br>
                                        <small class="text-muted">
                                            {{ $purchase->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="text-muted small font-weight-semibold mb-1">
                                    <i class="cil-layers mr-1"></i> Total Item
                                </label>
                                <div class="font-weight-bold">
                                    {{ $purchase->purchaseDetails->count() }} Item
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- /RIGHT --}}
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

        /* ========== Card Shadows ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Badge Sizes ========== */
        .badge-lg {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        /* ========== Table Styling ========== */
        .table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03);
        }

        /* ========== Info Labels ========== */
        .text-muted.small {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========== Alert Styling ========== */
        .alert {
            border-radius: 8px;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('#delete-purchase').click(function() {
                const id = $(this).data('id');
                const reference = $(this).data('reference');
                const url = '{{ route('purchases.destroy', ':id') }}'.replace(':id', id);

                Swal.fire({
                    title: 'Hapus Pembelian?',
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
        });
    </script>
@endpush
