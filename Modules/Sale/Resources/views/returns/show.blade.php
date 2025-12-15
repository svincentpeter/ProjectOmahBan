@extends('layouts.app-flowbite')

@section('title', 'Detail Retur - ' . $saleReturn->reference)

@section('content')
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penjualan', 'url' => route('sales.index')],
            ['text' => 'Retur', 'url' => route('sale-returns.index')],
            ['text' => $saleReturn->reference, 'url' => '#', 'icon' => 'bi bi-eye'],
        ],
    ])
@endsection

<div class="max-w-5xl mx-auto">
    {{-- Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-arrow-return-left mr-2 text-orange-600"></i>
                    {{ $saleReturn->reference }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Dibuat: {{ $saleReturn->created_at->format('d M Y H:i') }} oleh {{ $saleReturn->creator->name ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex px-3 py-1.5 text-sm font-medium rounded-full {{ $saleReturn->status_badge_class }}">
                    {{ $saleReturn->status }}
                </span>
                @if($saleReturn->status === 'Pending')
                    @can('edit_sale_returns')
                    <a href="{{ route('sale-returns.edit', $saleReturn) }}"
                        class="px-4 py-2 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 font-medium text-sm">
                        <i class="bi bi-pencil mr-1"></i> Edit
                    </a>
                    @endcan
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Original Sale --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Transaksi Asal</p>
                    @if($saleReturn->sale)
                    <a href="{{ route('sales.show', $saleReturn->sale) }}" class="font-bold text-blue-600 hover:underline">
                        {{ $saleReturn->sale->reference }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1">{{ $saleReturn->sale->date->format('d M Y') }}</p>
                    @else
                    <p class="font-bold text-gray-700">-</p>
                    @endif
                </div>

                {{-- Customer --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                    <p class="font-bold text-gray-800 dark:text-white">{{ $saleReturn->customer_display_name }}</p>
                </div>

                {{-- Refund Method --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Metode Refund</p>
                    <p class="font-bold text-gray-800 dark:text-white">
                        <i class="bi bi-{{ $saleReturn->refund_method === 'Cash' ? 'cash' : ($saleReturn->refund_method === 'Credit' ? 'credit-card' : 'wallet2') }} mr-1"></i>
                        {{ $saleReturn->refund_method }}
                    </p>
                </div>

                {{-- Refund Amount --}}
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nilai Refund</p>
                    <p class="font-bold text-xl text-orange-600">{{ format_currency($saleReturn->refund_amount) }}</p>
                </div>
            </div>

            @if($saleReturn->reason)
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Alasan Retur</p>
                <p class="text-gray-800 dark:text-white">{{ $saleReturn->reason }}</p>
            </div>
            @endif

            @if($saleReturn->note)
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan</p>
                <p class="text-gray-800 dark:text-white whitespace-pre-line">{{ $saleReturn->note }}</p>
            </div>
            @endif

            @if($saleReturn->approver)
            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Diproses Oleh</p>
                <p class="text-gray-800 dark:text-white">
                    <i class="bi bi-check-circle text-green-600 mr-1"></i>
                    {{ $saleReturn->approver->name }} pada {{ $saleReturn->approved_at->format('d M Y H:i') }}
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700">
            <h4 class="text-md font-bold text-gray-800 dark:text-white flex items-center">
                <i class="bi bi-box-seam mr-2 text-blue-600"></i>
                Item yang Diretur ({{ $saleReturn->details->count() }} item)
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Restock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($saleReturn->details as $detail)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $detail->product_name }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->product_code ?? '-' }}</p>
                            <span class="inline-block mt-1 text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded">
                                {{ $detail->source_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-medium">{{ $detail->quantity }}</td>
                        <td class="px-6 py-4 text-right">{{ format_currency($detail->unit_price) }}</td>
                        <td class="px-6 py-4 text-right font-bold">{{ format_currency($detail->sub_total) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $detail->condition_badge_class }}">
                                {{ ucfirst($detail->condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($detail->restock)
                                <i class="bi bi-check-circle text-green-600 text-lg"></i>
                            @else
                                <i class="bi bi-x-circle text-gray-400 text-lg"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-600">Total:</td>
                        <td class="px-6 py-4 text-right font-bold text-lg text-orange-600">{{ format_currency($saleReturn->total_amount) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('sale-returns.index') }}"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 font-medium text-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali
        </a>

        @if($saleReturn->status === 'Pending')
        <div class="flex gap-3">
            @can('approve_sale_returns')
            <button type="button" onclick="approveReturn({{ $saleReturn->id }})"
                class="px-4 py-2 text-white bg-green-600 rounded-xl hover:bg-green-700 font-medium text-sm">
                <i class="bi bi-check-lg mr-1"></i> Setujui
            </button>
            <button type="button" onclick="rejectReturn({{ $saleReturn->id }})"
                class="px-4 py-2 text-white bg-red-600 rounded-xl hover:bg-red-700 font-medium text-sm">
                <i class="bi bi-x-lg mr-1"></i> Tolak
            </button>
            @endcan
        </div>
        @endif
    </div>
</div>

{{-- Include modals for approve/reject --}}
@include('sale::returns.partials.modals')
@endsection

@push('page_scripts')
<script>
    let currentReturnId = null;

    function approveReturn(id) {
        currentReturnId = id;
        Swal.fire({
            title: 'Setujui Retur?',
            text: 'Stok akan dipulihkan untuk item yang ditandai restock.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/sale-returns/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }

    function rejectReturn(id) {
        Swal.fire({
            title: 'Tolak Retur?',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan (Opsional)',
            inputPlaceholder: 'Masukkan alasan...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/sale-returns/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ rejection_reason: result.value || '' }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
