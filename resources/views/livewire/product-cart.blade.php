<div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Product</th>
                    <th scope="col" class="px-6 py-3">Price</th>
                    <th scope="col" class="px-6 py-3">Quantity</th>
                    <th scope="col" class="px-6 py-3">Subtotal</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cart_items as $cart_item)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $cart_item->name }} <br>
                            <span class="text-xs text-gray-500">{{ $cart_item->options->code }}</span>
                        </td>
                         <td class="px-6 py-4">
                            {{ format_currency($cart_item->price) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <button wire:click="decreaseQuantity('{{ $cart_item->rowId }}')" class="inline-flex items-center p-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                    <span class="sr-only">Decrease</span>
                                    <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                                </button>
                                <div>
                                    <input type="number" wire:model.blur="quantity.{{ $cart_item->id }}" wire:change="updateQuantity('{{ $cart_item->rowId }}', '{{ $cart_item->id }}')" class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1 text-center" required>
                                </div>
                                <button wire:click="increaseQuantity('{{ $cart_item->rowId }}')" class="inline-flex items-center p-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                    <span class="sr-only">Increase</span>
                                    <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ format_currency($cart_item->price * $cart_item->qty) }}
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="removeItem('{{ $cart_item->rowId }}')" class="font-medium text-red-600 dark:text-red-500 hover:underline">Remove</button>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Cart is empty. Please search and add products.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
             <div class="mb-3">
                <label for="tax_percentage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Tax (%)</label>
                <input type="number" wire:model.live.debounce.500ms="global_tax" id="tax_percentage" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="0" max="100">
            </div>
            <div class="mb-3">
                <label for="discount_percentage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Discount (%)</label>
                <input type="number" wire:model.live.debounce.500ms="global_discount" id="discount_percentage" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="0" max="100">
            </div>
             <div class="mb-3">
                <label for="shipping_amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Shipping ({{ settings()->currency_symbol }})</label>
                <input type="number" wire:model.live.debounce.500ms="shipping" id="shipping_amount" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="0">
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg border flex flex-col justify-center">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium text-gray-900">{{ format_currency(Cart::instance($cart_instance)->subtotal()) }}</span>
            </div>
             <div class="flex justify-between mb-2">
                <span class="text-gray-600">Tax</span>
                <span class="font-medium text-gray-900">{{ format_currency(Cart::instance($cart_instance)->tax()) }}</span>
            </div>
             <div class="flex justify-between mb-2">
                <span class="text-gray-600">Discount</span>
                <span class="font-medium text-gray-900">{{ format_currency(Cart::instance($cart_instance)->discount()) }}</span>
            </div>
             <div class="flex justify-between mb-2">
                <span class="text-gray-600">Shipping</span>
                <span class="font-medium text-gray-900">{{ format_currency($shipping) }}</span>
            </div>
            <div class="border-t pt-2 mt-2 flex justify-between">
                <span class="text-xl font-bold text-gray-900">Total</span>
                <span class="text-xl font-bold text-blue-600">{{ format_currency(Cart::instance($cart_instance)->total() + $shipping) }}</span>
            </div>
            
            {{-- Hidden inputs to submit form --}}
            <input type="hidden" name="tax_percentage" value="{{ $global_tax }}">
            <input type="hidden" name="discount_percentage" value="{{ $global_discount }}">
            <input type="hidden" name="shipping_amount" value="{{ $shipping }}">
             <input type="hidden" name="tax_amount" value="{{ Cart::instance($cart_instance)->tax() }}">
             <input type="hidden" name="discount_amount" value="{{ Cart::instance($cart_instance)->discount() }}">
             <input type="hidden" name="total_amount" value="{{ Cart::instance($cart_instance)->total() + $shipping }}">
        </div>
    </div>
</div>
