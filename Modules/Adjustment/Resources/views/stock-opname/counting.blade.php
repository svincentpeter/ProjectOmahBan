@extends('layouts.app')

@section('title', 'Counting - ' . $stockOpname->reference)

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.index') }}">Stock Opname</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.show', $stockOpname->id) }}">{{ $stockOpname->reference }}</a></li>
        <li class="breadcrumb-item active">Counting</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    {{-- HEADER CARD: INFO & PROGRESS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1">
                        <i class="bi bi-calculator"></i> {{ $stockOpname->reference }}
                    </h4>
                    <small>
                        <i class="bi bi-calendar3"></i> {{ $stockOpname->opname_date->format('d/m/Y') }}
                        &nbsp;|&nbsp;
                        <i class="bi bi-person"></i> PIC: {{ $stockOpname->pic->name }}
                    </small>
                </div>
                <div class="col-md-6 text-md-right">
                    <h5 class="mb-1">Progress Penghitungan</h5>
                    <div class="progress bg-white" style="height: 30px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                             role="progressbar" 
                             style="width: {{ $stockOpname->completion_percentage }}%"
                             id="progress-bar">
                            <strong id="progress-text">{{ $stockOpname->completion_percentage }}%</strong>
                        </div>
                    </div>
                    <small class="text-white-50 mt-1 d-block">
                        <span id="counted-items">{{ $stockOpname->items->whereNotNull('actual_qty')->count() }}</span> 
                        dari 
                        <span id="total-items">{{ $stockOpname->items->count() }}</span> 
                        item telah dihitung
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body bg-light">
            {{-- QUICK STATS --}}
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Item</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stockOpname->items->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sudah Dihitung</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-counted">
                                {{ $stockOpname->items->whereNotNull('actual_qty')->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Dihitung</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-pending">
                                {{ $stockOpname->items->whereNull('actual_qty')->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger shadow-sm h-100">
                        <div class="card-body py-3">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ada Selisih</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-variance">
                                {{ $stockOpname->items->whereNotNull('actual_qty')->filter(fn($i) => $i->variance_qty != 0)->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                        <input type="text" 
                               class="form-control border-left-0" 
                               id="search-product" 
                               placeholder="Cari kode/nama produk...">
                    </div>
                </div>

                <div class="col-md-3">
                    <select class="form-control" id="filter-variance">
                        <option value="all">Semua Item</option>
                        <option value="pending">Belum Dihitung</option>
                        <option value="counted">Sudah Dihitung</option>
                        <option value="match">Cocok (No Variance)</option>
                        <option value="surplus">Surplus (Lebih)</option>
                        <option value="shortage">Shortage (Kurang)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-control" id="filter-category">
                        <option value="">Semua Kategori</option>
                        @foreach($stockOpname->items->pluck('product.category')->unique('id') as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary btn-block" id="reset-filters">
                        <i class="bi bi-x-circle"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCTS GRID --}}
    <div id="products-grid">
        <div class="row" id="product-cards">
            @forelse($stockOpname->items as $item)
                @include('adjustment::stock-opname.partials._counting-card', ['item' => $item])
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Tidak ada item untuk dihitung.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- FLOATING ACTION BUTTON --}}
    <div class="floating-actions">
        <button type="button" 
                class="btn btn-lg btn-success shadow-lg" 
                id="complete-btn"
                data-toggle="tooltip"
                title="Selesaikan Opname">
            <i class="bi bi-check-circle-fill"></i> Selesaikan
        </button>

        <button type="button" 
                class="btn btn-lg btn-secondary shadow-lg ml-2" 
                onclick="window.location.href='{{ route('stock-opnames.show', $stockOpname->id) }}'"
                data-toggle="tooltip"
                title="Simpan & Keluar">
            <i class="bi bi-save"></i> Simpan & Keluar
        </button>
    </div>
</div>

{{-- MODAL: INPUT ACTUAL QTY --}}
<div class="modal fade" id="countModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calculator"></i> Input Hasil Hitungan Fisik
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="product-info mb-3 p-3 bg-light rounded">
                    <h6 class="mb-2" id="modal-product-name"></h6>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Kode:</span>
                        <strong id="modal-product-code"></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Stok Sistem:</span>
                        <strong class="text-primary" id="modal-system-qty"></strong>
                    </div>
                </div>

                <form id="count-form">
                    <input type="hidden" id="item-id" name="item_id">

                    <div class="form-group">
                        <label for="actual_qty" class="font-weight-bold">
                            Hasil Hitungan Fisik <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control form-control-lg count-input text-center" 
                               id="actual_qty" 
                               name="actual_qty" 
                               min="0" 
                               step="1" 
                               required 
                               autofocus>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Masukkan jumlah produk yang Anda hitung secara fisik
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="variance_reason">Alasan Selisih (Jika Ada)</label>
                        <textarea class="form-control" 
                                  id="variance_reason" 
                                  name="variance_reason" 
                                  rows="2" 
                                  placeholder="Contoh: Produk rusak 2 unit, hilang 1 unit"></textarea>
                    </div>

                    {{-- VARIANCE PREVIEW --}}
                    <div class="alert d-none" id="variance-preview">
                        <strong>Selisih:</strong> 
                        <span id="variance-value"></span>
                        <span id="variance-type-badge"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="save-count-btn">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_styles')
<style>
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .border-left-danger { border-left: 4px solid #e74a3b; }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Product Card */
    .product-card {
        transition: all 0.3s ease;
        border: 2px solid #e3e6f0;
        cursor: pointer;
        min-height: 280px;
    }

    .product-card:hover {
        border-color: #4e73df;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        transform: translateY(-5px);
    }

    .product-card.counted {
        background: linear-gradient(to bottom, #f0fff4 0%, #ffffff 100%);
        border-color: #1cc88a;
    }

    .product-card.variance-shortage {
        background: linear-gradient(to bottom, #fff5f5 0%, #ffffff 100%);
        border-left: 5px solid #e74a3b;
    }

    .product-card.variance-surplus {
        background: linear-gradient(to bottom, #f0f9ff 0%, #ffffff 100%);
        border-left: 5px solid #36b9cc;
    }

    .product-card.variance-match {
        background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        border-left: 5px solid #1cc88a;
    }

    /* Count Input */
    .count-input {
        font-size: 1.5rem;
        font-weight: 700;
        border: 2px solid #dee2e6;
        border-radius: 0.5rem;
    }

    .count-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Floating Actions */
    .floating-actions {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }

    /* Badges */
    .badge-xl {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }

    /* Skeleton Loading (optional) */
    .skeleton {
        animation: skeleton-loading 1s linear infinite alternate;
    }

    @keyframes skeleton-loading {
        0% { background-color: hsl(200, 20%, 80%); }
        100% { background-color: hsl(200, 20%, 95%); }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .floating-actions {
            bottom: 15px;
            right: 15px;
        }
        
        .floating-actions .btn {
            display: block;
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    let currentItem = null;
    let systemQty = 0;

    // ================================================
    // OPEN COUNT MODAL
    // ================================================
    $(document).on('click', '.product-card', function() {
        const itemId = $(this).data('item-id');
        const productName = $(this).data('product-name');
        const productCode = $(this).data('product-code');
        systemQty = parseInt($(this).data('system-qty'));
        const currentActual = $(this).data('actual-qty');

        currentItem = itemId;

        // Populate modal
        $('#item-id').val(itemId);
        $('#modal-product-name').text(productName);
        $('#modal-product-code').text(productCode);
        $('#modal-system-qty').text(systemQty + ' unit');
        
        // Set current value jika sudah pernah dihitung
        if (currentActual !== null && currentActual !== '') {
            $('#actual_qty').val(currentActual);
            calculateVariance();
        } else {
            $('#actual_qty').val('');
            $('#variance-preview').addClass('d-none');
        }

        // Open modal
        $('#countModal').modal('show');
        
        // Focus input setelah modal muncul
        setTimeout(() => {
            $('#actual_qty').focus().select();
        }, 500);
    });

    // ================================================
    // CALCULATE VARIANCE ON INPUT
    // ================================================
    $('#actual_qty').on('input', function() {
        calculateVariance();
    });

    function calculateVariance() {
        const actualQty = parseInt($('#actual_qty').val()) || 0;
        const variance = actualQty - systemQty;

        if (variance === 0) {
            $('#variance-preview')
                .removeClass('d-none alert-danger alert-info alert-success')
                .addClass('alert-success')
                .html(`
                    <strong>✓ Cocok!</strong> Tidak ada selisih.
                `);
        } else if (variance > 0) {
            $('#variance-preview')
                .removeClass('d-none alert-danger alert-success')
                .addClass('alert-info')
                .html(`
                    <strong>Surplus:</strong> 
                    <span class="text-info">+${variance} unit (Lebih dari sistem)</span>
                `);
        } else {
            $('#variance-preview')
                .removeClass('d-none alert-info alert-success')
                .addClass('alert-danger')
                .html(`
                    <strong>Shortage:</strong> 
                    <span class="text-danger">${variance} unit (Kurang dari sistem)</span>
                `);
        }
    }

    // ================================================
    // SAVE COUNT (AJAX)
    // ================================================
    $('#save-count-btn').on('click', function() {
        const itemId = $('#item-id').val();
        const actualQty = $('#actual_qty').val();
        const varianceReason = $('#variance_reason').val();

        if (!actualQty || actualQty < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Valid',
                text: 'Masukkan jumlah hasil hitungan fisik!',
                confirmButtonColor: '#4e73df'
            });
            return;
        }

        // Show loading
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

        // AJAX Save
        $.ajax({
            url: `/stock-opnames/items/${itemId}/update-count`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                actual_qty: actualQty,
                variance_reason: varianceReason
            },
            success: function(response) {
                if (response.success) {
                    // Update card UI
                    updateCardUI(itemId, actualQty, response.variance, response.variance_type);

                    // Update stats
                    updateStats(response.completion);

                    // Close modal
                    $('#countModal').modal('hide');

                    // Toast success
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });

                    // Reset form
                    $('#count-form')[0].reset();
                } else {
                    throw new Error(response.message || 'Gagal menyimpan');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan',
                    confirmButtonColor: '#e74a3b'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ================================================
    // UPDATE CARD UI AFTER SAVE
    // ================================================
    function updateCardUI(itemId, actualQty, variance, varianceType) {
        const $card = $(`.product-card[data-item-id="${itemId}"]`);
        
        // Update actual qty display
        $card.find('.actual-qty-display').text(actualQty);
        $card.data('actual-qty', actualQty);

        // Update variance display
        let varianceBadge = '';
        $card.removeClass('counted variance-shortage variance-surplus variance-match');
        
        if (varianceType === 'match') {
            varianceBadge = '<span class="badge badge-success">Cocok ✓</span>';
            $card.addClass('counted variance-match');
        } else if (varianceType === 'surplus') {
            varianceBadge = `<span class="badge badge-info">+${variance} (Lebih)</span>`;
            $card.addClass('counted variance-surplus');
        } else if (varianceType === 'shortage') {
            varianceBadge = `<span class="badge badge-danger">${variance} (Kurang)</span>`;
            $card.addClass('counted variance-shortage');
        }

        $card.find('.variance-display').html(varianceBadge);

        // Add checkmark icon
        if (!$card.find('.counted-icon').length) {
            $card.find('.card-header').append(
                '<i class="bi bi-check-circle-fill text-success counted-icon" style="font-size: 1.5rem; position: absolute; top: 10px; right: 10px;"></i>'
            );
        }
    }

    // ================================================
    // UPDATE STATS & PROGRESS BAR
    // ================================================
    function updateStats(completionPercentage) {
        // Update progress bar
        $('#progress-bar').css('width', completionPercentage + '%');
        $('#progress-text').text(completionPercentage + '%');

        // Recalculate stats from DOM
        const totalItems = $('.product-card').length;
        const countedItems = $('.product-card.counted').length;
        const pendingItems = totalItems - countedItems;
        const varianceItems = $('.product-card.variance-shortage, .product-card.variance-surplus').length;

        $('#counted-items').text(countedItems);
        $('#stat-counted').text(countedItems);
        $('#stat-pending').text(pendingItems);
        $('#stat-variance').text(varianceItems);

        // Auto-enable complete button jika 100%
        if (completionPercentage >= 100) {
            $('#complete-btn').removeClass('disabled').prop('disabled', false);
            
            Swal.fire({
                icon: 'success',
                title: '🎉 Semua Item Telah Dihitung!',
                text: 'Klik tombol "Selesaikan" untuk menyelesaikan stock opname.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#1cc88a'
            });
        }
    }

    // ================================================
    // FILTER & SEARCH
    // ================================================
    $('#search-product').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterProducts();
    });

    $('#filter-variance, #filter-category').on('change', function() {
        filterProducts();
    });

    $('#reset-filters').on('click', function() {
        $('#search-product').val('');
        $('#filter-variance').val('all');
        $('#filter-category').val('');
        filterProducts();
    });

    function filterProducts() {
        const searchTerm = $('#search-product').val().toLowerCase();
        const varianceFilter = $('#filter-variance').val();
        const categoryFilter = $('#filter-category').val();

        $('.product-card').each(function() {
            const $card = $(this);
            const productName = $card.data('product-name').toLowerCase();
            const productCode = $card.data('product-code').toLowerCase();
            const varianceType = $card.hasClass('variance-match') ? 'match' :
                                $card.hasClass('variance-surplus') ? 'surplus' :
                                $card.hasClass('variance-shortage') ? 'shortage' :
                                $card.hasClass('counted') ? 'counted' : 'pending';
            const categoryId = $card.data('category-id');

            let showCard = true;

            // Search filter
            if (searchTerm && !productName.includes(searchTerm) && !productCode.includes(searchTerm)) {
                showCard = false;
            }

            // Variance filter
            if (varianceFilter !== 'all') {
                if (varianceFilter === 'counted' && !$card.hasClass('counted')) {
                    showCard = false;
                } else if (varianceFilter === 'pending' && $card.hasClass('counted')) {
                    showCard = false;
                } else if (varianceFilter !== 'counted' && varianceFilter !== 'pending' && varianceType !== varianceFilter) {
                    showCard = false;
                }
            }

            // Category filter
            if (categoryFilter && categoryId != categoryFilter) {
                showCard = false;
            }

            $card.closest('.col-md-4').toggle(showCard);
        });
    }

    // ================================================
    // COMPLETE BUTTON
    // ================================================
    $('#complete-btn').on('click', function() {
        const uncountedItems = $('.product-card').not('.counted').length;
        
        if (uncountedItems > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Selesai',
                text: `Masih ada ${uncountedItems} item yang belum dihitung!`,
                confirmButtonColor: '#f6c23e'
            });
            return;
        }

        Swal.fire({
            title: 'Selesaikan Stock Opname?',
            html: `
                <p>Setelah diselesaikan:</p>
                <ul class="text-left">
                    <li>Sistem akan membuat <strong>Adjustment</strong> untuk selisih</li>
                    <li>Data tidak bisa diubah lagi</li>
                    <li>Status berubah menjadi <strong>COMPLETED</strong></li>
                </ul>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("stock-opnames.complete", $stockOpname->id) }}';
            }
        });
    });

    // ================================================
    // KEYBOARD SHORTCUTS
    // ================================================
    $(document).on('keydown', '#actual_qty', function(e) {
        // Enter = Save
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#save-count-btn').click();
        }
        // Esc = Close modal
        if (e.key === 'Escape') {
            $('#countModal').modal('hide');
        }
    });

    // ================================================
    // TOOLTIP INIT
    // ================================================
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
