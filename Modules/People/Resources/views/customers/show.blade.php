@extends('layouts.app-flowbite')

@section('title', 'Detail Customer')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Detail Customer',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Customer', 'url' => route('customers.index')],
            ['text' => 'Detail', 'url' => '#']
        ]
    ])

    <div class="p-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT COLUMN: Customer Info & Sales History --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Customer Information Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
                    <div class="p-6 border-b border-zinc-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                                <i class="bi bi-person-badge-fill text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-zinc-800">Detail Customer</h5>
                                <p class="text-sm text-zinc-500">Informasi lengkap customer</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                             @can('edit_customers')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="px-3 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors flex items-center gap-2">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                            @endcan
                            <a href="{{ route('customers.index') }}" class="px-3 py-2 text-xs font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition-colors flex items-center gap-2">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama Customer --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-indigo-500 shadow-sm">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Nama Customer</label>
                                </div>
                                <div class="text-lg font-bold text-zinc-800 ps-10">
                                    {{ $customer->customer_name }}
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-rose-500 shadow-sm">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Email</label>
                                </div>
                                <div class="text-sm text-zinc-800 ps-10">
                                    <a href="mailto:{{ $customer->customer_email }}" class="hover:text-indigo-600 hover:underline">
                                        {{ $customer->customer_email }}
                                    </a>
                                </div>
                            </div>

                            {{-- No. Telepon --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-emerald-500 shadow-sm">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">No. Telepon</label>
                                </div>
                                <div class="text-sm text-zinc-800 ps-10">
                                    <a href="tel:{{ $customer->customer_phone }}" class="hover:text-indigo-600 hover:underline">
                                        {{ $customer->customer_phone }}
                                    </a>
                                </div>
                            </div>

                            {{-- Kota --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-amber-500 shadow-sm">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Kota</label>
                                </div>
                                <div class="ps-10">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ $customer->city }}
                                    </span>
                                </div>
                            </div>

                            {{-- Negara --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-cyan-500 shadow-sm">
                                        <i class="bi bi-globe-asia-australia"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Negara</label>
                                </div>
                                <div class="text-sm text-zinc-800 ps-10">
                                    {{ $customer->country }}
                                </div>
                            </div>

                            {{-- Tanggal Terdaftar --}}
                            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-violet-500 shadow-sm">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Terdaftar</label>
                                </div>
                                <div class="text-sm text-zinc-800 ps-10">
                                    {{ $customer->created_at->format('d M Y, H:i') }}
                                    <span class="text-xs text-zinc-400 block mt-1">({{ $customer->created_at->diffForHumans() }})</span>
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="md:col-span-2 bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-1.5 bg-white rounded-lg text-slate-500 shadow-sm">
                                        <i class="bi bi-house-door-fill"></i>
                                    </div>
                                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Alamat Lengkap</label>
                                </div>
                                <div class="text-sm text-zinc-700 ps-10 leading-relaxed">
                                    {{ $customer->address }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales History Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 overflow-hidden">
                    <div class="p-6 border-b border-zinc-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                                <i class="bi bi-cart-check-fill text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-zinc-800">Riwayat Penjualan</h5>
                                <p class="text-sm text-zinc-500">10 transaksi terakhir dari customer ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-0">
                        @if ($customer->sales->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-zinc-500 uppercase bg-zinc-50 border-b border-zinc-100">
                                        <tr>
                                            <th class="px-6 py-4 font-semibold">#</th>
                                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                                            <th class="px-6 py-4 font-semibold">Reference</th>
                                            <th class="px-6 py-4 font-semibold">Total</th>
                                            <th class="px-6 py-4 font-semibold">Status</th>
                                            <th class="px-6 py-4 font-semibold">Pembayaran</th>
                                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100">
                                        @foreach ($customer->sales as $index => $sale)
                                            <tr class="bg-white hover:bg-zinc-50 transition-colors">
                                                <td class="px-6 py-4 text-center text-zinc-500 w-12">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 text-zinc-700">{{ $sale->date->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="font-mono font-medium text-indigo-600">{{ $sale->reference }}</span>
                                                </td>
                                                <td class="px-6 py-4 font-medium text-zinc-800">{{ format_currency($sale->total_amount) }}</td>
                                                <td class="px-6 py-4">
                                                    @if ($sale->status == 'Completed')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Completed
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800">
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($sale->payment_status == 'Lunas')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                            Lunas
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                            Belum Lunas
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @can('show_sales')
                                                        <a href="{{ route('sales.show', $sale->id) }}"
                                                            class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center text-zinc-400">
                                <div class="inline-flex items-center justify-center p-4 bg-zinc-50 rounded-full mb-4">
                                    <i class="bi bi-cart-x text-3xl"></i>
                                </div>
                                <h6 class="text-sm font-semibold text-zinc-600 mb-1">Belum ada riwayat penjualan</h6>
                                <p class="text-xs">Transaksi akan muncul setelah melakukan penjualan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Statistics & Actions --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Statistics Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
                    <div class="p-5 border-b border-zinc-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600">
                                <i class="bi bi-graph-up-arrow text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-zinc-800">Statistik</h5>
                                <p class="text-sm text-zinc-500">Ringkasan transaksi</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-5 space-y-4">
                         {{-- Total Penjualan --}}
                         <div class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-xl border border-indigo-100">
                            <div class="flex justify-between items-start">
                                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                    <i class="bi bi-cart-fill text-lg"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-indigo-600 font-medium mb-1">Total Penjualan</p>
                                    <h4 class="text-xl font-bold text-zinc-800">{{ $stats['total_sales'] }}</h4>
                                    <span class="text-[10px] text-zinc-400">transaksi</span>
                                </div>
                            </div>
                        </div>

                        {{-- Total Nilai --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-white p-4 rounded-xl border border-emerald-100">
                            <div class="flex justify-between items-start">
                                <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                                    <i class="bi bi-coin text-lg"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-emerald-600 font-medium mb-1">Total Nilai</p>
                                    <h4 class="text-lg font-bold text-zinc-800">{{ format_currency($stats['total_amount']) }}</h4>
                                </div>
                            </div>
                        </div>

                        {{-- Terbayar & Sisa --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-sky-50 p-3 rounded-xl border border-sky-100">
                                <div class="mb-2">
                                    <i class="bi bi-check-circle-fill text-sky-500"></i>
                                </div>
                                <p class="text-[10px] text-sky-600 font-bold uppercase">Terbayar</p>
                                <p class="text-sm font-bold text-zinc-800">{{ format_currency($stats['total_paid']) }}</p>
                            </div>
                            
                            <div class="bg-amber-50 p-3 rounded-xl border border-amber-100">
                                <div class="mb-2">
                                    <i class="bi bi-clock-history text-amber-500"></i>
                                </div>
                                <p class="text-[10px] text-amber-600 font-bold uppercase">Sisa Piutang</p>
                                <p class="text-sm font-bold text-zinc-800">{{ format_currency($stats['total_due']) }}</p>
                            </div>
                        </div>

                        {{-- Last Sale --}}
                        @if ($stats['last_sale_date'])
                            <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-100 flex justify-between items-center">
                                <span class="text-xs text-zinc-500 font-medium flex items-center gap-2">
                                    <i class="bi bi-calendar3"></i> Penjualan Terakhir
                                </span>
                                <span class="text-xs font-bold text-zinc-800">
                                    {{ \Carbon\Carbon::parse($stats['last_sale_date'])->format('d M Y') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
                    <div class="p-5 border-b border-zinc-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-50 rounded-xl text-slate-600">
                                <i class="bi bi-activity text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-zinc-800">Status</h5>
                                <p class="text-sm text-zinc-500">Aktivitas customer</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        @if ($customer->is_active)
                            <div class="flex items-start gap-4 p-3 bg-green-50 rounded-xl border border-green-100">
                                <div class="mt-1">
                                    <i class="bi bi-check-circle-fill text-green-500 text-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-sm font-bold text-green-900 mb-1">Customer Aktif</h6>
                                    <p class="text-xs text-green-700 leading-relaxed">
                                        Customer ini memiliki transaksi dalam 6 bulan terakhir.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-4 p-3 bg-amber-50 rounded-xl border border-amber-100">
                                <div class="mt-1">
                                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-sm font-bold text-amber-900 mb-1">Tidak Aktif</h6>
                                    <p class="text-xs text-amber-700 leading-relaxed">
                                        Tidak ada transaksi dalam 6 bulan terakhir.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
                     <div class="p-5 border-b border-zinc-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-violet-50 rounded-xl text-violet-600">
                                <i class="bi bi-lightning-charge-fill text-xl"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-zinc-800">Aksi Cepat</h5>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                         <div class="space-y-3">
                            @can('create_sales')
                                <a href="{{ route('sales.create', ['customer_id' => $customer->id]) }}"
                                    class="w-full flex items-center justify-center px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 group">
                                    <i class="bi bi-plus-lg me-2 group-hover:scale-110 transition-transform"></i> Buat Penjualan Baru
                                </a>
                            @endcan

                            @can('edit_customers')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="w-full flex items-center justify-center px-4 py-3 bg-white border border-zinc-200 text-zinc-700 text-sm font-medium rounded-xl hover:bg-zinc-50 hover:border-zinc-300 transition-all">
                                    <i class="bi bi-pencil me-2"></i> Edit Customer
                                </a>
                            @endcan

                            @can('delete_customers')
                                <button type="button" class="w-full flex items-center justify-center px-4 py-3 bg-white border border-red-200 text-red-600 text-sm font-medium rounded-xl hover:bg-red-50 hover:border-red-300 transition-all delete-customer"
                                    data-id="{{ $customer->id }}" data-name="{{ $customer->customer_name }}"
                                    data-has-sales="{{ $customer->sales->count() > 0 ? 'true' : 'false' }}">
                                    <i class="bi bi-trash me-2"></i> Hapus Customer
                                </button>
                            @endcan
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
            // Konfirmasi Hapus Customer
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasSales = $(this).data('has-sales') === true; // Simplified boolean check
                const url = '{{ route('customers.destroy', ':id') }}'.replace(':id', id);

                let warningText = hasSales ?
                    `<div class="bg-yellow-50 p-4 rounded-lg text-left text-sm text-yellow-800 mb-4 border border-yellow-200 flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-yellow-500 mt-0.5 text-lg"></i>
                        <div>
                            <span class="font-bold">Peringatan:</span> Customer ini memiliki riwayat penjualan. Data hanya akan di-arsipkan (soft delete).
                        </div>
                     </div>` :
                    `<div class="bg-red-50 p-4 rounded-lg text-left text-sm text-red-800 mb-4 border border-red-200 flex items-start gap-3">
                        <i class="bi bi-exclamation-circle-fill text-red-500 mt-0.5 text-lg"></i>
                        <div>
                            <span class="font-bold">Perhatian:</span> Customer ini akan dihapus secara permanen karena belum memiliki transaksi.
                        </div>
                     </div>`;

                Swal.fire({
                    title: '<span class="text-xl font-bold text-zinc-800">Hapus Customer?</span>',
                    html: `
                        <div class="mb-2 text-zinc-600">Anda akan menghapus data customer:</div>
                        <div class="text-lg font-bold text-zinc-800 mb-4">${name}</div>
                        ${warningText}
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl border-0 shadow-xl',
                        confirmButton: 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors focus:ring-4 focus:ring-red-200',
                        cancelButton: 'px-5 py-2.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-700 rounded-xl font-medium transition-colors focus:ring-4 focus:ring-zinc-100 me-3'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '<h3 class="font-bold text-lg text-zinc-800">Menghapus...</h3>',
                            html: '<p class="text-zinc-500 text-sm">Mohon tunggu sebentar</p>',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
