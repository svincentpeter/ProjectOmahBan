@extends('layouts.app-flowbite')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index'), 'icon' => 'bi bi-box-seam-fill']
    ]])
@endsection



@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

   {{-- Statistics Cards (Line Color, bukan full gradient) --}}
@php
    $totalProduk = \Modules\Product\Entities\Product::active()->count();
    $stokRendah  = \Modules\Product\Entities\Product::active()
        ->whereColumn('product_quantity', '<=', 'product_stock_alert')
        ->where('product_quantity', '>', 0)
        ->count();
    $totalKategori = \Modules\Product\Entities\Category::count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Total Produk --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-blue-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                <i class="bi bi-box-seam text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Produk</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalProduk }}</p>
                <p class="text-xs text-slate-500 mt-1">Produk aktif terdaftar</p>
            </div>
        </div>
    </div>

    {{-- Stok Rendah (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route("products.index", ["quick_filter" => "low-stock"]) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                <i class="bi bi-exclamation-triangle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Stok Rendah</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $stokRendah }}</p>
                <p class="text-xs text-slate-500 mt-1">Butuh restock segera</p>
            </div>
        </div>
    </button>

    {{-- Total Kategori --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-emerald-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-folder text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Kategori</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalKategori }}</p>
                <p class="text-xs text-slate-500 mt-1">Kategori tersedia</p>
            </div>
        </div>
    </div>

</div>


    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="px-6 pt-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-box-seam text-blue-600"></i>
                        Daftar Produk
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola produk dan inventory toko Anda</p>
                </div>
                
                @can('create_products')
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk
                </a>
                @endcan
            </div>

            @php
                $categories = \Modules\Product\Entities\Category::orderBy('category_name')->pluck('category_name', 'id')->toArray();
            @endphp
        </div>

        {{-- Quick Filter - Inline --}}
        <div class="px-6 pt-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-5 shadow-md">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-funnel-fill text-white text-lg"></i>
                    <h3 class="text-white font-semibold">Quick Filter</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    {{-- Merk --}}
                    <div>
                        <label class="block text-blue-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-tag-fill mr-1"></i> Merk
                        </label>
                        <select name="brand_id" id="quick-brand-filter"
                                class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 transition-all">
                            <option value="">Semua Merk</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ukuran --}}
                    <div>
                        <label class="block text-blue-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-rulers mr-1"></i> Ukuran
                        </label>
                        <input type="text" name="product_size" id="quick-size-filter"
                               placeholder="Contoh: 185/65"
                               value="{{ request('product_size') }}"
                               class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 placeholder:text-slate-400 transition-all">
                    </div>

                    {{-- Ring --}}
                    <div>
                        <label class="block text-blue-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-circle mr-1"></i> Ring
                        </label>
                        <input type="text" name="ring" id="quick-ring-filter"
                               placeholder="Contoh: R15"
                               value="{{ request('ring') }}"
                               class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 placeholder:text-slate-400 transition-all">
                    </div>

                    {{-- Status Stok --}}
                    <div>
                        <label class="block text-blue-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-box-seam mr-1"></i> Status Stok
                        </label>
                        <select name="quick_filter" id="quick-status-filter"
                                class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 transition-all">
                            <option value="">Semua Status</option>
                            <option value="low-stock" {{ request('quick_filter') == 'low-stock' ? 'selected' : '' }}>Stok Menipis</option>
                            <option value="out-of-stock" {{ request('quick_filter') == 'out-of-stock' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2">
                        <button type="button" id="apply-quick-filter"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 bg-white hover:bg-blue-50 text-blue-700 font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm">
                            <i class="bi bi-search"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                        <button type="button" id="reset-quick-filter"
                                class="inline-flex items-center justify-center gap-1.5 bg-white/20 hover:bg-white/30 text-white font-medium py-2.5 px-4 rounded-lg transition-all">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="px-6 pb-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left text-gray-900 dark:text-gray-400', 'id' => 'products-table']) !!}
        </div>
    </div>

    {{-- Smart Filter Modal - Auto Show on Page Load --}}
    <div id="smart-filter-modal" tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative w-full max-w-md p-4">
            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="bi bi-search text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Cari Produk Cepat</h3>
                                <p class="text-blue-100 text-xs">Temukan ban dengan mudah</p>
                            </div>
                        </div>
                        <button type="button" id="close-filter-modal"
                                class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 transition-all">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-5">
                    {{-- Merk (Required) --}}
                    <div>
                        <label for="smart-brand-filter" class="block mb-2 text-sm font-semibold text-slate-700">
                            <i class="bi bi-tag-fill text-blue-600 mr-1"></i>
                            Merk Ban <span class="text-red-500">*</span>
                        </label>
                        <select id="smart-brand-filter" 
                                class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4 transition-all hover:bg-white">
                            <option value="">-- Pilih Merk --</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Size & Ring Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Ukuran (Optional) --}}
                        <div>
                            <label for="smart-size-filter" class="block mb-2 text-sm font-semibold text-slate-700">
                                <i class="bi bi-rulers text-emerald-600 mr-1"></i>
                                Ukuran
                            </label>
                            <input type="text" id="smart-size-filter" 
                                   placeholder="Contoh: 185/65"
                                   class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4 transition-all hover:bg-white placeholder:text-slate-400">
                        </div>

                        {{-- Ring (Optional) --}}
                        <div>
                            <label for="smart-ring-filter" class="block mb-2 text-sm font-semibold text-slate-700">
                                <i class="bi bi-circle text-amber-600 mr-1"></i>
                                Ring
                            </label>
                            <input type="text" id="smart-ring-filter" 
                                   placeholder="Contoh: R15"
                                   class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4 transition-all hover:bg-white placeholder:text-slate-400">
                        </div>
                    </div>

                    {{-- Info Text --}}
                    <p class="text-xs text-slate-500 flex items-start gap-2">
                        <i class="bi bi-info-circle text-blue-500 mt-0.5"></i>
                        Merk wajib dipilih. Ukuran dan Ring bersifat opsional untuk mempersempit pencarian.
                    </p>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" id="apply-smart-filter"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-sm hover:shadow-md">
                        <i class="bi bi-search"></i>
                        Cari Produk
                    </button>
                    <button type="button" id="skip-filter"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-3 px-4 rounded-xl transition-all">
                        <i class="bi bi-list-ul"></i>
                        Lihat Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{!! $dataTable->scripts() !!}

<script>
    // Smart Filter Modal Logic
    const smartFilterModal = document.getElementById('smart-filter-modal');
    const closeFilterModalBtn = document.getElementById('close-filter-modal');
    const applySmartFilterBtn = document.getElementById('apply-smart-filter');
    const skipFilterBtn = document.getElementById('skip-filter');
    const smartBrandFilter = document.getElementById('smart-brand-filter');
    const smartSizeFilter = document.getElementById('smart-size-filter');
    const smartRingFilter = document.getElementById('smart-ring-filter');

    // Store smart filter values for DataTable
    let smartFilterValues = {
        brand_id: '',
        product_size: '',
        ring: ''
    };

    // Check if user came with pre-applied filters (from URL params)
    const urlParams = new URLSearchParams(window.location.search);
    const hasPreFilters = urlParams.has('brand_id') || urlParams.has('quick_filter');

    // Show modal on page load (unless pre-filtered)
    document.addEventListener('DOMContentLoaded', function() {
        if (!hasPreFilters) {
            smartFilterModal.classList.remove('hidden');
            smartFilterModal.style.display = 'flex';
        } else {
            smartFilterModal.style.display = 'none';
        }
    });

    // Close modal function
    function closeSmartFilterModal() {
        smartFilterModal.style.opacity = '0';
        setTimeout(() => {
            smartFilterModal.style.display = 'none';
            smartFilterModal.style.opacity = '1';
        }, 200);
    }

    // Close button handler
    closeFilterModalBtn.addEventListener('click', function() {
        closeSmartFilterModal();
    });

    // Skip filter (view all) handler
    skipFilterBtn.addEventListener('click', function() {
        // Reset smart filter values
        smartFilterValues = { brand_id: '', product_size: '', ring: '' };
        closeSmartFilterModal();
        
        // Reload DataTable without smart filters
        if (window.LaravelDataTables && window.LaravelDataTables['products-table']) {
            window.LaravelDataTables['products-table'].ajax.reload();
        }
    });

    // Apply smart filter handler
    applySmartFilterBtn.addEventListener('click', function() {
        const brandVal = smartBrandFilter.value;
        
        if (!brandVal) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Merk',
                text: 'Silakan pilih merk ban terlebih dahulu',
                confirmButtonColor: '#3b82f6'
            });
            smartBrandFilter.focus();
            return;
        }

        // Store filter values
        smartFilterValues.brand_id = brandVal;
        smartFilterValues.product_size = smartSizeFilter.value.trim();
        smartFilterValues.ring = smartRingFilter.value.trim();

        closeSmartFilterModal();

        // Reload DataTable with smart filters
        if (window.LaravelDataTables && window.LaravelDataTables['products-table']) {
            window.LaravelDataTables['products-table'].ajax.reload();
        }
    });

    // Close modal on backdrop click
    smartFilterModal.addEventListener('click', function(e) {
        if (e.target === smartFilterModal) {
            closeSmartFilterModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && smartFilterModal.style.display !== 'none') {
            closeSmartFilterModal();
        }
    });

    // DataTable preXhr handler - send all filters
    document.addEventListener('DOMContentLoaded', function() {
        $('#products-table').on('preXhr.dt', function(e, settings, data) {
            // Quick Filter inline values
            data.brand_id = $('#quick-brand-filter').val() || smartFilterValues.brand_id;
            data.product_size = $('#quick-size-filter').val() || smartFilterValues.product_size;
            data.ring = $('#quick-ring-filter').val() || smartFilterValues.ring;
            data.quick_filter = $('#quick-status-filter').val();
            
            // Smart filter modal values (take priority if set and modal was used)
            if (smartFilterValues.brand_id && !$('#quick-brand-filter').val()) {
                data.brand_id = smartFilterValues.brand_id;
            }
            if (smartFilterValues.product_size && !$('#quick-size-filter').val()) {
                data.product_size = smartFilterValues.product_size;
            }
            if (smartFilterValues.ring && !$('#quick-ring-filter').val()) {
                data.ring = smartFilterValues.ring;
            }
        });

        // Quick Filter Apply Button
        $('#apply-quick-filter').on('click', function() {
            if (window.LaravelDataTables && window.LaravelDataTables['products-table']) {
                window.LaravelDataTables['products-table'].ajax.reload();
            }
        });

        // Quick Filter Reset Button
        $('#reset-quick-filter').on('click', function() {
            // Reset all quick filter inputs
            $('#quick-brand-filter').val('');
            $('#quick-size-filter').val('');
            $('#quick-ring-filter').val('');
            $('#quick-status-filter').val('');
            
            // Reset smart filter values too
            smartFilterValues = { brand_id: '', product_size: '', ring: '' };
            
            // Reload DataTable
            if (window.LaravelDataTables && window.LaravelDataTables['products-table']) {
                window.LaravelDataTables['products-table'].ajax.reload();
            }
        });

        // Allow Enter key to trigger search in Quick Filter inputs
        $('#quick-size-filter, #quick-ring-filter').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#apply-quick-filter').click();
            }
        });
    });

    // Delete confirmation
    $(document).ready(function() {
        $(document).on('click', '.delete-product', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Hapus Produk?',
                html: `Produk <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-zinc-500">Data yang dihapus tidak dapat dikembalikan!</small>`,
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
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

