@extends('layouts.app-flowbite')

@section('title', 'Detail Pembelian')

@section('content')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pembelian Stok', 'url' => route('purchases.index')],
            ['text' => 'Detail', 'url' => '#'],
        ],
    ])

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('purchases.index') }}"
            class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 transition-colors">
            <i class="cil-arrow-left mr-2"></i> Kembali
        </a>

        @can('edit_purchases')
            @if ($purchase->status == 'Pending')
                <a href="{{ route('purchases.edit', $purchase) }}"
                    class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-yellow-900 transition-colors">
                    <i class="cil-pencil mr-2 text-white"></i> Edit
                </a>
            @endif
        @endcan

        <a href="{{ route('purchases.pdf', $purchase->id) }}" target="_blank"
            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors">
            <i class="cil-print mr-2"></i> Print/PDF
        </a>

        @can('delete_purchases')
            <button type="button" id="delete-purchase" data-id="{{ $purchase->id }}" data-reference="{{ $purchase->reference }}"
                class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800 transition-colors">
                <i class="cil-trash mr-2"></i> Hapus
            </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left: Purchase Info & Products --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Header/Info Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-lg">
                    <h5 class="text-lg font-bold text-gray-800 dark:text-white mb-0">
                        <i class="cil-storage mr-2 text-purple-600"></i> {{ $purchase->reference }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $purchase->date->format('d F Y') }}
                            </div>
                        </div>
                        
                        {{-- Reference --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Reference</label>
                            <div class="text-sm font-bold text-purple-600">
                                {{ $purchase->reference }}
                            </div>
                        </div>

                        {{-- Supplier --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Supplier</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $purchase->supplier_name }}
                                @php $supplier = $purchase->supplier ?? null; @endphp
                                @if ($supplier && !empty($supplier->phone_number ?? $supplier->supplier_phone))
                                    <br>
                                    <span class="text-xs text-gray-500">
                                        <i class="cil-phone mr-1"></i> {{ $supplier->phone_number ?? $supplier->supplier_phone }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                            <div>
                                @if ($purchase->status == 'Completed')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        <i class="cil-check-circle mr-1"></i> Completed
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                        <i class="cil-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Metode Pembayaran</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <i class="cil-wallet mr-1 text-gray-400"></i> {{ $purchase->payment_method }}
                                @if ($purchase->payment_method == 'Transfer' && $purchase->bank_name)
                                    <br>
                                    <span class="text-xs text-gray-500">Bank: {{ $purchase->bank_name }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Payment Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status Pembayaran</label>
                            <div>
                                @if ($purchase->payment_status == 'Lunas')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        <i class="cil-check mr-1"></i> Lunas
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                        <i class="cil-warning mr-1"></i> Belum Lunas
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Note --}}
                        @if ($purchase->note)
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Catatan</label>
                                <div class="p-3 text-sm text-gray-700 bg-gray-50 rounded-lg dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    <i class="cil-notes mr-1"></i> {{ $purchase->note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Products Table --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-lg">
                    <h6 class="mb-0 font-bold text-gray-800 dark:text-white">
                        <i class="cil-basket mr-2 text-purple-600"></i> Daftar Produk
                    </h6>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Nama Produk</th>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3 text-center">Qty</th>
                                <th class="px-6 py-3 text-right">Harga Satuan</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($purchase->purchaseDetails as $detail)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $detail->product_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <code class="text-purple-600 bg-purple-100 dark:bg-purple-900 dark:text-purple-300 px-1 py-0.5 rounded">{{ $detail->product_code }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{ rupiah($detail->unit_price) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        {{ rupiah($detail->sub_total) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right">TOTAL PEMBELIAN:</td>
                                <td class="px-6 py-4 text-right text-purple-600 dark:text-purple-400 text-base">
                                    {{ rupiah($purchase->total_amount) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Summary & Additional Info --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Payment Summary --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-green-50 dark:bg-green-900/20 rounded-t-lg">
                    <h6 class="mb-0 font-bold text-green-800 dark:text-green-300">
                        <i class="cil-calculator mr-2"></i> Ringkasan Pembayaran
                    </h6>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Total Amount --}}
                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total:</span>
                        <span class="text-lg font-bold text-purple-600 dark:text-purple-400">
                            {{ rupiah($purchase->total_amount) }}
                        </span>
                    </div>

                    {{-- Paid Amount --}}
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Terbayar:</span>
                        <span class="text-base font-bold text-green-600 dark:text-green-400">
                            {{ rupiah($purchase->paid_amount) }}
                        </span>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Due Amount --}}
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Sisa Hutang:</span>
                        <span class="text-lg font-bold {{ $purchase->due_amount > 0 ? 'text-red-500' : 'text-gray-400' }}">
                            {{ rupiah($purchase->due_amount) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-lg">
                    <h6 class="mb-0 font-bold text-gray-800 dark:text-white">
                        <i class="cil-info mr-2 text-purple-600"></i> Informasi Tambahan
                    </h6>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            <i class="cil-user mr-1"></i> Diinput Oleh
                        </label>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $purchase->user->name ?? 'System' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            <i class="cil-calendar mr-1"></i> Dibuat Pada
                        </label>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $purchase->created_at->format('d F Y, H:i') }} WIB
                            <br>
                            <span class="text-xs text-gray-500">{{ $purchase->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if ($purchase->updated_at != $purchase->created_at)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            <i class="cil-pencil mr-1"></i> Terakhir Diupdate
                        </label>
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ $purchase->updated_at->format('d F Y, H:i') }} WIB
                            <br>
                            <span class="text-xs text-gray-500">{{ $purchase->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            <i class="cil-layers mr-1"></i> Total Item
                        </label>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $purchase->purchaseDetails->count() }} Item
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Delete confirmation with SweetAlert
            $('#delete-purchase').click(function() {
                const id = $(this).data('id');
                const reference = $(this).data('reference');
                const url = '{{ route('purchases.destroy', ':id') }}'.replace(':id', id);

                Swal.fire({
                    title: 'Hapus Pembelian?',
                    text: `Pembelian "${reference}" akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e02424', // Red-600
                    cancelButtonColor: '#6b7280', // Gray-500
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>', {
                            'method': 'POST',
                            'action': url
                        });
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
