@extends('layouts.app-flowbite')

@section('title', 'Semua Penjualan')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Penjualan', 'url' => route('sales.index')],
                ['text' => 'Daftar Penjualan', 'url' => '#', 'icon' => 'bi bi-cart-check'],
            ]
        ])
    @endsection

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
        {{-- Total Penjualan --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-400 to-indigo-600"></div>
            <div class="p-3 mr-4 text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30">
                <i class="bi bi-cash-stack text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Penjualan</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-total">
                    Rp 0
                </p>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-green-400 to-emerald-600"></div>
            <div class="p-3 mr-4 text-white bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/30">
                <i class="bi bi-graph-up-arrow text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Profit</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-profit">
                    Rp 0
                </p>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-cyan-400 to-blue-500"></div>
            <div class="p-3 mr-4 text-white bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl shadow-lg shadow-cyan-500/30">
                <i class="bi bi-cart3 text-2xl"></i>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Transaksi</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-count">
                    0
                </p>
            </div>
        </div>
    </div>

    @php
        $months = [];
        $now = \Carbon\Carbon::now()->startOfMonth();
        for ($i = 0; $i < 24; $i++) {
            $c = $now->copy()->subMonths($i);
            $months[$c->format('Y-m')] = $c->locale('id')->translatedFormat('F Y');
        }
    @endphp

    {{-- Filter Card --}}
    @include('layouts.filter-card', [
        'action' => route('sales.index'),
        'title' => 'Filter Data Penjualan',
        'icon' => 'bi bi-funnel',
        'quickFilters' => [
            ['label' => 'Hari Ini', 'url' => request()->fullUrlWithQuery(['preset' => 'today']), 'param' => 'preset', 'value' => 'today', 'icon' => 'bi bi-clock'],
            ['label' => 'Minggu Ini', 'url' => request()->fullUrlWithQuery(['preset' => 'this_week']), 'param' => 'preset', 'value' => 'this_week', 'icon' => 'bi bi-calendar-week'],
            ['label' => 'Bulan Ini', 'url' => request()->fullUrlWithQuery(['preset' => 'this_month']), 'param' => 'preset', 'value' => 'this_month', 'icon' => 'bi bi-calendar-month'],
        ],
        'filters' => [
            ['name' => 'month', 'label' => 'Pilih Bulan', 'type' => 'select', 'options' => $months, 'placeholder' => 'Pilih Bulan'],
            ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => request('from')],
            ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => request('to')],
            ['name' => 'has_adjustment', 'label' => 'Ada Diskon', 'type' => 'select', 'options' => ['1' => 'Ya, Ada Diskon'], 'placeholder' => 'Semua'],
            ['name' => 'has_manual', 'label' => 'Input Manual', 'type' => 'select', 'options' => ['1' => 'Ya, Input Manual'], 'placeholder' => 'Semua'],
        ]
    ])

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-receipt-cutoff mr-2 text-blue-600"></i>
                    Daftar Transaksi Penjualan
                </h3>
                <p class="text-xs text-gray-500 mt-1">Kelola data transaksi penjualan harian</p>
            </div>
            <a href="{{ route('sales.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-blue-500/30 hover:scale-[1.02] transition-transform duration-200">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Penjualan
            </a>
        </div>
        
        <div class="p-4">
            {{ $dataTable->table() }}
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {{ $dataTable->scripts() }}
    
    <script>
        const SUMMARY_URL = "{{ route('sales.summary') }}";

        function formatRupiah(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(Number(n || 0));
        }

        // Global Action Functions
        window.toggleActionDropdown = function(event, id) {
            event.stopPropagation();
            const button = event.currentTarget;
            const dropdown = document.getElementById('dropdown-' + id);
            
            // Close all other dropdowns
            document.querySelectorAll('.action-dropdown').forEach(d => {
                if (d.id !== 'dropdown-' + id) {
                    d.style.display = 'none';
                }
            });
            
            // Generate position
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                const rect = button.getBoundingClientRect();
                // Positioning logic: align right edge of dropdown with right edge of button (approx)
                // or just fixed offset. Since button is small, let's center or align right.
                dropdown.style.top = (rect.bottom + 5) + 'px';
                dropdown.style.left = (rect.right - 224) + 'px'; // 224px is w-56
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        };

        $(document).ready(function() {
            // Close dropdowns on outside click
            $(document).on('click', function(event) {
                if (!$(event.target).closest('[id^="dropdown-"]').length && !$(event.target).closest('button').length) {
                    $('.action-dropdown').hide();
                }
            });

            // Handle scroll to hide dropdowns
            $('.dataTables_scrollBody').on('scroll', function() {
                $('.action-dropdown').hide();
            });

            // Custom Filter Injection
            const table = window.LaravelDataTables['sales-table'];
            
            // Helper to collect current values
            function getFilterValues() {
                // If using layouts.filter-card, we might have standard inputs.
                // Or if we clicked a generic pill (quick filter) it sets a param.
                // We'll read from the active form or URL params logic if using purely AJAX?
                // The filter-card component uses standard inputs. We can just read them.
                return {
                    preset: '{{ request('preset') }}', // This might be stale if only JS updates it? filter-card reloads page...
                    // Wait, filter-card normally reloads the page on apply/pill click.
                    // So request('preset') IS correct for initial load.
                    // But if we want AJAX filtering without reload, we need to intercept.
                    // The standard filter-card reloads. So preXhr just needs to send current inputs.
                    
                    month: $('input[name="month"]').val(),
                    from: $('input[name="from"]').val(),
                    to: $('input[name="to"]').val(),
                    has_adjustment: $('select[name="has_adjustment"]').val(),
                    has_manual: $('select[name="has_manual"]').val(),
                };
            }

            table.on('preXhr.dt', function ( e, settings, data ) {
                const filters = getFilterValues();
                // Merge into data
                Object.assign(data, filters);
                // Also support legacy logic if backend expects strict 'filter' array?
                // SalesDataTable uses $f['key'] ?? request('key').
                // So flat params like data.month work fine.
            });

            // Handle Stats Summary Update
            table.on('draw', function() {
                const params = table.ajax.params();
                // params contains all sent data including filters
                // We need to extract the filters we care about
                const summaryParams = {
                    preset: params.preset,
                    month: params.month,
                    from: params.from,
                    to: params.to,
                    has_adjustment: params.has_adjustment,
                    has_manual: params.has_manual,
                    // If filter array was used:
                    // ...params.filter 
                };

                $.get(SUMMARY_URL, { filter: summaryParams })
                    .done(function(d) {
                        $('#sum-total').text(formatRupiah(d.total_penjualan || 0));
                        $('#sum-profit').text(formatRupiah(d.total_profit || 0));
                        $('#sum-count').text(d.total_transaksi || 0);
                    })
                    .fail(function() {
                        console.error('Failed to fetch summary');
                    });
                
                // Re-style row detail buttons
                $('button.btn-expand').addClass('text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors shadow-sm')
                    .removeClass('btn btn-sm btn-primary');
            });

             // Handle Row Detail Expand
             $('#sales-table tbody').on('click', '.btn-expand', function() {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const url = $(this).data('url');
                const btn = $(this);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    btn.html('<i class="bi bi-chevron-down"></i>');
                } else {
                    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split animate-spin"></i>');
                    
                    $.get(url, function(html) {
                        row.child(html).show();
                        tr.addClass('shown');
                        btn.html('<i class="bi bi-chevron-up"></i>');
                    }).always(function() {
                        btn.prop('disabled', false);
                    });
                }
            });
        });
    </script>
@endpush
