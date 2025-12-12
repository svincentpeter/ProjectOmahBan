<div class="flex justify-center items-center gap-2">
    <a href="{{ route('units.edit', $data->id) }}" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-2 dark:focus:ring-yellow-900 transition-all shadow-sm hover:shadow-md">
        <i class="bi bi-pencil-square"></i>
    </a>

    <button type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900 transition-all shadow-sm hover:shadow-md btn-delete" data-id="{{ $data->id }}" data-name="{{ $data->name }}">
        <i class="bi bi-trash"></i>
    </button>
    <form id="delete-form-{{ $data->id }}" action="{{ route('units.destroy', $data->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
