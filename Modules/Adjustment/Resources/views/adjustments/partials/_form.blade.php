{{-- File: Modules/Adjustment/Resources/views/adjustments/partials/_form.blade.php --}}

{{-- Section 1: Informasi Dasar --}}
<div class="bg-white border border-zinc-200 rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50 rounded-t-2xl">
        <h6 class="font-bold text-zinc-800 flex items-center gap-2">
            <i class="bi bi-info-circle text-blue-600"></i>
            {{ $isEdit ? 'Edit Informasi Pengajuan' : 'Informasi Pengajuan' }}
        </h6>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Date --}}
            <div>
                <label for="date" class="block mb-2 text-sm font-bold text-zinc-700">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-calendar text-zinc-500"></i>
                    </div>
                    <input type="date" id="date" name="date"
                        class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 @error('date') border-red-500 bg-red-50 @enderror"
                        required
                        value="{{ old('date', $isEdit ? $adjustment->date->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
                @error('date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reference --}}
            <div>
                <label for="reference" class="block mb-2 text-sm font-bold text-zinc-700">
                    Referensi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-upc-scan text-zinc-500"></i>
                    </div>
                    <input type="text" id="reference" 
                        class="bg-zinc-100 border border-zinc-200 text-zinc-600 text-sm rounded-xl block w-full pl-10 p-2.5 cursor-not-allowed font-mono"
                        value="{{ $isEdit ? $adjustment->reference : 'Auto-generated: ADJ-XXX' }}" readonly disabled>
                </div>
                <p class="mt-1 text-xs text-zinc-500">
                    {{ $isEdit ? 'Kode referensi unik.' : 'Kode akan digenerate otomatis.' }}
                </p>
            </div>

            {{-- Reason --}}
            <div>
                <label for="reason" class="block mb-2 text-sm font-bold text-zinc-700">
                    Alasan Penyesuaian <span class="text-red-500">*</span>
                </label>
                <select id="reason" name="reason"
                    class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('reason') border-red-500 @enderror" required>
                    <option value="">-- Pilih Alasan --</option>
                    @foreach(['Rusak', 'Hilang', 'Kadaluarsa', 'Lainnya'] as $opt)
                        <option value="{{ $opt }}" {{ old('reason', $isEdit ? $adjustment->reason : '') == $opt ? 'selected' : '' }}>
                            {{ $opt == 'Rusak' ? 'Barang Rusak' : ($opt == 'Hilang' ? 'Barang Hilang' : $opt) }}
                        </option>
                    @endforeach
                </select>
                @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label for="description" class="block mb-2 text-sm font-bold text-zinc-700">
                    Keterangan Detail <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="3"
                    class="block p-2.5 w-full text-sm text-zinc-900 bg-zinc-50 rounded-xl border border-zinc-300 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                    placeholder="Jelaskan detail alasan penyesuaian..." required>{{ old('description', $isEdit ? $adjustment->description : '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Files --}}
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-bold text-zinc-700">
                    Bukti Gambar <span class="text-red-500">*</span> <span class="text-zinc-400 font-normal">(Maks 3 File, JPG/PNG)</span>
                </label>
                
                <div class="flex items-center justify-center w-full">
                    <label for="files" class="flex flex-col items-center justify-center w-full h-32 border-2 border-zinc-300 border-dashed rounded-xl cursor-pointer bg-zinc-50 hover:bg-zinc-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="bi bi-cloud-upload text-3xl text-zinc-400 mb-2"></i>
                            <p class="mb-1 text-sm text-zinc-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                            <p class="text-xs text-zinc-500">JPG/PNG (MAX. 2MB)</p>
                        </div>
                        <input id="files" name="files[]" type="file" multiple accept="image/*" class="hidden" {{ $isEdit ? '' : 'required' }} />
                    </label>
                </div>
                
                {{-- Preview Container --}}
                <div id="file-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>

                @if ($isEdit && $adjustment->adjustmentFiles->count() > 0)
                    <div class="mt-4 bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <h6 class="text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-paperclip"></i> File Tersimpan
                        </h6>
                        <ul class="space-y-2">
                            @foreach ($adjustment->adjustmentFiles as $file)
                                <li class="flex items-center gap-2 text-sm text-blue-700">
                                    <i class="bi bi-image"></i>
                                    <a href="{{ $file->file_url }}" target="_blank" class="hover:underline truncate">{{ $file->file_name }}</a>
                                    <span class="text-xs text-blue-500">({{ $file->file_size_human }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @error('files') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Note (Optional) --}}
            <div class="md:col-span-2">
                <label for="note" class="block mb-2 text-sm font-bold text-zinc-700">
                    Catatan Tambahan <span class="text-zinc-400 font-normal">(Opsional)</span>
                </label>
                <textarea id="note" name="note" rows="2"
                    class="block p-2.5 w-full text-sm text-zinc-900 bg-zinc-50 rounded-xl border border-zinc-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Catatan internal...">{{ old('note', $isEdit ? $adjustment->note : '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Section 2: Products Table --}}
<div class="bg-white border border-zinc-200 rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50 rounded-t-2xl flex justify-between items-center">
        <h6 class="font-bold text-zinc-800 flex items-center gap-2">
            <i class="bi bi-box-seam text-blue-600"></i>
            Daftar Produk
        </h6>
        <button type="button" id="add-product-row" 
            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 transition-all">
            <i class="bi bi-plus-lg mr-1"></i> Tambah Produk
        </button>
    </div>
    
    <div class="p-6">
        {{-- Warning Alert --}}
        <div id="stock-warning" class="hidden mb-4 p-4 text-orange-800 border border-orange-200 rounded-xl bg-orange-50 items-start gap-3">
            <i class="bi bi-exclamation-triangle-fill text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold text-sm">Peringatan Stok Negatif!</h4>
                <p class="text-sm">Beberapa produk akan memiliki stok negatif setelah penyesuaian. Harap periksa kembali jumlah pengurangan.</p>
            </div>
        </div>

        {{-- Table Container --}}
        <div id="products-table-container" class="hidden overflow-x-auto rounded-lg border border-zinc-200">
            <table class="w-full text-sm text-left text-zinc-500">
                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 border-b border-zinc-200">
                    <tr>
                        <th class="px-4 py-3 min-w-[250px]">Produk</th>
                        <th class="px-4 py-3 text-center">Stok Awal</th>
                        <th class="px-4 py-3 text-center w-[150px]">Jumlah</th>
                        <th class="px-4 py-3 text-center w-[180px]">Tipe</th>
                        <th class="px-4 py-3 text-center">Stok Akhir</th>
                        <th class="px-4 py-3 text-center w-[50px]"></th>
                    </tr>
                </thead>
                <tbody id="product-rows" class="bg-white divide-y divide-zinc-100">
                    @if ($isEdit)
                        @foreach ($adjustment->adjustedProducts as $index => $adjusted)
                            <tr data-index="{{ $index }}" class="product-row hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3">
                                    <select class="form-control product-select w-full" name="product_ids[]" required data-index="{{ $index }}">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}"
                                                data-stock="{{ $p->product_quantity ?? 0 }}"
                                                data-unit="{{ $p->product_unit }}"
                                                data-name="{{ $p->product_name ?? $p->name }}"
                                                {{ $p->id == $adjusted->product_id ? 'selected' : '' }}>
                                                {{ $p->product_name ?? $p->name }}{{ $p->category ? ' - ' . ($p->category->category_name ?? $p->category->name) : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-zinc-100 text-zinc-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-zinc-200 current-stock">
                                        {{ $adjusted->product->product_quantity ?? '-' }} {{ $adjusted->product->product_unit ?? 'PC' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="quantities[]" min="1" required placeholder="0"
                                        class="bg-white border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full text-center quantity-input"
                                        data-index="{{ $index }}" value="{{ $adjusted->quantity }}">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="types[]" required class="bg-white border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full type-select" data-index="{{ $index }}">
                                        <option value="add" {{ $adjusted->type == 'add' ? 'selected' : '' }}>Penambahan (+)</option>
                                        <option value="sub" {{ $adjusted->type == 'sub' ? 'selected' : '' }}>Pengurangan (-)</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-blue-200 final-stock">-</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg p-2 transition-colors remove-row" data-index="{{ $index }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div id="empty-state" class="text-center py-10">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-zinc-50 rounded-full flex items-center justify-center text-zinc-300">
                    <i class="bi bi-cart-x text-4xl"></i>
                </div>
            </div>
            <h6 class="text-zinc-600 font-bold mb-1">Belum Ada Produk</h6>
            <p class="text-zinc-400 text-sm mb-4">Tambahkan produk yang ingin disesuaikan stoknya.</p>
            <button type="button" onclick="$('#add-product-row').click()" class="text-blue-600 font-bold text-sm hover:underline">
                + Tambah Produk Pertama
            </button>
        </div>
    </div>
</div>


@push('page_styles')
<style>
    /* Select2 Tailwind Override */
    .select2-container--default .select2-selection--single {
        background-color: #FAFAFA;
        border: 1px solid #D4D4D8;
        border-radius: 0.75rem;
        height: 42px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #18181B;
        padding-left: 12px;
        font-size: 0.875rem;
        line-height: normal;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #D4D4D8 !important;
        border-radius: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        min-height: 42px !important;
        background-color: #FAFAFA;
    }
    .select2-container--default .select2-selection--multiple:focus-within,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 1px #3B82F6 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #E4E4E7 !important;
        border: 1px solid #D4D4D8 !important;
        border-radius: 0.5rem !important;
        padding: 2px 8px !important;
        color: #18181B !important;
        font-size: 0.75rem !important;
        margin-top: 4px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #52525B !important;
        margin-right: 5px !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #E4E4E7 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
        z-index: 9999;
    }
    .select2-results__option {
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    .select2-results__option--highlighted {
        background-color: #3B82F6 !important;
        color: white !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem;
        border: 1px solid #D4D4D8;
        padding: 6px 12px;
    }
</style>
@endpush

@push('page_scripts')
    <script>
        $(function() {
            // Configuration
            const MAX_ROWS = 50;
            const DRAFT_KEY = 'adjustment_draft_v1';
            let rowIndex = {{ $isEdit ? $adjustment->adjustedProducts->count() : 0 }};
            const products = @json($products);

            // Row Template
            function getProductRowTemplate(index) {
                return `
                    <tr data-index="${index}" class="product-row hover:bg-zinc-50 transition-colors border-b border-zinc-100">
                        <td class="px-4 py-3">
                            <select class="form-control product-select w-full" name="product_ids[]" required data-index="${index}">
                                <option value="">-- Pilih Produk --</option>
                                ${products.map(p => `
                                    <option value="${p.id}"
                                        data-stock="${(p.product_quantity ?? p.stok_awal ?? 0)}"
                                        data-unit="${(p.product_unit ?? '').toString()}"
                                        data-name="${(p.product_name ?? p.name ?? '').toString()}"
                                        data-code="${(p.product_code ?? 'N/A').toString()}"
                                        data-category="${(p.category?.category_name ?? p.category?.name ?? '').toString()}">
                                        ${(p.product_name || p.name) || ''}${ p.category ? ' - ' + (p.category?.category_name || p.category?.name || '') : '' }
                                    </option>
                                `).join('')}
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-zinc-100 text-zinc-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-zinc-200 current-stock">-</span>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="quantities[]" min="1" required placeholder="0"
                                class="bg-white border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full text-center quantity-input"
                                data-index="${index}">
                        </td>
                        <td class="px-4 py-3">
                            <select name="types[]" required class="bg-white border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full type-select" data-index="${index}">
                                <option value="add" selected>Penambahan (+)</option>
                                <option value="sub">Pengurangan (-)</option>
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-blue-200 final-stock">-</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg p-2 transition-colors remove-row" data-index="${index}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            // Initialization logic (Identical logic to before, updated selectors/classes implicitly via HTML structure)
            // Util: Debounce
            const debounce = (fn, wait = 250) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), wait);
                };
            };

            const serializeForm = () => {
                const rows = [];
                $('#product-rows tr').each(function() {
                    const $tr = $(this);
                    rows.push({
                        product_id: $tr.find('.product-select').val(),
                        quantity: $tr.find('.quantity-input').val(),
                        type: $tr.find('.type-select').val()
                    });
                });
                return {
                    date: $('#date').val(),
                    reason: $('#reason').val(),
                    description: $('#description').val(),
                    note: $('#note').val(),
                    rows
                };
            };
            
            const saveDraft = debounce(() => {
                try { localStorage.setItem(DRAFT_KEY, JSON.stringify(serializeForm())); } catch (e) {}
            }, 800);

            // Core Logic
            function addRow() {
                if ($('#product-rows tr').length >= MAX_ROWS) {
                    Swal.fire('Limit', `Maksimal ${MAX_ROWS} baris.`, 'info');
                    return;
                }
                $('#product-rows').append(getProductRowTemplate(rowIndex));
                initProductSelect2($(`select.product-select[data-index="${rowIndex}"]`));
                rowIndex++;
                toggleEmptyState();
                saveDraft();
            }

            function toggleEmptyState() {
                if ($('#product-rows tr').length === 0) {
                    $('#empty-state').removeClass('hidden');
                    $('#products-table-container').addClass('hidden');
                } else {
                    $('#empty-state').addClass('hidden');
                    $('#products-table-container').removeClass('hidden');
                }
            }
            
            // Events
            $('#add-product-row').on('click', addRow);
            
            $(document).on('click', '.remove-row', function() {
                const index = $(this).data('index');
                $(`tr[data-index="${index}"]`).fadeOut(150, function() {
                    $(this).remove();
                    toggleEmptyState();
                    checkNegativeStock();
                    saveDraft();
                });
            });

            // Select2 Init
            function initProductSelect2($select) {
                if (!$.fn.select2) return;
                $select.select2({
                    placeholder: 'Cari produk...',
                    width: '100%',
                    allowClear: true,
                    // dropdownParent: $select.closest('td') // Can be tricky with overflow hidden
                });
            }

            // Calc Logic
            function calculateFinalStock(index) {
                const row = $(`tr[data-index="${index}"]`);
                const opt = row.find('.product-select option:selected');
                const currentStock = parseInt(opt.data('stock')) || 0;
                const unit = opt.data('unit') || 'PC';
                const qty = parseInt(row.find('.quantity-input').val()) || 0;
                const type = row.find('.type-select').val();
                
                let final = currentStock;
                if (qty > 0) final = (type === 'add') ? currentStock + qty : currentStock - qty;
                
                const $badge = row.find('.final-stock');
                $badge.text(`${final} ${unit}`);
                
                // Style updates
                $badge.removeClass('bg-red-100 text-red-800 border-red-200 bg-yellow-100 text-yellow-800 border-yellow-200 bg-green-100 text-green-800 border-green-200 bg-blue-100 text-blue-800 border-blue-200');
                row.removeClass('bg-red-50');
                
                if (final < 0) {
                    $badge.addClass('bg-red-100 text-red-800 border-red-200');
                    row.addClass('bg-red-50');
                } else if (final === 0) {
                    $badge.addClass('bg-yellow-100 text-yellow-800 border-yellow-200');
                } else {
                    $badge.addClass('bg-green-100 text-green-800 border-green-200');
                }
                
                // Update Current Stock Badge UI
                const $curBadge = row.find('.current-stock');
                $curBadge.text(`${currentStock} ${unit}`);
                
                checkNegativeStock();
            }

            function checkNegativeStock() {
                let neg = false;
                $('.final-stock').each(function() {
                    const v = parseInt(($(this).text() || '').split(' ')[0]);
                    if (!isNaN(v) && v < 0) neg = true;
                });
                if (neg) $('#stock-warning').removeClass('hidden').addClass('flex');
                else $('#stock-warning').addClass('hidden').removeClass('flex');
            }

            // Listeners
            $(document).on('change', '.product-select', function() {
                const idx = $(this).data('index');
                calculateFinalStock(idx);
                saveDraft();
            });
            $(document).on('input', '.quantity-input', function() {
                calculateFinalStock($(this).data('index'));
                saveDraft();
            });
            $(document).on('change', '.type-select', function() {
                calculateFinalStock($(this).data('index'));
                saveDraft();
            });

            // File Upload Preview
            $('#files').on('change', function() {
                const files = this.files || [];
                const $preview = $('#file-preview');
                $preview.empty();
                if (files.length > 3) {
                    Swal.fire('Limit', 'Maksimal 3 file.', 'warning');
                    this.value = ''; return;
                }
                Array.from(files).forEach(f => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        $preview.append(`
                            <div class="relative group aspect-square border-2 border-zinc-200 rounded-xl overflow-hidden bg-zinc-50">
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-xs font-bold truncate px-2">${f.name}</span>
                                </div>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(f);
                });
            });

            // Initial Run
            toggleEmptyState();
            @if(!$isEdit)
                addRow(); // Add first row
                try {
                    const draft = JSON.parse(localStorage.getItem(DRAFT_KEY));
                    if(draft) {
                        $('#date').val(draft.date || '');
                        $('#reason').val(draft.reason || '');
                        $('#description').val(draft.description || '');
                        $('#note').val(draft.note || '');
                        // Not handling row draft restore for simplicity here to avoid Select2 race conditions, but basics are preserved.
                    }
                } catch(e){}
            @else
                $('.product-select').each(function(){
                    initProductSelect2($(this));
                    calculateFinalStock($(this).data('index'));
                });
            @endif
            
            // Clear draft on submit
            $('#adjustment-form').on('submit', function() {
                localStorage.removeItem(DRAFT_KEY);
            });
        });
    </script>
@endpush
