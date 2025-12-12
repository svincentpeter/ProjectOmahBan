{{--
    DataTable Dropdown Actions Component
    
    Usage:
    @include('partials.datatable-actions', [
        'id' => $data->id,
        'showRoute' => route('products.show', $data->id),    // optional
        'editRoute' => route('products.edit', $data->id),    // optional
        'deleteRoute' => route('products.destroy', $data->id), // optional
        'showPermission' => 'show_products',  // optional, defaults to showing
        'editPermission' => 'edit_products',  // optional
        'deletePermission' => 'delete_products', // optional
        'deleteMessage' => 'Apakah Anda yakin ingin menghapus data ini?', // optional
        'itemName' => $data->name, // optional, for delete confirmation
    ])
--}}

@php
    $uniqueId = 'dt-action-' . ($id ?? uniqid());
@endphp

{{-- 3-Dots Trigger Button --}}
<button 
    id="{{ $uniqueId }}-btn" 
    data-dropdown-toggle="{{ $uniqueId }}-menu" 
    class="inline-flex items-center justify-center w-8 h-8 text-gray-500 bg-white rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 focus:outline-none dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600 transition-colors" 
    type="button"
    title="Aksi"
>
    <i class="bi bi-three-dots-vertical text-base"></i>
</button>

{{-- Dropdown Menu --}}
<div 
    id="{{ $uniqueId }}-menu" 
    class="z-50 hidden bg-white divide-y divide-gray-100 rounded-xl shadow-xl w-48 dark:bg-gray-700 dark:divide-gray-600 border border-gray-200 dark:border-gray-600 overflow-hidden"
>
    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="{{ $uniqueId }}-btn">
        
        {{-- View/Detail Action --}}
        @if(isset($showRoute))
            @if(!isset($showPermission) || Gate::allows($showPermission))
            <li>
                <a href="{{ $showRoute }}" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                    <i class="bi bi-eye text-blue-600 dark:text-blue-400"></i>
                    <span>Detail</span>
                </a>
            </li>
            @endif
        @endif
        
        {{-- Edit Action --}}
        @if(isset($editRoute))
            @if(!isset($editPermission) || Gate::allows($editPermission))
            <li>
                <a href="{{ $editRoute }}" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                    <i class="bi bi-pencil-square text-amber-600 dark:text-amber-400"></i>
                    <span>Edit</span>
                </a>
            </li>
            @endif
        @endif
        
        {{-- Custom Actions Slot --}}
        @if(isset($customActions))
            {!! $customActions !!}
        @endif
        
    </ul>
    
    {{-- Delete Action (Separated with divider) --}}
    @if(isset($deleteRoute))
        @if(!isset($deletePermission) || Gate::allows($deletePermission))
        <div class="py-1">
            <a 
                href="javascript:void(0)" 
                class="flex items-center gap-2 px-4 py-2.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-600 transition-colors btn-dt-delete"
                data-id="{{ $id }}"
                data-name="{{ $itemName ?? 'data ini' }}"
                data-form="{{ $uniqueId }}-form"
            >
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </a>
            <form id="{{ $uniqueId }}-form" class="hidden" action="{{ $deleteRoute }}" method="POST">
                @csrf
                @method('DELETE')
            </form>
        </div>
        @endif
    @endif
</div>

{{-- Delete Confirmation Script (Only loaded once) --}}
@pushOnce('page_scripts')
<script>
$(document).on('click', '.btn-dt-delete', function(e) {
    e.preventDefault();
    const formId = $(this).data('form');
    const name = $(this).data('name');
    
    Swal.fire({
        title: 'Hapus Data?',
        html: `Data <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-gray-500">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        iconColor: '#ef4444',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $('#' + formId).submit();
        }
    });
});
</script>
@endPushOnce
