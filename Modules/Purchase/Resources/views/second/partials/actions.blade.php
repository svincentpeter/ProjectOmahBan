{{-- Modern Flowbite Actions Dropdown --}}
<div class="relative">
    <button type="button" 
            class="inline-flex items-center justify-center w-10 h-10 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
            onclick="toggleActionDropdown(event, '{{ $data->id }}')">
        <i class="bi bi-three-dots-vertical text-xl"></i>
    </button>
    
    <div id="dropdown-{{ $data->id }}" 
         style="display: none; position: fixed; z-index: 9999;"
         class="action-dropdown w-56 rounded-xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black ring-opacity-5 border border-gray-100 dark:border-gray-700">
        <div class="py-2">
            {{-- Cetak Nota PDF --}}
            <a href="{{ route('purchases.second.pdf', $data->id) }}" target="_blank"
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                <i class="bi bi-file-earmark-pdf mr-3 text-lg text-green-600"></i>
                Cetak Nota
            </a>

            @can('show_purchases')
                <a href="{{ route('purchases.second.show', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors">
                    <i class="bi bi-eye mr-3 text-lg text-purple-600"></i>
                    Detail
                </a>
            @endcan

            @can('edit_purchases')
                @if ($data->status == 'Pending')
                    <a href="{{ route('purchases.second.edit', $data->id) }}" 
                       class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                        <i class="bi bi-pencil mr-3 text-lg text-blue-600"></i>
                        Edit
                    </a>
                @endif
            @endcan

            @can('delete_purchases')
                <button type="button" 
                        onclick="confirmDeletePurchaseSecond({{ $data->id }})"
                        class="w-full text-left flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="bi bi-trash mr-3 text-lg text-red-600"></i>
                    Hapus
                </button>
                <form id="destroy{{ $data->id }}" class="hidden" action="{{ route('purchases.second.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            @endcan
        </div>
    </div>
</div>
