@extends('layouts.app-flowbite')

@section('title', 'Pengeluaran Harian')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pengeluaran', 'url' => route('expenses.index')],
                ['text' => 'Buku Pengeluaran', 'url' => '#', 'icon' => 'bi bi-wallet2'],
            ]
        ])
    @endsection

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <i class="bi bi-wallet2 text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 icon-float shadow-inner">
                    <i class="bi bi-currency-dollar text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Pengeluaran</p>
                    <h3 class="text-2xl font-bold tracking-tight">Rp {{ number_format($total, 0, ',', '.') }}</h3>
                    @if($from && $to)
                        <div class="inline-flex items-center mt-2 px-2 py-1 bg-white/10 rounded-lg text-xs text-blue-50">
                            <i class="bi bi-calendar-range mr-1.5"></i>
                            {{ \Carbon\Carbon::parse($from)->format('d M') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    @include('layouts.filter-card', [
        'action' => route('expenses.index'),
        'title' => 'Filter Data Pengeluaran',
        'icon' => 'bi bi-funnel',
        'quickFilters' => [
            ['label' => 'Hari Ini', 'url' => route('expenses.index', array_merge(request()->query(), ['quick_filter' => 'today'])), 'param' => 'quick_filter', 'value' => 'today', 'icon' => 'bi bi-clock'],
            ['label' => 'Kemarin', 'url' => route('expenses.index', array_merge(request()->query(), ['quick_filter' => 'yesterday'])), 'param' => 'quick_filter', 'value' => 'yesterday', 'icon' => 'bi bi-clock-history'],
            ['label' => 'Minggu Ini', 'url' => route('expenses.index', array_merge(request()->query(), ['quick_filter' => 'this_week'])), 'param' => 'quick_filter', 'value' => 'this_week', 'icon' => 'bi bi-calendar-week'],
            ['label' => 'Bulan Ini', 'url' => route('expenses.index', array_merge(request()->query(), ['quick_filter' => 'this_month'])), 'param' => 'quick_filter', 'value' => 'this_month', 'icon' => 'bi bi-calendar-month'],
            ['label' => 'Semua', 'url' => route('expenses.index', array_merge(request()->query(), ['quick_filter' => 'all'])), 'param' => 'quick_filter', 'value' => 'all', 'icon' => 'bi bi-collection'],
        ],
        'filters' => [
            ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => $from ?? request('from')],
            ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => $to ?? request('to')],
            ['name' => 'category_id', 'label' => 'Kategori', 'type' => 'select', 'options' => $categories->pluck('category_name', 'id')->toArray(), 'placeholder' => 'Semua Kategori', 'value' => request('category_id')],
        ]
    ])

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm rounded-2xl overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-700/20">
            <div>
                <h5 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-wallet2 text-blue-600"></i>
                    Daftar Pengeluaran
                </h5>
                <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Kelola dan monitor pengeluaran operasional bisnis.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('expenses.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 shadow-lg shadow-blue-500/30 transition-all duration-200">
                    <i class="bi bi-plus-lg mr-2"></i>
                    Tambah Pengeluaran
                </a>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="p-5">
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
        // Global SweetAlert Delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let name = $(this).data('name');
            
            Swal.fire({
                title: 'Hapus Pengeluaran?',
                text: "Anda yakin ingin menghapus data pengeluaran \"" + name + "\"? Aksi ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#E5E7EB',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-red-600 text-white hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2',
                    cancelButton: 'bg-gray-100 text-gray-800 hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${id}`).submit();
                }
            });
        });
    </script>
@endpush

