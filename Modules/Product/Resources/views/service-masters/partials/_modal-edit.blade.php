{{-- 
    Modal: Edit Jasa (Bootstrap 4 + AutoNumeric)
--}}

<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">

            {{-- MODAL HEADER --}}
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="editServiceLabel">
                    <i class="cil-pencil mr-2 text-warning"></i>
                    <span class="font-weight-bold">Edit Jasa</span>
                </h5>
                {{-- BS4 close button --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- MODAL BODY --}}
            <form id="editServiceForm" method="POST" class="form-validation">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- FIELD: Nama Jasa --}}
                    <div class="mb-3">
                        <label for="editServiceName" class="form-label small font-weight-semibold">
                            Nama Jasa <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('service_name') is-invalid @enderror"
                            id="editServiceName" name="service_name" placeholder="Nama jasa" required maxlength="100">
                        @error('service_name')
                            <div class="invalid-feedback d-block">
                                <i class="cil-x-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- DISPLAY: Harga Lama --}}
                    <div class="mb-3">
                        <label class="form-label small font-weight-semibold text-muted">Harga Standar Saat Ini</label>
                        <div class="p-3 bg-light rounded border border-info"
                            style="border-left: 4px solid #39f !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Harga Lama:</span>
                                <h5 id="oldPriceDisplay" class="mb-0 text-info font-weight-bold">Rp 0</h5>
                            </div>
                        </div>
                    </div>

                    {{-- FIELD: Harga Baru (AutoNumeric) --}}
                    <div class="mb-3">
                        <label for="editStandardPrice" class="form-label small font-weight-semibold">
                            Harga Standar Baru (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-flat">
                            <span class="input-group-text bg-light"><strong>Rp</strong></span>
                            <input type="text" class="form-control @error('standard_price') is-invalid @enderror"
                                id="editStandardPrice" name="standard_price" placeholder="25.000" inputmode="numeric"
                                required value="0">
                            @error('standard_price')
                                <div class="invalid-feedback d-block">
                                    <i class="cil-x-circle mr-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <small class="form-text text-muted d-block mt-1">
                            <i class="cil-info mr-1"></i> Perubahan harga akan dicatat dalam audit log
                        </small>
                    </div>

                    {{-- DISPLAY: Info Perubahan Harga --}}
                    <div class="mb-3" id="priceChangeAlert" style="display:none;">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="cil-warning mr-2 mt-1" style="font-size:1.25rem;"></i>
                                <div>
                                    <h6 class="mb-2"><strong>Perubahan Harga Terdeteksi</strong></h6>
                                    <p class="mb-2 small">
                                        Harga akan berubah dari <strong id="priceChangeOld">Rp 0</strong>
                                        menjadi <strong id="priceChangeNew">Rp 0</strong><br>
                                        Perubahan: <strong id="priceChangePercent" class="text-danger">0%</strong>
                                    </p>
                                    <small class="text-muted">Silakan isi alasan perubahan di bawah</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FIELD: Alasan Perubahan Harga --}}
                    <div class="mb-3" id="priceChangeReasonDiv" style="display:none;">
                        <label for="editPriceChangeReason" class="form-label small font-weight-semibold">
                            Alasan Perubahan Harga <span class="text-muted">(Opsional)</span>
                        </label>
                        <select class="form-control form-select" id="editPriceChangeReason" name="price_change_reason">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="inflasi">Inflasi / Kenaikan Bahan Baku</option>
                            <option value="kompetitor">Penyesuaian Harga Kompetitor</option>
                            <option value="promo">Promosi / Diskon Khusus</option>
                            <option value="perbaikan">Perbaikan / Update Layanan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- FIELD: Kategori --}}
                    <div class="mb-3">
                        <label for="editCategory" class="form-label small font-weight-semibold">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-control form-select @error('category') is-invalid @enderror"
                            id="editCategory" name="category" required>
                            <option value="service">Service (Jasa)</option>
                            <option value="goods">Goods (Barang)</option>
                            <option value="custom">Custom (Khusus)</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback d-block">
                                <i class="cil-x-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- FIELD: Deskripsi --}}
                    <div class="mb-1">
                        <label for="editDescription" class="form-label small font-weight-semibold">
                            Deskripsi <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="editDescription" name="description"
                            rows="3" placeholder="Deskripsi singkat tentang jasa" maxlength="500"></textarea>
                        <small class="form-text text-muted d-block mt-1">
                            <i class="cil-info mr-1"></i> Max 500 karakter
                        </small>
                        @error('description')
                            <div class="invalid-feedback d-block">
                                <i class="cil-x-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- MODAL FOOTER --}}
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">
                        <i class="cil-x mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="cil-save mr-1"></i> Update Jasa
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('page_scripts')
    {{-- AutoNumeric (pakai versi sama seperti modal Add) --}}
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
    <script>
        const AN_OPS_IDR_INT = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            maximumValue: '999999999',
            modifyValueOnWheel: false,
            unformatOnSubmit: false, // kita unformat manual sebelum submit (AJAX FormData)
        };

        let anEditPrice;

        $(function() {
            // Init AutoNumeric sekali
            anEditPrice = new AutoNumeric('#editStandardPrice', AN_OPS_IDR_INT);

            // Populate saat modal dibuka
            $('#editServiceModal').on('show.bs.modal', function(e) {
                const btn = $(e.relatedTarget); // tombol yang memicu modal
                const id = btn.data('id');
                const name = btn.data('name');
                const price = parseInt(btn.data('price')) || 0;
                const category = btn.data('category');
                const description = btn.data('description') || '';

                // Set action form
                const actionUrl = '{{ route('service-masters.update', ':id') }}'.replace(':id', id);
                $('#editServiceForm').attr('action', actionUrl);

                // Set field
                $('#editServiceName').val(name);
                $('#editCategory').val(category);
                $('#editDescription').val(description);

                // Harga: tampilkan lama & set ke AutoNumeric
                const formattedOld = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(price);
                $('#oldPriceDisplay').text(formattedOld);

                // Simpan oldPrice untuk komparasi
                $('#editServiceForm').data('oldPrice', price);

                // Set nilai awal ke AutoNumeric (akan tampil "25.000")
                anEditPrice.set(price);

                // Reset alert
                $('#priceChangeAlert').hide();
                $('#priceChangeReasonDiv').hide();
                $('#editPriceChangeReason').val('');
            });

            // Monitor perubahan harga (pakai event AutoNumeric agar pasti)
            $('#editStandardPrice').on('autoNumeric:rawValueModified input change keyup', function() {
                const oldPrice = parseInt($('#editServiceForm').data('oldPrice')) || 0;
                const newPrice = AutoNumeric.getAutoNumericElement('#editStandardPrice')?.getNumber() || 0;

                if (newPrice < 0) {
                    anEditPrice.set(0);
                    return;
                }

                if (oldPrice !== newPrice) {
                    const diff = newPrice - oldPrice;
                    const percent = oldPrice > 0 ? ((diff / oldPrice) * 100).toFixed(1) : 0;

                    const f = (v) => new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(v);
                    $('#priceChangeOld').text(f(oldPrice));
                    $('#priceChangeNew').text(f(newPrice));

                    const percentClass = diff > 0 ? 'text-danger' : 'text-success';
                    const percentSymbol = diff > 0 ? '+' : '';
                    $('#priceChangePercent')
                        .text(percentSymbol + percent + '%')
                        .removeClass('text-danger text-success')
                        .addClass(percentClass);

                    $('#priceChangeAlert').slideDown(200);
                    $('#priceChangeReasonDiv').slideDown(200);
                } else {
                    $('#priceChangeAlert').slideUp(200);
                    $('#priceChangeReasonDiv').slideUp(200);
                }
            });

            // Validasi + siapkan payload angka murni saat submit
            $('#editServiceForm').on('submit', function(e) {
                const serviceName = $('#editServiceName').val().trim();
                const category = $('#editCategory').val();

                if (!serviceName) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Error',
                        text: 'Nama jasa harus diisi',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    $('#editServiceName').focus();
                    return false;
                }
                if (!category) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Error',
                        text: 'Kategori harus dipilih',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    $('#editCategory').focus();
                    return false;
                }

                // Pastikan field harga berisi angka murni sebelum FormData dibaca oleh handler AJAX global
                try {
                    const anEl = AutoNumeric.getAutoNumericElement('#editStandardPrice');
                    if (anEl) {
                        $('#editStandardPrice').val(anEl.getNumericString()); // "25000"
                    }
                } catch (err) {
                    console.warn('AutoNumeric not initialized:', err);
                }

                // (Jika kamu submit via AJAX di index.blade.php, biarkan handler global yang e.preventDefault & kirim)
                // (Jika kamu submit normal non-AJAX, ya akan tetap terkirim sebagai integer string "25000")
            });

            // Bersih saat modal ditutup
            $('#editServiceModal').on('hidden.bs.modal', function() {
                $('#editServiceForm')[0].reset();
                $('#priceChangeAlert').hide();
                $('#priceChangeReasonDiv').hide();
                if (anEditPrice) anEditPrice.set(0);
            });
        });
    </script>
@endpush

@push('page_styles')
    <style>
        #editServiceForm .form-control,
        #editServiceForm .form-select {
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: .875rem;
            transition: .2s;
        }

        #editServiceForm .form-control:focus,
        #editServiceForm .form-select:focus {
            border-color: #ffc451;
            box-shadow: 0 0 0 .2rem rgba(249, 177, 21, .15);
        }

        #editServiceForm .form-control.is-invalid,
        #editServiceForm .form-select.is-invalid {
            border-color: #dc3545;
        }

        #editServiceForm .form-control.is-invalid:focus,
        #editServiceForm .form-select.is-invalid:focus {
            box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .25);
        }

        .input-group-text {
            border: 1px solid #ddd;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            z-index: 3;
        }

        .form-label {
            color: #4f5d73;
            margin-bottom: .5rem;
        }

        .font-weight-semibold {
            font-weight: 600;
        }

        .modal-header {
            background: #f8f9fa;
            padding: 1.25rem;
        }

        .modal-footer {
            padding: 1rem 1.25rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: .2s;
        }

        .btn-light {
            background: #f8f9fa;
            border-color: #ddd;
            color: #4f5d73;
        }

        .btn-light:hover {
            background: #e9ecef;
            border-color: #ccc;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 177, 21, .3);
        }

        .alert-warning {
            background: #fff8e1;
            border-color: #ffc451;
            color: #856404;
        }

        .alert-warning strong {
            color: #f9b115;
        }
    </style>
@endpush
