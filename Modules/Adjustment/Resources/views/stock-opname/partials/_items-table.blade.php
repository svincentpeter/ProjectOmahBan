{{--
    Items Table Partial (Tailwind Version)
    Variables: $items (Collection of StockOpnameItem)
--}}

@if($items->isEmpty())
    <div class="text-center py-12 text-zinc-500">
        <i class="bi bi-inbox text-4xl"></i>
        <p class="mt-2">Tidak ada item untuk ditampilkan.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-100">
            <thead class="bg-zinc-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-black uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-black uppercase tracking-wider">Kode Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-black uppercase tracking-wider">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-black uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-black uppercase tracking-wider">Stok Sistem</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-black uppercase tracking-wider">Hasil Hitung</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-black uppercase tracking-wider">Selisih</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-black uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-zinc-100">
                @foreach($items as $index => $item)
                    <tr class="hover:bg-zinc-50">
                        <td class="px-4 py-3 text-sm text-black">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm font-mono font-medium text-black">{{ $item->product->product_code }}</td>
                        <td class="px-4 py-3 text-sm text-black">{{ $item->product->product_name }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                {{ $item->product->category->category_name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-black">{{ number_format($item->system_qty) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium">
                            @if($item->actual_qty !== null)
                                <span class="text-blue-600">{{ number_format($item->actual_qty) }}</span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold">
                            @if($item->variance_qty > 0)
                                <span class="text-blue-600">+{{ $item->variance_qty }}</span>
                            @elseif($item->variance_qty < 0)
                                <span class="text-red-600">{{ $item->variance_qty }}</span>
                            @elseif($item->actual_qty !== null)
                                <span class="text-emerald-600">0</span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->variance_type === 'match')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    <i class="bi bi-check-circle me-1"></i> Cocok
                                </span>
                            @elseif($item->variance_type === 'surplus')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                    <i class="bi bi-arrow-up me-1"></i> Surplus
                                </span>
                            @elseif($item->variance_type === 'shortage')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                    <i class="bi bi-arrow-down me-1"></i> Shortage
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-500">
                                    <i class="bi bi-hourglass me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
