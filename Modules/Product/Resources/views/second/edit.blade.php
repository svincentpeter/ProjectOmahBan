@extends('layouts.app')

@section('title', 'Edit Produk Bekas')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products_second.index') }}">Produk Bekas</a></li>
        <li class="breadcrumb-item active">Edit: {{ $product->name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="product-form" action="{{ route('products_second.update', $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Edit Produk Bekas: {{ $product->name }}
                            </h5>
                            <small class="text-muted">Perbarui informasi produk bekas</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products_second.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Include Modern Form --}}
                @include('product::second._form', ['product' => $product])
            </form>
        </div>
    </div>
@endsection

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        // DISABLE AUTO-DISCOVER - IMPORTANT!
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            // Manual Dropzone Initialization
            var uploadedDocumentMap = {};

            var myDropzone = new Dropzone("#document-dropzone", {
                url: '{{ route('dropzone.upload') }}',
                maxFilesize: 1, // 1MB
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

                @if (isset($product) && $product->getMedia('images')->count() > 0)
                    init: function() {
                        var thisDropzone = this;
                        // Load existing images
                        var files = {!! json_encode($product->getMedia('images')) !!};

                        $.each(files, function(key, file) {
                            var mockFile = {
                                name: file.file_name,
                                size: file.size,
                                accepted: true
                            };

                            thisDropzone.emit("addedfile", mockFile);
                            thisDropzone.emit("thumbnail", mockFile, file.original_url);
                            thisDropzone.emit("complete", mockFile);

                            // Add to form
                            $('form').append('<input type="hidden" name="document[]" value="' +
                                file.file_name + '">');
                            uploadedDocumentMap[mockFile.name] = file.file_name;
                        });
                    },
                @endif

                success: function(file, response) {
                    console.log('Upload success:', response);
                    $('form').append('<input type="hidden" name="document[]" value="' + response.name +
                        '">');
                    uploadedDocumentMap[file.name] = response.name;
                },

                removedfile: function(file) {
                    console.log('Removing file:', file.name);

                    // Remove preview
                    if (file.previewElement) {
                        file.previewElement.remove();
                    }

                    // Remove from form
                    var name = uploadedDocumentMap[file.name] || file.file_name;
                    $('form').find('input[name="document[]"][value="' + name + '"]').remove();

                    // Remove from map
                    delete uploadedDocumentMap[file.name];
                },

                error: function(file, message) {
                    console.error('Upload error:', message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gagal',
                        text: typeof message === 'string' ? message :
                            'Terjadi kesalahan saat upload',
                        confirmButtonColor: '#4834DF'
                    });
                }
            });

            // Mask Money
            $('#purchase_price, #selling_price').maskMoney({
                prefix: '{{ settings()->currency->symbol }} ',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                precision: 0
            });

            $('#purchase_price, #selling_price').maskMoney('mask');

            // Calculate Profit
            function calculateProfit() {
                const purchase = parseFloat($('#purchase_price').maskMoney('unmasked')[0]) || 0;
                const selling = parseFloat($('#selling_price').maskMoney('unmasked')[0]) || 0;

                if (purchase > 0 && selling > 0) {
                    const profit = selling - purchase;
                    const percentage = ((profit / purchase) * 100).toFixed(2);

                    const formattedProfit = '{{ settings()->currency->symbol }} ' + profit.toString().replace(
                        /\B(?=(\d{3})+(?!\d))/g, '{{ settings()->currency->thousand_separator }}');

                    $('#profitAmount').text(formattedProfit);
                    $('#profitPercentage').text(percentage + '%');
                    $('#profitMarginAlert').fadeIn();

                    // Color coding
                    if (percentage < 10) {
                        $('#profitPercentage').removeClass('badge-primary badge-success badge-warning').addClass(
                            'badge-danger');
                    } else if (percentage < 30) {
                        $('#profitPercentage').removeClass('badge-primary badge-danger badge-success').addClass(
                            'badge-warning');
                    } else {
                        $('#profitPercentage').removeClass('badge-danger badge-warning').addClass('badge-success');
                    }
                } else {
                    $('#profitMarginAlert').fadeOut();
                }
            }

            $('#purchase_price, #selling_price').on('blur', calculateProfit);

            @if (isset($product))
                // Calculate initial profit for edit mode
                calculateProfit();
            @endif

            // Form Submit
            $('#product-form').on('submit', function(e) {
                e.preventDefault();

                // Unmask
                $('#purchase_price').val($('#purchase_price').maskMoney('unmasked')[0]);
                $('#selling_price').val($('#selling_price').maskMoney('unmasked')[0]);

                const purchase = parseFloat($('#purchase_price').val());
                const selling = parseFloat($('#selling_price').val());

                // Validate
                if (purchase <= 0 || selling <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Harga Tidak Valid',
                        text: 'Harga beli dan jual harus lebih besar dari 0',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#purchase_price, #selling_price').maskMoney('mask');
                    return false;
                }

                // Warning if loss
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
                    // Edit mode - confirmation
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
                    // Create mode - direct submit
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

            // Auto-focus
            @if (!isset($product))
                $('#name').focus();
            @endif

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush

@push('page_styles')
    <style>
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .action-bar {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
        }

        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }
    </style>
@endpush
