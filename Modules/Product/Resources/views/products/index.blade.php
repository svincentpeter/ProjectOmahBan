@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Produk</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                {{-- Total Produk --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon">
                            <i class="cil-basket"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Produk</div>
                            <div class="stats-value">
                                {{ \Modules\Product\Entities\Product::count() }}
                            </div>
                        </div>
                    </div>
                </div>

                
                {{-- Stok Rendah --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon">
                            <i class="cil-warning"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Stok Rendah</div>
                            <div class="stats-value">
                                {{ \Modules\Product\Entities\Product::where('product_quantity', '<=', 10)->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Kategori --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon">
                            <i class="cil-tags"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-label">Total Kategori</div>
                            <div class="stats-value">
                                {{ \Modules\Product\Entities\Category::count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="card shadow-sm">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Daftar Produk
                            </h5>
                            <small class="text-muted">Kelola produk dan inventory toko Anda</small>
                        </div>

                        <div class="btn-group" role="group">
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="cil-plus mr-2"></i> Tambah Produk
                            </a>
                            
                        </div>
                    </div>
                </div>

                {{-- Quick Filters --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>

                        {{-- Quick Filter Pills --}}
                        <div class="quick-filters mb-3">
                            <button type="button" class="filter-pill active" data-filter="all">
                                <i class="cil-apps"></i>
                                <span>Semua Produk</span>
                            </button>

                            <button type="button" class="filter-pill" data-filter="active">
                                <i class="cil-check-circle"></i>
                                <span>Aktif</span>
                            </button>

                            <button type="button" class="filter-pill" data-filter="inactive">
                                <i class="cil-x-circle"></i>
                                <span>Non-Aktif</span>
                            </button>

                            <button type="button" class="filter-pill" data-filter="low-stock">
                                <i class="cil-warning"></i>
                                <span>Stok Rendah</span>
                            </button>

                            <button type="button" class="filter-pill" data-filter="out-of-stock">
                                <i class="cil-ban"></i>
                                <span>Stok Habis</span>
                            </button>
                        </div>

                        {{-- Advanced Filters --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-folder mr-1 text-muted"></i> Kategori
                                </label>
                                <select id="filter-category" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    @foreach (\Modules\Product\Entities\Category::all() as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-barcode mr-1 text-muted"></i> Tipe Produk
                                </label>
                                <select id="filter-type" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="1">Single Product</option>
                                    <option value="2">Variable Product</option>
                                    <option value="3">Combo Product</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-sort-alpha-down mr-1 text-muted"></i> Urutkan
                                </label>
                                <select id="filter-sort" class="form-control">
                                    <option value="name-asc">Nama A-Z</option>
                                    <option value="name-desc">Nama Z-A</option>
                                    <option value="price-asc">Harga Terendah</option>
                                    <option value="price-desc">Harga Tertinggi</option>
                                    <option value="stock-asc">Stok Terendah</option>
                                    <option value="stock-desc">Stok Tertinggi</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2 d-block">
                                    &nbsp;
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <button id="btn-filter-apply" class="btn btn-primary">
                                        <i class="cil-filter mr-1"></i> Terapkan
                                    </button>
                                    <button id="btn-filter-reset" class="btn btn-outline-secondary">
                                        <i class="cil-reload mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'products-table']) !!}
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

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Statistics Cards ========== */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stats-card-purple {
            border-left-color: #4834DF;
        }

        .stats-card-success {
            border-left-color: #2eb85c;
        }

        .stats-card-warning {
            border-left-color: #f9b115;
        }

        .stats-card-info {
            border-left-color: #39f;
        }

        .stats-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            color: white;
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%);
            color: white;
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%);
            color: white;
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%);
            color: white;
        }

        .stats-content {
            flex: 1;
        }

        .stats-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
        }

        /* ========== Quick Filter Pills ========== */
        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            color: #4f5d73;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .filter-pill i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .filter-pill:hover {
            border-color: #4834DF;
            color: #4834DF;
            background: #f8f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.15);
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-color: #4834DF;
            color: white;
            box-shadow: 0 4px 15px rgba(72, 52, 223, 0.3);
        }

        /* ========== DataTable Wrapper ========== */
        .datatable-wrapper {
            padding: 1rem;
        }

        /* ========== DataTable Styling ========== */
        #products-table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #products-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        #products-table tbody tr {
            transition: all 0.2s ease;
        }

        #products-table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
        }

        /* ========== Product Image Thumbnail ========== */
        .product-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .quick-filters {
                flex-direction: column;
            }

            .filter-pill {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .stats-card {
                flex-direction: column;
                text-align: center;
            }

            .datatable-wrapper {
                padding: 0.5rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {
            // Quick filter pills
            $('.filter-pill').click(function() {
                $('.filter-pill').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).data('filter');

                // Reload DataTable with filter
                // Implement filter logic in your DataTable class
                console.log('Filter:', filter);

                if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables[
                        "products-table"]) {
                    window.LaravelDataTables["products-table"].ajax.reload();
                }
            });

            // Apply filters
            $('#btn-filter-apply').click(function() {
                const category = $('#filter-category').val();
                const type = $('#filter-type').val();
                const sort = $('#filter-sort').val();

                console.log('Filters:', {
                    category,
                    type,
                    sort
                });

                if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables[
                        "products-table"]) {
                    window.LaravelDataTables["products-table"].ajax.reload();
                }
            });

            // Reset filters
            $('#btn-filter-reset').click(function() {
                $('#filter-category').val('');
                $('#filter-type').val('');
                $('#filter-sort').val('name-asc');

                $('.filter-pill').removeClass('active');
                $('.filter-pill[data-filter="all"]').addClass('active');

                if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables[
                        "products-table"]) {
                    window.LaravelDataTables["products-table"].ajax.reload();
                }
            });

            // Delete confirmation
            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Produk?',
                    html: `Produk <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
                    icon: 'warning',
                    iconColor: '#e55353',
                    showCancelButton: true,
                    confirmButtonColor: '#e55353',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-trash mr-1"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const form = $('<form>', {
                            'method': 'POST',
                            'action': url
                        });

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
