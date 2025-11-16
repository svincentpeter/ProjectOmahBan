{{-- Modules/Product/Resources/views/partials/_scripts.blade.php --}}

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script>
        // Hindari auto discover biar nggak double attach sama komponen x-image-upload
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
                // ambil hanya angka dan minus, buang Rp, titik, spasi, dll
                str = str.replace(/[^\d\-]/g, '');

                if (str === '' || str === '-') return 0;

                var intVal = parseInt(str, 10);
                return isNaN(intVal) ? 0 : intVal;
            }

            // ============================
            // MASK MONEY
            // ============================
            $('#product_cost, #product_price').maskMoney({
                prefix: '{{ settings()->currency->symbol }} ',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                precision: 0
            });

            // format nilai awal
            $('#product_cost, #product_price').maskMoney('mask');

            // ============================
            // HITUNG MARGIN
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
                    $('#profitMarginAlert').fadeIn();

                    // warna badge
                    if (percentage < 10) {
                        $('#profitPercentage')
                            .removeClass('badge-primary badge-success badge-warning')
                            .addClass('badge-danger');
                    } else if (percentage < 30) {
                        $('#profitPercentage')
                            .removeClass('badge-primary badge-danger badge-success')
                            .addClass('badge-warning');
                    } else {
                        $('#profitPercentage')
                            .removeClass('badge-danger badge-warning')
                            .addClass('badge-success');
                    }
                } else {
                    $('#profitMarginAlert').fadeOut();
                }
            }

            // Update saat user mengubah harga
            $('#product_cost, #product_price').on('blur change keyup', calculateProfit);

            @if (isset($product))
                // Edit mode: langsung tampilkan margin awal
                calculateProfit();
            @else
                // Create mode: default disembunyikan
                $('#profitMarginAlert').hide();
            @endif

            // ============================
            // TOMBOL GENERATE KODE (hanya ada di Create, aman di Edit)
            // ============================
            $('#generateCode').on('click', function () {
                var randomNum = Math.floor(Math.random() * 9999) + 1;
                var newCode   = 'PRD-' + randomNum.toString().padStart(4, '0');
                $('#product_code').val(newCode);

                $(this).find('i').addClass('rotating');
                setTimeout(() => {
                    $(this).find('i').removeClass('rotating');
                }, 500);
            });

            // ============================
            // SUBMIT FORM + VALIDASI
            // ============================
            $('#product-form').on('submit', function (e) {
                e.preventDefault();

                var cost  = parseRupiah($('#product_cost').val());
                var price = parseRupiah($('#product_price').val());

                // set nilai murni ke input sebelum dikirim ke server
                $('#product_cost').val(cost);
                $('#product_price').val(price);

                // Validasi dasar
                if (cost <= 0 || price <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Harga Tidak Valid',
                        text: 'Modal dan harga jual harus lebih besar dari 0',
                        confirmButtonColor: '#4834DF'
                    });

                    // balikin ke format uang
                    $('#product_cost, #product_price').maskMoney('mask');
                    return false;
                }

                // Warning kalau rugi
                if (price < cost) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harga jual lebih rendah dari modal. Yakin ingin melanjutkan?',
                        showCancelButton: true,
                        confirmButtonColor: '#4834DF',
                        cancelButtonColor: '#768192',
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
                    // EDIT MODE → konfirmasi dulu
                    Swal.fire({
                        title: 'Simpan Perubahan?',
                        html: 'Produk <strong>"{{ $product->product_name ?? '' }}"</strong> akan diperbarui',
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
                            $('#product_cost, #product_price').maskMoney('mask');
                        }
                    });
                @else
                    // CREATE MODE → langsung submit
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

            // Auto-focus dan tooltip
            $('#product_name').focus();
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
