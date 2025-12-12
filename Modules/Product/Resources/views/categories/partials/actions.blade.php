<div class="flex items-center justify-center gap-2">
    <button type="button"
            class="btn-edit-category p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
            data-id="{{ $data->id }}"
            data-code="{{ $data->category_code }}"
            data-name="{{ $data->category_name }}"
            title="Edit">
        <i class="bi bi-pencil"></i>
    </button>
    
    <button type="button"
            class="btn-delete-category p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            data-id="{{ $data->id }}"
            data-name="{{ $data->category_name }}"
            title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
    
    <form id="delete-form-{{ $data->id }}" 
          action="{{ route('product-categories.destroy', $data->id) }}" 
          method="POST" 
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
