{{-- Modules/Product/Resources/views/products/partials/_scripts.blade.php --}}

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script>
        // Avoid auto discover to prevent double attach with x-image-upload component
        if (window.Dropzone) {
            Dropzone.autoDiscover = false;
        }
    </script>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {

            // ============================
            // HELPER: PARSE "Rp 600.000" → 600000
            // ============================
            function parseRupiah(value) {
                if (value === null || value === undefined) return 0;

                var str = value.toString();
                // Take only digits and minus, remove Rp, dots, spaces, etc
                str = str.replace(/[^\d\-]/g, '');

                if (str === '' || str === '-') return 0;

                var intVal = parseInt(str, 10);
                return isNaN(intVal) ? 0 : intVal;
            }

            // ============================
            // DROPZONE: PRODUCT IMAGES
            // ============================
            var uploadedDocumentMap = {};
            var existingFiles = @json(isset($product) ? $product->getMedia('images') : []);

            var myDropzone = new Dropzone("#document-dropzone", {
                url: '{{ route('dropzone.upload') }}',
                maxFilesize: 2, // MB
                acceptedFiles: '.jpg, .jpeg, .png, .gif',
                maxFiles: 3,
                addRemoveLinks: true,
                dictDefaultMessage: '',
                dictRemoveFile: '<i class="bi bi-x-circle text-red-500"></i> Hapus',
                dictMaxFilesExceeded: 'Maksimal 3 gambar',
                dictCancelUpload: 'Batal',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                init: function() {
                    var dz = this;

                    // Edit mode: show existing images
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
                                '<input type="hidden" name="document[]" value="' + file.file_name + '">'
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
                    console.log('✅ Upload berhasil:', response);
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
                    console.error('❌ Upload error:', message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gagal',
                        text: (typeof message === 'string') ?
                            message :
                            'Terjadi kesalahan saat upload',
                        confirmButtonColor: '#2563EB'
                    });
                }
            });

            // ============================
            // MASK MONEY
            // ============================
            $('#product_cost, #product_price').maskMoney({
                prefix: '{{ settings()->currency->symbol }} ',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                precision: 0
            });

            // Format initial values
            $('#product_cost, #product_price').maskMoney('mask');

            // ============================
            // CALCULATE MARGIN
            // ============================
            function calculateProfit() {
                var cost  = parseRupiah($('#product_cost').val());
                var price = parseRupiah($('#product_price').val());

                if (cost > 0 && price > 0) {
                    var profit     = price - cost;               // full rupiah
                    var percentage = ((profit / cost) * 100).toFixed(2);

                    var formattedProfit = '{{ settings()->currency->symbol }} ' +
                        Number(profit).toLocaleString('id-ID');

                    $('#profitAmount').text(formattedProfit);
                    $('#profitPercentage').text(percentage + '%');
                    $('#profitMarginAlert').removeClass('hidden').addClass('flex');

                    // Badge colors using Tailwind classes
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

            // Update when user changes price
            $('#product_cost, #product_price').on('blur change keyup', calculateProfit);

            @if (isset($product))
                // Edit mode: show initial margin
                calculateProfit();
            @else
                // Create mode: hidden by default
                // Already handled by hidden class in HTML
            @endif

            // ============================
            // GENERATE CODE BUTTON (Create mode)
            // ============================
            $('#generateCode').on('click', function () {
                var randomNum = Math.floor(Math.random() * 9999) + 1;
                var newCode   = 'PRD-' + randomNum.toString().padStart(4, '0');
                $('#product_code').val(newCode);

                $(this).find('i').addClass('animate-spin');
                setTimeout(() => {
                    $(this).find('i').removeClass('animate-spin');
                }, 500);
            });

            // ============================
            // FORM SUBMIT + VALIDATION
            // ============================
            $('#product-form').on('submit', function (e) {
                e.preventDefault();

                var cost  = parseRupiah($('#product_cost').val());
                var price = parseRupiah($('#product_price').val());

                // Set pure value to input before sending to server
                $('#product_cost').val(cost);
                $('#product_price').val(price);

                // Basic validation
                if (cost <= 0 || price <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Harga Tidak Valid',
                        text: 'Modal dan harga jual harus lebih besar dari 0',
                        confirmButtonColor: '#2563EB'
                    });

                    // Restore money format
                    $('#product_cost, #product_price').maskMoney('mask');
                    return false;
                }

                // Warning if loss
                if (price < cost) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan Margin',
                        html: 'Harga jual lebih rendah dari modal.<br>Anda berpotensi mengalami kerugian.',
                        showCancelButton: true,
                        confirmButtonColor: '#2563EB',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedSubmit();
                        } else {
                            $('#product_cost, #product_price').maskMoney('mask');
                        }
                    });
                    return false;
                }

                proceedSubmit();
            });

            function proceedSubmit() {
                @if (isset($product))
                    // EDIT MODE
                    Swal.fire({
                        title: 'Simpan Perubahan?',
                        html: 'Produk <strong>"{{ $product->product_name ?? '' }}"</strong> akan diperbarui',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563EB',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: '<i class="bi bi-save me-1"></i> Ya, Simpan!',
                        cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
                        reverseButtons: true,
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoadingAndSubmit();
                        } else {
                            $('#product_cost, #product_price').maskMoney('mask');
                        }
                    });
                @else
                    // CREATE MODE
                    showLoadingAndSubmit();
                @endif
            }

            function showLoadingAndSubmit() {
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $('#product-form')[0].submit();
            }

            // Auto-focus logic
            $('#product_name').focus();
        });
    </script>
@endpush
