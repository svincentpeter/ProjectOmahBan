@extends('layouts.app-flowbite')

@section('title', 'Counting - ' . $stockOpname->reference)

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('breadcrumb_items')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.index') }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">Stock Opname</a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.show', $stockOpname->id) }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">{{ $stockOpname->reference }}</a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <span class="text-sm font-bold text-zinc-900">Counting</span>
        </div>
    </li>
@endsection

@section('content')
    {{-- HEADER CARD: INFO & PROGRESS --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 mb-6 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-2xl font-bold flex items-center gap-2">
                        <i class="bi bi-calculator"></i> {{ $stockOpname->reference }}
                    </h4>
                    <p class="text-indigo-100 text-sm mt-1 flex items-center gap-3">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ $stockOpname->opname_date->format('d/m/Y') }}</span>
                        <span><i class="bi bi-person me-1"></i> PIC: {{ $stockOpname->pic->name }}</span>
                    </p>
                </div>
                <div class="text-right md:min-w-[280px]">
                    <h5 class="text-sm font-semibold text-indigo-100 mb-2">Progress Penghitungan</h5>
                    <div class="w-full bg-white/30 rounded-full h-8 mb-2 overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all flex items-center justify-center text-indigo-700 font-bold text-sm animate-pulse" 
                             id="progress-bar" 
                             style="width: {{ $stockOpname->completion_percentage }}%">
                            <span id="progress-text">{{ $stockOpname->completion_percentage }}%</span>
                        </div>
                    </div>
                    <p class="text-indigo-100 text-xs">
                        <span id="counted-items">{{ $stockOpname->items->whereNotNull('actual_qty')->count() }}</span> 
                        dari 
                        <span id="total-items">{{ $stockOpname->items->count() }}</span> 
                        item telah dihitung
                    </p>
                </div>
            </div>
        </div>

        {{-- QUICK STATS --}}
        <div class="p-6 bg-gradient-to-b from-slate-50 to-white">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white border-l-4 border-blue-500 rounded-xl p-4 shadow-sm">
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Total Item</p>
                    <p class="text-2xl font-bold text-black">{{ $stockOpname->items->count() }}</p>
                </div>
                <div class="bg-white border-l-4 border-emerald-500 rounded-xl p-4 shadow-sm">
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Sudah Dihitung</p>
                    <p class="text-2xl font-bold text-emerald-600" id="stat-counted">{{ $stockOpname->items->whereNotNull('actual_qty')->count() }}</p>
                </div>
                <div class="bg-white border-l-4 border-amber-500 rounded-xl p-4 shadow-sm">
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Belum Dihitung</p>
                    <p class="text-2xl font-bold text-amber-600" id="stat-pending">{{ $stockOpname->items->whereNull('actual_qty')->count() }}</p>
                </div>
                <div class="bg-white border-l-4 border-red-500 rounded-xl p-4 shadow-sm">
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Ada Selisih</p>
                    <p class="text-2xl font-bold text-red-600" id="stat-variance">{{ $stockOpname->items->whereNotNull('actual_qty')->filter(fn($i) => $i->variance_qty != 0)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH CARD --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-md mb-6 p-5">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label class="block mb-1.5 text-xs font-bold text-zinc-600">
                    <i class="bi bi-search me-1"></i> Cari Produk
                </label>
                <input type="text" 
                       id="search-product" 
                       placeholder="Cari kode/nama produk..."
                       class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-medium">
            </div>

            <div class="md:col-span-3">
                <label class="block mb-1.5 text-xs font-bold text-zinc-600">
                    <i class="bi bi-funnel me-1"></i> Filter Status
                </label>
                <select id="filter-variance" class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-medium">
                    <option value="all">Semua Item</option>
                    <option value="pending">Belum Dihitung</option>
                    <option value="counted">Sudah Dihitung</option>
                    <option value="match">Cocok (No Variance)</option>
                    <option value="surplus">Surplus (Lebih)</option>
                    <option value="shortage">Shortage (Kurang)</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block mb-1.5 text-xs font-bold text-zinc-600">
                    <i class="bi bi-collection me-1"></i> Kategori
                </label>
                <select id="filter-category" class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($stockOpname->items->pluck('product.category')->unique('id') as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <button type="button" id="reset-filters" class="w-full text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 font-bold rounded-xl text-sm px-4 py-2.5 focus:outline-none transition-all shadow-sm">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- PRODUCTS GRID --}}
    <div id="products-grid">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="product-cards">
            @forelse($stockOpname->items as $item)
                @include('adjustment::stock-opname.partials._counting-card', ['item' => $item])
            @empty
                <div class="col-span-full">
                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-4 text-amber-700">
                        <i class="bi bi-exclamation-triangle me-2"></i> Tidak ada item untuk dihitung.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- FLOATING ACTION BUTTONS --}}
    <div class="fixed bottom-6 right-6 z-50 flex flex-col md:flex-row gap-3">
        <button type="button" 
                id="complete-btn"
                class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-2xl shadow-lg hover:bg-emerald-700 transition-all hover:scale-105">
            <i class="bi bi-check-circle-fill me-2"></i> Selesaikan
        </button>

        <a href="{{ route('stock-opnames.show', $stockOpname->id) }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-zinc-600 text-white font-bold rounded-2xl shadow-lg hover:bg-zinc-700 transition-all hover:scale-105">
            <i class="bi bi-save me-2"></i> Simpan & Keluar
        </a>
    </div>

{{-- MODAL: INPUT ACTUAL QTY --}}
<div id="countModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="modalBackdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 z-10">
        <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl">
            <h5 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-calculator"></i> Input Hasil Hitungan Fisik
            </h5>
            <button type="button" id="closeModal" class="absolute top-4 right-4 text-white/80 hover:text-white">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="bg-zinc-50 rounded-xl p-4 mb-4">
                <h6 class="font-bold text-black mb-2" id="modal-product-name"></h6>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Kode:</span>
                    <strong class="text-black" id="modal-product-code"></strong>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Stok Sistem:</span>
                    <strong class="text-blue-600" id="modal-system-qty"></strong>
                </div>
            </div>

            <form id="count-form">
                <input type="hidden" id="item-id" name="item_id">

                <div class="mb-4">
                    <label for="actual_qty" class="block mb-2 text-sm font-bold text-zinc-700">
                        Hasil Hitungan Fisik <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="actual_qty" 
                           name="actual_qty" 
                           min="0" 
                           step="1" 
                           required 
                           class="w-full text-center text-3xl font-bold border-2 border-zinc-200 rounded-xl p-4 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-zinc-500 mt-1">
                        <i class="bi bi-info-circle me-1"></i> Masukkan jumlah produk yang Anda hitung secara fisik
                    </p>
                </div>

                <div class="mb-4">
                    <label for="variance_reason" class="block mb-2 text-sm font-bold text-zinc-700">
                        Alasan Selisih (Jika Ada)
                    </label>
                    <textarea id="variance_reason" 
                              name="variance_reason" 
                              rows="2" 
                              placeholder="Contoh: Produk rusak 2 unit, hilang 1 unit"
                              class="w-full border border-zinc-300 rounded-xl p-3 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                {{-- VARIANCE PREVIEW --}}
                <div class="hidden rounded-xl p-4 mb-4" id="variance-preview"></div>
            </form>
        </div>
        
        <div class="p-5 border-t border-zinc-100 flex justify-end gap-3 bg-zinc-50 rounded-b-2xl">
            <button type="button" id="cancelModalBtn" class="px-5 py-2.5 bg-white border border-zinc-300 text-zinc-700 font-medium rounded-xl hover:bg-zinc-50 transition-all">
                <i class="bi bi-x-circle me-1"></i> Batal
            </button>
            <button type="button" id="save-count-btn" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all">
                <i class="bi bi-check-circle me-1"></i> Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('page_styles')
<style>
    /* Product Card */
    .product-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15);
    }
    .product-card.counted {
        background: linear-gradient(to bottom, #f0fdf4 0%, #ffffff 100%);
        border-color: #10b981;
    }
    .product-card.variance-shortage {
        background: linear-gradient(to bottom, #fef2f2 0%, #ffffff 100%);
        border-left: 4px solid #ef4444;
    }
    .product-card.variance-surplus {
        background: linear-gradient(to bottom, #eff6ff 0%, #ffffff 100%);
        border-left: 4px solid #3b82f6;
    }
    .product-card.variance-match {
        background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
        border-left: 4px solid #10b981;
    }

    /* Progress bar animation */
    @keyframes progressPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    let currentItem = null;
    let systemQty = 0;

    // ================================================
    // MODAL FUNCTIONS
    // ================================================
    function openModal() {
        $('#countModal').removeClass('hidden').addClass('flex');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => $('#actual_qty').focus().select(), 100);
    }

    function closeModal() {
        $('#countModal').removeClass('flex').addClass('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $('#modalBackdrop, #closeModal, #cancelModalBtn').on('click', closeModal);

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

        $('#item-id').val(itemId);
        $('#modal-product-name').text(productName);
        $('#modal-product-code').text(productCode);
        $('#modal-system-qty').text(systemQty + ' unit');
        
        if (currentActual !== null && currentActual !== '') {
            $('#actual_qty').val(currentActual);
            calculateVariance();
        } else {
            $('#actual_qty').val('');
            $('#variance-preview').addClass('hidden');
        }

        openModal();
    });

    // ================================================
    // CALCULATE VARIANCE ON INPUT
    // ================================================
    $('#actual_qty').on('input', calculateVariance);

    function calculateVariance() {
        const actualQty = parseInt($('#actual_qty').val()) || 0;
        const variance = actualQty - systemQty;
        const $preview = $('#variance-preview');

        $preview.removeClass('hidden bg-emerald-50 bg-blue-50 bg-red-50 text-emerald-700 text-blue-700 text-red-700');

        if (variance === 0) {
            $preview.addClass('bg-emerald-50 text-emerald-700')
                .html('<strong>✓ Cocok!</strong> Tidak ada selisih.');
        } else if (variance > 0) {
            $preview.addClass('bg-blue-50 text-blue-700')
                .html(`<strong>Surplus:</strong> +${variance} unit (Lebih dari sistem)`);
        } else {
            $preview.addClass('bg-red-50 text-red-700')
                .html(`<strong>Shortage:</strong> ${variance} unit (Kurang dari sistem)`);
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
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...');

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
                    updateCardUI(itemId, actualQty, response.variance, response.variance_type);
                    updateStats(response.completion);
                    closeModal();

                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });

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
                    confirmButtonColor: '#ef4444'
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
        
        $card.find('.actual-qty-display').text(actualQty);
        $card.data('actual-qty', actualQty);

        let varianceBadge = '';
        $card.removeClass('counted variance-shortage variance-surplus variance-match');
        
        if (varianceType === 'match') {
            varianceBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Cocok ✓</span>';
            $card.addClass('counted variance-match');
        } else if (varianceType === 'surplus') {
            varianceBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">+${variance} (Lebih)</span>`;
            $card.addClass('counted variance-surplus');
        } else if (varianceType === 'shortage') {
            varianceBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">${variance} (Kurang)</span>`;
            $card.addClass('counted variance-shortage');
        }

        $card.find('.variance-display').html(varianceBadge);

        if (!$card.find('.counted-icon').length) {
            $card.find('.product-card-header').append(
                '<i class="bi bi-check-circle-fill text-emerald-500 absolute top-3 right-3 text-xl"></i>'
            );
        }
    }

    // ================================================
    // UPDATE STATS & PROGRESS BAR
    // ================================================
    function updateStats(completionPercentage) {
        $('#progress-bar').css('width', completionPercentage + '%');
        $('#progress-text').text(completionPercentage + '%');

        const totalItems = $('.product-card').length;
        const countedItems = $('.product-card.counted').length;
        const pendingItems = totalItems - countedItems;
        const varianceItems = $('.product-card.variance-shortage, .product-card.variance-surplus').length;

        $('#counted-items').text(countedItems);
        $('#stat-counted').text(countedItems);
        $('#stat-pending').text(pendingItems);
        $('#stat-variance').text(varianceItems);

        if (completionPercentage >= 100) {
            $('#complete-btn').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
            
            Swal.fire({
                icon: 'success',
                title: '🎉 Semua Item Telah Dihitung!',
                text: 'Klik tombol "Selesaikan" untuk menyelesaikan stock opname.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10b981'
            });
        }
    }

    // ================================================
    // FILTER & SEARCH
    // ================================================
    $('#search-product').on('keyup', filterProducts);
    $('#filter-variance, #filter-category').on('change', filterProducts);

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
            const productName = ($card.data('product-name') || '').toLowerCase();
            const productCode = ($card.data('product-code') || '').toLowerCase();
            const varianceType = $card.hasClass('variance-match') ? 'match' :
                                $card.hasClass('variance-surplus') ? 'surplus' :
                                $card.hasClass('variance-shortage') ? 'shortage' :
                                $card.hasClass('counted') ? 'counted' : 'pending';
            const categoryId = $card.data('category-id');

            let showCard = true;

            if (searchTerm && !productName.includes(searchTerm) && !productCode.includes(searchTerm)) {
                showCard = false;
            }

            if (varianceFilter !== 'all') {
                if (varianceFilter === 'counted' && !$card.hasClass('counted')) {
                    showCard = false;
                } else if (varianceFilter === 'pending' && $card.hasClass('counted')) {
                    showCard = false;
                } else if (varianceFilter !== 'counted' && varianceFilter !== 'pending' && varianceType !== varianceFilter) {
                    showCard = false;
                }
            }

            if (categoryFilter && categoryId != categoryFilter) {
                showCard = false;
            }

            $card.toggle(showCard);
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
                confirmButtonColor: '#f59e0b'
            });
            return;
        }

        Swal.fire({
            title: 'Selesaikan Stock Opname?',
            html: `
                <p class="text-left text-zinc-600">Setelah diselesaikan:</p>
                <ul class="text-left text-sm text-zinc-600 mt-2 space-y-1">
                    <li>• Sistem akan membuat <strong>Adjustment</strong> untuk selisih</li>
                    <li>• Data tidak bisa diubah lagi</li>
                    <li>• Status berubah menjadi <strong>COMPLETED</strong></li>
                </ul>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                window.location.href = '{{ route("stock-opnames.complete", $stockOpname->id) }}';
            }
        });
    });

    // ================================================
    // KEYBOARD SHORTCUTS
    // ================================================
    $(document).on('keydown', '#actual_qty', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#save-count-btn').click();
        }
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});
</script>
@endpush
