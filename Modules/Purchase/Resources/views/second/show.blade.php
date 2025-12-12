@extends('layouts.app-flowbite')

@section('title', 'Detail Pembelian Stok Bekas')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pembelian Bekas', 'url' => route('purchases.second.index')],
            ['text' => 'Detail Pembelian', 'url' => '#'],
        ],
    ])

    {{-- Action Buttons --}}
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('purchases.second.index') }}"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all text-sm font-medium">
            <i class="bi bi-arrow-left mr-1"></i> Kembali
        </a>
        @can('edit_purchases')
            @if ($purchaseSecond->status == 'Pending')
                <a href="{{ route('purchases.second.edit', $purchaseSecond) }}"
                    class="px-4 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-all text-sm font-medium">
                    <i class="bi bi-pencil mr-1"></i> Edit
                </a>
            @endif
        @endcan
        <a href="{{ route('purchases.second.pdf', $purchaseSecond->id) }}" target="_blank"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm font-medium">
            <i class="bi bi-printer mr-1"></i> Print/PDF
        </a>
        @can('delete_purchases')
            <button type="button" id="delete-purchase" data-id="{{ $purchaseSecond->id }}"
                data-reference="{{ $purchaseSecond->reference }}"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all text-sm font-medium">
                <i class="bi bi-trash mr-1"></i> Hapus
            </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: Purchase Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Purchase Header Card --}}
            <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-purple-600 rounded-t-lg">
                    <h5 class="text-xl font-bold text-white flex items-center">
                        <i class="bi bi-file-earmark-text mr-2"></i>
                        {{ $purchaseSecond->reference }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Tanggal</label>
                            <div class="text-base font-semibold text-gray-900 dark:text-white mt-1">
                                {{ $purchaseSecond->date->format('d F Y') }}
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Reference</label>
                            <div class="text-base font-bold text-purple-600 dark:text-purple-400 mt-1">
                                {{ $purchaseSecond->reference }}
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Customer</label>
                            <div class="text-base font-semibold text-gray-900 dark:text-white mt-1">
                                {{ $purchaseSecond->customer_name }}
                                @if ($purchaseSecond->customer_phone)
                                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400 flex items-center mt-0.5">
                                        <i class="bi bi-telephone mr-1"></i> {{ $purchaseSecond->customer_phone }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Status</label>
                            <div class="mt-1">
                                @include('purchase::second.partials.status', ['data' => $purchaseSecond])
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Metode Pembayaran</label>
                            <div class="text-base font-semibold text-gray-900 dark:text-white mt-1">
                                <i class="bi bi-wallet2 mr-1"></i> {{ $purchaseSecond->payment_method }}
                                @if ($purchaseSecond->payment_method == 'Transfer' && $purchaseSecond->bank_name)
                                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5">
                                        Bank: {{ $purchaseSecond->bank_name }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Status Pembayaran</label>
                            <div class="mt-1">
                                @include('purchase::second.partials.payment-status', ['data' => $purchaseSecond])
                            </div>
                        </div>

                        @if ($purchaseSecond->note)
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Catatan</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    <i class="bi bi-sticky mr-1 text-yellow-500"></i> {{ $purchaseSecond->note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Products Table Card --}}
            <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-box-seam mr-2 text-purple-600"></i>
                        Daftar Produk Bekas
                    </h6>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">#</th>
                                <th scope="col" class="px-6 py-3">Produk</th>
                                <th scope="col" class="px-6 py-3">Kode</th>
                                <th scope="col" class="px-6 py-3">Kondisi</th>
                                <th scope="col" class="px-6 py-3 text-right">Harga Beli</th>
                                <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseSecond->purchaseSecondDetails as $detail)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $detail->product_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                            {{ $detail->product_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $detail->condition_notes ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{ rupiah($detail->unit_price) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-purple-600 dark:text-purple-400">
                                        {{ rupiah($detail->sub_total) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: Payment Summary & Info --}}
        <div class="space-y-6">
            {{-- Payment Summary Card --}}
            <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-green-50 dark:bg-gray-700/50">
                    <h6 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-cash-stack mr-2 text-green-600"></i>
                        Ringkasan Pembayaran
                    </h6>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Total Amount --}}
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pembelian</span>
                        <span class="text-lg font-bold text-purple-600 dark:text-purple-400">
                            {{ rupiah($purchaseSecond->total_amount) }}
                        </span>
                    </div>

                    {{-- Paid Amount --}}
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Terbayar</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                            {{ rupiah($purchaseSecond->paid_amount) }}
                        </span>
                    </div>

                    {{-- Due Amount --}}
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Sisa Hutang</span>
                        <span class="text-lg font-bold {{ $purchaseSecond->due_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }}">
                            {{ rupiah($purchaseSecond->due_amount) }}
                        </span>
                    </div>

                    {{-- Payment Status --}}
                    <div class="pt-2">
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Pembayaran</span>
                        @if ($purchaseSecond->payment_status == 'Lunas')
                            <div class="p-3 bg-green-100 text-green-800 rounded-lg flex items-center justify-center font-bold">
                                <i class="bi bi-check-circle-fill mr-2"></i> Lunas
                            </div>
                        @else
                            <div class="p-3 bg-yellow-100 text-yellow-800 rounded-lg flex items-center justify-center font-bold">
                                <i class="bi bi-exclamation-triangle-fill mr-2"></i> Belum Lunas
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Additional Information Card --}}
            <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-info-circle mr-2 text-blue-600"></i>
                        Informasi Tambahan
                    </h6>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <i class="bi bi-person mr-1"></i> Diinput Oleh
                        </label>
                        <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                            {{ $purchaseSecond->user->name ?? 'System' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <i class="bi bi-calendar-event mr-1"></i> Dibuat Pada
                        </label>
                        <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                            {{ $purchaseSecond->created_at->format('d F Y, H:i') }} WIB
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $purchaseSecond->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if ($purchaseSecond->updated_at != $purchaseSecond->created_at)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                <i class="bi bi-pencil-square mr-1"></i> Terakhir Diupdate
                            </label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                {{ $purchaseSecond->updated_at->format('d F Y, H:i') }} WIB
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ $purchaseSecond->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <i class="bi bi-layers mr-1"></i> Total Item
                        </label>
                        <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                            {{ $purchaseSecond->purchaseSecondDetails->count() }} Item
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Form (Hidden) --}}
    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('#delete-purchase').click(function() {
                const id = $(this).data('id');
                const reference = $(this).data('reference');
                const url = "{{ route('purchases.second.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Hapus Pembelian Bekas?',
                    text: `Apakah Anda yakin ingin menghapus data "${reference}"? Data yang dihapus tidak dapat dikembalikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form');
                        form.action = url;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
