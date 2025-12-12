@extends('layouts.app-flowbite')

@section('title', 'Detail Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index')],
        ['text' => 'Detail Produk', 'url' => '#', 'icon' => 'bi bi-eye']
    ]])
@endsection

@section('content')
    {{-- Action Bar --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                Detail Produk: <span class="text-blue-600 dark:text-blue-400">{{ $product->product_name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi lengkap dan status inventaris produk</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 hover:shadow-sm transition-all">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('products.edit', $product->id) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                <i class="bi bi-pencil me-1"></i> Edit Produk
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Product Info --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Information Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                        <i class="bi bi-card-text text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Dasar</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        {{-- Kode --}}
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kode Barang</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-base font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                    {{ $product->product_code }}
                                </span>
                            </div>
                        </div>

                        {{-- Nama --}}
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nama Barang</span>
                            <span class="text-base font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</span>
                        </div>

                        {{-- Kategori --}}
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kategori</span>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $product->category->category_name }}
                                </span>
                            </div>
                        </div>

                        {{-- Merek --}}
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Merek</span>
                            <span class="text-base text-gray-900 dark:text-white">{{ $product->brand->name ?? '-' }}</span>
                        </div>

                        {{-- Spesifikasi --}}
                        <div class="col-span-1 md:col-span-2">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2 block">Spesifikasi</span>
                            <div class="flex flex-wrap gap-2">
                                @if ($product->product_size)
                                    <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600 text-sm">
                                        <span class="text-gray-500 dark:text-gray-400 mr-1">Ukuran:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->product_size }}</span>
                                    </div>
                                @endif
                                @if ($product->ring)
                                    <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600 text-sm">
                                        <span class="text-gray-500 dark:text-gray-400 mr-1">Ring:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->ring }}</span>
                                    </div>
                                @endif
                                @if ($product->product_year)
                                    <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600 text-sm">
                                        <span class="text-gray-500 dark:text-gray-400 mr-1">Tahun:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->product_year }}</span>
                                    </div>
                                @endif
                                @if (!$product->product_size && !$product->ring && !$product->product_year)
                                    <span class="text-gray-400 italic text-sm">Tidak ada spesifikasi khusus</span>
                                @endif
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-span-1 md:col-span-2">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 block">Catatan</span>
                            <p class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700 italic">
                                {{ $product->product_note ?? 'Tidak ada catatan tambahan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Price & Stock Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Pricing Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg">
                            <i class="bi bi-currency-dollar text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Harga & Margin</h3>
                    </div>
                    <div class="p-6 flex-1 flex flex-col gap-4">
                        {{-- Modal --}}
                        <div class="flex justify-between items-end border-b border-gray-100 dark:border-gray-700 pb-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Harga Modal</span>
                            <span class="text-lg font-semibold text-red-600 dark:text-red-400">{{ format_currency($product->product_cost) }}</span>
                        </div>
                        {{-- Jual --}}
                        <div class="flex justify-between items-end border-b border-gray-100 dark:border-gray-700 pb-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Harga Jual</span>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ format_currency($product->product_price) }}</span>
                        </div>
                        {{-- Margin --}}
                        @php
                            $profit = $product->product_price - $product->product_cost;
                            $percentage = $product->product_cost > 0 ? ($profit / $product->product_cost) * 100 : 0;
                        @endphp
                        <div class="mt-auto pt-2">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-100 dark:border-blue-800">
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase">Margin Keuntungan</span>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($percentage, 1) }}%</span>
                                    <span class="text-sm font-medium text-blue-500">({{ format_currency($profit) }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                            <i class="bi bi-box-seam text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Status Inventaris</h3>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-center">
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Current Stock --}}
                            <div class="col-span-2 text-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                <span class="text-sm text-gray-500 dark:text-gray-400 block mb-1">Stok Saat Ini</span>
                                <span class="text-4xl font-bold {{ $product->product_quantity <= $product->product_stock_alert ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                                    {{ $product->product_quantity }}
                                </span>
                                <span class="text-sm text-gray-400 ml-1">{{ $product->product_unit }}</span>
                            </div>

                            {{-- Alert Limit --}}
                            <div class="text-center p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                <span class="text-xs text-gray-500 block">Batas Minimum</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $product->product_stock_alert }}</span>
                            </div>

                            {{-- Initial Stock --}}
                            <div class="text-center p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                <span class="text-xs text-gray-500 block">Stok Awal</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $product->stok_awal }}</span>
                            </div>
                        </div>

                        @if ($product->product_quantity <= $product->product_stock_alert)
                            <div class="mt-4 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span class="font-medium">Stok menipis! Harap segera restock.</span>
                            </div>
                        @else
                            <div class="mt-4 flex items-center gap-2 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                                <i class="bi bi-check-circle-fill"></i>
                                <span class="font-medium">Stok aman.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Adjustment History --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg">
                            <i class="bi bi-clock-history text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Penyesuaian</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Referensi</th>
                                <th class="px-6 py-4 text-center">Jumlah</th>
                                <th class="px-6 py-4 text-center">Tipe</th>
                                <th class="px-6 py-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($product->adjustedProducts as $adjusted)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($adjusted->adjustment->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('adjustments.show', $adjusted->adjustment->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">
                                            {{ $adjusted->adjustment->reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold">
                                        {{ $adjusted->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if (strtolower($adjusted->type) == 'add')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                <i class="bi bi-plus me-1"></i> Tambah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                <i class="bi bi-dash me-1"></i> Kurang
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 italic">
                                        {{ $adjusted->adjustment->note ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                                            <p>Belum ada riwayat penyesuaian</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Images --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 rounded-lg">
                        <i class="bi bi-images text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Galeri Produk</h3>
                </div>

                @php
                    $media = $product->getMedia('images');
                    $hasImage = $media->count() > 0;
                @endphp

                @if ($hasImage)
                    {{-- Main Image --}}
                    <div class="aspect-square w-full bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 mb-4 group relative">
                        <img src="{{ $media->first()->getFullUrl() }}" 
                             alt="{{ $product->product_name }}"
                             class="w-full h-full object-contain p-2 transition-transform duration-300 group-hover:scale-105" 
                             id="mainProductImage">
                        <div class="absolute inset-x-0 bottom-0 bg-black/50 backdrop-blur-sm text-white text-xs p-2 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                            Klik thumbnail untuk ganti
                        </div>
                    </div>

                    {{-- Image Info --}}
                    <div class="flex justify-between items-center text-xs text-gray-500 mb-4 px-1">
                        <span class="truncate max-w-[70%]"><i class="bi bi-file-earmark-image me-1"></i> {{ $media->first()->file_name }}</span>
                        <span>{{ number_format($media->first()->size / 1024, 2) }} KB</span>
                    </div>

                    {{-- Thumbnails --}}
                    @if ($media->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($media as $image)
                                <button type="button" 
                                        onclick="changeMainImage('{{ $image->getFullUrl() }}', this)"
                                        class="aspect-square rounded-lg border-2 border-transparent hover:border-blue-500 focus:border-blue-500 focus:outline-none overflow-hidden bg-gray-50 transition-all thumbnail-btn {{ $loop->first ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}">
                                    <img src="{{ $image->getFullUrl() }}" 
                                         class="w-full h-full object-cover" 
                                         alt="Thumbnail">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- No Image State --}}
                    <div class="aspect-square w-full bg-gray-50 dark:bg-gray-700/30 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center p-6 text-center">
                        <i class="bi bi-image text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Tidak ada gambar</h4>
                        <p class="text-xs text-gray-500 mb-4">Produk ini belum memiliki gambar yang diunggah.</p>
                        <a href="{{ route('products.edit', $product->id) }}" 
                           class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">
                            + Upload Gambar Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function changeMainImage(url, btn) {
        const mainImage = document.getElementById('mainProductImage');
        
        // Simple fade effect
        mainImage.style.opacity = '0.5';
        setTimeout(() => {
            mainImage.src = url;
            mainImage.style.opacity = '1';
        }, 150);

        // Update active class on thumbnails
        document.querySelectorAll('.thumbnail-btn').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-1');
        });
        btn.classList.add('ring-2', 'ring-blue-500', 'ring-offset-1');
    }
</script>
@endsection
