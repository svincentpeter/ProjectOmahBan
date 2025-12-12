@extends('layouts.app-flowbite')

@section('title', 'Daftar Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Produk Bekas', 'url' => route('products_second.index'), 'icon' => 'bi bi-recycle']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Produk --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-purple-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-recycle text-2xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Produk</p>
                    <p class="text-3xl font-bold">{{ $products->total() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Tersedia --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="window.location.href='{{ route('products_second.index', ['status' => 'available']) }}'">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Tersedia</p>
                    <p class="text-3xl font-bold">{{ $products->where('status', 'available')->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Terjual --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg shadow-rose-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="window.location.href='{{ route('products_second.index', ['status' => 'sold']) }}'">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-x-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-rose-100 text-sm font-medium mb-1">Terjual</p>
                    <p class="text-3xl font-bold">{{ $products->where('status', 'sold')->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-recycle text-purple-600"></i>
                        Daftar Produk Bekas
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola produk ban & velg bekas</p>
                </div>
                
                <a href="{{ route('products_second.create') }}" 
                   class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk Bekas
                </a>
            </div>

            {{-- Global Filter Component --}}
            @include('layouts.filter-card', [
                'action' => route('products_second.index'),
                'title' => 'Filter Status',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                    [
                        'label' => 'Semua',
                        'url' => route('products_second.index'),
                        'param' => 'status',
                        'value' => '',
                        'icon' => 'bi bi-grid'
                    ],
                    [
                        'label' => 'Tersedia',
                        'url' => route('products_second.index', ['status' => 'available']),
                        'param' => 'status',
                        'value' => 'available',
                        'icon' => 'bi bi-check-circle'
                    ],
                    [
                        'label' => 'Terjual',
                        'url' => route('products_second.index', ['status' => 'sold']),
                        'param' => 'status',
                        'value' => 'sold',
                        'icon' => 'bi bi-x-circle'
                    ]
                ],
                'filters' => []
            ])
        </div>

        {{-- Table --}}
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-gray-400" id="second-products-table">
                <thead class="text-xs uppercase bg-slate-50 border-b-2 border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-black">Nama Barang</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Merk</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Tahun</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Ukuran</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Ring</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Modal</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Harga Jual</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center">Status</th>
                        <th class="px-4 py-3 font-extrabold text-black text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 align-middle">
                            <span class="font-bold text-black">{{ $product->name }}</span>
                            @if($product->condition_notes)
                            <p class="text-xs text-zinc-500 mt-0.5">{{ Str::limit($product->condition_notes, 30) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            @if($product->brand)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $product->brand->name }}
                            </span>
                            @else
                            <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center align-middle font-medium text-zinc-700">
                            {{ $product->product_year ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center align-middle font-bold text-black">
                            {{ $product->size ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                R{{ $product->ring ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle font-medium text-zinc-600">
                            {{ format_currency($product->purchase_price) }}
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="font-bold text-emerald-600">{{ format_currency($product->selling_price) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            @if ($product->status == 'available')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <i class="bi bi-check-circle me-1"></i> Tersedia
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                <i class="bi bi-x-circle me-1"></i> Terjual
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('products_second.edit', $product->id) }}"
                                   class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <button type="button"
                                        class="btn-delete p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                                
                                <form id="delete-form-{{ $product->id }}" 
                                      action="{{ route('products_second.destroy', $product->id) }}" 
                                      method="POST" 
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-400">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-inbox text-3xl text-slate-300"></i>
                                </div>
                                <p class="font-semibold text-zinc-600 mb-1">Belum ada produk bekas</p>
                                <small class="text-zinc-500">Klik tombol "Tambah Produk Bekas" untuk mulai menambah data</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (Handled by DataTables) --}}
        {{-- @if ($products->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-zinc-500">
                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
            </p>
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif --}}
    </div>
@endsection

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
<script>
$(document).ready(function() {
    // Initialize DataTable (Client-side)
    $('#second-products-table').DataTable({
        processing: true, // Use global processing logic
        responsive: true,
        columnDefs: [{ orderable: false, targets: [8] }] // Disable sorting on Action column
    });

    // Delete confirmation
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Produk Bekas?',
            html: `Produk <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-zinc-500">Data yang dihapus tidak dapat dikembalikan!</small>`,
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
