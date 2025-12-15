@extends('layouts.app-flowbite')

@section('title', 'Detail Penjualan')

@section('content')
    <div class="px-4 pt-4 mb-4">
        {{-- Breadcrumb --}}
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <a href="{{ route('sales.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Penjualan</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detail #{{ $sale->reference ?? $sale->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        @php
            // Mapping badge jenis item
            $jenisMap = [
                'new' => ['Baru', 'bg-green-100 text-green-800 border-green-400'],
                'second' => ['Bekas', 'bg-yellow-100 text-yellow-800 border-yellow-400'],
                'manual' => ['Manual', 'bg-gray-100 text-gray-800 border-gray-400'],
            ];

            $details = $sale->saleDetails ?? ($sale->details ?? collect());
            $adjustedDetails = $details instanceof \Illuminate\Support\Collection ? $details->where('is_price_adjusted', 1) : collect();
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="bi bi-receipt text-blue-600"></i> Detail Penjualan
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded border border-blue-400">{{ $sale->reference ?? '#' . $sale->id }}</span>
                </h4>
                <div class="flex gap-2">
                    <a href="{{ route('sales.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center transition-colors">
                        <i class="bi bi-arrow-left mr-2"></i> Kembali
                    </a>
                    @php
                        // Build WhatsApp message
                        $waMessage = "Halo, berikut invoice penjualan:\n\n";
                        $waMessage .= "*" . ($sale->reference ?? '#' . $sale->id) . "*\n";
                        $waMessage .= "Tanggal: " . \Carbon\Carbon::parse($sale->date)->format('d/m/Y') . "\n";
                        $waMessage .= "Total: " . format_currency($sale->total_amount) . "\n";
                        if ($sale->due_amount > 0) {
                            $waMessage .= "Kurang Bayar: " . format_currency($sale->due_amount) . "\n";
                        }
                        $waMessage .= "\nTerima kasih atas kepercayaan Anda!";
                        $waLink = "https://wa.me/?text=" . urlencode($waMessage);
                    @endphp
                    <a href="{{ $waLink }}" target="_blank" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center shadow hover:shadow-lg transition-all">
                        <i class="bi bi-whatsapp mr-2"></i> WhatsApp
                    </a>
                    <a href="{{ route('sales.pdf', $sale->id) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center shadow hover:shadow-lg transition-all">
                        <i class="bi bi-printer mr-2"></i> Cetak
                    </a>
                </div>
            </div>

            <div class="p-6">
                {{-- Info Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="space-y-2">
                        <div class="flex justify-between md:block">
                            <span class="text-sm text-gray-500 font-medium">Tanggal</span>
                            <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</div>
                        </div>
                        <div class="flex justify-between md:block">
                            <span class="text-sm text-gray-500 font-medium">Customer</span>
                            <div class="font-semibold text-gray-900">{{ $sale->customer_name ?: '-' }}</div>
                        </div>
                        <div class="flex justify-between md:block">
                            <span class="text-sm text-gray-500 font-medium">Kasir</span>
                            <div class="font-semibold text-gray-900">{{ optional($sale->user)->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                         <div class="flex justify-between md:block items-center">
                            <span class="text-sm text-gray-500 font-medium block">Status</span>
                            <div class="mt-1">@include('sale::partials.status', ['data' => $sale])</div>
                        </div>
                         <div class="flex justify-between md:block items-center">
                            <span class="text-sm text-gray-500 font-medium block">Pembayaran</span>
                            <div class="mt-1">@include('sale::partials.payment-status', ['data' => $sale])</div>
                        </div>
                        <div class="flex justify-between md:block">
                            <span class="text-sm text-gray-500 font-medium">Metode</span>
                             <div class="font-semibold text-gray-900">
                                {{ $sale->payment_method ?? '-' }}
                                @if ($sale->bank_name)
                                    <span class="text-gray-500 font-normal">({{ $sale->bank_name }})</span>
                                @endif
                             </div>
                        </div>
                    </div>

                    <div class="md:text-right">
                        <h3 class="text-3xl font-bold text-green-600">{{ format_currency($sale->total_amount) }}</h3>
                        <div class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Grand Total</div>

                        @if ($sale->has_price_adjustment)
                            @php
                                $totalDiscount = $details instanceof \Illuminate\Support\Collection
                                    ? (int) $details->where('is_price_adjusted', 1)->sum('price_adjustment_amount')
                                    : 0;
                            @endphp
                            <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <i class="bi bi-tag-fill mr-1"></i> Ada Diskon: {{ format_currency($totalDiscount) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Badges Summary --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200 flex items-center">
                        <i class="bi bi-person mr-1"></i> Kasir: {{ optional($sale->user)->name ?? '—' }}
                    </span>

                    @if ((int) ($sale->has_manual_input ?? 0) === 1)
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded border border-yellow-200 flex items-center">
                            <i class="bi bi-pencil-square mr-1"></i> {{ (int) ($sale->manual_input_count ?? 0) }} item manual
                        </span>
                    @endif

                    @if ((int) ($sale->has_price_adjustment ?? 0) === 1)
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-200 flex items-center">
                            <i class="bi bi-tag-fill mr-1"></i> Ada edit harga
                        </span>
                    @endif
                </div>

                {{-- Adjusted Items Table --}}
                @if (($adjustedDetails->count() ?? 0) > 0)
                    <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 font-medium text-gray-700 text-sm">
                            Item dengan Edit Harga
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3">Produk</th>
                                        <th class="px-4 py-3 text-center">Qty</th>
                                        <th class="px-4 py-3 text-right">Harga Asli</th>
                                        <th class="px-4 py-3 text-right">Harga Baru</th>
                                        <th class="px-4 py-3 text-right">Potongan</th>
                                        <th class="px-4 py-3">Alasan</th>
                                        <th class="px-4 py-3">Diubah Oleh</th>
                                        <th class="px-4 py-3 text-center">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($adjustedDetails as $d)
                                        @php
                                            $orig = (int) ($d->original_price ?? $d->price + max(0, (int) ($d->price_adjustment_amount ?? 0)));
                                            $new = (int) ($d->price ?? 0);
                                            $disc = (int) ($d->price_adjustment_amount ?? max(0, $orig - $new));
                                        @endphp
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $d->product_name }}</td>
                                            <td class="px-4 py-3 text-center">{{ (int) $d->quantity }}</td>
                                            <td class="px-4 py-3 text-right">{{ format_currency($orig) }}</td>
                                            <td class="px-4 py-3 text-right">{{ format_currency($new) }}</td>
                                            <td class="px-4 py-3 text-right">
                                                @if ($disc > 0)
                                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">{{ format_currency($disc) }}</span>
                                                @elseif($disc < 0)
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">+{{ format_currency(abs($disc)) }}</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $d->price_adjustment_note ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ optional($d->adjuster)->name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-center">{{ optional(\Carbon\Carbon::parse($d->adjusted_at ?? null))->format('d/m/Y H:i') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Payments Table --}}
                 @if (($sale->salePayments->count() ?? 0) > 0)
                    <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 font-medium text-gray-700 text-sm">
                            Riwayat Pembayaran
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Metode</th>
                                        <th class="px-4 py-3">Bank</th>
                                        <th class="px-4 py-3 text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->salePayments()->orderByDesc('date')->orderByDesc('id')->get() as $p)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3">{{ $p->payment_method ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $p->bank_name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-right font-medium text-gray-900">{{ format_currency((int) $p->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 border-t">
                                     <tr>
                                         <td colspan="3" class="px-4 py-3 text-right font-semibold">Total Dibayar:</td>
                                         <td class="px-4 py-3 text-right font-bold text-gray-900">{{ format_currency((int) $sale->paid_amount) }}</td>
                                     </tr>
                                     @if((int)$sale->due_amount > 0)
                                     <tr>
                                         <td colspan="3" class="px-4 py-3 text-right font-semibold text-red-600">Kurang Bayar:</td>
                                         <td class="px-4 py-3 text-right font-bold text-red-600">{{ format_currency(max((int) $sale->due_amount, 0)) }}</td>
                                     </tr>
                                     @endif
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Main Items Table --}}
                <h5 class="mb-4 text-base font-bold text-gray-800 flex items-center gap-2">
                     <i class="bi bi-list-ul"></i> Detail Item
                </h5>
                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-200 mb-6">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">#</th>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3 text-right">HPP</th>
                                <th class="px-4 py-3 text-right">Harga Jual</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Diskon Item</th>
                                <th class="px-4 py-3 text-right">Pajak Item</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3 text-center">Info Diskon</th>
                            </tr>
                        </thead>
                       <tbody>
                            @php
                                $i = 1; $totalQty = 0; $totalJual = 0;
                            @endphp

                            @forelse($details as $detail)
                                @php
                                    $name = $detail->product_name;
                                    $code = $detail->product_code;
                                    $qty = (int) $detail->quantity;
                                    $unitPrice = (int) $detail->price;
                                    $subTotal = (int) $detail->sub_total;
                                    $diskon = (int) ($detail->product_discount_amount ?? 0);
                                    $pajak = (int) ($detail->product_tax_amount ?? 0);
                                    
                                     // HPP
                                    $hppUnit = (int) ($detail->hpp ?? 0);
                                    if (($detail->source_type ?? null) === 'manual' && ($detail->manual_kind ?? null) === 'goods') {
                                        $hppUnit = (int) ($detail->manual_hpp ?? 0);
                                    }

                                    // Badge jenis
                                    $jenis = $detail->source_type ?? 'new';
                                    [$jenisText, $jenisBadgeClass] = $jenisMap[$jenis] ?? ['Unknown', 'bg-gray-100 text-gray-800 border-gray-400'];
                                    
                                    if ($jenis === 'manual') {
                                        $jenisText = $detail->manual_kind === 'service' ? 'Jasa' : 'Barang';
                                    }

                                    $totalQty += $qty;
                                    $totalJual += $subTotal;
                                @endphp
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">{{ $i++ }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $name }}</td>
                                    <td class="px-4 py-3">{{ $code ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded border {{ $jenisBadgeClass }}">
                                            {{ $jenisText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ format_currency($hppUnit) }}</td>
                                    <td class="px-4 py-3 text-right">
                                         @if ($detail->is_price_adjusted ?? false)
                                            <div class="flex flex-col items-end">
                                                <span class="text-xs text-gray-400 line-through">{{ format_currency((int) ($detail->original_price ?? 0)) }}</span>
                                                <span class="font-bold text-red-600">{{ format_currency($unitPrice) }}</span>
                                            </div>
                                        @else
                                            {{ format_currency($unitPrice) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $qty }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($diskon > 0)
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">{{ format_currency($diskon) }}</span>
                                        @else
                                            <span class="text-gray-400">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ format_currency($pajak) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">{{ format_currency($subTotal) }}</td>
                                    
                                    {{-- Info Diskon --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($detail->is_price_adjusted ?? false)
                                            @php
                                                $adjAmt = (int) ($detail->price_adjustment_amount ?? 0);
                                                $orig = (int) ($detail->original_price ?? $unitPrice + max(0, $adjAmt));
                                                $pct = $orig > 0 ? round(($adjAmt / $orig) * 100, 1) : 0;
                                            @endphp
                                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded p-2 text-xs text-left shadow-sm">
                                                <div class="mb-1 flex items-center justify-between">
                                                    <span><i class="bi bi-tag-fill mr-1"></i> -{{ format_currency($adjAmt) }}</span>
                                                    <span class="text-gray-500 text-[10px]">{{ number_format($pct, 1) }}%</span>
                                                </div>
                                                @if (!empty($detail->price_adjustment_note))
                                                     <div class="border-t border-yellow-200 pt-1 mt-1 text-[10px] text-gray-600">
                                                        <em>{{ $detail->price_adjustment_note }}</em>
                                                    </div>
                                                @endif
                                                 @if (!empty($detail->adjusted_at) || !empty($detail->adjuster))
                                                    <div class="border-t border-yellow-200 pt-1 mt-1 text-[10px] text-gray-500">
                                                        {{ optional($detail->adjuster)->name ?? '-' }}<br>
                                                        {{ optional(\Carbon\Carbon::parse($detail->adjusted_at))->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                        <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                                        Tidak ada detail item
                                    </td>
                                </tr>
                            @endforelse
                       </tbody>
                       @if (($details->count() ?? 0) > 0)
                            <tfoot class="bg-gray-100 font-semibold text-gray-900">
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-center">{{ $totalQty }}</td>
                                    <td colspan="2"></td>
                                    <td class="px-4 py-3 text-right">{{ format_currency($totalJual) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Grand Summary Box --}}
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-1/3 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <tr class="bg-green-50">
                                    <td class="px-4 py-3 font-semibold text-gray-900">Grand Total</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">{{ format_currency((int) $sale->total_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-gray-600">Total HPP</td>
                                    <td class="px-4 py-2 text-right text-gray-600">{{ format_currency((int) ($sale->total_hpp ?? 0)) }}</td>
                                </tr>
                                @php $grandProfit = (int) ($sale->total_profit ?? ($totalJual - ($totalHpp ?? 0))); @endphp
                                <tr class="{{ $grandProfit >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                                    <td class="px-4 py-2 font-semibold {{ $grandProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">Total Laba</td>
                                    <td class="px-4 py-2 text-right font-bold {{ $grandProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ format_currency($grandProfit) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-gray-600">Dibayar</td>
                                    <td class="px-4 py-2 text-right font-medium">{{ format_currency((int) $sale->paid_amount) }}</td>
                                </tr>
                                <tr class="{{ (int) $sale->due_amount > 0 ? 'bg-yellow-50' : '' }}">
                                    <td class="px-4 py-2 font-semibold text-gray-700">Kurang Bayar</td>
                                    <td class="px-4 py-2 text-right font-bold text-red-600">{{ format_currency(max((int) $sale->due_amount, 0)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Note --}}
                @if (!empty($sale->note))
                    <div class="mt-6 p-4 bg-blue-50 text-blue-800 rounded-lg border border-blue-100 flex gap-3">
                        <i class="bi bi-chat-left-text text-xl"></i>
                        <div>
                            <h6 class="font-bold text-sm mb-1">Catatan:</h6>
                            <p class="text-sm whitespace-pre-wrap">{{ $sale->note }}</p>
                        </div>
                    </div>
                @endif

            </div> {{-- P-6 --}}
        </div>
    </div>
@endsection
