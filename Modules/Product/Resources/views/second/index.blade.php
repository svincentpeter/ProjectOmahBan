@extends('layouts.app-flowbite')

@section('title', 'Daftar Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Manajemen Produk', 'url' => '#'],
            ['text' => 'Produk Bekas', 'url' => route('products_second.index'), 'icon' => 'bi bi-recycle'],
        ],
    ])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards (Line Color, bukan full gradient) --}}
@php
    $totalSecond   = \Modules\Product\Entities\ProductSecond::count();
    $availableSecond = \Modules\Product\Entities\ProductSecond::where('status', 'available')->count();
    $soldSecond      = \Modules\Product\Entities\ProductSecond::where('status', 'sold')->count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Total Produk --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-purple-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-purple-50 text-purple-700 ring-1 ring-purple-100">
                <i class="bi bi-recycle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Produk</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Produk bekas terdaftar</p>
            </div>
        </div>
    </div>

    {{-- Tersedia (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route('products_second.index', ['status' => 'available']) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-check-circle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Tersedia</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $availableSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Siap dijual</p>
            </div>
        </div>
    </button>

    {{-- Terjual (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route('products_second.index', ['status' => 'sold']) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-rose-50 text-rose-700 ring-1 ring-rose-100">
                <i class="bi bi-x-circle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Terjual</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $soldSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Sudah keluar stok</p>
            </div>
        </div>
    </button>

</div>


    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">

        {{-- Card Header --}}
        <div class="px-6 pt-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-recycle text-purple-600"></i>
                        Daftar Produk Bekas
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola produk ban & velg bekas</p>
                </div>

                <a href="{{ route('products_second.create') }}"
                    class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk Bekas
                </a>
            </div>
        </div>

        {{-- Quick Filter - Inline --}}
        <div class="px-6 pt-6">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl p-5 shadow-md">
                <div class="flex items-center gap-2 mb-4">
                    <i class="bi bi-funnel-fill text-white text-lg"></i>
                    <h3 class="text-white font-semibold">Quick Filter</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Merk --}}
                    <div>
                        <label class="block text-purple-100 text-xs font-medium mb-1.5">
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
                        <label class="block text-purple-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-rulers mr-1"></i> Ukuran
                        </label>
                        <input type="text" name="size" id="quick-size-filter"
                               placeholder="Contoh: 185/65"
                               value="{{ request('size') }}"
                               class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 placeholder:text-slate-400 transition-all">
                    </div>

                    {{-- Ring --}}
                    <div>
                        <label class="block text-purple-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-circle mr-1"></i> Ring
                        </label>
                        <input type="text" name="ring" id="quick-ring-filter"
                               placeholder="Contoh: R15"
                               value="{{ request('ring') }}"
                               class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 placeholder:text-slate-400 transition-all">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-purple-100 text-xs font-medium mb-1.5">
                            <i class="bi bi-check-circle mr-1"></i> Status
                        </label>
                        <select name="status" id="quick-status-filter"
                                class="w-full rounded-lg border-0 bg-white/90 text-slate-800 text-sm py-2.5 px-3 focus:ring-2 focus:ring-white/50 transition-all">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Terjual</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2">
                        <button type="button" id="apply-quick-filter"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 bg-white hover:bg-purple-50 text-purple-700 font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm">
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

        {{-- DataTable --}}
        <div class="px-6 py-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left', 'id' => 'product-second-table']) !!}
        </div>
    </div>

    {{-- Smart Filter Modal - Auto Show on Page Load --}}
    <div id="smart-filter-modal" tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative w-full max-w-md p-4">
            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="bi bi-search text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Cari Produk Bekas</h3>
                                <p class="text-purple-100 text-xs">Temukan ban bekas dengan mudah</p>
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
                            <i class="bi bi-tag-fill text-purple-600 mr-1"></i>
                            Merk Ban <span class="text-red-500">*</span>
                        </label>
                        <select id="smart-brand-filter" 
                                class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-purple-500 focus:border-purple-500 py-3 px-4 transition-all hover:bg-white">
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
                                   class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-purple-500 focus:border-purple-500 py-3 px-4 transition-all hover:bg-white placeholder:text-slate-400">
                        </div>

                        {{-- Ring (Optional) --}}
                        <div>
                            <label for="smart-ring-filter" class="block mb-2 text-sm font-semibold text-slate-700">
                                <i class="bi bi-circle text-amber-600 mr-1"></i>
                                Ring
                            </label>
                            <input type="text" id="smart-ring-filter" 
                                   placeholder="Contoh: R15"
                                   class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm focus:ring-purple-500 focus:border-purple-500 py-3 px-4 transition-all hover:bg-white placeholder:text-slate-400">
                        </div>
                    </div>

                    {{-- Info Text --}}
                    <p class="text-xs text-slate-500 flex items-start gap-2">
                        <i class="bi bi-info-circle text-purple-500 mt-0.5"></i>
                        Merk wajib dipilih. Ukuran dan Ring bersifat opsional untuk mempersempit pencarian.
                    </p>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" id="apply-smart-filter"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-sm hover:shadow-md">
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
            size: '',
            ring: ''
        };

        // Check if user came with pre-applied filters (from URL params)
        const urlParams = new URLSearchParams(window.location.search);
        const hasPreFilters = urlParams.has('brand_id') || urlParams.has('status');

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
            smartFilterValues = { brand_id: '', size: '', ring: '' };
            closeSmartFilterModal();
            
            if (window.LaravelDataTables && window.LaravelDataTables['product-second-table']) {
                window.LaravelDataTables['product-second-table'].ajax.reload();
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
                    confirmButtonColor: '#9333ea'
                });
                smartBrandFilter.focus();
                return;
            }

            smartFilterValues.brand_id = brandVal;
            smartFilterValues.size = smartSizeFilter.value.trim();
            smartFilterValues.ring = smartRingFilter.value.trim();

            closeSmartFilterModal();

            if (window.LaravelDataTables && window.LaravelDataTables['product-second-table']) {
                window.LaravelDataTables['product-second-table'].ajax.reload();
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
            $('#product-second-table').on('preXhr.dt', function(e, settings, data) {
                // Quick Filter inline values
                data.brand_id = $('#quick-brand-filter').val() || smartFilterValues.brand_id;
                data.size = $('#quick-size-filter').val() || smartFilterValues.size;
                data.ring = $('#quick-ring-filter').val() || smartFilterValues.ring;
                data.status = $('#quick-status-filter').val();
                
                // Smart filter modal values (take priority if set and modal was used)
                if (smartFilterValues.brand_id && !$('#quick-brand-filter').val()) {
                    data.brand_id = smartFilterValues.brand_id;
                }
                if (smartFilterValues.size && !$('#quick-size-filter').val()) {
                    data.size = smartFilterValues.size;
                }
                if (smartFilterValues.ring && !$('#quick-ring-filter').val()) {
                    data.ring = smartFilterValues.ring;
                }
            });

            // Quick Filter Apply Button
            $('#apply-quick-filter').on('click', function() {
                if (window.LaravelDataTables && window.LaravelDataTables['product-second-table']) {
                    window.LaravelDataTables['product-second-table'].ajax.reload();
                }
            });

            // Quick Filter Reset Button
            $('#reset-quick-filter').on('click', function() {
                $('#quick-brand-filter').val('');
                $('#quick-size-filter').val('');
                $('#quick-ring-filter').val('');
                $('#quick-status-filter').val('');
                
                smartFilterValues = { brand_id: '', size: '', ring: '' };
                
                if (window.LaravelDataTables && window.LaravelDataTables['product-second-table']) {
                    window.LaravelDataTables['product-second-table'].ajax.reload();
                }
            });

            // Allow Enter key to trigger search
            $('#quick-size-filter, #quick-ring-filter').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#apply-quick-filter').click();
                }
            });
        });
    </script>
@endpush

