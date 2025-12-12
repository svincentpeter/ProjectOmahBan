{{--
    Adjustments Partial (Tailwind Version)
    Variables: $items (Collection of StockOpnameItem with adjustment)
--}}

@if($items->isEmpty())
    <div class="text-center py-12 text-zinc-500">
        <i class="bi bi-file-earmark-diff text-4xl"></i>
        <p class="mt-2">Tidak ada adjustment yang dibuat otomatis.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-100">
            <thead class="bg-zinc-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-700 uppercase tracking-wider">Product</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-zinc-700 uppercase tracking-wider">Adjustment</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-zinc-700 uppercase tracking-wider">Adjusted By</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-zinc-700 uppercase tracking-wider">Adjustment ID</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-zinc-100">
                @foreach($items as $item)
                    <tr class="hover:bg-zinc-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-zinc-900">{{ $item->product->product_name }}</div>
                            <div class="text-xs text-zinc-500 font-mono">{{ $item->product->product_code }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold">
                            @if($item->variance_qty > 0)
                                <span class="text-blue-600">+{{ $item->variance_qty }} (IN)</span>
                            @elseif($item->variance_qty < 0)
                                <span class="text-red-600">{{ $item->variance_qty }} (OUT)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-600">
                            System (Auto)
                        </td>
                        <td class="px-4 py-3 text-center text-sm">
                            @if($item->adjustment)
                                <a href="{{ route('adjustments.show', $item->adjustment->id) }}" class="inline-flex items-center px-2 py-1 bg-zinc-100 text-zinc-700 rounded hover:bg-zinc-200 transition-colors text-xs font-mono">
                                    {{ $item->adjustment->reference }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
