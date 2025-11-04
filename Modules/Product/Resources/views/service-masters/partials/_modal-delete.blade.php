{{-- 
    Modal: Konfirmasi Hapus Jasa
    Styling: Konsisten dengan modal lainnya
    - Header warning/danger
    - Info detail jasa yang akan dihapus
    - Konfirmasi dengan double-check
    - Button Batal/Hapus
    - Hint: Riwayat penggunaan tetap tersimpan
--}}

<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceLabel" aria-hidden="true">

    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            {{-- MODAL HEADER --}}
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteServiceLabel">
                    <i class="cil-trash mr-2"></i>
                    <span class="font-weight-bold">Hapus Jasa</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>

            {{-- MODAL BODY --}}
            <div class="modal-body">

                {{-- WARNING ALERT --}}
                <div class="alert alert-danger alert-icon alert-icon-border rounded-1" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                                <path d="M12 13v.01" />
                                <path d="M12 16v.01" />
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="alert-title">
                                <strong>Peringatan!</strong>
                            </h4>
                            <div class="text-secondary">
                                Anda akan menghapus jasa berikut. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFO: Jasa yang akan dihapus --}}
                <div class="card border border-danger border-opacity-25 bg-light">
                    <div class="card-body">
                        <h6 class="mb-2 text-muted small font-weight-semibold">
                            <i class="cil-info mr-1"></i>
                            Jasa yang akan dihapus:
                        </h6>
                        <h4 id="deleteServiceName" class="mb-3 text-danger font-weight-bold">
                            -
                        </h4>
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Harga Standar:</small>
                                <strong id="deleteServicePrice">Rp 0</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Kategori:</small>
                                <strong id="deleteServiceCategory">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFO: Konsekuensi hapus --}}
                <div class="mt-3">
                    <p class="mb-2 small">
                        <strong>Apa yang akan terjadi:</strong>
                    </p>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-1">
                            <i class="cil-check-circle text-success mr-2"></i>
                            Jasa <strong>tidak akan</strong> muncul di dropdown POS
                        </li>
                        <li class="mb-1">
                            <i class="cil-check-circle text-success mr-2"></i>
                            Riwayat penggunaan jasa tetap tersimpan untuk audit
                        </li>
                        <li class="mb-1">
                            <i class="cil-check-circle text-success mr-2"></i>
                            Tidak ada data yang hilang dari database
                        </li>
                    </ul>
                </div>

                {{-- CHECKBOX: Double confirmation --}}
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDelete" value="">
                    <label class="form-check-label small" for="confirmDelete">
                        Saya yakin ingin menghapus jasa ini
                    </label>
                </div>

            </div>

            {{-- MODAL FOOTER --}}
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="cil-x mr-1"></i>
                    Jangan Hapus
                </button>
                <form id="deleteServiceForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="btnConfirmDelete" disabled>
                        <i class="cil-trash mr-1"></i>
                        Ya, Hapus Jasa
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

@push('page_scripts')
    <script>
        $(function() {
            // Populate modal saat ditrigger dari tombol Delete di table
            $('#deleteServiceModal').on('show.bs.modal', function(e) {
                const btn = $(e.relatedTarget); // Tombol yang trigger modal

                // Ambil data attributes dari tombol
                const id = btn.data('id');
                const name = btn.data('name');
                const price = parseInt(btn.data('price')) || 0;
                const category = btn.data('category') || '-';

                // Set form action URL
                const actionUrl = '{{ route('service-masters.destroy', ':id') }}'.replace(':id', id);
                $('#deleteServiceForm').attr('action', actionUrl);

                // Populate display fields
                $('#deleteServiceName').text(name);

                const formattedPrice = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(price);
                $('#deleteServicePrice').text(formattedPrice);

                // Format kategori display
                const categoryMap = {
                    'service': 'Service (Jasa)',
                    'goods': 'Goods (Barang)',
                    'custom': 'Custom (Khusus)'
                };
                $('#deleteServiceCategory').text(categoryMap[category] || category);

                // Reset checkbox
                $('#confirmDelete').prop('checked', false);
                $('#btnConfirmDelete').prop('disabled', true);
            });

            // Toggle button disabled based on checkbox
            $('#confirmDelete').on('change', function() {
                $('#btnConfirmDelete').prop('disabled', !this.checked);
            });

            // Form submission dengan konfirmasi
            $('#deleteServiceForm').on('submit', function(e) {
                // Cek checkbox (redundant safety check)
                if (!$('#confirmDelete').is(':checked')) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Gagal',
                        text: 'Anda harus mencentang checkbox untuk melanjutkan',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu, jasa sedang dihapus',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });

            // Reset modal saat ditutup
            $('#deleteServiceModal').on('hidden.bs.modal', function() {
                $('#confirmDelete').prop('checked', false);
                $('#btnConfirmDelete').prop('disabled', true);
            });

            // Keyboard shortcut: ESC untuk batal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#deleteServiceModal').hasClass('show')) {
                    $('#deleteServiceModal').modal('hide');
                }
            });
        });
    </script>
@endpush

@push('page_styles')
    <style>
        /* Alert styling */
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-danger .alert-icon {
            color: #f9b115;
        }

        .alert-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        /* Card untuk info jasa */
        .card {
            border-radius: 8px;
            transition: .2s;
        }

        .card.border-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .card-body {
            padding: 1rem;
        }

        /* Checkbox styling */
        .form-check-input {
            width: 1.25em;
            height: 1.25em;
            margin-top: 0.3em;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: .2s;
        }

        .form-check-input:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .form-check-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        /* Modal styling */
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        .modal-footer.bg-light {
            background-color: #f8f9fa !important;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: .2s;
        }

        .btn-danger:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, .3);
        }

        .btn-danger:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-light {
            background-color: #f8f9fa;
            border-color: #ddd;
            color: #4f5d73;
        }

        .btn-light:hover {
            background-color: #e9ecef;
            border-color: #ccc;
        }

        /* List styling */
        .list-unstyled li {
            line-height: 1.6;
        }

        /* SVG icon styling */
        svg.alert-icon {
            color: #dc3545;
        }
    </style>
@endpush
