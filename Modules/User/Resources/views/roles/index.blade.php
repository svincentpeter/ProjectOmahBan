@extends('layouts.app-flowbite')

@section('title', 'Peran & Hak Akses')

@section('content')
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                @include('layouts.breadcrumb-flowbite', ['items' => [
                    ['text' => 'Home', 'url' => route('home')],
                    ['text' => 'Peran & Hak Akses', 'url' => '#']
                ]])
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Peran & Hak Akses</h1>
            </div>
            
            {{-- Info Alert --}}
             <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 inline w-4 h-4 me-3"></i>
                <div class="flex-1">
                    <span class="font-medium">Perhatian:</span> Peran mengontrol akses pengguna ke fitur sistem. Pastikan memberikan permissions yang tepat untuk menjaga keamanan data.
                </div>
                <div class="ml-auto">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white dark:bg-gray-700 px-3 py-1 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mr-1">Total Peran</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ \Spatie\Permission\Models\Role::count() - 1 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="sm:pr-3" action="#" method="GET">
                        <label for="roles-search" class="sr-only">Search</label>
                    </form>
                </div>
                <a href="{{ route('roles.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Peran
                </a>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="p-4 bg-white dark:bg-gray-900">
                        {{ $dataTable->table(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-600'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('includes.datatables-flowbite-css')
@endsection

@push('page_scripts')
    {{ $dataTable->scripts() }}
    
    <script>
        $(document).ready(function() {
            // SweetAlert2 Delete Confirmation
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const roleName = $(this).data('name');
                const form = $(this).closest('form');
                
                Swal.fire({
                    title: 'Hapus Peran?',
                    html: `Peran <strong>"${roleName}"</strong> akan dihapus dari sistem.<br><br>` +
                          `<span class="text-red-500 text-sm font-medium"><i class="bi bi-exclamation-triangle me-1"></i>` +
                          `Pengguna dengan peran ini akan kehilangan akses mereka!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E02424', // Red-600
                    cancelButtonColor: '#6B7280', // Gray-500
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
