<div class="flex items-center justify-center gap-2">
    <a href="{{ route('expense-categories.edit', $data->id) }}" 
       class="font-medium text-blue-600 dark:text-blue-500 hover:underline" 
       data-tooltip-target="tooltip-edit-{{ $data->id }}">
        <i class="bi bi-pencil-square text-lg"></i>
    </a>
    <div id="tooltip-edit-{{ $data->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
        Edit Kategori
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>

    <button type="button" 
            class="font-medium text-red-600 dark:text-red-500 hover:underline btn-delete" 
            data-id="{{ $data->id }}" 
            data-name="{{ $data->category_name }}"
            data-tooltip-target="tooltip-delete-{{ $data->id }}">
        <i class="bi bi-trash text-lg"></i>
    </button>
    <div id="tooltip-delete-{{ $data->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
        Hapus Kategori
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>

    <form id="delete-form-{{ $data->id }}" 
          action="{{ route('expense-categories.destroy', $data->id) }}" 
          method="POST" 
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
