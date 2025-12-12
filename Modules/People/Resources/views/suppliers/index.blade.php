@extends('layouts.app-flowbite')

@section('title', 'Daftar Supplier')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Supplier',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Supplier', 'url' => '#']
        ]
    ])
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Supplier -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Supplier</p>
                    <h3 class="text-2xl font-black text-blue-600">{{ \Modules\People\Entities\Supplier::count() }}</h3>
                </div>
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-people text-xl"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50 rounded-full group-hover:bg-blue-100 transition-colors z-0"></div>
        </div>

        <!-- Supplier Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Supplier Aktif</p>
                    <h3 class="text-2xl font-black text-emerald-600">
                        {{ \Modules\People\Entities\Supplier::whereHas('purchases', function ($q) {
                            $q->where('date', '>=', now()->subMonths(6));
                        })->count() }}
                    </h3>
                </div>
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:bg-emerald-100 transition-colors z-0"></div>
        </div>

        <!-- Total Kota -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Jangkauan Kota</p>
                    <h3 class="text-2xl font-black text-violet-600">
                        {{ \Modules\People\Entities\Supplier::distinct('city')->count('city') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-violet-50 text-violet-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-geo-alt text-xl"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-violet-50 rounded-full group-hover:bg-violet-100 transition-colors z-0"></div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Transaksi</p>
                    <h3 class="text-2xl font-black text-amber-500">
                        {{ \Modules\Purchase\Entities\Purchase::count() }}
                    </h3>
                </div>
                <div class="p-2.5 bg-amber-50 text-amber-500 rounded-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-cart-check text-xl"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-50 rounded-full group-hover:bg-amber-100 transition-colors z-0"></div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
            {{-- Header & Toolbar --}}
            <div class="p-5 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-lg font-bold text-zinc-800 flex items-center">
                        <i class="bi bi-people-fill text-blue-600 me-2"></i>
                        Daftar Supplier
                    </h5>
                    <p class="text-sm text-zinc-500 mt-1">Kelola data mitra pemasok untuk inventaris toko</p>
                </div>
                <div>
                    @can('create_suppliers')
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm hover:shadow-md">
                        <i class="bi bi-plus-lg me-2"></i> Tambah Supplier
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Filter Section --}}
            {{-- Filter Section --}}
            @include('layouts.filter-card', [
                'action' => route('suppliers.index'),
                'title' => 'Filter Data',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                     [
                        'label' => 'Semua', 
                        'value' => '', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => '']),
                        'icon' => 'bi bi-grid'
                     ],
                     [
                        'label' => 'Aktif', 
                        'value' => 'active', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => 'active']),
                        'icon' => 'bi bi-check-circle'
                     ],
                     [
                        'label' => 'Tidak Aktif', 
                        'value' => 'inactive', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => 'inactive']),
                        'icon' => 'bi bi-x-circle'
                     ],
                ],
                'filters' => [
                    [
                        'name' => 'city',
                        'label' => 'Kota',
                        'type' => 'select',
                        'options' => collect(\Modules\People\Entities\Supplier::getUniqueCities())->mapWithKeys(fn($item) => [$item => $item])->toArray()
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            'active' => 'Aktif (Ada Transaksi)',
                            'inactive' => 'Tidak Aktif'
                        ]
                    ]
                ]
            ])
        </div>
        </div>

        <!-- DataTable -->
        <div class="p-0">
            <div class="overflow-x-auto">
                {{ $dataTable->table(['class' => 'w-full text-sm text-left text-zinc-500 dark:text-zinc-400', 'id' => 'suppliers-table'], true) }}
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
    <script>
        $(document).ready(function() {
            // Use jQuery selector to avoid race condition with window.LaravelDataTables
            $('#suppliers-table').on('preXhr.dt', function ( e, settings, data ) {
                data.city = $('#city').val();
                data.status = $('#status').val();
            });
            
            // Handle Filter Changes
            $('#city, #status').on('change', function() {
                $('#suppliers-table').DataTable().draw();
            });

            // SweetAlert2 Delete Confirmation (Delegated Event)
            $(document).on('click', '.delete-supplier', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasPurchases = $(this).data('has-purchases'); // boolean string 'true'/'false'
                const url = '{{ route('suppliers.destroy', ':id') }}'.replace(':id', id);

                // Customize message based on purchase history
                let warningTitle = 'Hapus Supplier?';
                let warningText = `Anda akan menghapus data supplier <strong>"${name}"</strong>.`;
                let warningIcon = 'warning'; 

                if (hasPurchases === 'true') {
                    warningText += `<br><br><span class="text-orange-600 font-bold"><i class="bi bi-archive me-1"></i> Perhatian:</span> Supplier ini memiliki riwayat transaksi. Data akan diarsipkan (Soft Delete) demi integritas data.`;
                } else {
                    warningText += `<br><br><span class="text-red-600 font-bold"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan:</span> Tindakan ini bersifat permanen dan tidak dapat dibatalkan!`;
                }

                Swal.fire({
                    title: warningTitle,
                    html: warningText,
                    icon: warningIcon,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create and submit form
                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        });
                        
                        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                        
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
