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

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-folder text-blue-600"></i>
                        Kategori Produk
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola kategori untuk mengorganisir produk</p>
                </div>
                
                <button type="button" 
                        data-modal-target="modal-create-category" 
                        data-modal-toggle="modal-create-category"
                        class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Kategori
                </button>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="px-6 pt-4">
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-start gap-3">
                <i class="bi bi-info-circle text-blue-600 text-lg mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium">
                        <strong>Informasi:</strong> Kategori membantu Anda mengorganisir produk dengan lebih baik. 
                        Setiap produk dapat ditempatkan dalam satu kategori untuk memudahkan pencarian dan manajemen.
                    </p>
                </div>
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="p-6 pt-4 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left text-slate-500 dark:text-gray-400', 'id' => 'categories-table']) !!}
        </div>
    </div>

    {{-- Include Modals from Partials --}}
    @include('product::categories.partials._modal-create')
    @include('product::categories.partials._modal-edit')
@endsection

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    // Handle Edit button click - populate modal and show it
    // Helper to toggle modal visibility
    function toggleModal(modalId, show) {
        const modal = $('#' + modalId);
        if (show) {
            modal.removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden'); // Prevent background scroll
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
    
    // Close edit modal
    $(document).on('click', '[data-modal-hide="modal-edit-category"]', function() {
        toggleModal('modal-edit-category', false);
    });
    
    // Close create modal
    $(document).on('click', '[data-modal-hide="modal-create-category"]', function() {
        toggleModal('modal-create-category', false);
    });
    
    // Show create modal - manually handle to ensure consistency
    $('[data-modal-target="modal-create-category"]').on('click', function(e) {
        // e.preventDefault(); // Optional, depending on if flowbite listener is also attached
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
                // Close modal using helper
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
                
                // Reload page untuk refresh DataTables
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
                // Close modal using helper
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
                
                // Reload page untuk refresh DataTables
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
