{{-- 
    Reusable Form untuk Create & Edit Stock Opname
    
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
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">
            <i class="bi bi-1-circle-fill text-primary"></i> Informasi Dasar
        </h5>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="opname_date">
                        Tanggal Opname <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('opname_date') is-invalid @enderror" 
                           id="opname_date" 
                           name="opname_date" 
                           value="{{ old('opname_date', $isEdit ? $stockOpname->opname_date->format('Y-m-d') : now()->toDateString()) }}"
                           required>
                    @error('opname_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="bi bi-calendar3"></i> Tanggal pelaksanaan penghitungan fisik
                    </small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Reference</label>
                    <input type="text" 
                           class="form-control bg-light" 
                           value="{{ $isEdit ? $stockOpname->reference : 'SO-' . now()->format('Ymd') . '-#####' }}" 
                           readonly>
                    <small class="form-text text-muted">
                        <i class="bi bi-hash"></i> Nomor referensi {{ $isEdit ? 'tidak bisa diubah' : 'dibuat otomatis' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: SCOPE PRODUK --}}
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">
            <i class="bi bi-2-circle-fill text-primary"></i> Pilih Produk yang Akan Dihitung
        </h5>

        @if($isEdit)
            {{-- WARNING: Edit scope akan reset progress counting --}}
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Perhatian!</strong> 
                Jika Anda mengubah jenis opname atau produk yang dipilih, semua data hitungan akan direset ulang.
            </div>
        @endif

        {{-- SCOPE TYPE SELECTOR --}}
        <div class="form-group">
            <label>Jenis Opname <span class="text-danger">*</span></label>
            <div class="row">
                {{-- OPTION 1: ALL PRODUCTS --}}
                <div class="col-md-4">
                    <div class="card scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'all' ? 'active' : '' }}" 
                         data-scope="all">
                        <div class="card-body text-center">
                            <input type="radio" 
                                   name="scope_type" 
                                   value="all" 
                                   id="scope_all"
                                   {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'all' ? 'checked' : '' }}
                                   class="d-none scope-radio">
                            <label for="scope_all" class="w-100 mb-0 cursor-pointer">
                                <i class="bi bi-box-seam text-primary" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1">Semua Produk</h6>
                                <small class="text-muted">
                                    Hitung semua produk aktif di sistem
                                </small>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- OPTION 2: BY CATEGORY --}}
                <div class="col-md-4">
                    <div class="card scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? 'active' : '' }}" 
                         data-scope="category">
                        <div class="card-body text-center">
                            <input type="radio" 
                                   name="scope_type" 
                                   value="category" 
                                   id="scope_category"
                                   {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? 'checked' : '' }}
                                   class="d-none scope-radio">
                            <label for="scope_category" class="w-100 mb-0 cursor-pointer">
                                <i class="bi bi-collection text-warning" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1">Per Kategori</h6>
                                <small class="text-muted">
                                    Hitung produk di kategori tertentu (Ban, Velg, dll)
                                </small>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- OPTION 3: CUSTOM PRODUCTS --}}
                <div class="col-md-4">
                    <div class="card scope-option-card {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? 'active' : '' }}" 
                         data-scope="custom">
                        <div class="card-body text-center">
                            <input type="radio" 
                                   name="scope_type" 
                                   value="custom" 
                                   id="scope_custom"
                                   {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? 'checked' : '' }}
                                   class="d-none scope-radio">
                            <label for="scope_custom" class="w-100 mb-0 cursor-pointer">
                                <i class="bi bi-list-check text-success" style="font-size: 2.5rem;"></i>
                                <h6 class="mt-2 mb-1">Pilih Manual</h6>
                                <small class="text-muted">
                                    Pilih produk tertentu secara manual
                                </small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @error('scope_type')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        {{-- SCOPE DETAIL: CATEGORY SELECTOR --}}
        <div id="category-selector" class="scope-detail {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'category' ? '' : 'd-none' }}">
            <div class="card bg-light">
                <div class="card-body">
                    <label>
                        <i class="bi bi-funnel"></i> Pilih Kategori <span class="text-danger">*</span>
                    </label>
                    <select name="category_ids[]" 
                            id="category_ids" 
                            class="form-control select2-multiple @error('category_ids') is-invalid @enderror" 
                            multiple>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ in_array($category->id, old('category_ids', $isEdit && $stockOpname->scope_type == 'category' ? ($stockOpname->scope_ids ?? []) : [])) ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Bisa pilih lebih dari 1 kategori (contoh: Ban dan Velg)
                    </small>
                </div>
            </div>
        </div>

        {{-- SCOPE DETAIL: CUSTOM PRODUCT SELECTOR --}}
        <div id="product-selector" class="scope-detail {{ old('scope_type', $isEdit ? $stockOpname->scope_type : '') == 'custom' ? '' : 'd-none' }}">
            <div class="card bg-light">
                <div class="card-body">
                    <label>
                        <i class="bi bi-search"></i> Pilih Produk <span class="text-danger">*</span>
                    </label>
                    <select name="product_ids[]" 
                            id="product_ids" 
                            class="form-control select2-ajax @error('product_ids') is-invalid @enderror" 
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
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Ketik nama/kode produk untuk mencari. Bisa pilih banyak sekaligus.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: CATATAN --}}
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">
            <i class="bi bi-3-circle-fill text-primary"></i> Catatan (Opsional)
        </h5>

        <div class="form-group">
            <label for="notes">
                <i class="bi bi-chat-left-text"></i> Catatan Tambahan
            </label>
            <textarea name="notes" 
                      id="notes" 
                      rows="3" 
                      class="form-control @error('notes') is-invalid @enderror"
                      placeholder="Contoh: Stock opname bulanan Desember 2025, fokus di produk ban Bridgestone dan Michelin">{{ old('notes', $isEdit ? $stockOpname->notes : '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Maksimal 500 karakter
            </small>
        </div>
    </div>

    {{-- SUMMARY INFO --}}
    <div id="summary-info" class="alert alert-light border d-none">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle text-info mr-3" style="font-size: 2rem;"></i>
            <div>
                <strong>Estimasi Produk yang Akan Dihitung:</strong>
                <span id="estimated-count" class="badge badge-primary ml-2">
                    {{ $isEdit ? $stockOpname->items->count() : 0 }} produk
                </span>
                <p class="mb-0 mt-1 small text-muted">
                    Data akan di-snapshot pada saat opname {{ $isEdit ? 'diupdate' : 'dibuat' }}. 
                    Perubahan stok setelah opname dimulai tidak akan mempengaruhi data.
                </p>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <div>
            <button type="submit" class="btn btn-primary btn-lg px-5" id="submit-btn">
                <i class="bi bi-{{ $isEdit ? 'save' : 'check-circle' }}"></i> 
                {{ $isEdit ? 'Update Stock Opname' : 'Buat Stock Opname' }}
            </button>
        </div>
    </div>
</form>

{{-- SHARED STYLES --}}
@push('page_styles')
<style>
    .scope-option-card {
        border: 2px solid #e3e6f0;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    .scope-option-card:hover {
        border-color: #4e73df;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    .scope-option-card.active {
        border-color: #4e73df;
        background-color: #f8f9fc;
        box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.2);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .select2-container {
        width: 100% !important;
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
        
        // Update visual state
        $('.scope-option-card').removeClass('active');
        $(this).addClass('active');
        
        // Check radio
        radio.prop('checked', true);
        
        // Show/hide relevant selectors
        $('.scope-detail').addClass('d-none');
        
        if (scope === 'category') {
            $('#category-selector').removeClass('d-none');
            initCategorySelect2();
        } else if (scope === 'custom') {
            $('#product-selector').removeClass('d-none');
            initProductSelect2();
        }

        // Update estimated count
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
            theme: 'bootstrap4',
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
            theme: 'bootstrap4',
            placeholder: 'Ketik nama atau kode produk...',
            allowClear: true,
            width: '100%',
            minimumInputLength: isEdit ? 0 : 2, // Jika edit, tidak perlu minimal input
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
                            more: data.pagination.more
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
                $('#summary-info').addClass('d-none');
            }
        } else if (scope === 'custom') {
            count = $('#product_ids').val() ? $('#product_ids').val().length : 0;
            displayEstimate(count);
        }
    }

    function displayEstimate(count) {
        if (count > 0) {
            $('#estimated-count').text(`${count} produk`);
            $('#summary-info').removeClass('d-none');
        } else {
            $('#summary-info').addClass('d-none');
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
                confirmButtonColor: '#4e73df'
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
                    confirmButtonColor: '#4e73df'
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
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }
        }

        @if($isEdit)
        // Konfirmasi edit
        if (!confirm('Mengubah scope akan mereset semua data hitungan. Lanjutkan?')) {
            e.preventDefault();
            return false;
        }
        @endif

        // Show loading
        const $btn = $('#submit-btn');
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');
    });
});
</script>
@endpush
