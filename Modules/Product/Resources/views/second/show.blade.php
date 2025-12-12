@extends('layouts.app-flowbite')

@section('title', 'Detail Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Barang Bekas', 'url' => route('products_second.index')],
        ['text' => 'Detail Produk', 'url' => '#', 'icon' => 'bi bi-eye']
    ]])
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Action Bar --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 p-4 flex justify-between items-center dark:bg-gray-800 dark:border-gray-700">
            <div>
                <h5 class="mb-1 text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-600"></i>
                    Detail Produk: {{ $product->name }}
                </h5>
                <p class="text-sm text-gray-500 dark:text-gray-400">Informasi lengkap produk bekas</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('products_second.index') }}" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('products_second.edit', $product->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <i class="bi bi-pencil me-1"></i> Edit Produk
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Product Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-grid me-2 text-blue-600"></i>
                            Informasi Produk
                        </h6>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <tbody>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800 w-1/3">
                                        <i class="bi bi-upc-scan text-gray-400 me-2"></i> Kode Unik
                                    </th>
                                    <td class="px-6 py-4">
                                        <span class="font-mono bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $product->unique_code }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        <i class="bi bi-tag text-gray-400 me-2"></i> Nama Barang
                                    </th>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                        {{ $product->name }}
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        <i class="bi bi-folder text-gray-400 me-2"></i> Kategori
                                    </th>
                                    <td class="px-6 py-4">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                                            {{ $product->category->category_name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        <i class="bi bi-award text-gray-400 me-2"></i> Merek
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $product->brand->name ?? '-' }}
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        <i class="bi bi-sliders text-gray-400 me-2"></i> Spesifikasi
                                    </th>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @if ($product->size)
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                                    <strong>Ukuran:</strong> {{ $product->size }}
                                                </span>
                                            @endif
                                            @if ($product->ring)
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                                    <strong>Ring:</strong> {{ $product->ring }}
                                                </span>
                                            @endif
                                            @if ($product->product_year)
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                                    <strong>Tahun:</strong> {{ $product->product_year }}
                                                </span>
                                            @endif
                                            @if (!$product->size && !$product->ring && !$product->product_year)
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        <i class="bi bi-toggle-on text-gray-400 me-2"></i> Status
                                    </th>
                                    <td class="px-6 py-4">
                                        @if ($product->status == 'available')
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                                <i class="bi bi-check-circle me-1"></i> Tersedia
                                            </span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                                <i class="bi bi-x-circle me-1"></i> Terjual
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800 align-top">
                                        <i class="bi bi-journal-text text-gray-400 me-2"></i> Deskripsi Kondisi
                                    </th>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                            {{ $product->condition_notes ?? '-' }}
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pricing Information Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-cash-stack me-2 text-blue-600"></i>
                            Informasi Harga
                        </h6>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Modal --}}
                            <div class="p-4 bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl text-center dark:bg-gray-700 dark:from-gray-700 dark:to-gray-600 dark:border-gray-600">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1 font-semibold">
                                    <i class="bi bi-arrow-down-circle me-1"></i> Modal
                                </div>
                                <div class="text-xl font-bold text-red-600 dark:text-red-400">
                                    {{ format_currency($product->purchase_price) }}
                                </div>
                            </div>

                            {{-- Harga Jual --}}
                            <div class="p-4 bg-gradient-to-br from-green-50 to-white border border-green-200 rounded-xl text-center dark:bg-gray-700 dark:from-gray-700 dark:to-gray-600 dark:border-green-800">
                                <div class="text-sm text-green-700 dark:text-green-300 mb-1 font-semibold">
                                    <i class="bi bi-arrow-up-circle me-1"></i> Harga Jual
                                </div>
                                <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                    {{ format_currency($product->selling_price) }}
                                </div>
                            </div>

                            {{-- Margin --}}
                            <div class="p-4 bg-gradient-to-br from-blue-50 to-white border border-blue-200 rounded-xl text-center dark:bg-gray-700 dark:from-gray-700 dark:to-gray-600 dark:border-blue-800">
                                <div class="text-sm text-blue-700 dark:text-blue-300 mb-1 font-semibold">
                                    <i class="bi bi-graph-up-arrow me-1"></i> Margin
                                </div>
                                @php
                                    $profit = $product->selling_price - $product->purchase_price;
                                    $percentage = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;
                                @endphp
                                <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($percentage, 2) }}%
                                </div>
                                <small class="text-gray-500 dark:text-gray-400">{{ format_currency($profit) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Images --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 lg:sticky lg:top-[88px]">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-images me-2 text-blue-600"></i>
                            Gambar Produk
                        </h6>
                    </div>
                    <div class="p-4">
                        @php
                            $media = $product->getMedia('images');
                            $hasImage = $media->count() > 0;
                        @endphp

                        @if ($hasImage)
                            {{-- Main Image --}}
                            <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 dark:bg-gray-700 dark:border-gray-600 relative group aspect-square flex items-center justify-center">
                                <img src="{{ $media->first()->getFullUrl() }}" alt="{{ $product->name }}"
                                    class="max-w-full max-h-full object-contain cursor-pointer transition-transform duration-300 group-hover:scale-105" 
                                    id="mainProductImage"
                                    onclick="window.open(this.src, '_blank')">
                            </div>

                            {{-- Thumbnail Gallery --}}
                            @if ($media->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($media as $image)
                                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:border-blue-500 transition-colors dark:bg-gray-700 dark:border-gray-600 {{ $loop->first ? 'ring-2 ring-blue-500' : '' }} thumbnail-item"
                                            onclick="changeMainImage('{{ $image->getFullUrl() }}', this)">
                                            <img src="{{ $image->getFullUrl() }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <p class="mt-4 text-xs text-center text-gray-500 dark:text-gray-400">
                                <i class="bi bi-info-circle me-1"></i> Klik gambar untuk memperbesar
                            </p>
                        @else
                            {{-- No Image Fallback --}}
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4 dark:bg-gray-600">
                                    <i class="bi bi-image text-3xl text-gray-400 dark:text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada gambar</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Produk ini belum memiliki gambar.</p>
                                <a href="{{ route('products_second.edit', $product->id) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Gambar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        function changeMainImage(imageUrl, element) {
            // Update main image
            const mainImg = document.getElementById('mainProductImage');
            mainImg.style.opacity = '0.5';
            setTimeout(() => {
                mainImg.src = imageUrl;
                mainImg.style.opacity = '1';
            }, 150);

            // Update active state
            document.querySelectorAll('.thumbnail-item').forEach(el => {
                el.classList.remove('ring-2', 'ring-blue-500');
            });
            element.classList.add('ring-2', 'ring-blue-500');
        }
    </script>
@endpush
