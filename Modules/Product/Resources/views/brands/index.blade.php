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

    {{-- Info Alert --}}
    <div class="mb-4">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-xl flex items-start gap-3 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300">
            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div>
                <p class="text-sm font-medium">
                    <strong>Informasi:</strong> Merek digunakan untuk mengidentifikasi produsen produk. 
                    Pastikan nama merek yang diinput sudah sesuai dengan merek asli produk.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <x-flowbite-table 
        title="Merek Produk" 
        description="Kelola merek ban dan velg"
        icon="bi-bookmark-star"
        :items="$brands"
        searchPlaceholder="Cari merek..."
    >
        {{-- Action Buttons --}}
        <x-slot name="actions">
            <button type="button" 
                    data-modal-target="modal-create" 
                    data-modal-toggle="modal-create"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Merek
            </button>
        </x-slot>

        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama Merek</th>
                <th scope="col" class="px-6 py-4">Jumlah Produk</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($brands as $index => $brand)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $brands->firstItem() + $index }}
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $brand->name }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    {{ $brand->products_count ?? 0 }} produk
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <button type="button" 
                            class="btn-edit text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}"
                            title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </button>
                    
                    {{-- Delete Button --}}
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-bookmark-x text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada merek</p>
                    <p class="text-sm">Klik tombol "Tambah Merek" untuk membuat merek baru.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </x-flowbite-table>

    {{-- Include Modals --}}
    @include('product::brands.partials._modal-create')
    @include('product::brands.partials._modal-edit')
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    // Helper to toggle modal
    function toggleModal(modalId, show) {
        const modal = $('#' + modalId);
        if (show) {
            modal.removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');
        } else {
            modal.addClass('hidden').removeClass('flex');
            $('body').removeClass('overflow-hidden');
        }
    }

    // Handle Edit button click
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $('#form-edit').attr('action', '{{ url("brands") }}/' + id);
        $('#edit-error').addClass('hidden').text('');
        
        toggleModal('modal-edit', true);
    });

    // Close modals
    $(document).on('click', '[data-modal-hide="modal-edit"]', function() {
        toggleModal('modal-edit', false);
    });
    $(document).on('click', '[data-modal-hide="modal-create"]', function() {
        toggleModal('modal-create', false);
    });
    $('[data-modal-target="modal-create"]').on('click', function() {
        toggleModal('modal-create', true);
    });

    // Handle Create form submit
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
                toggleModal('modal-create', false);
                form[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Merek baru berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#create-error').removeClass('hidden').text(errors.name[0]);
                } else {
                    $('#create-error').removeClass('hidden').text('Terjadi kesalahan.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle Edit form submit
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
                toggleModal('modal-edit', false);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Merek berhasil diperbarui',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.name) {
                    $('#edit-error').removeClass('hidden').text(errors.name[0]);
                } else {
                    $('#edit-error').removeClass('hidden').text('Terjadi kesalahan.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Delete confirmation
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
                
                $.ajax({
                    url: "{{ url('brands') }}/" + id,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Merek berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
