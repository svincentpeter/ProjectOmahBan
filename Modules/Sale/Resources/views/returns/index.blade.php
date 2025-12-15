@extends('layouts.app-flowbite')

@section('title', 'Retur Penjualan')

@section('content')
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penjualan', 'url' => route('sales.index')],
            ['text' => 'Retur Penjualan', 'url' => '#', 'icon' => 'bi bi-arrow-return-left'],
        ],
    ])
@endsection

{{-- Stats Cards --}}
<div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
    {{-- Pending --}}
    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-yellow-400 to-orange-500"></div>
        <div class="p-3 mr-4 text-white bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl shadow-lg shadow-yellow-500/30">
            <i class="bi bi-hourglass-split text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Menunggu Persetujuan</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['pending'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Approved --}}
    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-400 to-indigo-600"></div>
        <div class="p-3 mr-4 text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30">
            <i class="bi bi-check2-circle text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Disetujui</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['approved'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Completed --}}
    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-green-400 to-emerald-600"></div>
        <div class="p-3 mr-4 text-white bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/30">
            <i class="bi bi-check-all text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Selesai</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['completed'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Total Refund This Month --}}
    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-red-400 to-rose-600"></div>
        <div class="p-3 mr-4 text-white bg-gradient-to-br from-red-500 to-rose-600 rounded-xl shadow-lg shadow-red-500/30">
            <i class="bi bi-cash-coin text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Refund Bulan Ini</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ format_currency($stats['total_refund_this_month'] ?? 0) }}</p>
        </div>
    </div>
</div>

{{-- Main Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="px-6 pt-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <i class="bi bi-arrow-return-left mr-2 text-orange-600"></i>
                Daftar Retur Penjualan
            </h3>
            <p class="text-xs text-gray-500 mt-1">Kelola retur dan refund penjualan</p>
        </div>
        @can('create_sale_returns')
        <a href="{{ route('sale-returns.create') }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-orange-500/30 hover:scale-[1.02] transition-transform duration-200">
            <i class="bi bi-plus-lg mr-2"></i> Buat Retur
        </a>
        @endcan
    </div>

    {{-- Filter Card --}}
    <div class="px-6 pt-6">
        @include('layouts.filter-card', [
            'action' => route('sale-returns.index'),
            'title' => 'Filter Data Retur',
            'icon' => 'bi bi-funnel',
            'quickFilters' => [
                [
                    'label' => 'Hari Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'today']),
                    'param' => 'preset',
                    'value' => 'today',
                    'icon' => 'bi bi-clock',
                ],
                [
                    'label' => 'Minggu Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'this_week']),
                    'param' => 'preset',
                    'value' => 'this_week',
                    'icon' => 'bi bi-calendar-week',
                ],
                [
                    'label' => 'Bulan Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'this_month']),
                    'param' => 'preset',
                    'value' => 'this_month',
                    'icon' => 'bi bi-calendar-month',
                ],
            ],
            'filters' => [
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'type' => 'select',
                    'options' => [
                        'Pending' => 'Menunggu',
                        'Approved' => 'Disetujui',
                        'Rejected' => 'Ditolak',
                        'Completed' => 'Selesai',
                    ],
                    'placeholder' => 'Semua Status',
                ],
                ['name' => 'dari', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => request('dari')],
                ['name' => 'sampai', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => request('sampai')],
            ],
        ])
    </div>

    <div class="px-6 pb-6">
        {{ $dataTable->table() }}
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="relative p-4 w-full max-w-md">
        <div class="relative bg-white rounded-2xl shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="bi bi-check-circle text-green-600 mr-2"></i>
                    Setujui Retur
                </h3>
                <button type="button" onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Apakah Anda yakin ingin menyetujui retur ini? Stok akan dipulihkan untuk item yang ditandai untuk restock.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="button" onclick="confirmApprove()" class="px-4 py-2 text-white bg-green-600 rounded-xl hover:bg-green-700">
                        <i class="bi bi-check-lg mr-1"></i> Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="relative p-4 w-full max-w-md">
        <div class="relative bg-white rounded-2xl shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="bi bi-x-circle text-red-600 mr-2"></i>
                    Tolak Retur
                </h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penolakan (Opsional)</label>
                    <textarea id="rejectionReason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="button" onclick="confirmReject()" class="px-4 py-2 text-white bg-red-600 rounded-xl hover:bg-red-700">
                        <i class="bi bi-x-lg mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_styles')
@include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{{ $dataTable->scripts() }}

<script>
    let currentReturnId = null;

    function approveReturn(id) {
        currentReturnId = id;
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        currentReturnId = null;
        document.getElementById('approveModal').classList.add('hidden');
    }

    function confirmApprove() {
        if (!currentReturnId) return;

        fetch(`/sale-returns/${currentReturnId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(res => res.json())
        .then(data => {
            closeApproveModal();
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.LaravelDataTables['sale-returns-table'].ajax.reload();
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(err => {
            closeApproveModal();
            Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        });
    }

    function rejectReturn(id) {
        currentReturnId = id;
        document.getElementById('rejectionReason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        currentReturnId = null;
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function confirmReject() {
        if (!currentReturnId) return;

        const reason = document.getElementById('rejectionReason').value;

        fetch(`/sale-returns/${currentReturnId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ rejection_reason: reason }),
        })
        .then(res => res.json())
        .then(data => {
            closeRejectModal();
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.LaravelDataTables['sale-returns-table'].ajax.reload();
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(err => {
            closeRejectModal();
            Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        });
    }
</script>
@endpush
