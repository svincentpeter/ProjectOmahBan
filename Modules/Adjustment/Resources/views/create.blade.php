@extends('layouts.app')

@section('title', 'Buat Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active">Buat Penyesuaian</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="adjustment-form" action="{{ route('adjustments.store') }}" method="POST">
                @csrf

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-transfer mr-2 text-primary"></i>
                                Buat Penyesuaian Stok
                            </h5>
                            <small class="text-muted">Tambah atau kurangi stok produk</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-check-circle mr-1"></i> Simpan Penyesuaian
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Header Info Card --}}
                    <div class="col-lg-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-notes mr-2 text-primary"></i>
                                    Informasi Penyesuaian
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    {{-- Date --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date" class="form-label font-weight-semibold">
                                                <i class="cil-calendar mr-1 text-muted"></i> Tanggal
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" id="date" name="date"
                                                class="form-control form-control-lg @error('date') is-invalid @enderror"
                                                required value="{{ old('date', date('Y-m-d')) }}">
                                            @error('date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Reference (Auto-generated) --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference" class="form-label font-weight-semibold">
                                                <i class="cil-barcode mr-1 text-muted"></i> Referensi
                                            </label>
                                            <input type="text" id="reference"
                                                class="form-control form-control-lg bg-light"
                                                value="Auto-generated: ADJ-XXX" readonly disabled>
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Kode referensi akan dibuat otomatis
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label for="note" class="form-label font-weight-semibold">
                                                <i class="cil-pencil mr-1 text-muted"></i> Catatan
                                            </label>
                                            <textarea id="note" name="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                                                placeholder="Alasan penyesuaian stok (opsional)...">{{ old('note') }}</textarea>
                                            @error('note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-lightbulb mr-1"></i>
                                                Contoh: Stok rusak, hilang, retur, atau koreksi pencatatan
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Products Table Card --}}
                    <div class="col-lg-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="cil-list mr-2 text-primary"></i>
                                        Daftar Produk
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="add-product-row">
                                        <i class="cil-plus mr-1"></i> Tambah Produk
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                {{-- Warning Alert (Hidden by default) --}}
                                <div id="stock-warning" class="alert alert-warning" style="display: none;" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Peringatan Stok Negatif!</strong>
                                            <p class="mb-0">
                                                <small>Beberapa produk akan memiliki stok negatif setelah penyesuaian. Harap
                                                    periksa kembali jumlah pengurangan.</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Products Table --}}
                                <div class="table-responsive" id="products-table-container" style="display: none;">
                                    <table class="table table-hover" id="products-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="35%">Produk</th>
                                                <th width="15%" class="text-center">Stok Saat Ini</th>
                                                <th width="15%" class="text-center">Jumlah</th>
                                                <th width="15%" class="text-center">Tipe</th>
                                                <th width="15%" class="text-center">Stok Akhir</th>
                                                <th width="5%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-rows">
                                            {{-- Dynamic rows --}}
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Empty State --}}
                                <div id="empty-state" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="cil-inbox" style="font-size: 4rem; color: #e2e8f0;"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Belum Ada Produk</h6>
                                    <p class="text-muted small mb-3">
                                        Klik tombol di bawah untuk menambahkan produk yang akan disesuaikan stoknya
                                    </p>
                                    <button type="button" class="btn btn-primary"
                                        onclick="$('#add-product-row').click()">
                                        <i class="cil-plus mr-1"></i> Tambah Produk Pertama
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('third_party_scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            let rowIndex = 0;
            const products = @json(\Modules\Product\Entities\Product::with('category', 'brand')->get());

            // Product row template
            function getProductRowTemplate(index) {
                return `
            <tr data-index="${index}" class="product-row">
                <td>
                    <select class="form-control product-select" name="product_ids[]" required data-index="${index}">
                        <option value="">-- Pilih Produk --</option>
                        ${products.map(p => `
                                <option value="${p.id}" 
                                        data-stock="${p.product_quantity}" 
                                        data-unit="${p.product_unit}"
                                        data-name="${p.product_name}">
                                    [${p.product_code}] ${p.product_name}${p.category ? ' - ' + p.category.category_name : ''}
                                </option>
                            `).join('')}
                    </select>
                </td>
                <td class="text-center align-middle">
                    <span class="badge badge-secondary badge-lg current-stock">-</span>
                </td>
                <td>
                    <input type="number" 
                           class="form-control text-center quantity-input" 
                           name="quantities[]" 
                           min="1" 
                           required 
                           placeholder="0" 
                           data-index="${index}">
                </td>
                <td>
                    <select class="form-control type-select" name="types[]" required data-index="${index}">
                        <option value="add" selected>
                            <i class="cil-plus"></i> Penambahan
                        </option>
                        <option value="sub">
                            <i class="cil-minus"></i> Pengurangan
                        </option>
                    </select>
                </td>
                <td class="text-center align-middle">
                    <span class="badge badge-info badge-lg final-stock">-</span>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger remove-row" data-index="${index}" title="Hapus">
                        <i class="cil-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            }

            // Add product row
            $('#add-product-row').click(function() {
                const newRow = getProductRowTemplate(rowIndex);
                $('#product-rows').append(newRow);

                // Initialize Select2
                $(`select.product-select[data-index="${rowIndex}"]`).select2({
                    placeholder: 'Cari produk...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('.card-body')
                });

                rowIndex++;
                toggleEmptyState();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                const index = $(this).data('index');

                Swal.fire({
                    title: 'Hapus Produk?',
                    text: 'Produk ini akan dihapus dari daftar',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`tr[data-index="${index}"]`).fadeOut(300, function() {
                            $(this).remove();
                            toggleEmptyState();
                            checkNegativeStock();
                        });
                    }
                });
            });

            // Product selection change
            $(document).on('change', '.product-select', function() {
                const index = $(this).data('index');
                const selectedOption = $(this).find('option:selected');
                const stock = selectedOption.data('stock') || 0;
                const unit = selectedOption.data('unit') || 'PC';

                $(`tr[data-index="${index}"] .current-stock`)
                    .text(`${stock} ${unit}`)
                    .removeClass('badge-secondary')
                    .addClass('badge-info');

                calculateFinalStock(index);
            });

            // Quantity or type change
            $(document).on('input change', '.quantity-input, .type-select', function() {
                const index = $(this).data('index');
                calculateFinalStock(index);
            });

            // Calculate final stock
            function calculateFinalStock(index) {
                const row = $(`tr[data-index="${index}"]`);
                const productSelect = row.find('.product-select');
                const selectedOption = productSelect.find('option:selected');
                const currentStock = parseInt(selectedOption.data('stock')) || 0;
                const unit = selectedOption.data('unit') || 'PC';
                const quantity = parseInt(row.find('.quantity-input').val()) || 0;
                const type = row.find('.type-select').val();

                let finalStock = currentStock;
                if (quantity > 0) {
                    finalStock = type === 'add' ? currentStock + quantity : currentStock - quantity;
                }

                const finalStockSpan = row.find('.final-stock');
                finalStockSpan.text(`${finalStock} ${unit}`);

                // Color coding
                if (finalStock < 0) {
                    finalStockSpan.removeClass('badge-info badge-success badge-warning')
                        .addClass('badge-danger');
                } else if (finalStock === 0) {
                    finalStockSpan.removeClass('badge-info badge-success badge-danger')
                        .addClass('badge-warning');
                } else {
                    finalStockSpan.removeClass('badge-danger badge-warning badge-info')
                        .addClass('badge-success');
                }

                checkNegativeStock();
            }

            // Check for negative stock
            function checkNegativeStock() {
                let hasNegativeStock = false;

                $('.final-stock').each(function() {
                    const text = $(this).text().split(' ')[0];
                    const value = parseInt(text);
                    if (!isNaN(value) && value < 0) {
                        hasNegativeStock = true;
                    }
                });

                if (hasNegativeStock) {
                    $('#stock-warning').slideDown();
                } else {
                    $('#stock-warning').slideUp();
                }
            }

            // Toggle empty state
            function toggleEmptyState() {
                if ($('#product-rows tr').length === 0) {
                    $('#empty-state').fadeIn();
                    $('#products-table-container').hide();
                } else {
                    $('#empty-state').hide();
                    $('#products-table-container').fadeIn();
                }
            }

            // Form validation & submission
            $('#adjustment-form').submit(function(e) {
                e.preventDefault();

                // Check if products added
                if ($('#product-rows tr').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ada Produk',
                        text: 'Minimal satu produk harus ditambahkan!',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                // Check for duplicate products
                const productIds = [];
                let hasDuplicate = false;

                $('.product-select').each(function() {
                    const val = $(this).val();
                    if (val && productIds.includes(val)) {
                        hasDuplicate = true;
                    }
                    productIds.push(val);
                });

                if (hasDuplicate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Produk Duplikat',
                        text: 'Tidak boleh ada produk yang sama dalam satu penyesuaian',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                // Check for negative stock
                let hasNegativeStock = false;
                $('.final-stock').each(function() {
                    const text = $(this).text().split(' ')[0];
                    const value = parseInt(text);
                    if (!isNaN(value) && value < 0) {
                        hasNegativeStock = true;
                    }
                });

                if (hasNegativeStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Negatif Terdeteksi',
                        text: 'Beberapa produk akan memiliki stok negatif. Lanjutkan?',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Periksa Kembali'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                } else {
                    confirmSubmit();
                }

                return false;
            });

            function confirmSubmit() {
                const productCount = $('#product-rows tr').length;

                Swal.fire({
                    title: 'Simpan Penyesuaian?',
                    html: `<strong>${productCount}</strong> produk akan disesuaikan stoknya`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4834DF',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="cil-check mr-1"></i> Ya, Simpan!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            }

            function submitForm() {
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $('#adjustment-form')[0].submit();
            }

            // Initialize
            toggleEmptyState();

            // Auto-add first row
            $('#add-product-row').click();
        });
    </script>
@endpush

@push('page_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        /* ========== Form Controls ========== */
        .form-control-lg {
            height: 50px;
            font-size: 1rem;
        }

        .form-control:focus,
        select.form-control:focus,
        textarea.form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        textarea.form-control {
            resize: vertical;
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
        }

        .table td {
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

        /* ========== Select2 Customization ========== */
        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            height: 38px !important;
            border-color: #ced4da !important;
        }

        .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #4834DF !important;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25) !important;
        }

        /* ========== Button Gaps ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Empty State ========== */
        #empty-state {
            padding: 3rem 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .action-bar .d-flex>div {
                width: 100%;
            }

            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush
