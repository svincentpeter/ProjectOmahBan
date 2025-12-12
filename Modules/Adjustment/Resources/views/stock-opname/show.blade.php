@extends('layouts.app-flowbite')

@section('title', 'Detail Stock Opname - ' . $stockOpname->reference)

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('breadcrumb_items')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.index') }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">Stock Opname</a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <span class="text-sm font-bold text-zinc-900">{{ $stockOpname->reference }}</span>
        </div>
    </li>
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- HEADER CARD --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 mb-6">
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-black flex items-center gap-2">
                        <i class="bi bi-clipboard-check text-blue-600"></i> {{ $stockOpname->reference }}
                    </h3>
                    <div class="flex items-center gap-3 mt-2 text-sm text-zinc-600">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-calendar3"></i> {{ $stockOpname->opname_date->format('d F Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-person"></i> PIC: {{ $stockOpname->pic->name }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $statusStyles = [
                            'draft' => 'bg-zinc-100 text-zinc-700',
                            'in_progress' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-red-100 text-red-700'
                        ];
                        $statusIcons = [
                            'draft' => 'bi-file-earmark',
                            'in_progress' => 'bi-arrow-repeat',
                            'completed' => 'bi-check-circle-fill',
                            'cancelled' => 'bi-x-circle-fill'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold {{ $statusStyles[$stockOpname->status] ?? 'bg-zinc-100 text-zinc-700' }}">
                        <i class="bi {{ $statusIcons[$stockOpname->status] ?? 'bi-circle' }} me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $stockOpname->status)) }}
                    </span>
                    @if($stockOpname->status === 'completed')
                        <div class="mt-2 text-sm text-emerald-600 font-medium">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Selesai pada {{ $stockOpname->updated_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Info Kolom Kiri --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-zinc-500 w-32">Jenis Opname:</span>
                        <span class="font-bold text-black">
                            @if($stockOpname->scope_type === 'all')
                                <i class="bi bi-box-seam text-blue-600 me-1"></i> Semua Produk
                            @elseif($stockOpname->scope_type === 'category')
                                <i class="bi bi-collection text-amber-600 me-1"></i> Per Kategori
                            @else
                                <i class="bi bi-list-check text-emerald-600 me-1"></i> Custom
                            @endif
                        </span>
                    </div>
                    @if($stockOpname->scope_type === 'category' && $stockOpname->scope_ids)
                        <div class="flex items-start gap-3">
                            <span class="text-sm text-zinc-500 w-32">Kategori:</span>
                            <div class="flex flex-wrap gap-1">
                                @php
                                    $categories = \Modules\Product\Entities\Category::whereIn('id', $stockOpname->scope_ids)->get();
                                @endphp
                                @foreach($categories as $cat)
                                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-700 rounded-full text-xs font-medium">{{ $cat->category_name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-zinc-500 w-32">Total Item:</span>
                        <span class="font-bold text-black">{{ $stockOpname->items->count() }} produk</span>
                    </div>
                </div>

                {{-- Info Kolom Kanan --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-zinc-500 w-32">Progress:</span>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-bold text-zinc-700">{{ $stockOpname->completion_percentage }}%</span>
                            </div>
                            <div class="w-full bg-zinc-200 rounded-full h-2.5">
                                @php
                                    $progressColor = $stockOpname->completion_percentage < 50 ? 'bg-red-500' : 
                                                    ($stockOpname->completion_percentage < 100 ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <div class="{{ $progressColor }} h-2.5 rounded-full transition-all" style="width: {{ $stockOpname->completion_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    @if($stockOpname->supervisor_id)
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-zinc-500 w-32">Supervisor:</span>
                            <span class="font-bold text-black">{{ $stockOpname->supervisor->name }}</span>
                        </div>
                    @endif
                    @if($stockOpname->notes)
                        <div class="flex items-start gap-3">
                            <span class="text-sm text-zinc-500 w-32">Catatan:</span>
                            <span class="text-zinc-700">{{ $stockOpname->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="p-6 bg-zinc-50 rounded-b-2xl border-t border-zinc-100">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('stock-opnames.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-zinc-700 font-medium rounded-xl hover:bg-zinc-50 transition-all">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>

                    @can('edit_stock_opname')
                        @if($stockOpname->status === 'draft')
                            <a href="{{ route('stock-opnames.edit', $stockOpname->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white font-medium rounded-xl hover:bg-amber-600 transition-all">
                                <i class="bi bi-pencil me-2"></i> Edit
                            </a>
                        @endif

                        @if(in_array($stockOpname->status, ['draft', 'in_progress']))
                            <a href="{{ route('stock-opnames.counting', $stockOpname->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all">
                                <i class="bi bi-calculator me-2"></i> 
                                {{ $stockOpname->status === 'draft' ? 'Mulai Counting' : 'Lanjutkan Counting' }}
                            </a>
                        @endif
                    @endcan
                </div>

                <div class="flex flex-wrap gap-2">
                    @can('show_stock_opname')
                        <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-zinc-700 font-medium rounded-xl hover:bg-zinc-50 transition-all">
                            <i class="bi bi-printer me-2"></i> Print
                        </button>
                    @endcan

                    @can('delete_stock_opname')
                        @if($stockOpname->status === 'draft')
                            <button type="button" id="delete-btn" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-all">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY STATISTICS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border-l-4 border-blue-500 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Total Item</p>
                    <p class="text-2xl font-bold text-black">{{ $stockOpname->items->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-box-seam text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-l-4 border-emerald-500 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Cocok (Match)</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $summary['match'] }}</p>
                    <p class="text-xs text-zinc-500">
                        {{ $stockOpname->items->count() > 0 ? round(($summary['match'] / $stockOpname->items->count()) * 100) : 0 }}%
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-l-4 border-cyan-500 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Surplus (Lebih)</p>
                    <p class="text-2xl font-bold text-cyan-600">{{ $summary['surplus'] }}</p>
                    <p class="text-xs text-zinc-500">
                        Total: +{{ $stockOpname->items->where('variance_type', 'surplus')->sum('variance_qty') }} unit
                    </p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-arrow-up-circle text-cyan-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-l-4 border-red-500 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase text-zinc-500 font-semibold">Shortage (Kurang)</p>
                    <p class="text-2xl font-bold text-red-600">{{ $summary['shortage'] }}</p>
                    <p class="text-xs text-zinc-500">
                        Total: {{ $stockOpname->items->where('variance_type', 'shortage')->sum('variance_qty') }} unit
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-arrow-down-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS CONTENT --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50">
        <div class="border-b border-zinc-100">
            <ul class="flex flex-wrap -mb-px text-sm font-medium" id="stockOpnameTabs" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 border-blue-600 text-blue-600 font-bold rounded-t-lg" id="items-tab" data-tabs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="true">
                        <i class="bi bi-list-ul"></i> Detail Item 
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $stockOpname->items->count() }}</span>
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 font-medium rounded-t-lg" id="variance-tab" data-tabs-target="#variance" type="button" role="tab" aria-controls="variance" aria-selected="false">
                        <i class="bi bi-exclamation-triangle"></i> Variance Only
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">{{ $summary['surplus'] + $summary['shortage'] }}</span>
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 font-medium rounded-t-lg" id="timeline-tab" data-tabs-target="#timeline" type="button" role="tab" aria-controls="timeline" aria-selected="false">
                        <i class="bi bi-clock-history"></i> Timeline
                    </button>
                </li>
                @if($stockOpname->status === 'completed')
                    <li role="presentation">
                        <button class="inline-flex items-center gap-2 p-4 border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 font-medium rounded-t-lg" id="adjustments-tab" data-tabs-target="#adjustments" type="button" role="tab" aria-controls="adjustments" aria-selected="false">
                            <i class="bi bi-clipboard-check"></i> Adjustments
                        </button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="p-6" id="stockOpnameTabContent">
            {{-- TAB 1: ALL ITEMS --}}
            <div class="" id="items" role="tabpanel" aria-labelledby="items-tab">
                @include('adjustment::stock-opname.partials._items-table', [
                    'items' => $stockOpname->items
                ])
            </div>

            {{-- TAB 2: VARIANCE ONLY --}}
            <div class="hidden" id="variance" role="tabpanel" aria-labelledby="variance-tab">
                @include('adjustment::stock-opname.partials._items-table', [
                    'items' => $stockOpname->items->filter(fn($i) => $i->variance_qty != 0)
                ])
            </div>

            {{-- TAB 3: TIMELINE --}}
            <div class="hidden" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                @include('adjustment::stock-opname.partials._timeline', [
                    'logs' => $stockOpname->logs
                ])
            </div>

            {{-- TAB 4: ADJUSTMENTS (jika completed) --}}
            @if($stockOpname->status === 'completed')
                <div class="hidden" id="adjustments" role="tabpanel" aria-labelledby="adjustments-tab">
                    @include('adjustment::stock-opname.partials._adjustments', [
                        'items' => $stockOpname->items->whereNotNull('adjustment_id')
                    ])
                </div>
            @endif
        </div>
    </div>

{{-- DELETE CONFIRMATION FORM --}}
<form action="{{ route('stock-opnames.destroy', $stockOpname->id) }}" method="POST" id="delete-form" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    // Tab switching
    const tabs = document.querySelectorAll('[data-tabs-target]');
    const tabContents = document.querySelectorAll('#stockOpnameTabContent > div');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset all tabs
            tabs.forEach(t => {
                t.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
                t.classList.add('border-transparent', 'text-zinc-500', 'font-medium');
            });
            tabContents.forEach(c => c.classList.add('hidden'));

            // Activate clicked tab
            tab.classList.remove('border-transparent', 'text-zinc-500', 'font-medium');
            tab.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
            document.querySelector(tab.dataset.tabsTarget).classList.remove('hidden');
        });
    });

    // DELETE BUTTON
    $('#delete-btn').on('click', function() {
        Swal.fire({
            title: 'Hapus Stock Opname?',
            html: 'Data tidak dapat dikembalikan setelah dihapus!<br><small class="text-red-500">Aksi ini permanen.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $('#delete-form').submit();
            }
        });
    });

    // Auto-activate tab from URL hash
    const hash = window.location.hash;
    if (hash) {
        const targetTab = document.querySelector(`[data-tabs-target="${hash}"]`);
        if (targetTab) targetTab.click();
    }
});
</script>
@endpush

@push('page_styles')
<style>
    @media print {
        .btn, button, .rounded-b-2xl.bg-zinc-50, nav, .border-b-2 {
            display: none !important;
        }
    }
</style>
@endpush
