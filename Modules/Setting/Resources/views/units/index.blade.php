@extends('layouts.app-flowbite')

@section('title', 'Satuan Produk')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
      <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
        <li class="inline-flex items-center">
          <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-zinc-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
            <i class="bi bi-house-door me-2"></i> Beranda
          </a>
        </li>
        <li aria-current="page">
          <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-1 text-xs"></i>
            <span class="ms-1 text-sm font-medium text-zinc-900 md:ms-2 dark:text-gray-400">Satuan</span>
          </div>
        </li>
      </ol>
    </nav>
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Info Alert --}}
    <div class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
        <i class="bi bi-info-circle flex-shrink-0 w-4 h-4 me-2"></i>
        <span class="sr-only">Info</span>
        <div class="text-sm font-medium">
             Satuan digunakan untuk mengatur unit pengukuran produk. Operator dan nilai operasi digunakan untuk konversi antar satuan.
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight">
                        <i class="bi bi-calculator me-2 text-blue-600"></i>Daftar Satuan Produk
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola satuan dan konversi unit produk</p>
                </div>
                
                @can('create_units')
                <div>
                    <a href="{{ route('units.create') }}" class="text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow inline-flex items-center">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Satuan
                    </a>
                </div>
                @endcan
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="p-6 pt-0 mt-2 overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-gray-400" id="data-table">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">No.</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">Nama Satuan</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Singkatan</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Operator</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Nilai Operasi</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($units as $unit)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 text-center font-bold text-zinc-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900 dark:text-blue-300 me-3">
                                     <i class="bi bi-calculator"></i>
                                </div>
                                <span class="font-extrabold text-black dark:text-white">{{ $unit->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                             <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $unit->short_name }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                             <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">{{ $unit->operator }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-extrabold text-black dark:text-white">
                            {{ $unit->operation_value }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @can('edit_units')
                                <a href="{{ route('units.edit', $unit) }}" 
                                   class="p-2 text-slate-500 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                
                                @can('delete_units')
                                <button type="button" 
                                        class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors btn-delete"
                                        data-id="{{ $unit->id }}"
                                        data-name="{{ $unit->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                
                                <form id="delete-form-{{ $unit->id }}" 
                                      action="{{ route('units.destroy', $unit) }}" 
                                      method="POST" 
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center py-6">
                                <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500 font-bold">Belum ada satuan</p>
                                <p class="text-xs text-gray-400">Klik tombol "Tambah Satuan" untuk mulai menambah data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function () {
             // Initialize DataTable
             var table = $('#data-table').DataTable({
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center gap-2"l>f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip>',
                ordering: true,
                order: [[1, 'asc']],
                paging: true,
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada satuan",
                    search: "",
                    lengthMenu: "_MENU_"
                },
                columnDefs: [
                    { orderable: false, targets: [5] } // Disable sorting on Action column
                ],
                drawCallback: function() {
                    $('.dataTables_filter input').attr('placeholder', 'Cari satuan...');
                }
            });

            // SweetAlert2 Delete
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const form = $(`#delete-form-${id}`);
                
                Swal.fire({
                    title: 'Hapus Satuan?',
                    html: `Satuan <strong>"${name}"</strong> akan dihapus permanen.`,
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
                            text: 'Mohon tunggu',
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
