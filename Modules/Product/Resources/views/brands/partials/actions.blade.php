<div class="flex items-center justify-center gap-2">
    <button type="button"
            class="btn-edit p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
            data-id="{{ $id }}"
            data-name="{{ $name }}"
            data-modal-target="modal-edit"
            data-modal-toggle="modal-edit"
            title="Edit">
        <i class="bi bi-pencil"></i>
    </button>
    
    <button type="button"
            class="btn-delete p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            data-id="{{ $id }}"
            data-name="{{ $name }}"
            title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
    
    <form id="delete-form-{{ $id }}" 
          action="{{ route('brands.destroy', $id) }}" 
          method="POST" 
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
