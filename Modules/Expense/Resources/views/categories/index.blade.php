@extends('layouts.app-flowbite')

@section('title', 'Kategori Pengeluaran')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pengeluaran', 'url' => route('expenses.index')],
                ['text' => 'Kategori', 'url' => '#', 'icon' => 'bi bi-folder2-open'],
            ]
        ])
    @endsection

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Total Kategori --}}
        <div class="relative bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <i class="bi bi-layers-fill text-9xl"></i>
            </div>
            <div class="relative z-10 flex items-center">
                <div class="p-3 bg-white/20 rounded-xl mr-4 backdrop-blur-sm shadow-inner icon-float">
                    <i class="bi bi-tag-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Kategori</p>
                    <h3 class="text-3xl font-bold">{{ $categories_count }}</h3>
                </div>
            </div>
        </div>

        {{-- Status Sistem (Static) --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl mr-4">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-gray-400 text-sm font-medium mb-1">Status Sistem</p>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Aktif</h3>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="px-4 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider rounded-lg border border-emerald-200 dark:border-emerald-800">
                    Online
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm rounded-2xl overflow-hidden">
        {{-- Card Header --}}
        <div class="p-6 border-b border-slate-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-700/20">
            <div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-folder2-open text-blue-600"></i>
                    Daftar Kategori
                </h2>
                <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Kelola kategori untuk pengelompokan pengeluaran operasional.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('expense-categories.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 shadow-lg shadow-blue-500/30 transition-all duration-200">
                    <i class="bi bi-plus-lg mr-2"></i>
                    Tambah Kategori
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
        // Global SweetAlert Delete (Reusable / Standardized)
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const form = $('#delete-form-' + id);
            
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Kategori \"" + name + "\" akan dihapus permanen.",
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
                    form.submit();
                }
            });
        });
    </script>
@endpush
