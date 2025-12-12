<div class="flex items-center justify-center gap-2">
    @can('edit_suppliers')
    <a href="{{ route('suppliers.edit', $data->id) }}" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    @endcan
    
    @can('show_suppliers')
    <a href="{{ route('suppliers.show', $data->id) }}" class="p-2 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100 rounded-lg transition-colors" title="Lihat Detail">
        <i class="bi bi-eye"></i>
    </a>
    @endcan
    
    @can('delete_suppliers')
    <button type="button" 
            class="delete-supplier p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-colors"
            data-id="{{ $data->id }}"
            data-name="{{ $data->supplier_name }}"
            data-has-purchases="{{ $data->purchases_count > 0 ? 'true' : 'false' }}"
            title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
    @endcan
</div>
