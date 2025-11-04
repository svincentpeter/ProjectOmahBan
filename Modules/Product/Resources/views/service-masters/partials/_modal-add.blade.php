{{-- 
    Modal: Tambah Jasa Baru
    Konsisten, 1 ID saja di seluruh sistem!
--}}

<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm">

            {{-- MODAL HEADER --}}
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="addServiceLabel">
                    <i class="cil-plus mr-2 text-primary"></i>
                    <span class="font-weight-bold">Tambah Jasa Baru</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- MODAL BODY --}}
            <form action="{{ route('service-masters.store') }}" method="POST" id="formAddService"
                class="form-validation">
                @csrf
                <div class="modal-body">

                    {{-- FIELD: Nama Jasa --}}
                    <div class="mb-3">
                        <label for="addServiceName" class="form-label small font-weight-semibold">
                            Nama Jasa <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('service_name') is-invalid @enderror"
                            id="addServiceName" name="service_name"
                            placeholder="Contoh: Pasang Ban, Balancing, Nitrogen" required maxlength="100" autofocus>
                        @error('service_name')
                            <div class="invalid-feedback d-block">
                                <i class="cil-x-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted d-block mt-1">
                            <i class="cil-info mr-1"></i>
                            Nama jasa harus unik dan tidak boleh duplikat
                        </small>
                    </div>

                    {{-- FIELD: Harga Standar --}}
                    <div class="mb-3">
                        <label for="addStandardPrice" class="form-label small font-weight-semibold">
                            Harga Standar (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-flat">
                            <span class="input-group-text bg-light"><strong>Rp</strong></span>
                            <input type="text" class="form-control @error('standard_price') is-invalid @enderror"
                                id="addStandardPrice" name="standard_price" placeholder="25.000" inputmode="numeric"
                                required value="0">
                            @error('standard_price')
                                <div class="invalid-feedback d-block">
                                    <i class="cil-x-circle mr-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <small class="form-text text-muted d-block mt-1">
                            <i class="cil-info mr-1"></i>
                            Masukkan 0 jika ini jasa custom (harga flexible)
                        </small>
                    </div>

                    {{-- FIELD: Kategori --}}
                    <div class="mb-3">
                        <label for="addCategory" class="form-label small font-weight-semibold">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-control form-select @error('category') is-invalid @enderror"
                            id="addCategory" name="category" required>
                            <option value="">-- Pilih Kategori --</option>
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
                        <label for="addDescription" class="form-label small font-weight-semibold">
                            Deskripsi <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="addDescription" name="description"
                            rows="3" maxlength="500"
                            placeholder="Deskripsi singkat tentang jasa ini. Contoh: Pemasangan ban baru dengan quality check"></textarea>
                        <small class="form-text text-muted d-block mt-1">
                            <i class="cil-info mr-1"></i>
                            Max 500 karakter
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
                        <i class="cil-x mr-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="cil-save mr-1"></i>
                        Simpan Jasa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page_scripts')
    {{-- AutoNumeric CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
    <script>
        // Opsi IDR: titik pemisah ribuan, koma desimal, tanpa desimal, min 0
        const AN_OPS_IDR_INT = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            maximumValue: '999999999',
            modifyValueOnWheel: false,
            unformatOnSubmit: false, // kita handle manual karena pakai AJAX FormData
        };

        let anAddPrice;
        $(function() {
            // Init AutoNumeric untuk field harga
            anAddPrice = new AutoNumeric('#addStandardPrice', AN_OPS_IDR_INT);
        });
    </script>
@endpush

@push('page_scripts')
    <script>
        $(function() {
            // Validasi user-side sebelum AJAX
            $('#formAddService').on('submit', function(e) {
                try {
                    const anEl = AutoNumeric.getAutoNumericElement('#addStandardPrice');
                    if (anEl) {
                        // getNumericString() = "25000" meski tampilan "25.000"
                        $('#addStandardPrice').val(anEl.getNumericString());
                    }
                } catch (err) {
                    console.warn('AutoNumeric not initialized:', err);
                }

                const serviceName = $('#addServiceName').val().trim();
                const category = $('#addCategory').val();

                if (!serviceName) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Error',
                        text: 'Nama jasa harus diisi',
                        icon: 'warning'
                    });
                    $('#addServiceName').focus();
                    return false;
                }
                if (!category) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Error',
                        text: 'Kategori harus dipilih',
                        icon: 'warning'
                    });
                    $('#addCategory').focus();
                    return false;
                }
            });

            // Reset form ketika modal ditutup
            $('#addServiceModal').on('hidden.bs.modal', function() {
                $('#formAddService')[0].reset();
                $('.invalid-feedback').hide();
                $('.is-invalid').removeClass('is-invalid');
            });
        });
    </script>
@endpush

@push('page_styles')
    <style>
        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 0.875rem;
            transition: .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, .15);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
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
            margin-bottom: 0.5rem;
        }

        .font-weight-semibold {
            font-weight: 600;
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 52, 223, .3);
        }
    </style>
@endpush
