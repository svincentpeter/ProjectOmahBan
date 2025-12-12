@php
    $src   = (string) data_get($it->options,'source_type','manual');
    $code  = (string) data_get($it->options,'code','-');
    $price = (int) $it->price;
    $qty   = (int) $it->qty;
    $disc  = (int) data_get($it->options,'discount',0);
    $tax   = (int) data_get($it->options,'tax',0);
@endphp

<tr x-data="rowEditor({
        rowId: '{{ $it->rowId }}',
        source: '{{ $src }}',
        price: {{ $price }},
        qty: {{ $qty }},
        discount: {{ $disc }},
        tax: {{ $tax }}
    })" x-init="init()" class="bg-white border-b hover:bg-gray-50">

    {{-- Produk & badge --}}
    <td class="px-3 py-2">
        <div class="font-semibold text-gray-800">{{ $it->name }}</div>
        <div class="text-xs text-gray-500 flex items-center gap-1">
            {{ $code }}
            @if ($src === 'second') 
                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded border border-gray-500">Second</span> 
            @endif
            @if ($src === 'manual') 
                <span class="bg-gray-700 text-white text-xs font-medium px-2 py-0.5 rounded">Manual</span> 
            @endif
        </div>
    </td>

    {{-- Harga --}}
    <td class="px-3 py-2">
        <input type="text"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 text-right js-money"
               data-field="price"
               value="{{ number_format($price,0,',','.') }}"
               @input.debounce.300ms="onPriceInput($event)">
    </td>

    {{-- Qty --}}
    <td class="px-3 py-2 text-center">
        <input type="number"
               min="1"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 text-center"
               x-model.number="qty"
               value="{{ $qty }}"
               @input.debounce.400ms="onQtyInput($event)"
               :readonly="source === 'second'"
               :class="{'cursor-not-allowed bg-gray-100': source === 'second'}">
    </td>

    {{-- Diskon --}}
    <td class="px-3 py-2">
        <input type="text"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 text-right js-money"
               data-field="discount"
               value="{{ number_format($disc,0,',','.') }}"
               @input.debounce.300ms="onDiscInput($event)">
    </td>

    {{-- Pajak --}}
    <td class="px-3 py-2">
        <input type="text"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 text-right js-money"
               data-field="tax"
               value="{{ number_format($tax,0,',','.') }}"
               @input.debounce.300ms="onTaxInput($event)">
    </td>

    {{-- Subtotal --}}
    <td class="px-3 py-2 text-right">
        <span class="js-line-subtotal font-semibold text-gray-900" x-text="formatIDR(lineSubtotal)"></span>
    </td>

    {{-- Aksi --}}
    <td class="px-3 py-2 text-center whitespace-nowrap">
        <button type="button"
                class="text-gray-500 hover:text-blue-600 focus:outline-none p-1 rounded hover:bg-gray-100"
                @click="_pushUpdate()"
                title="Sinkronkan">
            <i class="bi bi-arrow-repeat text-lg"></i>
        </button>
        <button type="button"
                class="text-red-500 hover:text-red-700 focus:outline-none p-1 rounded hover:bg-red-50 ml-1"
                @click="remove()"
                title="Hapus baris">
            <i class="bi bi-x-lg"></i>
        </button>
    </td>
</tr>
