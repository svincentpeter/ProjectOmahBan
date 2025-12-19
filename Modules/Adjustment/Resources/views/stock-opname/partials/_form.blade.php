{{-- 
    Reusable Form untuk Create & Edit Stock Opname (Tailwind Version)
    
    Variables yang dibutuhkan:
    - $isEdit (boolean) - true jika mode edit
    - $stockOpname (Model) - jika edit, null jika create
    - $categories (Collection) - daftar kategori
--}}

@php
    $isEdit = $isEdit ?? false;
    $stockOpname = $stockOpname ?? null;
@endphp

<form action="{{ $isEdit ? route('stock-opnames.update', $stockOpname->id) : route('stock-opnames.store') }}" 
      method="POST" 
      id="opname-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- SECTION 1: INFORMASI DASAR --}}
    <div class="mb-6">
        <h5 class="text-lg font-bold text-black border-b border-zinc-200 pb-2 mb-4 flex items-center gap-2">
            <span class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
            Informasi Dasar
        </h5>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="opname_date" class="block mb-1.5 text-sm font-bold text-black">
                    Tanggal Opname <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-medium @error('opname_date') border-red-500 @enderror" 
                       id="opname_date" 
                       name="opname_date" 
                       value="{{ old('opname_date', $isEdit ? $stockOpname->opname_date->format('Y-m-d') : now()->toDateString()) }}"
                       required>
                @error('opname_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-zinc-500 mt-1">
                    <i class="bi bi-calendar3 me-1"></i> Tanggal pelaksanaan penghitungan fisik
                </p>
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-black">Reference</label>
                <input type="text" 
                       class="bg-zinc-100 border border-zinc-300 text-black text-sm rounded-xl block w-full p-2.5 font-medium cursor-not-allowed" 
                       value="{{ $isEdit ? $stockOpname->reference : 'SO-' . now()->format('Ymd') . '-#####' }}" 
                       readonly>
                <p class="text-xs text-zinc-500 mt-1">
                    <i class="bi bi-hash me-1"></i> Nomor referensi {{ $isEdit ? 'tidak bisa diubah' : 'dibuat otomatis' }}
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: SCOPE PRODUK --}}
    <div class="mb-6">
        <h5 class="text-lg font-bold text-black border-b border-zinc-200 pb-2 mb-4 flex items-center gap-2">
            <span class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
            Pilih Produk yang Akan Dihitung
        </h5>

        @if($isEdit)
            <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-4 mb-4 text-amber-700">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Perhatian!</strong> 
                Jika Anda mengubah jenis opname atau produk yang dipilih, semua data hitungan akan direset ulang.
            </div>
        @endif

        {{-- SCOPE TYPE SELECTOR --}}
        <div class="mb-4">
            <label class="block mb-2 text-sm font-bold text-black">
                Jenis Opname <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- OPTION 1: ALL PRODUCTS --}}
                <div class="scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'all' ? 'active' : '' }}" 
                     data-scope="all">
                    <input type="radio" 
                           name="scope_type" 
                           value="all" 
                           id="scope_all"
                           {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'all' ? 'checked' : '' }}
                           class="hidden scope-radio">
                    <label for="scope_all" class="cursor-pointer block text-center p-6">
                        <i class="bi bi-box-seam text-blue-600 text-4xl"></i>
                        <h6 class="font-bold text-black mt-3 mb-1">Semua Produk</h6>
                        <p class="text-xs text-zinc-500">Hitung semua produk aktif di sistem</p>
                    </label>
                </div>

                {{-- OPTION 2: BY CATEGORY --}}
                <div class="scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? 'active' : '' }}" 
                     data-scope="category">
                    <input type="radio" 
                           name="scope_type" 
                           value="category" 
                           id="scope_category"
                           {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? 'checked' : '' }}
                           class="hidden scope-radio">
                    <label for="scope_category" class="cursor-pointer block text-center p-6">
                        <i class="bi bi-collection text-amber-500 text-4xl"></i>
                        <h6 class="font-bold text-black mt-3 mb-1">Per Kategori</h6>
                        <p class="text-xs text-zinc-500">Hitung produk di kategori tertentu (Ban, Velg, dll)</p>
                    </label>
                </div>

                {{-- OPTION 3: CUSTOM PRODUCTS --}}
                <div class="scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? 'active' : '' }}" 
                     data-scope="custom">
                    <input type="radio" 
                           name="scope_type" 
                           value="custom" 
                           id="scope_custom"
                           {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? 'checked' : '' }}
                           class="hidden scope-radio">
                    <label for="scope_custom" class="cursor-pointer block text-center p-6">
                        <i class="bi bi-list-check text-emerald-500 text-4xl"></i>
                        <h6 class="font-bold text-black mt-3 mb-1">Pilih Manual</h6>
                        <p class="text-xs text-zinc-500">Pilih produk tertentu secara manual</p>
                    </label>
                </div>
            </div>
            @error('scope_type')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- SCOPE DETAIL: CATEGORY SELECTOR --}}
        <div id="category-selector" class="scope-detail {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? '' : 'hidden' }}">
            <div class="bg-zinc-50 rounded-xl p-4 border border-zinc-200">
                <label class="block mb-2 text-sm font-bold text-black">
                    <i class="bi bi-funnel me-1"></i> Pilih Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_ids[]" 
                        id="category_ids" 
                        class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm @error('category_ids') border-red-500 @enderror" 
                        multiple>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ in_array($category->id, old('category_ids', $isEdit && $stockOpname->scope_type == 'category' ? ($stockOpname->scope_ids ?? []) : [])) ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-zinc-500 mt-2">
                    Bisa pilih lebih dari 1 kategori (contoh: Ban dan Velg)
                </p>
            </div>
        </div>

        {{-- SCOPE DETAIL: CUSTOM PRODUCT SELECTOR --}}
        <div id="product-selector" class="scope-detail {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? '' : 'hidden' }}">
            <div class="bg-zinc-50 rounded-xl p-4 border border-zinc-200">
                <label class="block mb-2 text-sm font-bold text-black">
                    <i class="bi bi-search me-1"></i> Pilih Produk <span class="text-red-500">*</span>
                </label>
                <select name="product_ids[]" 
                        id="product_ids" 
                        class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm @error('product_ids') border-red-500 @enderror" 
                        multiple>
                    @if($isEdit && $stockOpname->scope_type == 'custom')
                        @foreach($stockOpname->items as $item)
                            <option value="{{ $item->product_id }}" selected>
                                {{ $item->product->product_code }} - {{ $item->product->product_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('product_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-zinc-500 mt-2">
                    Ketik nama/kode produk untuk mencari. Bisa pilih banyak sekaligus.
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 3: CATATAN --}}
    <div class="mb-6">
        <h5 class="text-lg font-bold text-black border-b border-zinc-200 pb-2 mb-4 flex items-center gap-2">
            <span class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
            Catatan (Opsional)
        </h5>

        <div>
            <label for="notes" class="block mb-1.5 text-sm font-bold text-black">
                <i class="bi bi-chat-left-text me-1"></i> Catatan Tambahan
            </label>
            <textarea name="notes" 
                      id="notes" 
                      rows="3" 
                      class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm @error('notes') border-red-500 @enderror"
                      placeholder="Contoh: Stock opname bulanan Desember 2025, fokus di produk ban Bridgestone dan Michelin">{{ old('notes', $isEdit ? $stockOpname->notes : '') }}</textarea>
            @error('notes')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-zinc-500 mt-1">Maksimal 500 karakter</p>
        </div>
    </div>

    {{-- SUMMARY INFO --}}
    <div id="summary-info" class="hidden bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="bi bi-info-circle text-blue-600 text-2xl"></i>
            <div>
                <p class="font-bold text-blue-800">
                    Estimasi Produk yang Akan Dihitung: 
                    <span id="estimated-count" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white ms-1">
                        {{ $isEdit ? $stockOpname->items->count() : 0 }} produk
                    </span>
                </p>
                <p class="text-sm text-blue-700 mt-1">
                    Data akan di-snapshot pada saat opname {{ $isEdit ? 'diupdate' : 'dibuat' }}. 
                    Perubahan stok setelah opname dimulai tidak akan mempengaruhi data.
                </p>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-4 border-t border-zinc-200">
        <a href="{{ route('stock-opnames.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-black font-medium rounded-xl hover:bg-zinc-50 transition-all">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>

        <button type="submit" id="submit-btn" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
            <i class="bi bi-{{ $isEdit ? 'save' : 'check-circle' }} me-2"></i> 
            {{ $isEdit ? 'Update Stock Opname' : 'Buat Stock Opname' }}
        </button>
    </div>
</form>

{{-- SHARED STYLES --}}
@push('page_styles')
<style>
    .scope-option-card {
        border: 2px solid #e4e4e7;
        border-radius: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    .scope-option-card:hover {
        border-color: #2563eb;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    .scope-option-card.active {
        border-color: #2563eb;
        background: linear-gradient(to bottom, #eff6ff, white);
        box-shadow: 0 10px 25px -5px rgba(37,99,235,0.2);
    }
    
    /* Select2 Tailwind Override */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d4d4d8 !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem !important;
        min-height: 42px !important;
    }
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2563eb !important;
        border: none !important;
        border-radius: 9999px !important;
        padding: 2px 10px !important;
        color: white !important;
        font-size: 0.75rem !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #e4e4e7 !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
    }
    .select2-results__option--highlighted {
        background-color: #2563eb !important;
    }
</style>
@endpush

{{-- SHARED SCRIPTS --}}
@push('page_scripts')
<script>
$(document).ready(function() {
    const isEdit = {{ $isEdit ? 'true' : 'false' }};

    // ============================================
    // SCOPE TYPE SELECTOR
    // ============================================
    $('.scope-option-card').on('click', function() {
        const scope = $(this).data('scope');
        const radio = $(this).find('.scope-radio');
        
        $('.scope-option-card').removeClass('active');
        $(this).addClass('active');
        radio.prop('checked', true);
        
        $('.scope-detail').addClass('hidden');
        
        if (scope === 'category') {
            $('#category-selector').removeClass('hidden');
            initCategorySelect2();
        } else if (scope === 'custom') {
            $('#product-selector').removeClass('hidden');
            initProductSelect2();
        }

        updateEstimatedCount(scope);
    });

    // Trigger active state on page load
    const selectedScope = $('input[name="scope_type"]:checked').val();
    if (selectedScope) {
        $(`.scope-option-card[data-scope="${selectedScope}"]`).addClass('active');
        
        if (selectedScope === 'category') {
            initCategorySelect2();
        } else if (selectedScope === 'custom') {
            initProductSelect2();
        }

        @if($isEdit)
        updateEstimatedCount(selectedScope);
        @endif
    }

    // ============================================
    // SELECT2 INITIALIZATION
    // ============================================
    function initCategorySelect2() {
        if ($('#category_ids').hasClass('select2-hidden-accessible')) {
            return;
        }

        $('#category_ids').select2({
            placeholder: 'Pilih satu atau lebih kategori...',
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            updateEstimatedCount('category');
        });
    }

    function initProductSelect2() {
        if ($('#product_ids').hasClass('select2-hidden-accessible')) {
            return;
        }

        $('#product_ids').select2({
            placeholder: 'Ketik nama atau kode produk...',
            allowClear: true,
            width: '100%',
            minimumInputLength: isEdit ? 0 : 2,
            ajax: {
                url: '{{ route("products.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.id,
                                text: `${item.product_code} - ${item.product_name} (Stok: ${item.product_quantity})`
                            };
                        }),
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                cache: true
            }
        }).on('change', function() {
            updateEstimatedCount('custom');
        });
    }

    // ============================================
    // ESTIMATED COUNT
    // ============================================
    function updateEstimatedCount(scope) {
        let count = 0;
        
        if (scope === 'all') {
            $.get('{{ route("products.count-active") }}', function(response) {
                displayEstimate(response.count);
            });
        } else if (scope === 'category') {
            const categoryIds = $('#category_ids').val();
            if (categoryIds && categoryIds.length > 0) {
                $.post('{{ route("products.count-by-category") }}', {
                    _token: '{{ csrf_token() }}',
                    category_ids: categoryIds
                }, function(response) {
                    displayEstimate(response.count);
                });
            } else {
                $('#summary-info').addClass('hidden');
            }
        } else if (scope === 'custom') {
            count = $('#product_ids').val() ? $('#product_ids').val().length : 0;
            displayEstimate(count);
        }
    }

    function displayEstimate(count) {
        if (count > 0) {
            $('#estimated-count').text(`${count} produk`);
            $('#summary-info').removeClass('hidden');
        } else {
            $('#summary-info').addClass('hidden');
        }
    }

    // ============================================
    // FORM VALIDATION
    // ============================================
    $('#opname-form').on('submit', function(e) {
        const scope = $('input[name="scope_type"]:checked').val();
        
        if (!scope) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Jenis Opname Belum Dipilih',
                text: 'Silakan pilih jenis opname terlebih dahulu!',
                confirmButtonColor: '#2563eb'
            });
            return false;
        }

        if (scope === 'category') {
            const categories = $('#category_ids').val();
            if (!categories || categories.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Kategori Belum Dipilih',
                    text: 'Silakan pilih minimal 1 kategori!',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
        }

        if (scope === 'custom') {
            const products = $('#product_ids').val();
            if (!products || products.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Produk Belum Dipilih',
                    text: 'Silakan pilih minimal 1 produk!',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
        }

        @if($isEdit)
        if (!confirm('Mengubah scope akan mereset semua data hitungan. Lanjutkan?')) {
            e.preventDefault();
            return false;
        }
        @endif

        const $btn = $('#submit-btn');
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split animate-spin me-2"></i> Menyimpan...');
    });
});
</script>
@endpush
