@extends('layouts.app')

@section('title', 'Detail Stock Opname - ' . $stockOpname->reference)

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.index') }}">Stock Opname</a></li>
        <li class="breadcrumb-item active">{{ $stockOpname->reference }}</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    {{-- HEADER CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="mb-1">
                        <i class="bi bi-clipboard-check"></i> {{ $stockOpname->reference }}
                    </h3>
                    <div class="text-muted">
                        <i class="bi bi-calendar3"></i> {{ $stockOpname->opname_date->format('d F Y') }}
                        &nbsp;|&nbsp;
                        <i class="bi bi-person"></i> PIC: {{ $stockOpname->pic->name }}
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    {!! $stockOpname->status_badge !!}
                    
                    @if($stockOpname->status === 'completed')
                        <div class="mt-2 text-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <small>Selesai pada {{ $stockOpname->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Info Kolom Kiri --}}
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150" class="text-muted">Jenis Opname:</td>
                            <td class="font-weight-bold">
                                @if($stockOpname->scope_type === 'all')
                                    <i class="bi bi-box-seam text-primary"></i> Semua Produk
                                @elseif($stockOpname->scope_type === 'category')
                                    <i class="bi bi-collection text-warning"></i> Per Kategori
                                @else
                                    <i class="bi bi-list-check text-success"></i> Custom
                                @endif
                            </td>
                        </tr>
                        @if($stockOpname->scope_type === 'category' && $stockOpname->scope_ids)
                            <tr>
                                <td class="text-muted">Kategori:</td>
                                <td>
                                    @php
                                        $categories = \Modules\Product\Entities\Category::whereIn('id', $stockOpname->scope_ids)->get();
                                    @endphp
                                    @foreach($categories as $cat)
                                        <span class="badge badge-secondary">{{ $cat->category_name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Total Item:</td>
                            <td class="font-weight-bold">{{ $stockOpname->items->count() }} produk</td>
                        </tr>
                    </table>
                </div>

                {{-- Info Kolom Kanan --}}
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150" class="text-muted">Progress:</td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: {{ $stockOpname->completion_percentage }}%">
                                        <strong>{{ $stockOpname->completion_percentage }}%</strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @if($stockOpname->supervisor_id)
                            <tr>
                                <td class="text-muted">Supervisor:</td>
                                <td class="font-weight-bold">{{ $stockOpname->supervisor->name }}</td>
                            </tr>
                        @endif
                        @if($stockOpname->notes)
                            <tr>
                                <td class="text-muted">Catatan:</td>
                                <td>{{ $stockOpname->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    @can('edit_stock_opname')
                        @if($stockOpname->status === 'draft')
                            <a href="{{ route('stock-opnames.edit', $stockOpname->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @endif

                        @if(in_array($stockOpname->status, ['draft', 'in_progress']))
                            <a href="{{ route('stock-opnames.counting', $stockOpname->id) }}" class="btn btn-primary">
                                <i class="bi bi-calculator"></i> 
                                {{ $stockOpname->status === 'draft' ? 'Mulai Counting' : 'Lanjutkan Counting' }}
                            </a>
                        @endif
                    @endcan
                </div>

                <div>
                    @can('show_stock_opname')
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('stock-opnames.export-excel', $stockOpname->id) }}">
                                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('stock-opnames.export-pdf', $stockOpname->id) }}" target="_blank">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:window.print()">
                                    <i class="bi bi-printer"></i> Print
                                </a>
                            </div>
                        </div>
                    @endcan

                    @can('delete_stock_opname')
                        @if($stockOpname->status === 'draft')
                            <button type="button" class="btn btn-danger" id="delete-btn">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY STATISTICS --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Item
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stockOpname->items->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Cocok (Match)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['match'] }}
                            </div>
                            <small class="text-muted">
                                {{ $stockOpname->items->count() > 0 ? round(($summary['match'] / $stockOpname->items->count()) * 100) : 0 }}%
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Surplus (Lebih)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['surplus'] }}
                            </div>
                            <small class="text-muted">
                                Total: +{{ $stockOpname->items->where('variance_type', 'surplus')->sum('variance_qty') }} unit
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-up-circle text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Shortage (Kurang)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['shortage'] }}
                            </div>
                            <small class="text-muted">
                                Total: {{ $stockOpname->items->where('variance_type', 'shortage')->sum('variance_qty') }} unit
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-down-circle text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS CONTENT --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="items-tab" data-toggle="tab" href="#items" role="tab">
                        <i class="bi bi-list-ul"></i> Detail Item 
                        <span class="badge badge-primary">{{ $stockOpname->items->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="variance-tab" data-toggle="tab" href="#variance" role="tab">
                        <i class="bi bi-exclamation-triangle"></i> Variance Only
                        <span class="badge badge-warning">{{ $summary['surplus'] + $summary['shortage'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#timeline" role="tab">
                        <i class="bi bi-clock-history"></i> Timeline
                    </a>
                </li>
                @if($stockOpname->status === 'completed')
                    <li class="nav-item">
                        <a class="nav-link" id="adjustments-tab" data-toggle="tab" href="#adjustments" role="tab">
                            <i class="bi bi-clipboard-check"></i> Adjustments
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- TAB 1: ALL ITEMS --}}
                <div class="tab-pane fade show active" id="items" role="tabpanel">
                    @include('adjustment::stock-opname.partials._items-table', [
                        'items' => $stockOpname->items
                    ])
                </div>

                {{-- TAB 2: VARIANCE ONLY --}}
                <div class="tab-pane fade" id="variance" role="tabpanel">
                    @include('adjustment::stock-opname.partials._items-table', [
                        'items' => $stockOpname->items->filter(fn($i) => $i->variance_qty != 0)
                    ])
                </div>

                {{-- TAB 3: TIMELINE --}}
                <div class="tab-pane fade" id="timeline" role="tabpanel">
                    @include('adjustment::stock-opname.partials._timeline', [
                        'logs' => $stockOpname->logs
                    ])
                </div>

                {{-- TAB 4: ADJUSTMENTS (jika completed) --}}
                @if($stockOpname->status === 'completed')
                    <div class="tab-pane fade" id="adjustments" role="tabpanel">
                        @include('adjustment::stock-opname.partials._adjustments', [
                            'items' => $stockOpname->items->whereNotNull('adjustment_id')
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<form action="{{ route('stock-opnames.destroy', $stockOpname->id) }}" method="POST" id="delete-form">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('page_styles')
<style>
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    .border-left-info { border-left: 4px solid #36b9cc; }
    .border-left-danger { border-left: 4px solid #e74a3b; }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
        font-weight: 600;
    }

    @media print {
        .card-footer,
        .btn,
        .nav-tabs,
        .breadcrumb {
            display: none !important;
        }
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    // DELETE BUTTON
    $('#delete-btn').on('click', function() {
        Swal.fire({
            title: 'Hapus Stock Opname?',
            text: "Data tidak dapat dikembalikan setelah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form').submit();
            }
        });
    });

    // Auto-activate tab from URL hash
    const hash = window.location.hash;
    if (hash) {
        $(`.nav-link[href="${hash}"]`).tab('show');
    }

    // Update URL hash when tab changes
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        window.location.hash = e.target.hash;
    });
});
</script>
@endpush
