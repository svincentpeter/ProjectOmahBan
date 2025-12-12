<div class="flex justify-center items-center gap-2">
    <a href="{{ route('currencies.edit', $data->id) }}" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-2 dark:focus:ring-yellow-900 transition-all shadow-sm hover:shadow-md" title="Ubah">
        <i class="bi bi-pencil-square"></i>
    </a>

    @php
        $isDefault = function_exists('settings') && settings() && settings()->default_currency_id == $data->id;
    @endphp

    @if($isDefault)
        <button class="text-white bg-gray-400 font-medium rounded-lg text-sm px-3 py-2 cursor-not-allowed" disabled title="Tidak dapat menghapus mata uang default">
            <i class="bi bi-trash"></i>
        </button>
    @else
        <a href="{{ route('currencies.destroy', $data->id) }}" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900 transition-all shadow-sm hover:shadow-md delete-currency" data-name="{{ $data->currency_name }}" title="Hapus">
            <i class="bi bi-trash"></i>
        </a>
    @endif
</div>
