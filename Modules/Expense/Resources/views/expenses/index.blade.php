@extends('layouts.app')

@section('title', 'Pengeluaran Harian')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengeluaran Harian</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        {{-- Success/Error Alert --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="cil-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        {{-- Main Card --}}
        <div class="card shadow-sm">
            {{-- Card Header --}}
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <h5 class="mb-1 font-weight-bold">
                            <i class="cil-list mr-2 text-primary"></i>
                            Pengeluaran Harian
                        </h5>
                        <small class="text-muted">Kelola dan monitor pengeluaran operasional</small>
                    </div>
                    
                    @can('create_expenses')
                    <a href="{{ route('expenses.create') }}" 
                       class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Pengeluaran
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Quick Filters --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="filter-container">
                    {{-- Quick Filter Title --}}
                    <div class="d-flex align-items-center mb-3">
                        <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                        <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                    </div>

                    {{-- Quick Filter Pills --}}
                    <div class="quick-filters mb-3">
                        <a href="{{ route('expenses.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" 
                           class="filter-pill {{ (!request()->has('quick_filter') && request('from') == now()->toDateString()) || (!request()->filled('from')) ? 'active' : '' }}">
                            <i class="cil-calendar"></i>
                            <span>Hari Ini</span>
                        </a>

                        <a href="{{ route('expenses.index', ['quick_filter' => 'yesterday']) }}" 
                           class="filter-pill {{ request('quick_filter') == 'yesterday' ? 'active' : '' }}">
                            <i class="cil-calendar"></i>
                            <span>Kemarin</span>
                        </a>

                        <a href="{{ route('expenses.index', ['quick_filter' => 'this_week']) }}" 
                           class="filter-pill {{ request('quick_filter') == 'this_week' ? 'active' : '' }}">
                            <i class="cil-calendar"></i>
                            <span>Minggu Ini</span>
                        </a>

                        <a href="{{ route('expenses.index', ['quick_filter' => 'this_month']) }}" 
                           class="filter-pill {{ request('quick_filter') == 'this_month' ? 'active' : '' }}">
                            <i class="cil-calendar"></i>
                            <span>Bulan Ini</span>
                        </a>

                        <a href="{{ route('expenses.index', ['quick_filter' => 'last_month']) }}" 
                           class="filter-pill {{ request('quick_filter') == 'last_month' ? 'active' : '' }}">
                            <i class="cil-calendar"></i>
                            <span>Bulan Lalu</span>
                        </a>

                        <a href="{{ route('expenses.index', ['quick_filter' => 'all']) }}" 
                           class="filter-pill {{ request('quick_filter') == 'all' ? 'active' : '' }}">
                            <i class="cil-infinite"></i>
                            <span>Semua</span>
                        </a>

                        <button type="button" 
                                class="filter-pill filter-pill-custom" 
                                id="customFilterToggle">
                            <i class="cil-settings"></i>
                            <span>Custom</span>
                        </button>
                    </div>

                    {{-- Advanced Filter (Collapsed by default) --}}
                    <div id="advancedFilter" class="advanced-filter" style="display: none;">
                        <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small font-weight-semibold mb-1">Dari Tanggal</label>
                                <input type="date" 
                                       name="from" 
                                       class="form-control" 
                                       value="{{ $from ?? now()->toDateString() }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small font-weight-semibold mb-1">Sampai Tanggal</label>
                                <input type="date" 
                                       name="to" 
                                       class="form-control" 
                                       value="{{ $to ?? now()->toDateString() }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small font-weight-semibold mb-1">Kategori</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                        {{ $cat->category_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="cil-filter mr-2"></i> Terapkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="card-body py-3" style="background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);">
                <div class="row align-items-center text-white">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon mr-3">
                                <i class="cil-chart-pie" style="font-size: 2.5rem; opacity: 0.9;"></i>
                            </div>
                            <div>
                                <small class="d-block" style="opacity: 0.8;">Total Pengeluaran</small>
                                <h3 class="mb-0 font-weight-bold">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </h3>
                                @if($from && $to)
                                <small style="opacity: 0.9;">
                                    <i class="cil-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <small class="d-block" style="opacity: 0.8;">Total Transaksi</small>
                        <h4 class="mb-0 font-weight-bold">{{ $expenses->total() }} item</h4>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th width="100" class="border-0">Tanggal</th>
                                <th width="100" class="border-0">Ref</th>
                                <th width="150" class="border-0">Kategori</th>
                                <th class="border-0">Deskripsi</th>
                                <th width="100" class="border-0">Metode</th>
                                <th width="120" class="border-0">Bank</th>
                                <th width="150" class="text-right border-0">Nominal (Rp)</th>
                                <th width="100" class="text-center border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $e)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <i class="cil-calendar mr-1"></i>
                                        {{ $e->date->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $e->reference }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2" style="width: 28px; height: 28px; background: #4834DF; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="cil-tag text-white" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <span class="font-weight-semibold">
                                            {{ $e->category->category_name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $e->details }}</td>
                                <td>
                                    @if($e->payment_method === 'Tunai')
                                    <span class="badge badge-success">
                                        <i class="cil-wallet mr-1"></i> Tunai
                                    </span>
                                    @else
                                    <span class="badge badge-info">
                                        <i class="cil-bank mr-1"></i> Transfer
                                    </span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $e->bank_name ?? '-' }}</small></td>
                                <td class="text-right">
                                    <strong class="text-danger">{{ number_format($e->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    @include('expense::expenses.partials.actions', ['e' => $e])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3">Belum ada data pengeluaran</p>
                                        <small>Klik tombol "Tambah Pengeluaran" untuk mulai mencatat</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($expenses->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $expenses->firstItem() }} - {{ $expenses->lastItem() }} 
                        dari {{ $expenses->total() }} transaksi
                    </small>
                    {{ $expenses->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('page_styles')
<style>
    /* ========== Quick Filter Pills ========== */
    .quick-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        color: #4f5d73;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
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
        text-decoration: none;
    }

    .filter-pill.active {
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-color: #4834DF;
        color: white;
        box-shadow: 0 4px 15px rgba(72, 52, 223, 0.3);
    }

    .filter-pill.active:hover {
        background: linear-gradient(135deg, #3d2bb8 0%, #5a5fc9 100%);
        color: white;
    }

    .filter-pill-custom {
        background: white;
        border-color: #d0d0d0;
    }

    .filter-pill-custom:hover {
        border-color: #a0a0a0;
        background: #f5f5f5;
        color: #333;
    }

    /* ========== Advanced Filter ========== */
    .advanced-filter {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px dashed #e0e0e0;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ========== Table Enhancements ========== */
    .table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 12px 16px;
    }

    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03);
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .quick-filters {
            flex-direction: column;
        }

        .filter-pill {
            width: 100%;
            justify-content: center;
        }
    }

    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(() => $('.alert').fadeOut('slow'), 3000);

    // Toggle custom filter form
    $('#customFilterToggle').on('click', function() {
        $('#advancedFilter').slideToggle(300);
        $(this).toggleClass('active');
    });
});
</script>
@endpush
