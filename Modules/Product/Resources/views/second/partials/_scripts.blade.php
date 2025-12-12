{{-- Modules/Product/Resources/views/second/partials/_scripts.blade.php --}}

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script>
        // WAJIB: matikan autoDiscover begitu Dropzone diload
        if (window.Dropzone) {
            Dropzone.autoDiscover = false;
        }
    </script>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function() {

            // =========================================
            // HELPER: PARSE "Rp 600.000" → 600000
            // =========================================
            function parseRupiah(value) {
                if (value === null || value === undefined) return 0;

                var str = value.toString();

                // Ambil hanya angka dan minus (buang Rp, titik, spasi, dll)
                str = str.replace(/[^\d\-]/g, '');

                if (str === '' || str === '-') return 0;

                var intVal = parseInt(str, 10);
                return isNaN(intVal) ? 0 : intVal;
            }

            // =========================================
            // DROPZONE: FOTO PRODUK BEKAS
            // =========================================
            var uploadedDocumentMap = {};
            var existingFiles = @json(isset($product) ? $product->getMedia('images') : []);

            var myDropzone = new Dropzone("#document-dropzone", {
                url: '{{ route('dropzone.upload') }}',
                maxFilesize: 1, // MB
                acceptedFiles: '.jpg, .jpeg, .png',
                maxFiles: 3,
                addRemoveLinks: true,
                dictDefaultMessage: '<i class="cil-cloud-upload" style="font-size: 3rem;"></i><br>Drop foto di sini atau klik untuk upload',
                dictRemoveFile: '<i class="cil-x-circle"></i> Hapus',
                dictMaxFilesExceeded: 'Maksimal 3 foto',
                dictCancelUpload: 'Batal',
                dictRemoveFileConfirmation: 'Yakin hapus foto ini?',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                init: function() {
                    var dz = this;

                    // Edit mode: tampilkan foto existing
                    if (existingFiles.length > 0) {
                        existingFiles.forEach(function(file) {
                            var mockFile = {
                                name: file.file_name,
                                size: file.size,
                                accepted: true
                            };

                            dz.emit("addedfile", mockFile);
                            dz.emit("thumbnail", mockFile, file.original_url);
                            dz.emit("complete", mockFile);

                            $('form#product-form').append(
                                '<input type="hidden" name="document[]" value="' + file
                                .file_name + '">'
                            );

                            uploadedDocumentMap[mockFile.name] = file.file_name;
                        });

                        dz.options.maxFiles = dz.options.maxFiles - existingFiles.length;
                        if (dz.options.maxFiles < 0) {
                            dz.options.maxFiles = 0;
                        }
                    }
                },
                success: function(file, response) {
                    $('form#product-form').append(
                        '<input type="hidden" name="document[]" value="' + response.name + '">'
                    );
                    uploadedDocumentMap[file.name] = response.name;
                },
                removedfile: function(file) {
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }

                    var name = uploadedDocumentMap[file.name] || file.file_name;
                    $('form#product-form')
                        .find('input[name="document[]"][value="' + name + '"]')
                        .remove();

                    delete uploadedDocumentMap[file.name];
                },
                error: function(file, message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gagal',
                        text: (typeof message === 'string') ?
                            message :
                            'Terjadi kesalahan saat upload',
                        confirmButtonColor: '#4834DF'
                    });
                }
            });

            // =========================================
            // MASK MONEY (AUTONUMERIC) & PROFIT
            // =========================================
            $('#purchase_price, #selling_price').maskMoney({
                prefix: '{{ settings()->currency->symbol }} ',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                precision: 0
            });

            // Terapkan mask ke nilai awal
            $('#purchase_price, #selling_price').maskMoney('mask');

            function calculateProfit() {
                // Pakai helper parseRupiah dari nilai input (bukan unmasked)
                var purchase = parseRupiah($('#purchase_price').val());
                var selling = parseRupiah($('#selling_price').val());

                if (purchase > 0 && selling > 0) {
                    var profit = selling - purchase; // full rupiah

                    // Persentase
                    var percentage = ((profit / purchase) * 100).toFixed(2);

                    // Format rupiah untuk tampilan
                    var formattedProfit = '{{ settings()->currency->symbol }} ' +
                        Number(profit).toLocaleString('id-ID');

                    $('#profitAmount').text(formattedProfit);
                    $('#profitPercentage').text(percentage + '%');
                    $('#profitMarginAlert').removeClass('hidden').addClass('flex');

                    // Warna badge using Tailwind classes
                    var badge = $('#profitPercentage');
                    badge.removeClass('bg-red-100 text-red-800 bg-yellow-100 text-yellow-800 bg-green-100 text-green-800');

                    if (percentage < 10) {
                        badge.addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300');
                    } else if (percentage < 30) {
                        badge.addClass('bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300');
                    } else {
                        badge.addClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300');
                    }
                } else {
                    $('#profitMarginAlert').addClass('hidden').removeClass('flex');
                }
            }

            // Hitung saat blur & change
            $('#purchase_price, #selling_price').on('blur change', calculateProfit);

            @if (isset($product))
                // Edit mode: langsung hitung margin awal
                calculateProfit();
            @endif

            // =========================================
            // SUBMIT FORM + VALIDASI
            // =========================================
            $('#product-form').on('submit', function(e) {
                e.preventDefault();

                // Konversi "Rp 600.000" → 600000 sebelum dikirim ke server
                var purchase = parseRupiah($('#purchase_price').val());
                var selling = parseRupiah($('#selling_price').val());

                $('#purchase_price').val(purchase);
                $('#selling_price').val(selling);

                // Validasi dasar
                if (purchase <= 0 || selling <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Harga Tidak Valid',
                        text: 'Harga beli dan jual harus lebih besar dari 0',
                        confirmButtonColor: '#4834DF'
                    });

                    // Balikkan ke format uang lagi
                    $('#purchase_price, #selling_price').maskMoney('mask');
                    return false;
                }

                // Warning kalau rugi
                if (selling < purchase) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Harga Jual Lebih Rendah',
                        text: 'Harga jual lebih rendah dari harga beli. Yakin melanjutkan?',
                        showCancelButton: true,
                        confirmButtonColor: '#4834DF',
                        cancelButtonColor: '#768192',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        } else {
                            $('#purchase_price, #selling_price').maskMoney('mask');
                        }
                    });
                    return false;
                }

                submitForm();
            });

            function submitForm() {
                @if (isset($product))
                    Swal.fire({
                        title: 'Simpan Perubahan?',
                        html: 'Produk bekas <strong>"{{ $product->name }}"</strong> akan diperbarui',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4834DF',
                        cancelButtonColor: '#768192',
                        confirmButtonText: '<i class="cil-save mr-1"></i> Ya, Simpan!',
                        cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoadingAndSubmit();
                        } else {
                            $('#purchase_price, #selling_price').maskMoney('mask');
                        }
                    });
                @else
                    showLoadingAndSubmit();
                @endif
            }

            function showLoadingAndSubmit() {
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $('#product-form')[0].submit();
            }

            // Auto-focus nama di create mode
            @if (!isset($product))
                $('#name').focus();
            @endif

            // Tooltip bootstrap
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
