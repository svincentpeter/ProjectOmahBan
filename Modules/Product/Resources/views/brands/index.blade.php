@extends('layouts.app-flowbite')

@section('title', 'Merek Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Merek Produk', 'url' => route('brands.index'), 'icon' => 'bi bi-bookmark-star']
    ]])
@endsection



@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Filter Component --}}
    @include('layouts.filter-card', [
        'action' => route('brands.index'),
        'title' => 'Filter Merek',
        'icon' => 'bi bi-bookmark-star',
        'quickFilters' => [
            [
                'label' => 'Semua Merek',
                'url' => route('brands.index'),
                'param' => 'filter',
                'value' => 'all',
                'icon' => 'bi bi-grid'
            ]
        ],
        'filters' => []
    ])

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-bookmark-star text-blue-600"></i>
                        Merek Produk
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola merek ban dan velg</p>
                </div>
                
                <button type="button" 
                        data-modal-target="modal-create" 
                        data-modal-toggle="modal-create"
                        class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Merek
                </button>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="px-6 pt-4">
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-start gap-3">
                <i class="bi bi-info-circle text-blue-600 text-lg mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium">
                        <strong>Informasi:</strong> Merek digunakan untuk mengidentifikasi produsen produk. 
                        Pastikan nama merek yang diinput sudah sesuai dengan merek asli produk.
                    </p>
                </div>
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="p-6 pt-4 overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-gray-400" id="brands-table">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider" style="width: 80px;">No.</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">Nama Merek</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($brands as $brand)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                                {{ $loop->iteration }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md">
                                    <i class="bi bi-bookmark-star"></i>
                                </div>
                                <span class="font-bold text-black">{{ $brand->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                        class="btn-edit p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                        data-id="{{ $brand->id }}"
                                        data-name="{{ $brand->name }}"
                                        data-modal-target="modal-edit"
                                        data-modal-toggle="modal-edit"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <button type="button"
                                        class="btn-delete p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-id="{{ $brand->id }}"
                                        data-name="{{ $brand->name }}"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                                
                                <form id="delete-form-{{ $brand->id }}" 
                                      action="{{ route('brands.destroy', $brand->id) }}" 
                                      method="POST" 
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-400">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-inbox text-3xl text-slate-300"></i>
                                </div>
                                <p class="font-semibold text-zinc-600 mb-1">Belum ada merek</p>
                                <small class="text-zinc-500">Klik tombol "Tambah Merek" untuk mulai menambah data</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Include Modals from Partials --}}
    @include('product::brands.partials._modal-create')
    @include('product::brands.partials._modal-edit')
@endsection

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
<script>
$(document).ready(function() {
    // Initialize DataTables
    // Initialize DataTables
    const table = $('#brands-table').DataTable({
        columnDefs: [{ orderable: false, targets: [0, 2] }]
    });

    // Handle Edit button click - populate modal
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $('#form-edit').attr('action', '{{ url("brands") }}/' + id);
        $('#edit-error').addClass('hidden').text('');
    });

    // Handle Create form submit with AJAX
    $('#form-create').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin me-1"></i> Menyimpan...');
        $('#create-error').addClass('hidden');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                // Close modal
                const modal = FlowbiteInstances.getInstance('Modal', 'modal-create');
                if (modal) modal.hide();
                
                // Reset form
                form[0].reset();
                
                // Show success toast
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Merek baru berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                // Reload page to refresh data
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#create-error').removeClass('hidden').text(errors.name[0]);
                } else {
                    $('#create-error').removeClass('hidden').text('Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle Edit form submit with AJAX
    $('#form-edit').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin me-1"></i> Memperbarui...');
        $('#edit-error').addClass('hidden');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                // Close modal
                const modal = FlowbiteInstances.getInstance('Modal', 'modal-edit');
                if (modal) modal.hide();
                
                // Show success toast
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Merek berhasil diperbarui',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                // Reload page to refresh data
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#edit-error').removeClass('hidden').text(errors.name[0]);
                } else {
                    $('#edit-error').removeClass('hidden').text('Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Delete confirmation with SweetAlert2
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Merek?',
            html: `Merek <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-zinc-500">Pastikan tidak ada produk yang menggunakan merek ini!</small>`,
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $('#delete-form-' + id).submit();
            }
        });
    });

    // Reset create form when modal is closed
    document.getElementById('modal-create')?.addEventListener('hidden.bs.modal', function() {
        $('#form-create')[0].reset();
        $('#create-error').addClass('hidden');
    });
});
</script>
@endpush
