@extends('layouts.app-flowbite')

@section('title', 'Mata Uang')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Mata Uang',
        'items' => [['text' => 'Home', 'url' => route('home')], ['text' => 'Mata Uang', 'url' => '#']],
    ])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Main Card --}}
    <div class="relative bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden">
        {{-- Card Header & Actions --}}
        <div class="p-6 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-zinc-800 flex items-center gap-2">
                    <i class="bi bi-currency-exchange text-blue-600"></i>
                    Daftar Mata Uang
                </h2>
                <p class="text-sm text-zinc-500 mt-1">Kelola mata uang untuk transaksi multi-currency.</p>
            </div>

            @can('create_currencies')
                <div class="flex items-center gap-2">
                    <a href="{{ route('currencies.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm shadow-blue-200">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Tambah Mata Uang
                    </a>
                </div>
            @endcan
        </div>

        {{-- Info Alert --}}
        <div class="px-6 pt-6">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <i class="bi bi-info-circle text-blue-600 text-lg flex-shrink-0 mt-0.5"></i>
                <div class="text-sm text-blue-800">
                    <span class="font-bold block mb-1">Informasi</span>
                    Mata uang digunakan untuk transaksi dengan nilai tukar berbeda. Anda dapat menambahkan, mengedit, atau
                    menghapus mata uang sesuai kebutuhan bisnis.
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="p-6">
            <div class="rounded-xl border border-zinc-200 overflow-hidden">
                <div class="overflow-x-auto">
                    {{ $dataTable->table(['class' => 'w-full text-sm text-left text-zinc-500'], true) }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            // Delete confirmation
            $(document).on('click', '.delete-currency', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Mata Uang?',
                    html: `Mata uang <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-slate-500">Data yang dihapus tidak dapat dikembalikan!</small>`,
                    icon: 'warning',
                    iconColor: '#EF4444',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#E4E4E7',
                    confirmButtonText: '<i class="bi bi-trash mr-2"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'swal2-confirm-btn',
                        cancelButton: 'swal2-cancel-btn',
                        title: 'swal2-title-custom',
                        htmlContainer: 'swal2-html-custom',
                        popup: 'swal2-popup-custom'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });

                        // Create and submit form
                        const form = $('<form>', {
                            'method': 'POST',
                            'action': url
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Set default confirmation
            $(document).on('click', '.set-default-currency', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Set Default?',
                    html: `Jadikan <strong>"${name}"</strong> sebagai mata uang default?`,
                    icon: 'question',
                    iconColor: '#2563EB',
                    showCancelButton: true,
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#E4E4E7',
                    confirmButtonText: '<i class="bi bi-check-lg mr-2"></i> Ya, Set Default',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'swal2-confirm-btn-blue',
                        cancelButton: 'swal2-cancel-btn',
                        title: 'swal2-title-custom',
                        htmlContainer: 'swal2-html-custom',
                        popup: 'swal2-popup-custom'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endpush

@include('includes.datatables-flowbite-css')
