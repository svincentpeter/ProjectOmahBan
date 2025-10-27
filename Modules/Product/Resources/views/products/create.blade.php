@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Tambah Produk</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        {{-- Alerts --}}
        @include('utils.alerts')

        <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Sticky Action Bar --}}
            <div class="action-bar shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 font-weight-bold">
                            <i class="cil-plus mr-2 text-primary"></i>
                            Tambah Produk Baru
                        </h5>
                        <small class="text-muted">Lengkapi informasi produk di bawah ini</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="cil-x mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="cil-check-circle mr-1"></i> Simpan Produk
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                {{-- Left Column: Product Info --}}
                <div class="col-lg-8">
                    {{-- Section 1: Basic Info --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Dasar
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                {{-- Product Name --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name" class="form-label font-weight-semibold">
                                            <i class="cil-tag mr-1 text-muted"></i> Nama Barang
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="product_name"
                                               name="product_name"
                                               class="form-control form-control-lg @error('product_name') is-invalid @enderror"
                                               value="{{ old('product_name') }}"
                                               placeholder="Contoh: Ban Mobil Bridgestone"
                                               required>
                                        @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Product Code --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_code" class="form-label font-weight-semibold">
                                            <i class="cil-barcode mr-1 text-muted"></i> Kode Barang
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text"
                                                   id="product_code"
                                                   name="product_code"
                                                   class="form-control form-control-lg @error('product_code') is-invalid @enderror"
                                                   value="{{ old('product_code', 'PRD-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}"
                                                   placeholder="PRD-0001"
                                                   required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="generateCode" title="Generate Kode Baru">
                                                    <i class="cil-reload"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('product_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="cil-info mr-1"></i>
                                            Kode unik untuk identifikasi produk
                                        </small>
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id" class="form-label font-weight-semibold">
                                            <i class="cil-folder mr-1 text-muted"></i> Kategori
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="category_id"
                                                name="category_id"
                                                class="form-control form-control-lg @error('category_id') is-invalid @enderror"
                                                required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Brand --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_id" class="form-label font-weight-semibold">
                                            <i class="cil-bookmark mr-1 text-muted"></i> Merek
                                        </label>
                                        <select id="brand_id"
                                                name="brand_id"
                                                class="form-control form-control-lg @error('brand_id') is-invalid @enderror">
                                            <option value="">-- Tanpa Merek --</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Product Specifications --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-settings mr-2 text-primary"></i>
                                Spesifikasi Produk
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                {{-- Size --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_size" class="form-label font-weight-semibold">
                                            <i class="cil-resize-both mr-1 text-muted"></i> Ukuran
                                        </label>
                                        <input type="text"
                                               id="product_size"
                                               name="product_size"
                                               class="form-control form-control-lg @error('product_size') is-invalid @enderror"
                                               value="{{ old('product_size') }}"
                                               placeholder="235/75 R15">
                                        @error('product_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Ring --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ring" class="form-label font-weight-semibold">
                                            <i class="cil-sun mr-1 text-muted"></i> Ring
                                        </label>
                                        <input type="text"
                                               id="ring"
                                               name="ring"
                                               class="form-control form-control-lg @error('ring') is-invalid @enderror"
                                               value="{{ old('ring') }}"
                                               placeholder="15">
                                        @error('ring')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Year --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_year" class="form-label font-weight-semibold">
                                            <i class="cil-calendar mr-1 text-muted"></i> Tahun Produksi
                                        </label>
                                        <input type="number"
                                               id="product_year"
                                               name="product_year"
                                               class="form-control form-control-lg @error('product_year') is-invalid @enderror"
                                               value="{{ old('product_year') }}"
                                               placeholder="{{ date('Y') }}"
                                               min="2000"
                                               max="{{ date('Y') + 1 }}">
                                        @error('product_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Pricing --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-dollar mr-2 text-primary"></i>
                                Harga & Modal
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                {{-- Cost --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_cost" class="form-label font-weight-semibold">
                                            <i class="cil-arrow-circle-bottom mr-1 text-muted"></i> Modal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="product_cost"
                                               name="product_cost"
                                               class="form-control form-control-lg @error('product_cost') is-invalid @enderror"
                                               value="{{ old('product_cost', 0) }}"
                                               required>
                                        @error('product_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="cil-info mr-1"></i>
                                            Harga beli dari supplier
                                        </small>
                                    </div>
                                </div>

                                {{-- Price --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_price" class="form-label font-weight-semibold">
                                            <i class="cil-arrow-circle-top mr-1 text-muted"></i> Harga Jual
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="product_price"
                                               name="product_price"
                                               class="form-control form-control-lg @error('product_price') is-invalid @enderror"
                                               value="{{ old('product_price', 0) }}"
                                               required>
                                        @error('product_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="cil-info mr-1"></i>
                                            Harga jual ke customer
                                        </small>
                                    </div>
                                </div>

                                {{-- Profit Margin Display --}}
                                <div class="col-12">
                                    <div class="alert alert-info" id="profitMarginAlert" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Margin Keuntungan:</strong>
                                                <span id="profitAmount"></span>
                                            </div>
                                            <div>
                                                <span class="badge badge-primary badge-lg" id="profitPercentage"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Stock --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-layers mr-2 text-primary"></i>
                                Stok & Satuan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                {{-- Initial Stock --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stok_awal" class="form-label font-weight-semibold">
                                            <i class="cil-plus mr-1 text-muted"></i> Stok Awal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               id="stok_awal"
                                               name="stok_awal"
                                               class="form-control form-control-lg @error('stok_awal') is-invalid @enderror"
                                               value="{{ old('stok_awal', 0) }}"
                                               min="0"
                                               required>
                                        @error('stok_awal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Stock Alert --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_stock_alert" class="form-label font-weight-semibold">
                                            <i class="cil-warning mr-1 text-muted"></i> Stok Minimum
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               id="product_stock_alert"
                                               name="product_stock_alert"
                                               class="form-control form-control-lg @error('product_stock_alert') is-invalid @enderror"
                                               value="{{ old('product_stock_alert', 4) }}"
                                               min="0"
                                               required>
                                        @error('product_stock_alert')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="cil-bell mr-1"></i>
                                            Alert saat stok mencapai jumlah ini
                                        </small>
                                    </div>
                                </div>

                                {{-- Unit --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_unit" class="form-label font-weight-semibold">
                                            <i class="cil-spreadsheet mr-1 text-muted"></i> Unit Satuan
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="product_unit"
                                                name="product_unit"
                                                class="form-control form-control-lg @error('product_unit') is-invalid @enderror"
                                                required>
                                            @foreach(\Modules\Setting\Entities\Unit::all() as $unit)
                                            <option value="{{ $unit->short_name }}" {{ old('product_unit') == $unit->short_name ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->short_name }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('product_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 5: Notes --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-notes mr-2 text-primary"></i>
                                Catatan Tambahan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-0">
                                <label for="product_note" class="form-label font-weight-semibold">
                                    <i class="cil-pencil mr-1 text-muted"></i> Catatan
                                </label>
                                <textarea id="product_note"
                                          name="product_note"
                                          rows="4"
                                          class="form-control @error('product_note') is-invalid @enderror"
                                          placeholder="Tambahkan catatan atau informasi tambahan tentang produk...">{{ old('product_note') }}</textarea>
                                @error('product_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="cil-info mr-1"></i>
                                    Informasi opsional untuk keperluan internal
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Images --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-sidebar">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-image mr-2 text-primary"></i>
                                Gambar Produk
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            {{-- Use the image upload component --}}
                            <x-image-upload 
                                max-files="3" 
                                label="" 
                                max-size="2"
                                help-text="Upload maksimal 3 gambar produk. Format: JPG, PNG. Ukuran maks: 2MB per file."
                            />
                            
                            <div class="alert alert-info mt-3" role="alert">
                                <small>
                                    <i class="cil-lightbulb mr-1"></i>
                                    <strong>Tips:</strong> Gunakan gambar dengan kualitas baik dan pencahayaan cukup untuk hasil terbaik.
                                </small>
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
<script src="{{ asset('js/dropzone.js') }}"></script>
@endsection

@push('page_scripts')
<script src="{{ asset('js/jquery-mask-money.js') }}"></script>
<script>
$(document).ready(function() {
    // Mask money for currency fields
    $('#product_cost, #product_price').maskMoney({
        prefix: '{{ settings()->currency->symbol }} ',
        thousands: '{{ settings()->currency->thousand_separator }}',
        decimal: '{{ settings()->currency->decimal_separator }}',
        precision: 0
    });

    $('#product_cost, #product_price').maskMoney('mask');

    // Calculate profit margin
    function calculateProfit() {
        const cost = parseFloat($('#product_cost').maskMoney('unmasked')[0]) || 0;
        const price = parseFloat($('#product_price').maskMoney('unmasked')[0]) || 0;
        
        if (cost > 0 && price > 0) {
            const profit = price - cost;
            const percentage = ((profit / cost) * 100).toFixed(2);
            
            const formattedProfit = '{{ settings()->currency->symbol }} ' + profit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '{{ settings()->currency->thousand_separator }}');
            
            $('#profitAmount').text(formattedProfit);
            $('#profitPercentage').text(percentage + '%');
            $('#profitMarginAlert').fadeIn();
            
            // Color coding
            if (percentage < 10) {
                $('#profitPercentage').removeClass('badge-primary badge-success').addClass('badge-danger');
            } else if (percentage < 30) {
                $('#profitPercentage').removeClass('badge-primary badge-danger').addClass('badge-warning');
            } else {
                $('#profitPercentage').removeClass('badge-danger badge-warning').addClass('badge-success');
            }
        } else {
            $('#profitMarginAlert').fadeOut();
        }
    }

    // Update profit on change
    $('#product_cost, #product_price').on('blur', calculateProfit);

    // Generate new product code
    $('#generateCode').on('click', function() {
        const randomNum = Math.floor(Math.random() * 9999) + 1;
        const newCode = 'PRD-' + randomNum.toString().padStart(4, '0');
        $('#product_code').val(newCode);
        
        // Show feedback
        $(this).find('i').addClass('rotating');
        setTimeout(() => {
            $(this).find('i').removeClass('rotating');
        }, 500);
    });

    // Unmask before submit
    $('#product-form').on('submit', function(e) {
        // Unmask currency values
        const cost = $('#product_cost').maskMoney('unmasked')[0];
        const price = $('#product_price').maskMoney('unmasked')[0];
        
        $('#product_cost').val(cost);
        $('#product_price').val(price);
        
        // Validate
        if (parseFloat(cost) <= 0 || parseFloat(price) <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Harga Tidak Valid',
                text: 'Modal dan harga jual harus lebih besar dari 0',
                confirmButtonColor: '#4834DF'
            });
            
            // Re-mask
            $('#product_cost, #product_price').maskMoney('mask');
            return false;
        }
        
        if (parseFloat(price) < parseFloat(cost)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Harga jual lebih rendah dari modal. Yakin ingin melanjutkan?',
                showCancelButton: true,
                confirmButtonColor: '#4834DF',
                cancelButtonColor: '#768192',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).off('submit').submit();
                } else {
                    // Re-mask
                    $('#product_cost, #product_price').maskMoney('mask');
                }
            });
            return false;
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Auto-focus first input
    $('#product_name').focus();
});

// CSS for rotating animation
const style = document.createElement('style');
style.textContent = `
    .rotating {
        animation: rotate 0.5s ease-in-out;
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
@endpush

@push('page_styles')
<style>
    /* ========== Animations ========== */
    .animated.fadeIn {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ========== Sticky Action Bar ========== */
    .action-bar {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: white;
        padding: 1.25rem;
        border-radius: 10px;
        margin-bottom: 0;
    }

    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    /* ========== Form Enhancements ========== */
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
        min-height: 80px;
    }

    /* ========== Sticky Sidebar ========== */
    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }

    /* ========== Badge Sizing ========== */
    .badge-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }

    /* ========== Input Group Button ========== */
    .input-group-append .btn {
        height: 50px;
        border-color: #ced4da;
    }

    .input-group-append .btn:hover {
        background-color: #f8f9fa;
        border-color: #4834DF;
        color: #4834DF;
    }

    /* ========== Alert Styling ========== */
    .alert-info {
        background-color: #e7f6fc;
        border-color: #8ad4ee;
        color: #115293;
        border-radius: 8px;
    }

    /* ========== Button Gap ========== */
    .d-flex.gap-2 > * {
        margin-left: 0.5rem;
    }

    .d-flex.gap-2 > *:first-child {
        margin-left: 0;
    }

    /* ========== Responsive ========== */
    @media (max-width: 992px) {
        .sticky-sidebar {
            position: relative;
            top: 0;
            margin-top: 1rem;
        }

        .action-bar {
            position: relative;
        }

        .action-bar .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .action-bar .d-flex > div {
            width: 100%;
        }
    }
</style>
@endpush
