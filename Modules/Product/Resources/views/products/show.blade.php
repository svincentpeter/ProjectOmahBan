@extends('layouts.app')

@section('title', 'Detail Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index')],
        ['text' => 'Detail Produk', 'url' => '#', 'icon' => 'bi bi-eye']
    ]])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Action Bar --}}
            <div class="action-bar shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 font-weight-bold">
                            <i class="cil-info mr-2 text-primary"></i>
                            Detail Produk: {{ $product->product_name }}
                        </h5>
                        <small class="text-muted">Informasi lengkap produk</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="cil-arrow-left mr-1"></i> Kembali
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                            <i class="cil-pencil mr-1"></i> Edit Produk
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Column: Product Info --}}
                <div class="col-lg-8">
                    {{-- Basic Information Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-notes mr-2 text-primary"></i>
                                Informasi Produk
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-detail mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-barcode text-muted mr-2"></i>
                                                Kode Barang
                                            </td>
                                            <td class="detail-value">
                                                <strong>{{ $product->product_code }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-tag text-muted mr-2"></i>
                                                Nama Barang
                                            </td>
                                            <td class="detail-value">
                                                {{ $product->product_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-folder text-muted mr-2"></i>
                                                Kategori
                                            </td>
                                            <td class="detail-value">
                                                <span
                                                    class="badge badge-info">{{ $product->category->category_name }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-bookmark text-muted mr-2"></i>
                                                Merek
                                            </td>
                                            <td class="detail-value">
                                                {{ $product->brand->name ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-settings text-muted mr-2"></i>
                                                Spesifikasi
                                            </td>
                                            <td class="detail-value">
                                                <div class="spec-group">
                                                    @if ($product->product_size)
                                                        <span class="spec-item">
                                                            <strong>Ukuran:</strong> {{ $product->product_size }}
                                                        </span>
                                                    @endif
                                                    @if ($product->ring)
                                                        <span class="spec-item">
                                                            <strong>Ring:</strong> {{ $product->ring }}
                                                        </span>
                                                    @endif
                                                    @if ($product->product_year)
                                                        <span class="spec-item">
                                                            <strong>Tahun:</strong> {{ $product->product_year }}
                                                        </span>
                                                    @endif
                                                    @if (!$product->product_size && !$product->ring && !$product->product_year)
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">
                                                <i class="cil-notes text-muted mr-2"></i>
                                                Catatan
                                            </td>
                                            <td class="detail-value">
                                                {{ $product->product_note ?? '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing Information Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-dollar mr-2 text-primary"></i>
                                Informasi Harga
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="price-box">
                                        <div class="price-label">
                                            <i class="cil-arrow-circle-bottom mr-1"></i>
                                            Modal
                                        </div>
                                        <div class="price-value text-danger">
                                            {{ format_currency($product->product_cost) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="price-box">
                                        <div class="price-label">
                                            <i class="cil-arrow-circle-top mr-1"></i>
                                            Harga Jual
                                        </div>
                                        <div class="price-value text-success">
                                            {{ format_currency($product->product_price) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="price-box">
                                        <div class="price-label">
                                            <i class="cil-chart-line mr-1"></i>
                                            Margin
                                        </div>
                                        @php
                                            $profit = $product->product_price - $product->product_cost;
                                            $percentage =
                                                $product->product_cost > 0
                                                    ? ($profit / $product->product_cost) * 100
                                                    : 0;
                                        @endphp
                                        <div class="price-value text-primary">
                                            {{ number_format($percentage, 2) }}%
                                        </div>
                                        <small class="text-muted">{{ format_currency($profit) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stock Information Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-layers mr-2 text-primary"></i>
                                Informasi Stok
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stock-box">
                                        <div class="stock-icon">
                                            <i class="cil-plus-circle text-info"></i>
                                        </div>
                                        <div class="stock-label">Stok Awal</div>
                                        <div class="stock-value">{{ $product->stok_awal }}
                                            <small>{{ $product->product_unit }}</small></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stock-box">
                                        <div class="stock-icon">
                                            <i class="cil-layers text-success"></i>
                                        </div>
                                        <div class="stock-label">Stok Sisa</div>
                                        <div
                                            class="stock-value 
                                        @if ($product->product_quantity <= $product->product_stock_alert) text-danger 
                                        @else 
                                            text-success @endif">
                                            {{ $product->product_quantity }} <small>{{ $product->product_unit }}</small>
                                        </div>
                                        @if ($product->product_quantity <= $product->product_stock_alert)
                                            <small class="text-danger">
                                                <i class="cil-warning"></i> Stok Rendah!
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stock-box">
                                        <div class="stock-icon">
                                            <i class="cil-warning text-warning"></i>
                                        </div>
                                        <div class="stock-label">Stok Minimum</div>
                                        <div class="stock-value">{{ $product->product_stock_alert }}
                                            <small>{{ $product->product_unit }}</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Adjustment History Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-history mr-2 text-primary"></i>
                                Riwayat Penyesuaian Stok
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Tanggal</th>
                                            <th width="20%">Referensi</th>
                                            <th width="15%" class="text-center">Jumlah</th>
                                            <th width="15%" class="text-center">Tipe</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($product->adjustedProducts as $adjusted)
                                            <tr>
                                                <td>
                                                    <i class="cil-calendar text-muted mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($adjusted->adjustment->date)->format('d M Y') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('adjustments.show', $adjusted->adjustment->id) }}"
                                                        class="text-primary">
                                                        <i class="cil-link mr-1"></i>
                                                        {{ $adjusted->adjustment->reference }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ $adjusted->quantity }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if (strtolower($adjusted->type) == 'add')
                                                        <span class="badge badge-success">
                                                            <i class="cil-plus mr-1"></i>
                                                            Penambahan
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="cil-minus mr-1"></i>
                                                            Pengurangan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $adjusted->adjustment->note ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="cil-info mr-2"></i>
                                                    Belum ada riwayat penyesuaian stok untuk produk ini
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Images --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-sidebar">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-image mr-2 text-primary"></i>
                                Gambar Produk
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $media = $product->getMedia('images');
                                $hasImage = $media->count() > 0;
                            @endphp

                            @if ($hasImage)
                                {{-- Main Image --}}
                                <div class="main-image-container mb-3">
                                    <img src="{{ $media->first()->getFullUrl() }}" alt="{{ $product->product_name }}"
                                        class="img-fluid main-product-image" id="mainProductImage">
                                </div>

                                {{-- Image Info --}}
                                <div class="image-info mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="cil-file mr-1"></i>
                                            {{ $media->first()->file_name }}
                                        </small>
                                        <small class="text-muted">
                                            {{ number_format($media->first()->size / 1024, 2) }} KB
                                        </small>
                                    </div>
                                </div>

                                {{-- Thumbnail Gallery --}}
                                @if ($media->count() > 1)
                                    <div class="thumbnail-gallery">
                                        <p class="small text-muted mb-2">
                                            <i class="cil-grid mr-1"></i>
                                            Semua Gambar ({{ $media->count() }})
                                        </p>
                                        <div class="row g-2">
                                            @foreach ($media as $image)
                                                <div class="col-4">
                                                    <img src="{{ $image->getFullUrl() }}"
                                                        alt="{{ $product->product_name }}"
                                                        class="img-fluid img-thumbnail thumbnail-image {{ $loop->first ? 'active' : '' }}"
                                                        style="cursor: pointer;"
                                                        onclick="changeMainImage('{{ $image->getFullUrl() }}', this)">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- No Image Fallback --}}
                                <div class="no-image-container text-center">
                                    <img src="{{ asset('images/fallback_product_image.png') }}" alt="Tidak ada gambar"
                                        class="img-fluid" style="max-height: 300px; opacity: 0.3;">
                                    <p class="text-muted mt-3">
                                        <i class="cil-image-broken mr-1"></i>
                                        Belum ada gambar untuk produk ini
                                    </p>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="cil-plus mr-1"></i>
                                        Tambah Gambar
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ========== Animations ========== */
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========== Action Bar ========== */
        .action-bar {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
        }

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Detail Table ========== */
        .table-detail td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .table-detail .detail-label {
            width: 35%;
            font-weight: 600;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .table-detail .detail-value {
            color: #212529;
        }

        /* ========== Spec Group ========== */
        .spec-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .spec-item {
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        /* ========== Price Box ========== */
        .price-box {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .price-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .price-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        /* ========== Stock Box ========== */
        .stock-box {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .stock-box:hover {
            border-color: #4834DF;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.1);
        }

        .stock-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stock-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .stock-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* ========== Image Styles ========== */
        .main-image-container {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
        }

        .main-product-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            display: block;
        }

        .thumbnail-gallery {
            margin-top: 1rem;
        }

        .thumbnail-image {
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }

        .thumbnail-image:hover,
        .thumbnail-image.active {
            border-color: #4834DF;
            transform: scale(1.05);
        }

        /* ========== Sticky Sidebar ========== */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
                margin-top: 1rem;
            }

            .price-value {
                font-size: 1.25rem;
            }

            .stock-value {
                font-size: 1.25rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        // Change main image when thumbnail clicked
        function changeMainImage(imageUrl, element) {
            // Update main image
            $('#mainProductImage').fadeOut(200, function() {
                $(this).attr('src', imageUrl).fadeIn(200);
            });

            // Update active thumbnail
            $('.thumbnail-image').removeClass('active');
            $(element).addClass('active');
        }

        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
