@extends('layouts.app-flowbite')

@section('title', 'Buat Peran Baru')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Peran & Hak Akses', 'url' => route('roles.index')],
        ['text' => 'Buat Peran', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form action="{{ route('roles.store') }}" method="POST" id="role-form">
        @csrf

        {{-- Sticky Action Bar --}}
        <div class="sticky top-[72px] z-50 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 transition-transform duration-300" id="actionBar">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-shield-plus me-2 text-blue-600"></i>
                         Buat Peran Baru
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tentukan nama dan hak akses untuk peran baru</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('roles.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-all font-medium text-sm">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all font-medium text-sm shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Peran
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-info-circle me-2 text-blue-600"></i>
                    Informasi Peran
                </h6>
            </div>
            <div class="p-6">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-tag me-1 text-gray-500"></i> Nama Peran <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Contoh: Manager Toko, Kasir, Admin Gudang" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan nama yang jelas dan mudah dipahami</p>
                </div>
            </div>
        </div>

        {{-- Permissions Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-shield-lock me-2 text-blue-600"></i>
                        Hak Akses (Permissions)
                    </h6>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pilih fitur yang dapat diakses oleh peran ini</p>
                </div>
                
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="select-all" class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Pilih Semua</span>
                </label>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                       $groups = [
                            'dashboard' => ['title' => 'Dashboard', 'icon' => 'bi-speedometer2'],
                            'user_management' => ['title' => 'Manajemen Pengguna', 'icon' => 'bi-people'],
                            'products' => ['title' => 'Produk', 'icon' => 'bi-box-seam'],
                            'adjustments' => ['title' => 'Penyesuaian Stok', 'icon' => 'bi-sliders'],
                            'stock_opname' => ['title' => 'Stock Opname', 'icon' => 'bi-clipboard-check'],
                            'quotations' => ['title' => 'Penawaran Harga', 'icon' => 'bi-file-earmark-text'],
                            'expenses' => ['title' => 'Pengeluaran', 'icon' => 'bi-wallet2'],
                            'customers' => ['title' => 'Pelanggan', 'icon' => 'bi-person-lines-fill'],
                            'suppliers' => ['title' => 'Pemasok', 'icon' => 'bi-truck'],
                            'sales' => ['title' => 'Penjualan', 'icon' => 'bi-cart'],
                            'sale_returns' => ['title' => 'Retur Penjualan', 'icon' => 'bi-arrow-counterclockwise'],
                            'purchases' => ['title' => 'Pembelian', 'icon' => 'bi-bag'],
                            'purchase_returns' => ['title' => 'Retur Pembelian', 'icon' => 'bi-arrow-repeat'],
                            'reports' => ['title' => 'Laporan', 'icon' => 'bi-graph-up'],
                            'settings' => ['title' => 'Pengaturan', 'icon' => 'bi-gear'],
                        ]; 
                    @endphp

                    @foreach ($groups as $key => $info)
                        <div class="permission-card h-full bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-500 hover:shadow-md transition-all dark:bg-gray-800 dark:border-gray-700 dark:hover:border-blue-500">
                            <div class="permission-card-header p-4 bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-700 flex items-center font-semibold text-gray-900 dark:text-white cursor-pointer select-none">
                                <i class="bi {{ $info['icon'] }} text-blue-600 me-2"></i>
                                {{ $info['title'] }}
                            </div>
                            <div class="permission-card-body p-4">
                                @include('user::roles.partials.permissions-list', [
                                    'group' => $key,
                                ])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Select/Deselect All
            $('#select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.permission-card-body input[type="checkbox"]').prop('checked', isChecked);

                // Visual feedback
                if (isChecked) {
                    $('.permission-card').addClass('ring-1 ring-blue-500 border-blue-500');
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Semua hak akses dipilih'
                    });
                } else {
                    $('.permission-card').removeClass('ring-1 ring-blue-500 border-blue-500');
                }
            });

            // Update "Select All" when individual checkboxes change
            $(document).on('change', '.permission-card-body input[type="checkbox"]', function() {
                const total = $('.permission-card-body input[type="checkbox"]').length;
                const checked = $('.permission-card-body input[type="checkbox"]:checked').length;

                $('#select-all').prop('checked', total === checked);
            });

            // Permission Card Click Effect to toggle all in card
            $('.permission-card-header').on('click', function() {
                const card = $(this).closest('.permission-card');
                const checkboxes = card.find('.permission-card-body input[type="checkbox"]');
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;

                // Toggle all checkboxes in this card
                checkboxes.prop('checked', !allChecked);

                 // Trigger change event manually to update Select All
                 checkboxes.first().trigger('change');
            });

            // Form Validation
            $('#role-form').on('submit', function(e) {
                const roleName = $('#name').val().trim();
                const checkedPermissions = $('.permission-card-body input[type="checkbox"]:checked').length;

                if (!roleName) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Nama Peran Wajib Diisi',
                        text: 'Mohon masukkan nama untuk peran ini',
                        confirmButtonColor: '#2563EB'
                    });
                    $('#name').focus();
                    return false;
                }

                if (checkedPermissions === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada Hak Akses',
                        text: 'Mohon pilih minimal satu hak akses untuk peran ini',
                        confirmButtonColor: '#2563EB'
                    });
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        });
    </script>
@endpush
