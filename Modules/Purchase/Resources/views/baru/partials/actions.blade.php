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
            @can('access_purchase_payments')
                <a href="{{ route('purchase-payments.index', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors">
                    <i class="bi bi-cash-coin mr-3 text-lg text-yellow-600"></i>
                    Show Payments
                </a>
            @endcan
            @can('access_purchase_payments')
                @if($data->due_amount > 0)
                    <a href="{{ route('purchase-payments.create', $data->id) }}" 
                       class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                        <i class="bi bi-plus-circle-dotted mr-3 text-lg text-green-600"></i>
                        Add Payment
                    </a>
                @endif
            @endcan
            @can('edit_purchases')
                <a href="{{ route('purchases.edit', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    <i class="bi bi-pencil mr-3 text-lg text-blue-600"></i>
                    Edit
                </a>
            @endcan
            @can('show_purchases')
                <a href="{{ route('purchases.show', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors">
                    <i class="bi bi-eye mr-3 text-lg text-purple-600"></i>
                    Details
                </a>
            @endcan
            @can('delete_purchases')
                <button type="button" 
                        onclick="confirmDeletePurchase({{ $data->id }})"
                        class="w-full text-left flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="bi bi-trash mr-3 text-lg text-red-600"></i>
                    Delete
                </button>
                <form id="destroy{{ $data->id }}" class="hidden" action="{{ route('purchases.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            @endcan
        </div>
    </div>
</div>
