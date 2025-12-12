@extends('layouts.app-flowbite')

@section('title', 'Daftar Pembelian Stok Bekas')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pembelian Bekas', 'url' => route('purchases.second.index')],
                ['text' => 'Daftar Pembelian', 'url' => '#', 'icon' => 'bi bi-recycle'],
            ]
        ])
    @endsection

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Pembelian --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md">
            <div class="p-3 mr-4 text-purple-600 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl dark:text-purple-100 dark:from-purple-900 dark:to-purple-800">
                <i class="bi bi-recycle text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Pembelian</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200">
                    {{ $summary['total_purchases'] }}
                </p>
            </div>
        </div>

        {{-- Total Nilai --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md">
            <div class="p-3 mr-4 text-blue-600 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl dark:text-blue-100 dark:from-blue-900 dark:to-blue-800">
                <i class="bi bi-credit-card text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Nilai</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200">
                    {{ format_currency($summary['total_amount']) }}
                </p>
            </div>
        </div>

        {{-- Terbayar --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md">
            <div class="p-3 mr-4 text-emerald-600 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl dark:text-emerald-100 dark:from-emerald-900 dark:to-emerald-800">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Terbayar</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200">
                    {{ format_currency($summary['total_paid']) }}
                </p>
            </div>
        </div>

        {{-- Sisa Hutang --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md">
            <div class="p-3 mr-4 text-orange-600 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl dark:text-orange-100 dark:from-orange-900 dark:to-orange-800">
                <i class="bi bi-exclamation-triangle text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Sisa Hutang</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200">
                    {{ format_currency($summary['total_due']) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    @include('layouts.filter-card', [
        'action' => route('purchases.second.index'),
        'title' => 'Filter Data Pembelian Bekas',
        'icon' => 'bi bi-funnel',
        'quickFilters' => [
            ['label' => 'Semua', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'all']), 'param' => 'quick_filter', 'value' => 'all', 'icon' => 'bi bi-grid'],
            ['label' => 'Hari Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'today']), 'param' => 'quick_filter', 'value' => 'today', 'icon' => 'bi bi-clock'],
            ['label' => 'Kemarin', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'yesterday']), 'param' => 'quick_filter', 'value' => 'yesterday', 'icon' => 'bi bi-clock-history'],
            ['label' => 'Minggu Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_week']), 'param' => 'quick_filter', 'value' => 'this_week', 'icon' => 'bi bi-calendar-week'],
            ['label' => 'Bulan Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_month']), 'param' => 'quick_filter', 'value' => 'this_month', 'icon' => 'bi bi-calendar-month'],
        ],
        'filters' => [
            ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => $from ?? null],
            ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => $to ?? null],
            ['name' => 'customer', 'label' => 'Customer', 'type' => 'text', 'value' => request('customer'), 'placeholder' => 'Cari Customer...'],
            ['name' => 'payment_status', 'label' => 'Status Bayar', 'type' => 'select', 'options' => ['Lunas' => 'Lunas', 'Belum Lunas' => 'Belum Lunas'], 'placeholder' => 'Semua Status'],
        ]
    ])

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-recycle mr-2 text-purple-600"></i>
                    Daftar Pembelian Produk Bekas
                </h3>
                <p class="text-xs text-gray-500 mt-1">Kelola pembelian ban & velg bekas dari customer</p>
            </div>
            @can('create_purchases')
                <a href="{{ route('purchases.second.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-purple-500/30 hover:scale-[1.02] transition-transform duration-200">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Pembelian
                </a>
            @endcan
        </div>
        
        <div class="p-4">
            {{ $dataTable->table() }}
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
        // Global Action Functions
        window.toggleActionDropdown = function(event, id) {
            event.stopPropagation();
            const button = event.currentTarget;
            const dropdown = document.getElementById('dropdown-' + id);
            
            // Close all other dropdowns
            document.querySelectorAll('.action-dropdown').forEach(d => {
                if (d.id !== 'dropdown-' + id) {
                    d.style.display = 'none';
                }
            });
            
            // Toggle current dropdown
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                const rect = button.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + 5) + 'px';
                dropdown.style.left = (rect.left - 200) + 'px'; // Custom left offset adjustment if needed, usually right aligned 
                // Better positioning logic for right alignment:
                dropdown.style.left = (rect.right - 224) + 'px'; // 224px is w-56
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        };

        window.confirmDeletePurchaseSecond = function(id) {
            Swal.fire({
                title: 'Hapus Pembelian?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-red-600 text-white font-bold py-2 px-4 rounded-xl',
                    cancelButton: 'bg-gray-400 text-white font-bold py-2 px-4 rounded-xl ml-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('destroy' + id).submit();
                }
            });
        };

        $(document).ready(function() {
            // Close dropdowns on outside click
            $(document).on('click', function(event) {
                if (!$(event.target).closest('[id^="dropdown-"]').length && !$(event.target).closest('button').length) {
                    $('.action-dropdown').hide();
                }
            });

            // Handle scroll to hide dropdowns
            $('.dataTables_scrollBody').on('scroll', function() {
                $('.action-dropdown').hide();
            });

            // Inject Filters into DataTable AJAX
            const table = window.LaravelDataTables['purchases-second-table'];
            table.on('preXhr.dt', function ( e, settings, data ) {
                data.quick_filter = '{{ request('quick_filter') }}';
                data.from = $('input[name="from"]').val();
                data.to = $('input[name="to"]').val();
                data.customer = $('input[name="customer"]').val();
                data.payment_status = $('select[name="payment_status"]').val();
            });
        });
    </script>
@endpush
