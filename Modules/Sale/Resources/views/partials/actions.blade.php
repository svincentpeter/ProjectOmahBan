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
            {{-- Cetak Struk POS --}}
            <a target="_blank" href="{{ route('sales.pos.pdf', $data->id) }}" 
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                <i class="bi bi-file-earmark-pdf mr-3 text-lg text-green-600"></i>
                Cetak Struk POS
            </a>

            @can('access_sale_payments')
                @if (Route::has('sale-payments.index'))
                    <a href="{{ route('sale-payments.index', $data->id) }}" 
                       class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors">
                        <i class="bi bi-cash-coin mr-3 text-lg text-yellow-600"></i>
                        Lihat Pembayaran
                    </a>
                @endif
            @endcan

            @if ((int) $data->due_amount > 0 && Route::has('sale-payments.create'))
                <a href="{{ route('sale-payments.create', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                    <i class="bi bi-plus-circle-dotted mr-3 text-lg text-green-600"></i>
                    Tambah Pembayaran
                </a>
            @endif

            @can('show_sales')
                <a href="{{ route('sales.show', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    <i class="bi bi-eye mr-3 text-lg text-blue-600"></i>
                    Detail Penjualan
                </a>
            @endcan

            @can('edit_sales')
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                <a href="{{ route('sales.edit', $data->id) }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    <i class="bi bi-pencil-square mr-3 text-lg text-blue-600"></i>
                    <span>Edit Penjualan
                        @if($data->payment_status === 'Paid' || $data->status === 'Completed')
                            <span class="text-xs text-gray-400 ml-1">(Lunas)</span>
                        @endif
                    </span>
                </a>
            @endcan
        </div>
    </div>
</div>
