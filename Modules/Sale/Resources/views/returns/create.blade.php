@extends('layouts.app-flowbite')

@section('title', 'Buat Retur Penjualan')

@section('content')
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penjualan', 'url' => route('sales.index')],
            ['text' => 'Retur', 'url' => route('sale-returns.index')],
            ['text' => 'Buat Retur', 'url' => '#', 'icon' => 'bi bi-plus-lg'],
        ],
    ])
@endsection

<div class="max-w-5xl mx-auto">
    <form action="{{ route('sale-returns.store') }}" method="POST" id="returnForm">
        @csrf

        {{-- Header Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-arrow-return-left mr-2 text-orange-600"></i>
                    Buat Retur Penjualan Baru
                </h3>
            </div>

            <div class="p-6 space-y-6">
                {{-- Sale Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="bi bi-receipt mr-1"></i> Pilih Transaksi Penjualan <span class="text-red-500">*</span>
                    </label>
                    <select name="sale_id" id="saleSelect" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach($recentSales as $s)
                            <option value="{{ $s->id }}" {{ ($sale && $sale->id == $s->id) ? 'selected' : '' }}>
                                {{ $s->reference }} - {{ $s->date->format('d M Y') }} - {{ $s->customer_display_name }} ({{ format_currency($s->total_amount) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sale Info Display --}}
                <div id="saleInfo" class="hidden p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Reference:</span>
                            <p class="font-bold text-gray-800 dark:text-white" id="saleRef">-</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                            <p class="font-bold text-gray-800 dark:text-white" id="saleDate">-</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                            <p class="font-bold text-gray-800 dark:text-white" id="saleCustomer">-</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Total:</span>
                            <p class="font-bold text-gray-800 dark:text-white" id="saleTotal">-</p>
                        </div>
                    </div>
                </div>

                {{-- Return Details --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Retur</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Refund</label>
                        <select name="refund_method"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="Cash">Cash</option>
                            <option value="Credit">Credit (Potong Tagihan)</option>
                            <option value="Store Credit">Store Credit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Retur</label>
                        <input type="text" name="reason" placeholder="Contoh: Barang rusak"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="note" rows="2" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
            </div>
        </div>

        {{-- Items Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700">
                <h4 class="text-md font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-box-seam mr-2 text-blue-600"></i>
                    Item yang Diretur
                </h4>
            </div>

            <div class="p-6">
                <div id="itemsContainer" class="space-y-4">
                    <div class="text-center py-8 text-gray-500" id="noItemsMsg">
                        <i class="bi bi-inbox text-4xl mb-2"></i>
                        <p>Pilih transaksi penjualan untuk melihat item</p>
                    </div>
                </div>

                {{-- Summary --}}
                <div id="summarySection" class="hidden mt-6 pt-6 border-t border-slate-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total Item Dikembalikan:</span>
                                <span class="font-bold text-gray-800 dark:text-white" id="totalItems">0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-800 dark:text-white">Total Refund:</span>
                                <span class="text-orange-600" id="totalRefund">Rp 0</span>
                            </div>
                            <input type="hidden" name="refund_amount" id="refundAmountInput" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('sale-returns.index') }}"
                class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-medium">
                <i class="bi bi-x-lg mr-1"></i> Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-xl hover:shadow-lg font-medium disabled:opacity-50"
                id="submitBtn" disabled>
                <i class="bi bi-check-lg mr-1"></i> Simpan Retur
            </button>
        </div>
    </form>
</div>
@endsection

@push('page_scripts')
<script>
    const saleSelect = document.getElementById('saleSelect');
    const itemsContainer = document.getElementById('itemsContainer');
    const noItemsMsg = document.getElementById('noItemsMsg');
    const saleInfo = document.getElementById('saleInfo');
    const summarySection = document.getElementById('summarySection');
    const submitBtn = document.getElementById('submitBtn');

    function formatRupiah(n) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Number(n || 0));
    }

    function updateSummary() {
        let totalItems = 0;
        let totalRefund = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const checkbox = row.querySelector('.item-checkbox');
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');

            if (checkbox && checkbox.checked) {
                const qty = parseInt(qtyInput.value) || 0;
                const price = parseInt(priceInput.value) || 0;
                totalItems += qty;
                totalRefund += qty * price;
            }
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalRefund').textContent = formatRupiah(totalRefund);
        document.getElementById('refundAmountInput').value = totalRefund;

        submitBtn.disabled = totalItems === 0;
    }

    saleSelect.addEventListener('change', function() {
        const saleId = this.value;
        if (!saleId) {
            itemsContainer.innerHTML = noItemsMsg.outerHTML;
            saleInfo.classList.add('hidden');
            summarySection.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        // Fetch sale items
        fetch(`/sale-returns/ajax/sale/${saleId}/items`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Gagal memuat data');
                    return;
                }

                // Show sale info
                document.getElementById('saleRef').textContent = data.sale.reference;
                document.getElementById('saleDate').textContent = data.sale.date;
                document.getElementById('saleCustomer').textContent = data.sale.customer_name;
                document.getElementById('saleTotal').textContent = formatRupiah(data.sale.total_amount);
                saleInfo.classList.remove('hidden');

                // Build items
                if (data.items.length === 0) {
                    itemsContainer.innerHTML = '<div class="text-center py-4 text-gray-500">Tidak ada item</div>';
                    summarySection.classList.add('hidden');
                    return;
                }

                let html = '';
                data.items.forEach((item, idx) => {
                    const sourceLabel = {new: 'Produk Baru', second: 'Second', service: 'Jasa', manual: 'Manual'}[item.source_type] || item.source_type;
                    const canRestock = ['new', 'second'].includes(item.source_type);

                    html += `
                        <div class="item-row p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <input type="checkbox" class="item-checkbox w-5 h-5 text-orange-600 rounded focus:ring-orange-500" checked onchange="updateSummary()">
                                </div>
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <div class="md:col-span-2">
                                        <p class="font-medium text-gray-800 dark:text-white">${item.product_name}</p>
                                        <p class="text-xs text-gray-500">${item.product_code || '-'}</p>
                                        <span class="inline-block mt-1 text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded">${sourceLabel}</span>
                                        <input type="hidden" name="items[${idx}][sale_detail_id]" value="${item.id}">
                                        <input type="hidden" name="items[${idx}][product_name]" value="${item.product_name}">
                                        <input type="hidden" name="items[${idx}][product_code]" value="${item.product_code || ''}">
                                        <input type="hidden" name="items[${idx}][source_type]" value="${item.source_type}">
                                        <input type="hidden" name="items[${idx}][productable_type]" value="${item.productable_type || ''}">
                                        <input type="hidden" name="items[${idx}][productable_id]" value="${item.productable_id || ''}">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Qty Dibeli</label>
                                        <p class="font-medium text-gray-700 dark:text-gray-300">${item.quantity_sold}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Qty Retur</label>
                                        <input type="number" name="items[${idx}][quantity]" value="${item.quantity_sold}" min="1" max="${item.quantity_sold}"
                                            class="item-qty w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            onchange="updateSummary()">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Harga Satuan</label>
                                        <input type="number" name="items[${idx}][unit_price]" value="${item.unit_price}" readonly
                                            class="item-price w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-100 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Kondisi</label>
                                        <select name="items[${idx}][condition]" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="good">Bagus</option>
                                            <option value="damaged">Rusak</option>
                                            <option value="defective">Cacat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            ${canRestock ? `
                            <div class="mt-3 ml-9">
                                <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <input type="checkbox" name="items[${idx}][restock]" value="1" checked class="mr-2 w-4 h-4 text-green-600 rounded focus:ring-green-500">
                                    Kembalikan ke stok
                                </label>
                            </div>
                            ` : ''}
                        </div>
                    `;
                });

                itemsContainer.innerHTML = html;
                summarySection.classList.remove('hidden');
                updateSummary();
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat data item');
            });
    });

    // Trigger initial load if sale is pre-selected
    @if($sale)
    saleSelect.dispatchEvent(new Event('change'));
    @endif
</script>
@endpush
