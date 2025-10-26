@extends('layouts.app')

@section('title', 'Buat Penyesuaian Stok')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
    <li class="breadcrumb-item active">Buat</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <form id="adjustment-form" action="{{ route('adjustments.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Penyesuaian
                    </button>
                </div>
            </div>

            {{-- Header Info Card --}}
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-info-circle text-primary"></i> Informasi Penyesuaian
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference">Referensi</label>
                                    <input type="text" class="form-control bg-light" id="reference" value="Auto-generated: ADJ-XXX" readonly disabled>
                                    <small class="text-muted">Referensi akan dibuat otomatis</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="note">Catatan</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Alasan penyesuaian stok (opsional)">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products Selection Card --}}
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-box-seam text-success"></i> Daftar Produk
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" id="add-product-row">
                                <i class="bi bi-plus-circle"></i> Tambah Produk
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Produk</th>
                                        <th width="15%">Stok Saat Ini</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Tipe</th>
                                        <th width="10%">Stok Akhir</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows">
                                    {{-- Dynamic rows akan ditambahkan di sini --}}
                                </tbody>
                            </table>
                        </div>

                        <div id="empty-state" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                            <p class="text-muted mt-2">Belum ada produk ditambahkan</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="$('#add-product-row').click()">
                                <i class="bi bi-plus-circle"></i> Tambah Produk Pertama
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    let rowIndex = 0;
    const products = @json(\Modules\Product\Entities\Product::with('category', 'brand')->get());
    
    // Template untuk row baru
    function getProductRowTemplate(index) {
        return `
            <tr data-index="${index}">
                <td>
                    <select class="form-control form-control-sm product-select" name="product_ids[]" required data-index="${index}">
                        <option value="">Pilih Produk</option>
                        ${products.map(p => `
                            <option value="${p.id}" 
                                    data-stock="${p.product_quantity}" 
                                    data-unit="${p.product_unit}"
                                    data-name="${p.product_name}">
                                ${p.product_code} - ${p.product_name} (${p.category ? p.category.category_name : 'No Category'})
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center current-stock bg-light" readonly value="-">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center quantity-input" 
                           name="quantities[]" min="1" required placeholder="0" data-index="${index}">
                </td>
                <td>
                    <select class="form-control form-control-sm type-select" name="types[]" required data-index="${index}">
                        <option value="add" selected>
                            <i class="bi bi-plus-circle"></i> Penambahan
                        </option>
                        <option value="sub">
                            <i class="bi bi-dash-circle"></i> Pengurangan
                        </option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center final-stock bg-light font-weight-bold" readonly value="-">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row" data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // Add product row
    $('#add-product-row').click(function() {
        const newRow = getProductRowTemplate(rowIndex);
        $('#product-rows').append(newRow);
        rowIndex++;
        toggleEmptyState();
        
        // Initialize Select2 for new row
        $(`select.product-select[data-index="${rowIndex - 1}"]`).select2({
            placeholder: 'Cari produk...',
            allowClear: true,
            width: '100%'
        });
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        const index = $(this).data('index');
        $(`tr[data-index="${index}"]`).remove();
        toggleEmptyState();
    });

    // Product selection change
    $(document).on('change', '.product-select', function() {
        const index = $(this).data('index');
        const selectedOption = $(this).find('option:selected');
        const stock = selectedOption.data('stock') || 0;
        const unit = selectedOption.data('unit') || 'PC';
        
        $(`tr[data-index="${index}"] .current-stock`).val(`${stock} ${unit}`);
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
        
        const finalStockInput = row.find('.final-stock');
        finalStockInput.val(`${finalStock} ${unit}`);
        
        // Warning untuk stok negatif
        if (finalStock < 0) {
            finalStockInput.addClass('text-danger font-weight-bold');
            finalStockInput.removeClass('text-success');
            
            // Show alert
            if (!$('#stock-warning').length) {
                const warning = `
                    <div id="stock-warning" class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Peringatan!</strong> Stok akhir produk akan menjadi negatif. Periksa kembali jumlah pengurangan.
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                $('#products-table').before(warning);
            }
        } else {
            finalStockInput.removeClass('text-danger');
            finalStockInput.addClass('text-success');
            $('#stock-warning').remove();
        }
    }

    // Toggle empty state
    function toggleEmptyState() {
        if ($('#product-rows tr').length === 0) {
            $('#empty-state').show();
            $('#products-table').hide();
        } else {
            $('#empty-state').hide();
            $('#products-table').show();
        }
    }

    // Form validation before submit
    $('#adjustment-form').submit(function(e) {
        if ($('#product-rows tr').length === 0) {
            e.preventDefault();
            alert('Minimal satu produk harus ditambahkan!');
            return false;
        }
        
        // Check for negative stock
        let hasNegativeStock = false;
        $('.final-stock').each(function() {
            const value = parseInt($(this).val());
            if (value < 0) {
                hasNegativeStock = true;
            }
        });
        
        if (hasNegativeStock) {
            if (!confirm('Ada produk dengan stok akhir negatif. Lanjutkan?')) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });

    // Initialize
    toggleEmptyState();
    
    // Add first row by default
    $('#add-product-row').click();
});
</script>
@endpush

@push('page_css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    
    .product-select {
        font-size: 0.875rem;
    }
    
    .current-stock, .final-stock {
        font-weight: 600;
    }
    
    .select2-container {
        width: 100% !important;
    }
    
    .select2-selection {
        height: calc(1.5em + 0.5rem + 2px) !important;
        font-size: 0.875rem !important;
    }
</style>
@endpush

@section('third_party_scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
