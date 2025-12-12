@extends('layouts.app-flowbite')

@section('title', 'Laporan Stok Menipis')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Laporan</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Stok Menipis</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="px-4 pt-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-orange-500"></i>
                Laporan Stok Menipis
            </h1>
            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">Daftar produk dengan stok di bawah atau sama dengan batas alert (≤ {{ $limit }}).</p>
        </div>
        
        <a href="{{ route('products.index') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center gap-2">
            <i class="bi bi-box-seam"></i> Ke Produk
        </a>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Kode</th>
                    <th scope="col" class="px-6 py-3">Nama Produk</th>
                    <th scope="col" class="px-6 py-3">Kategori</th>
                    <th scope="col" class="px-6 py-3">Merek</th>
                    <th scope="col" class="px-6 py-3">Ukuran</th>
                    <th scope="col" class="px-6 py-3 text-center">Stok Saat Ini</th>
                    <th scope="col" class="px-6 py-3 text-center">Batas Alert</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $p->product_code }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                @if($p->getFirstMediaUrl('images'))
                                    <img src="{{ $p->getFirstMediaUrl('images', 'thumb') }}" alt="Product Image" class="w-8 h-8 rounded-full border border-gray-200">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                                {{ $p->product_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ optional($p->category)->category_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ optional($p->brand)->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $p->product_size ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-red-600 font-bold">{{ $p->product_quantity }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $p->product_stock_alert }}
                        </td>
                         <td class="px-6 py-4 text-center">
                            @if($p->product_quantity == 0)
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Habis</span>
                            @else
                                <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-orange-300">Kritis</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-check-circle-fill text-green-500 text-4xl mb-3"></i>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">Stok Aman!</p>
                                <p class="text-sm">Tidak ada produk yang stoknya di bawah batas alert.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
