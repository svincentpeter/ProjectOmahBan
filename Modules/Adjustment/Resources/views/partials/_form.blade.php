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
                    @if ($isEdit && $adjustment->files->count() > 0)
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading mb-2">
                                    <i class="cil-file mr-1"></i> File Lampiran Existing
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($adjustment->files as $file)
                                        <li class="mb-1">
                                            <i class="cil-paperclip text-primary"></i>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="text-decoration-none">{{ $file->file_name }}</a>
                                            <small
                                                class="text-muted">({{ round(Storage::size('public/' . $file->file_path) / 1024, 2) }}
                                                KB)</small>
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
                                                        data-stock="{{ $adjusted->product->product_quantity ?? '-' }}"
                                                        data-unit="{{ $p->product_unit }}"
                                                        data-name="{{ $p->name }}"
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
            let rowIndex = {{ $isEdit ? $adjustment->adjustedProducts->count() : 0 }};
            const products = @json($products);

            // ---------- Template baris ----------
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
      ${ (p.product_name || p.name) || '' }${ p.category ? ' - ' + (p.category?.category_name || p.category?.name || '') : '' }
    </option>
                  `).join('')}
    </select>
  </td>
  <td class="text-center align-middle"><span class="badge badge-secondary badge-lg current-stock">-</span></td>
  <td><input type="number" class="form-control text-center quantity-input" name="quantities[]" min="1" required placeholder="0" data-index="${index}"></td>
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

            // ---------- Select2 templates & matcher ----------
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
      </div>
    `;
            }

            function formatProductSelection(state) {
  if (!state.id) return state.text;
  const code = $(state.element).data('code') || 'N/A';

  // buang [KODE] yang mungkin sudah ada di teks option
  let text = (state.text || '').replace(/^\[[^\]]+\]\s*/, '');

  return `[${code}] ${text}`;
}

            function productMatcher(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                const term = params.term.toLowerCase();
                const $opt = $(data.element);
                const haystack = [
                    data.text, $opt.data('name'), $opt.data('code'), $opt.data('category')
                ].join(' ').toLowerCase();
                return haystack.indexOf(term) > -1 ? data : null;
            }

            function initProductSelect2($select) {
                if (!$.fn.select2) {
                    console.error(
                        'Select2 tidak ter-load. Pastikan CSS/JS Select2 sudah disertakan di App.blade tepat urutannya.'
                    );
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

            // ---------- Tambah baris ----------
            $('#add-product-row').on('click', function() {
                const newRow = getProductRowTemplate(rowIndex);
                $('#product-rows').append(newRow);
                initProductSelect2($(`select.product-select[data-index="${rowIndex}"]`));
                rowIndex++;
                toggleEmptyState();
            });

            // ---------- Hapus baris ----------
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
                        $(`tr[data-index="${index}"]`).fadeOut(200, function() {
                            $(this).remove();
                            toggleEmptyState();
                            checkNegativeStock();
                        });
                    }
                });
            });

            // ---------- On change select produk ----------
            $(document).on('change', '.product-select', function() {
                const index = $(this).data('index');
                const opt = $(this).find('option:selected');
                const stock = parseInt(opt.data('stock')) || 0;
                const rawUnit = opt.data('unit');
                const unit = (!rawUnit || rawUnit === 'undefined' || rawUnit === 'null') ? 'PC' : rawUnit;

                $(`tr[data-index="${index}"] .current-stock`)
                    .text(`${stock} ${unit}`)
                    .removeClass('badge-secondary')
                    .addClass('badge-info');

                calculateFinalStock(index);
            });

            // ---------- Hitung stok akhir ----------
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

                const $final = row.find('.final-stock'); // <-- dipakai konsisten
                $final.text(`${finalStock} ${unit}`);

                // Warna
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

            // ---------- Cek stok negatif ----------
            function checkNegativeStock() {
                let neg = false;
                $('.final-stock').each(function() {
                    const v = parseInt(($(this).text() || '').split(' ')[0]);
                    if (!isNaN(v) && v < 0) neg = true;
                });
                $('#stock-warning')[neg ? 'slideDown' : 'slideUp']();
            }

            // ---------- Empty state ----------
            function toggleEmptyState() {
                if ($('#product-rows tr').length === 0) {
                    $('#empty-state').fadeIn();
                    $('#products-table-container').hide();
                } else {
                    $('#empty-state').hide();
                    $('#products-table-container').fadeIn();
                }
            }

            // ---------- Initial load ----------
            toggleEmptyState();

            @if (!$isEdit) // create: auto tambah 1 baris
    $('#add-product-row').click();
  @else
    // edit: inisialisasi select2 untuk baris yang sudah ada
    $('#product-rows select.product-select').each(function(){
      initProductSelect2($(this));
      calculateFinalStock($(this).data('index'));
    }); @endif
        });
    </script>
@endpush
