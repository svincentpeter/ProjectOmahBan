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
    })" x-init="init()">

    {{-- Produk & badge --}}
    <td>
        <div class="font-weight-600">{{ $it->name }}</div>
        <div class="small text-muted">
            {{ $code }}
            @if ($src === 'second') <span class="badge badge-secondary ml-1">Second</span> @endif
            @if ($src === 'manual') <span class="badge badge-dark ml-1">Manual</span> @endif
        </div>
    </td>

    {{-- Harga (AutoNumeric pegang tampilan, Alpine pegang angka mentah) --}}
    <td>
        <input type="text"
               class="form-control form-control-sm text-right js-money"
               data-field="price"
               value="{{ number_format($price,0,',','.') }}"
               @input.debounce.300ms="onPriceInput($event)">
    </td>

    {{-- Qty --}}
    <td class="text-center">
        <input type="number"
               min="1"
               class="form-control form-control-sm text-center"
               x-model.number="qty"
               value="{{ $qty }}"
               @input.debounce.400ms="onQtyInput($event)"
               :readonly="source === 'second'">
    </td>

    {{-- Diskon (per item) --}}
    <td>
        <input type="text"
               class="form-control form-control-sm text-right js-money"
               data-field="discount"
               value="{{ number_format($disc,0,',','.') }}"
               @input.debounce.300ms="onDiscInput($event)">
    </td>

    {{-- Pajak (per item) --}}
    <td>
        <input type="text"
               class="form-control form-control-sm text-right js-money"
               data-field="tax"
               value="{{ number_format($tax,0,',','.') }}"
               @input.debounce.300ms="onTaxInput($event)">
    </td>

    {{-- Subtotal baris: akan juga di-override oleh nilai server saat updateLine --}}
    <td class="text-right">
        <span class="js-line-subtotal" x-text="formatIDR(lineSubtotal)"></span>
    </td>

    {{-- Aksi --}}
    <td class="text-center">
        <button type="button"
                class="btn btn-sm btn-outline-secondary"
                @click="_pushUpdate()"
                title="Sinkronkan">
            <i class="bi bi-arrow-repeat"></i>
        </button>
        <button type="button"
                class="btn btn-sm btn-outline-danger"
                @click="remove()"
                title="Hapus baris">
            <i class="bi bi-x"></i>
        </button>
    </td>
</tr>
