@extends('layouts.app-flowbite')

@section('title', 'Detail Supplier')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-white">
                        <i class="bi bi-house-door me-2"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-zinc-400 text-xs mx-1"></i>
                        <a href="{{ route('suppliers.index') }}" class="ms-1 text-sm font-medium text-zinc-700 hover:text-blue-600 md:ms-2 dark:text-zinc-400 dark:hover:text-white">
                            Supplier
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-zinc-400 text-xs mx-1"></i>
                        <span class="ms-1 text-sm font-medium text-zinc-500 md:ms-2 dark:text-zinc-400">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight dark:text-white">Detail Supplier</h1>
                <p class="text-sm text-zinc-500 mt-1 dark:text-zinc-400">Informasi lengkap dan riwayat transaksi.</p>
            </div>
            
            <div class="flex gap-2">
                @can('edit_suppliers')
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <i class="bi bi-pencil-square me-2"></i> Edit
                </a>
                @endcan
                <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 transition-colors shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Supplier Info & Purchase History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Supplier Information Card -->
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-700">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="bi bi-person-badge-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Profil Supplier</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                     <!-- Nama Supplier -->
                     <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Nama Supplier</p>
                        <p class="text-base font-bold text-zinc-900 dark:text-zinc-100">{{ $supplier->supplier_name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Email</p>
                        <a href="mailto:{{ $supplier->supplier_email }}" class="text-base font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-2">
                            <i class="bi bi-envelope"></i> {{ $supplier->supplier_email }}
                        </a>
                    </div>

                    <!-- No. Telepon -->
                    <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">No. Telepon</p>
                        <a href="tel:{{ $supplier->supplier_phone }}" class="text-base font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-2">
                            <i class="bi bi-telephone"></i> {{ $supplier->supplier_phone }}
                        </a>
                    </div>

                    <!-- Kota -->
                    <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Kota</p>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $supplier->city }}
                        </span>
                    </div>

                    <!-- Negara -->
                    <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Negara</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $supplier->country }}</p>
                    </div>

                    <!-- Terdaftar -->
                    <div>
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Terdaftar Sejak</p>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $supplier->created_at->format('d M Y') }}
                            <span class="text-zinc-400 text-xs font-normal ms-1">({{ $supplier->created_at->diffForHumans() }})</span>
                        </p>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="md:col-span-2">
                        <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Alamat Lengkap</p>
                        <div class="p-3 bg-zinc-50 border border-zinc-100 rounded-xl text-zinc-700 text-sm">
                            {{ $supplier->address }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase History Card -->
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden dark:bg-zinc-800 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-100 flex justify-between items-center dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <i class="bi bi-cart-check-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Riwayat Pembelian</h3>
                            <p class="text-xs text-zinc-500">10 transaksi terakhir dari supplier ini</p>
                        </div>
                    </div>
                     @if ($supplier->purchases->count() > 0)
                        <a href="{{ route('purchases.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline">Lihat Semua</a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    @if ($supplier->purchases->count() > 0)
                    <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                        <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tanggal</th>
                                <th scope="col" class="px-6 py-3">Reference</th>
                                <th scope="col" class="px-6 py-3">Total</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Pembayaran</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier->purchases->take(10) as $purchase)
                            <tr class="bg-white border-b hover:bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-700 dark:hover:bg-zinc-600 transition-colors">
                                <td class="px-6 py-4">{{ $purchase->date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-bold text-blue-600">
                                    {{ $purchase->reference }}
                                </td>
                                <td class="px-6 py-4 font-bold text-emerald-600">
                                    {{ format_currency($purchase->total_amount) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($purchase->status == 'Completed')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Completed</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($purchase->payment_status == 'Lunas')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Lunas</span>
                                    @elseif ($purchase->payment_status == 'Partial')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Partial</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @can('show_purchases')
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-600 hover:text-blue-900 font-bold hover:underline" title="Lihat Detail">
                                        Detail
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-10 text-center flex flex-col items-center justify-center text-zinc-500">
                        <div class="p-4 bg-zinc-50 rounded-full mb-3">
                            <i class="bi bi-cart-x text-3xl text-zinc-400"></i>
                        </div>
                        <p class="font-medium">Belum ada riwayat pembelian.</p>
                        <p class="text-sm">Transaksi pembelian dari supplier ini akan muncul di sini.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Statistics & Quick Actions -->
        <div class="space-y-6">
            <!-- Statistcs Card -->
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                <h5 class="font-bold text-zinc-900 mb-6 border-b border-zinc-100 pb-2 dark:text-white">Statistik Transaksi</h5>
                
                <div class="space-y-4">
                    <!-- Total Pembelian -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="bi bi-bag-check-fill"></i>
                            </div>
                            <span class="text-sm font-medium text-blue-900">Total Beli</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-black text-blue-600">{{ $stats['total_purchases'] }}</span>
                            <span class="text-xs text-blue-500">transaksi</span>
                        </div>
                    </div>

                    <!-- Total Nilai -->
                    <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                         <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <span class="text-sm font-medium text-emerald-900">Total Nilai</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-black text-emerald-600">{{ format_currency($stats['total_amount']) }}</span>
                        </div>
                    </div>

                    <!-- Sisa Hutang -->
                    <div class="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-xl">
                        <div class="flex items-center gap-3">
                           <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                               <i class="bi bi-exclamation-circle-fill"></i>
                           </div>
                           <span class="text-sm font-medium text-red-900">Sisa Hutang</span>
                       </div>
                       <div class="text-right">
                           <span class="block text-lg font-black text-red-600">{{ format_currency($stats['total_due']) }}</span>
                       </div>
                   </div>

                    <!-- Pembelian Terakhir -->
                    @if ($stats['last_purchase_date'])
                    <div class="pt-4 mt-2 border-t border-zinc-100 text-center">
                        <p class="text-xs text-zinc-500 uppercase tracking-widest mb-1">Pembelian Terakhir</p>
                        <p class="text-sm font-bold text-zinc-900">
                             {{ \Carbon\Carbon::parse($stats['last_purchase_date'])->format('d F Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                 <h5 class="font-bold text-zinc-900 mb-4 dark:text-white">Status Supplier</h5>
                 @if ($supplier->is_active)
                 <div class="flex items-start gap-3 p-4 bg-green-50 rounded-xl border border-green-200">
                    <i class="bi bi-check-circle-fill text-green-500 text-xl mt-0.5"></i>
                    <div>
                        <h6 class="font-bold text-green-800">Aktif</h6>
                        <p class="text-xs text-green-700 mt-1">Supplier ini aktif bertransaksi dalam 6 bulan terakhir.</p>
                    </div>
                 </div>
                 @else
                 <div class="flex items-start gap-3 p-4 bg-zinc-50 rounded-xl border border-zinc-200">
                    <i class="bi bi-dash-circle-fill text-zinc-400 text-xl mt-0.5"></i>
                    <div>
                        <h6 class="font-bold text-zinc-600">Tidak Aktif</h6>
                        <p class="text-xs text-zinc-500 mt-1">Tidak ada transaksi dalam 6 bulan terakhir.</p>
                    </div>
                 </div>
                 @endif
            </div>

             <!-- Quick Actions -->
             <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                <h5 class="font-bold text-zinc-900 mb-4 dark:text-white">Aksi Cepat</h5>
                <div class="flex flex-col gap-3">
                    @can('create_purchases')
                    <a href="{{ route('purchases.create', ['supplier_id' => $supplier->id]) }}" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <i class="bi bi-plus-lg"></i> Buat Pembelian
                    </a>
                    @endcan

                    @can('delete_suppliers')
                    <button type="button" class="delete-supplier w-full text-red-600 bg-red-50 hover:bg-red-100 border border-transparent font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all flex items-center justify-center gap-2"
                        data-id="{{ $supplier->id }}" 
                        data-name="{{ $supplier->supplier_name }}"
                        data-has-purchases="{{ $supplier->purchases->count() > 0 ? 'true' : 'false' }}">
                        <i class="bi bi-trash"></i> Hapus Supplier
                    </button>
                    @endcan
                </div>
             </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
             // SweetAlert2 Delete (Same delegation logic used in index)
             $('.delete-supplier').on('click', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasPurchases = $(this).data('has-purchases') === 'true'; // string comparison from data attr
                const url = '{{ route('suppliers.destroy', ':id') }}'.replace(':id', id);

                let warningText = `Anda akan menghapus data supplier <strong>"${name}"</strong>.`;
                let warningIcon = 'warning'; 

                if (hasPurchases) {
                    warningText += `<br><br><span class="text-orange-600 font-bold"><i class="bi bi-archive me-1"></i> Perhatian:</span> Supplier ini memiliki riwayat transaksi. Data akan diarsipkan (Soft Delete).`;
                } else {
                    warningText += `<br><br><span class="text-red-600 font-bold"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan:</span> Tindakan ini bersifat permanen!`;
                }

                Swal.fire({
                    title: 'Hapus Supplier?',
                    html: warningText,
                    icon: warningIcon,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        });
                        
                        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                        
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
