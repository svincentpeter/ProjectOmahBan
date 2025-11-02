{{-- File: Modules/Adjustment/Resources/views/partials/_form.blade.php --}}
{{-- Reusable Form Partial untuk Create & Edit dengan styling asli yang cantik --}}
{{-- Variables: $isEdit (boolean), $adjustment (Adjustment model jika edit), $products (Product collection) --}}

{{-- Section 1: Informasi Dasar (Tanpa perubahan styling) --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-notes mr-2 text-primary"></i>
                    {{ $isEdit ? 'Edit Informasi Pengajuan' : 'Informasi Pengajuan' }}
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
                                class="form-control form-control-lg @error('date') is-invalid @enderror" required
                                value="{{ old('date', $isEdit ? $adjustment->date->format('Y-m-d') : date('Y-m-d')) }}">
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
                            <input type="text" id="reference" class="form-control form-control-lg bg-light"
                                value="{{ $isEdit ? $adjustment->reference : 'Auto-generated: ADJ-XXX' }}" readonly
                                disabled>
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                {{ $isEdit ? 'Kode referensi sudah dibuat' : 'Kode referensi akan dibuat otomatis' }}
                            </small>
                        </div>
                    </div>

                    {{-- Reason Dropdown --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reason" class="form-label font-weight-semibold">
                                <i class="cil-list mr-1 text-muted"></i> Alasan Penyesuaian
                                <span class="text-danger">*</span>
                            </label>
                            <select id="reason" name="reason"
                                class="form-control form-control-lg @error('reason') is-invalid @enderror" required>
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Rusak"
                                    {{ old('reason', $isEdit ? $adjustment->reason : '') == 'Rusak' ? 'selected' : '' }}>
                                    Barang Rusak</option>
                                <option value="Hilang"
                                    {{ old('reason', $isEdit ? $adjustment->reason : '') == 'Hilang' ? 'selected' : '' }}>
                                    Barang Hilang</option>
                                <option value="Kadaluarsa"
                                    {{ old('reason', $isEdit ? $adjustment->reason : '') == 'Kadaluarsa' ? 'selected' : '' }}>
                                    Kadaluarsa
                                </option>
                                <option value="Lainnya"
                                    {{ old('reason', $isEdit ? $adjustment->reason : '') == 'Lainnya' ? 'selected' : '' }}>
                                    Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Pilih alasan utama penyesuaian stok.</small>
                        </div>
                    </div>

                    {{-- Description Textarea --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description" class="form-label font-weight-semibold">
                                <i class="cil-file-text mr-1 text-muted"></i> Keterangan Detail
                                <span class="text-danger">*</span>
                            </label>
                            <textarea id="description" name="description" rows="4"
                                class="form-control form-control-lg @error('description') is-invalid @enderror" required
                                placeholder="Jelaskan secara rinci alasan penyesuaian, seperti lokasi, nomor batch, atau bukti fisik... (minimal 10 karakter)">{{ old('description', $isEdit ? $adjustment->description : '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Detail ini penting untuk proses approval owner.</small>
                        </div>
                    </div>

                    {{-- Files Upload --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="files" class="form-label font-weight-semibold">
                                <i class="cil-image mr-1 text-muted"></i> Bukti Gambar
                                <span class="text-danger">*</span> (Maksimal 3 File)
                            </label>
                            <input type="file" id="files" name="files[]" multiple accept="image/*"
                                class="form-control-file @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror"
                                {{ $isEdit ? '' : 'required' }}>
                            @error('files')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('files.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upload foto bukti (JPG/PNG, max 2MB/file). Wajib untuk
                                verifikasi, max 3 file.</small>
                            {{-- Preview Files --}}
                            <div id="file-preview" class="mt-2 row"></div>
                        </div>
                    </div>

                    {{-- Existing Files (Edit Only) --}}
                    @if ($isEdit && $adjustment->adjustmentFiles->count() > 0)
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading mb-2">
                                    <i class="cil-file mr-1"></i> File Lampiran Existing
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($adjustment->adjustmentFiles as $file)
                                        <li class="mb-1">
                                            <i class="cil-paperclip text-primary"></i>
                                            <a href="{{ $file->file_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $file->file_name }}
                                            </a>
                                            <small class="text-muted">({{ $file->file_size_human }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif


                    {{-- Notes (Optional) --}}
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label for="note" class="form-label font-weight-semibold">
                                <i class="cil-pencil mr-1 text-muted"></i> Catatan Tambahan (Opsional)
                            </label>
                            <textarea id="note" name="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                                placeholder="Catatan tambahan, misal referensi internal...">{{ old('note', $isEdit ? $adjustment->note : '') }}</textarea>
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
</div>

{{-- Section 2: Products Table (Tanpa perubahan styling) --}}
<div class="row">
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
                                <small>Beberapa produk akan memiliki stok negatif setelah penyesuaian. Harap periksa
                                    kembali jumlah pengurangan.</small>
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
                            {{-- Dynamic rows akan di-inject via JS (existing rows untuk edit) --}}
                            @if ($isEdit)
                                @foreach ($adjustment->adjustedProducts as $index => $adjusted)
                                    <tr data-index="{{ $index }}" class="product-row">
                                        <td>
                                            <select class="form-control product-select" name="product_ids[]" required
                                                data-index="{{ $index }}">
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
                                        <td class="text-center align-middle">
                                            <span
                                                class="badge badge-secondary badge-lg current-stock">{{ $adjusted->product->product_quantity ?? '-' }}
                                                {{ $adjusted->product->product_unit ?? 'PC' }}</span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center quantity-input"
                                                name="quantities[]" min="1" required placeholder="0"
                                                data-index="{{ $index }}" value="{{ $adjusted->quantity }}">
                                        </td>
                                        <td>
                                            <select class="form-control type-select" name="types[]" required
                                                data-index="{{ $index }}">
                                                <option value="add"
                                                    {{ $adjusted->type == 'add' ? 'selected' : '' }}>
                                                    <i class="cil-plus"></i> Penambahan
                                                </option>
                                                <option value="sub"
                                                    {{ $adjusted->type == 'sub' ? 'selected' : '' }}>
                                                    <i class="cil-minus"></i> Pengurangan
                                                </option>
                                            </select>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-info badge-lg final-stock">-</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-danger remove-row"
                                                data-index="{{ $index }}" title="Hapus">
                                                <i class="cil-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
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
                        Klik tombol di atas untuk menambahkan produk yang akan disesuaikan stoknya
                    </p>
                    <button type="button" class="btn btn-primary" onclick="$('#add-product-row').click()">
                        <i class="cil-plus mr-1"></i> Tambah Produk Pertama
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        $(function() {
            // ====== Konstanta & State ======
            const MAX_ROWS = 50;
            const DRAFT_KEY = 'adjustment_draft_v1';
            let rowIndex = {{ $isEdit ? $adjustment->adjustedProducts->count() : 0 }};
            const products = @json($products);

            // ====== Util ======
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

            const applyDraft = (draft) => {
                if (!draft) return;
                // set header fields
                $('#date').val(draft.date || $('#date').val());
                $('#reason').val(draft.reason || '');
                $('#description').val(draft.description || '');
                $('#note').val(draft.note || '');

                // clear rows
                $('#product-rows').empty();
                rowIndex = 0;

                // rebuild rows
                (draft.rows || []).forEach(r => {
                    addRow();
                    const $tr = $(`tr[data-index="${rowIndex-1}"]`);
                    const $sel = $tr.find('.product-select');
                    $sel.val(r.product_id);
                    $sel.trigger('change.select2');
                    $tr.find('.quantity-input').val(r.quantity || 0);
                    $tr.find('.type-select').val(r.type || 'add');
                    calculateFinalStock(rowIndex - 1);
                });
                toggleEmptyState();
            };

            const saveDraft = debounce(() => {
                try {
                    localStorage.setItem(DRAFT_KEY, JSON.stringify(serializeForm()));
                } catch (e) {}
            }, 800);

            const clearDraft = () => {
                try {
                    localStorage.removeItem(DRAFT_KEY);
                } catch (e) {}
            };

            // ====== Template Baris ======
            function getProductRowTemplate(index) {
                return `
<tr data-index="${index}" class="product-row">
  <td>
    <select class="form-control product-select" name="product_ids[]" required data-index="${index}">
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
  <td class="text-center align-middle"><span class="badge badge-secondary badge-lg current-stock">-</span></td>
  <td>
    <input type="number" class="form-control text-center quantity-input" name="quantities[]" min="1" required placeholder="0" data-index="${index}">
  </td>
  <td>
    <select class="form-control type-select" name="types[]" required data-index="${index}">
      <option value="add" selected>Penambahan</option>
      <option value="sub">Pengurangan</option>
    </select>
  </td>
  <td class="text-center align-middle"><span class="badge badge-info badge-lg final-stock">-</span></td>
  <td class="text-center align-middle">
    <button type="button" class="btn btn-sm btn-danger remove-row" data-index="${index}" title="Hapus"><i class="cil-trash"></i></button>
  </td>
</tr>`;
            }

            // ====== Select2 templates ======
            function formatProduct(state) {
                if (!state.id) return state.text;
                const $opt = $(state.element);
                const code = $opt.data('code') || 'N/A';
                const name = $opt.data('name') || state.text;
                const category = $opt.data('category') || '';
                const stock = $opt.data('stock') ?? 0;
                const rawUnit = $opt.data('unit');
                const unit = (!rawUnit || rawUnit === 'undefined' || rawUnit === 'null') ? 'PC' : rawUnit;

                return `
      <div style="display:flex;flex-direction:column;gap:2px">
        <div><strong>[${code}]</strong> ${name}</div>
        <div style="font-size:12px;color:#64748b">
          ${category ? `<i class="cil-tag"></i> ${category} • ` : ''}<i class="cil-layers"></i> Stok: ${stock} ${unit}
        </div>
      </div>`;
            }

            function formatProductSelection(state) {
                if (!state.id) return state.text;
                const code = $(state.element).data('code') || 'N/A';
                let text = (state.text || '').replace(/^\[[^\]]+\]\s*/, '');
                return `[${code}] ${text}`;
            }

            function productMatcher(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                const term = params.term.toLowerCase();
                const $opt = $(data.element);
                const haystack = [data.text, $opt.data('name'), $opt.data('code'), $opt.data('category')].join(' ')
                    .toLowerCase();
                return haystack.indexOf(term) > -1 ? data : null;
            }

            function initProductSelect2($select) {
                if (!$.fn.select2) {
                    console.error('Select2 belum ter-load');
                    return;
                }
                $select.select2({
                    placeholder: 'Cari produk… (nama / kode / kategori)',
                    width: '100%',
                    allowClear: true,
                    dropdownParent: $select.closest('td'),
                    minimumResultsForSearch: 0,
                    escapeMarkup: m => m,
                    templateResult: formatProduct,
                    templateSelection: formatProductSelection,
                    matcher: productMatcher
                });
            }

            // ====== File input guard + preview ======
            const MAX_FILES = 3;
            const MAX_SIZE_MB = 2;
            const $fileInput = $('#files');
            const $filePreview = $('#file-preview');

            function bytesToMB(b) {
                return (b / (1024 * 1024)).toFixed(2);
            }

            $fileInput.on('change', function() {
                const files = this.files || [];
                $filePreview.empty();

                if (files.length > MAX_FILES) {
                    Swal.fire('Kebanyakan file', `Maksimal ${MAX_FILES} file gambar.`, 'warning');
                    this.value = ''; // reset
                    return;
                }

                for (let f of files) {
                    const isImage = /^image\//.test(f.type);
                    const sizeMB = f.size / (1024 * 1024);

                    if (!isImage) {
                        Swal.fire('Tipe tidak valid', 'Hanya gambar (JPG/PNG).', 'warning');
                        this.value = '';
                        $filePreview.empty();
                        return;
                    }

                    if (sizeMB > MAX_SIZE_MB) {
                        Swal.fire('File terlalu besar',
                            `Setiap file maks ${MAX_SIZE_MB} MB (file: ${bytesToMB(f.size)} MB).`,
                            'warning');
                        this.value = '';
                        $filePreview.empty();
                        return;
                    }

                    // Preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const col = $(`
                <div class="col-6 col-md-3 mb-2">
                    <div class="border rounded p-1 text-center">
                        <img src="${e.target.result}" alt="${f.name}" style="max-width:100%;max-height:120px;object-fit:cover;">
                        <div class="small mt-1 text-truncate" title="${f.name}">${f.name}</div>
                        <div class="text-muted" style="font-size:12px">${bytesToMB(f.size)} MB</div>
                    </div>
                </div>`);
                        $filePreview.append(col);
                    };
                    reader.readAsDataURL(f);
                }
            });

            // ====== Fitur: Tambah/Hapus baris ======
            function addRow() {
                if ($('#product-rows tr').length >= MAX_ROWS) {
                    Swal.fire('Maksimum tercapai', `Maksimal ${MAX_ROWS} baris produk per pengajuan.`, 'info');
                    return;
                }
                const newRow = getProductRowTemplate(rowIndex);
                $('#product-rows').append(newRow);
                initProductSelect2($(`select.product-select[data-index="${rowIndex}"]`));
                rowIndex++;
                toggleEmptyState();
                saveDraft();
            }

            $('#add-product-row').on('click', addRow);

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
                }).then(r => {
                    if (r.isConfirmed) {
                        $(`tr[data-index="${index}"]`).fadeOut(120, function() {
                            $(this).remove();
                            toggleEmptyState();
                            checkNegativeStock();
                            saveDraft();
                        });
                    }
                });
            });

            // ====== Deteksi duplikasi produk ======
            function isDuplicateProduct(selectedId, currentIndex) {
                let dup = false;
                $('#product-rows .product-select').each(function() {
                    const idx = $(this).data('index');
                    if (idx != currentIndex && $(this).val() == selectedId && selectedId) dup = true;
                });
                return dup;
            }

            // ====== Event: change select produk ======
            $(document).on('change', '.product-select', function() {
                const index = $(this).data('index');
                const val = $(this).val();
                if (isDuplicateProduct(val, index)) {
                    Swal.fire('Produk duplikat',
                        'Produk yang sama sudah ada di daftar. Silakan pilih produk lain.', 'warning');
                    $(this).val('').trigger('change.select2');
                    $(`tr[data-index="${index}"] .current-stock`).text('-').removeClass('badge-info')
                        .addClass('badge-secondary');
                    calculateFinalStock(index);
                    return;
                }
                const opt = $(this).find('option:selected');
                const stock = parseInt(opt.data('stock')) || 0;
                const rawUnit = opt.data('unit');
                const unit = (!rawUnit || rawUnit === 'undefined' || rawUnit === 'null') ? 'PC' : rawUnit;

                $(`tr[data-index="${index}"] .current-stock`)
                    .text(`${stock} ${unit}`)
                    .removeClass('badge-secondary')
                    .addClass('badge-info');

                calculateFinalStock(index);
                saveDraft();
            });

            function applyQtyMaxRule(index) {
                const row = $(`tr[data-index="${index}"]`);
                const opt = row.find('.product-select option:selected');
                const type = row.find('.type-select').val();
                const $qty = row.find('.quantity-input');

                if (type === 'sub') {
                    const currentStock = parseInt(opt.data('stock')) || 0;
                    $qty.attr('max', currentStock > 0 ? currentStock : 0);
                } else {
                    $qty.removeAttr('max');
                }
            }


            // ====== Hitung stok akhir (dengan debounce untuk input qty/type) ======
            const recalc = debounce((idx) => calculateFinalStock(idx), 120);

            $(document).on('input', '.quantity-input', function() {
                recalc($(this).data('index'));
                saveDraft();
            });

            $(document).on('change', '.type-select', function() {
                recalc($(this).data('index'));
                saveDraft();
            });

            function calculateFinalStock(index) {
                const row = $(`tr[data-index="${index}"]`);
                const opt = row.find('.product-select option:selected');
                const currentStock = parseInt(opt.data('stock')) || 0;

                const rawUnit = opt.data('unit');
                const unit = (!rawUnit || rawUnit === 'undefined' || rawUnit === 'null') ? 'PC' : rawUnit;

                const qty = parseInt(row.find('.quantity-input').val()) || 0;
                const type = row.find('.type-select').val();

                let finalStock = currentStock;
                if (qty > 0) finalStock = (type === 'add') ? currentStock + qty : currentStock - qty;

                const $final = row.find('.final-stock');
                $final.text(`${finalStock} ${unit}`);

                $final.removeClass('badge-danger badge-warning badge-success badge-info');
                row.removeClass('table-danger');
                if (finalStock < 0) {
                    $final.addClass('badge-danger');
                    row.addClass('table-danger');
                } else if (finalStock === 0) {
                    $final.addClass('badge-warning');
                } else {
                    $final.addClass('badge-success');
                }
                checkNegativeStock();
            }

            function checkNegativeStock() {
                let neg = false;
                $('.final-stock').each(function() {
                    const v = parseInt(($(this).text() || '').split(' ')[0]);
                    if (!isNaN(v) && v < 0) neg = true;
                });
                $('#stock-warning')[neg ? 'slideDown' : 'slideUp']();
            }

            function toggleEmptyState() {
                if ($('#product-rows tr').length === 0) {
                    $('#empty-state').fadeIn();
                    $('#products-table-container').hide();
                } else {
                    $('#empty-state').hide();
                    $('#products-table-container').fadeIn();
                }
            }

            // ====== Loading state saat submit ======
            const $form = $('#adjustment-form');
            const $submit = $('#submit-btn');
            $form.on('submit', function(e) {
                // Minimal 1 baris
                if ($('#product-rows tr').length === 0) {
                    e.preventDefault();
                    Swal.fire('Belum ada produk', 'Tambahkan minimal satu produk.', 'info');
                    return false;
                }

                // Cek duplikasi lagi saat submit
                const seen = new Set();
                let hasDup = false;
                $('#product-rows .product-select').each(function() {
                    const val = $(this).val();
                    if (!val) return;
                    if (seen.has(val)) hasDup = true;
                    seen.add(val);
                });
                if (hasDup) {
                    e.preventDefault();
                    Swal.fire('Produk duplikat',
                        'Ada produk yang terpilih lebih dari sekali. Mohon cek kembali.', 'warning');
                    return false;
                }

                // Cek stok negatif
                let neg = false;
                $('.final-stock').each(function() {
                    const v = parseInt(($(this).text() || '').split(' ')[0]);
                    if (!isNaN(v) && v < 0) neg = true;
                });
                if (neg) {
                    // Konfirmasi agar user sadar
                    e.preventDefault();
                    Swal.fire({
                        title: 'Stok negatif terdeteksi',
                        text: 'Beberapa baris akan membuat stok akhir < 0. Lanjutkan kirim?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then(res => {
                        if (res.isConfirmed) {
                            // disable dan submit lagi
                            $submit.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengirim...'
                            );
                            clearDraft();
                            $form.off('submit'); // hindari loop
                            $form.trigger('submit');
                        }
                    });
                    return false;
                }

                // OK → disable & submit
                $submit.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengirim...'
                );
                clearDraft();
            });


            // ====== Initial load ======
            toggleEmptyState();

            @if (!$isEdit)
                // create → auto 1 baris
                addRow();
                // coba muat draft kalau ada
                try {
                    applyDraft(JSON.parse(localStorage.getItem(DRAFT_KEY)));
                } catch (e) {}
            @else
                // edit: inisialisasi select2 & kalkulasi setiap baris
                $('#product-rows select.product-select').each(function() {
                    initProductSelect2($(this));
                    calculateFinalStock($(this).data('index'));
                });
                // untuk edit, tidak auto-apply draft (hindari timpa data existing)
            @endif

            // simpan draft saat field utama berubah
            $('#date,#reason,#description,#note').on('input change', saveDraft);
        });
    </script>
@endpush
