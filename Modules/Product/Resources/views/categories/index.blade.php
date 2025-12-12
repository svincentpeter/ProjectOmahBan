@extends('layouts.app-flowbite')

@section('title', 'Kategori Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Kategori Produk', 'url' => route('product-categories.index'), 'icon' => 'bi bi-folder']
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
                    <strong>Informasi:</strong> Kategori membantu Anda mengorganisir produk dengan lebih baik. 
                    Setiap produk dapat ditempatkan dalam satu kategori untuk memudahkan pencarian dan manajemen.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table using Flowbite Table Component --}}
    <x-flowbite-table 
        title="Kategori Produk" 
        description="Kelola kategori untuk mengorganisir produk"
        icon="bi-folder"
        :items="$categories"
        searchPlaceholder="Cari kategori..."
    >
        {{-- Action Buttons in Header --}}
        <x-slot name="actions">
            <button type="button" 
                    data-modal-target="modal-create-category" 
                    data-modal-toggle="modal-create-category"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Kategori
            </button>
        </x-slot>

        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Kode</th>
                <th scope="col" class="px-6 py-4">Nama Kategori</th>
                <th scope="col" class="px-6 py-4">Jumlah Produk</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($categories as $index => $category)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $categories->firstItem() + $index }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                    {{ $category->category_code }}
                </span>
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $category->category_name }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    {{ $category->products()->count() }} produk
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <button type="button" 
                            class="btn-edit-category text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            data-id="{{ $category->id }}"
                            data-code="{{ $category->category_code }}"
                            data-name="{{ $category->category_name }}"
                            title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </button>
                    
                    {{-- Delete Button --}}
                    <button type="button" 
                            class="btn-delete-category text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->category_name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    {{-- Hidden Delete Form --}}
                    <form id="delete-form-{{ $category->id }}" action="{{ route('product-categories.destroy', $category->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-folder-x text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada kategori</p>
                    <p class="text-sm">Klik tombol "Tambah Kategori" untuk membuat kategori baru.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </x-flowbite-table>

    {{-- Include Modals from Partials --}}
    @include('product::categories.partials._modal-create')
    @include('product::categories.partials._modal-edit')
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    // Helper to toggle modal visibility
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
    $(document).on('click', '.btn-edit-category', function() {
        const id = $(this).data('id');
        const code = $(this).data('code');
        const name = $(this).data('name');
        
        $('#edit-category-id').val(id);
        $('#edit-category-code').val(code);
        $('#edit-category-name').val(name);
        $('#form-edit-category').attr('action', '{{ url("product-categories") }}/' + id);
        
        $('#edit-category-code-error, #edit-category-name-error').addClass('hidden').text('');
        
        toggleModal('modal-edit-category', true);
    });
    
    // Close modals
    $(document).on('click', '[data-modal-hide="modal-edit-category"]', function() {
        toggleModal('modal-edit-category', false);
    });
    
    $(document).on('click', '[data-modal-hide="modal-create-category"]', function() {
        toggleModal('modal-create-category', false);
    });
    
    $('[data-modal-target="modal-create-category"]').on('click', function(e) {
        toggleModal('modal-create-category', true);
    });

    // Handle Create form submit with AJAX
    $('#form-create-category').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin me-1"></i> Menyimpan...');
        $('#create-category-code-error, #create-category-name-error').addClass('hidden');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                toggleModal('modal-create-category', false);
                form[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori baru berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.category_code) {
                    $('#create-category-code-error').removeClass('hidden').text(errors.category_code[0]);
                }
                if (errors?.category_name) {
                    $('#create-category-name-error').removeClass('hidden').text(errors.category_name[0]);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle Edit form submit with AJAX
    $('#form-edit-category').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin me-1"></i> Memperbarui...');
        $('#edit-category-code-error, #edit-category-name-error').addClass('hidden');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                toggleModal('modal-edit-category', false);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori berhasil diperbarui',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
                
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors?.category_code) {
                    $('#edit-category-code-error').removeClass('hidden').text(errors.category_code[0]);
                }
                if (errors?.category_name) {
                    $('#edit-category-name-error').removeClass('hidden').text(errors.category_name[0]);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Delete confirmation with SweetAlert2
    $(document).on('click', '.btn-delete-category', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-zinc-500">Pastikan tidak ada produk yang menggunakan kategori ini!</small>`,
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
});
</script>
@endpush
