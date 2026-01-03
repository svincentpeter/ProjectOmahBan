{{-- resources/views/home.blade.php / dashboard.blade.php --}}
@extends('layouts.app-flowbite')

@section('title', 'Dashboard')

{{-- Define content for the reusable breadcrumb --}}
@section('breadcrumb_items')
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-1 text-xs"></i>
            <span class="ms-1 text-sm font-medium text-zinc-500 md:ms-2 dark:text-zinc-400">Dashboard</span>
        </div>
    </li>
@endsection

{{-- Override the main breadcrumb yield to use our new component --}}
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('content')
    
    {{-- =========================
         WELCOME BANNER (Vibrant Gradient)
    ========================== --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-3xl p-8 mb-8 shadow-xl text-white">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold border border-white/30 shadow-lg flex items-center gap-2">
                        <i class="bi bi-stars text-yellow-300"></i>
                        <span>Dashboard Pemilik</span>
                    </span>
                    <span class="px-3 py-1 bg-emerald-500/80 backdrop-blur-md rounded-full text-xs font-bold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        Data Langsung
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black mb-2 tracking-tight drop-shadow-lg leading-tight">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-blue-100 text-lg font-medium max-w-xl opacity-90">
                    Mari pantau performa bisnis hari ini dan lihat perkembangan terbaru Omah Ban.
                </p>
            </div>
            
            {{-- Date & Time Card --}}
            <div class="flex flex-col items-end">
                <div class="bg-white/10 backdrop-blur-xl px-6 py-4 rounded-2xl border border-white/20 shadow-2xl">
                    <div class="text-center">
                        <div class="text-3xl font-black tracking-tight font-mono mb-1">
                            {{ now()->format('H:i') }}
                        </div>
                        <div class="text-xs font-semibold text-blue-100 uppercase tracking-wider">
                            {{ now()->isoFormat('dddd') }}
                        </div>
                        <div class="text-sm font-medium text-white/90 mt-1">
                            {{ now()->isoFormat('D MMMM Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl"></div>
            <svg class="absolute bottom-0 left-0 w-full h-24 text-white/5" viewBox="0 0 1440 120" fill="currentColor">
                <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,58.7C960,64,1056,64,1152,58.7C1248,53,1344,43,1392,37.3L1440,32L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
            </svg>
        </div>
    </div>

    {{-- =========================
         SUMMARY CARDS (Vibrant with Gradients)
    ========================== --}}
    @can('show_total_stats')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            {{-- TOTAL PENJUALAN --}}
            <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
                 <div class="flex justify-between items-start mb-4">
                     <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                         <i class="bi bi-graph-up-arrow text-2xl"></i>
                     </div>
                     <div class="flex flex-col items-end gap-1">
                         <span class="inline-flex items-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                             <i class="bi bi-arrow-up text-sm me-1"></i> +12.5%
                         </span>
                         <span class="text-xs text-zinc-400 font-medium">vs bulan lalu</span>
                     </div>
                 </div>
                 <div class="space-y-1">
                     <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Penjualan</p>
                     <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ format_currency($revenue) }}</h3>
                 </div>
                 {{-- Mini Trend Indicator --}}
                 <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                     <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Periode: {{ now()->format('F Y') }}</span>
                     <div class="flex gap-0.5">
                         <div class="w-1 h-6 bg-blue-200 dark:bg-blue-800 rounded-full"></div>
                         <div class="w-1 h-8 bg-blue-300 dark:bg-blue-700 rounded-full"></div>
                         <div class="w-1 h-10 bg-blue-400 dark:bg-blue-600 rounded-full"></div>
                         <div class="w-1 h-12 bg-blue-500 dark:bg-blue-500 rounded-full"></div>
                     </div>
                 </div>
            </div>

            {{-- KEUNTUNGAN BERSIH --}}
            <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-emerald-500">
                 <div class="flex justify-between items-start mb-4">
                     <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                         <i class="bi bi-wallet2 text-2xl"></i>
                     </div>
                     <div class="flex flex-col items-end gap-1">
                         <span class="inline-flex items-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                             <i class="bi bi-arrow-up text-sm me-1"></i> +8.2%
                         </span>
                         <span class="text-xs text-zinc-400 font-medium">margin keuntungan</span>
                     </div>
                 </div>
                 <div class="space-y-1">
                     <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Keuntungan Bersih</p>
                     <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ format_currency($profit) }}</h3>
                 </div>
                 <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                     <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Margin: {{ $revenue > 0 ? number_format(($profit / $revenue) * 100, 1) : 0 }}%</span>
                     <div class="flex gap-0.5">
                         <div class="w-1 h-8 bg-emerald-200 dark:bg-emerald-800 rounded-full"></div>
                         <div class="w-1 h-10 bg-emerald-300 dark:bg-emerald-700 rounded-full"></div>
                         <div class="w-1 h-9 bg-emerald-400 dark:bg-emerald-600 rounded-full"></div>
                         <div class="w-1 h-12 bg-emerald-500 dark:bg-emerald-500 rounded-full"></div>
                     </div>
                 </div>
            </div>

            {{-- TOTAL PRODUK --}}
            <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500">
                 <div class="flex justify-between items-start mb-4">
                     <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
                         <i class="bi bi-box-seam text-2xl"></i>
                     </div>
                     <div class="flex flex-col items-end gap-1">
                         <span class="inline-flex items-center bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-bold px-2.5 py-1 rounded-lg border border-blue-200 dark:border-blue-800">
                             <i class="bi bi-check-circle text-sm me-1"></i> Aktif
                         </span>
                         <span class="text-xs text-zinc-400 font-medium">status</span>
                     </div>
                 </div>
                 <div class="space-y-1">
                     <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Produk</p>
                     <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $products }}</h3>
                 </div>
                 <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                     <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Terdaftar di database</span>
                     <i class="bi bi-arrow-right text-orange-500 group-hover:translate-x-1 transition-transform"></i>
                 </div>
            </div>

            {{-- TOTAL KATEGORI --}}
            <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
                 <div class="flex justify-between items-start mb-4">
                     <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                         <i class="bi bi-grid-3x3-gap text-2xl"></i>
                     </div>
                     <div class="flex flex-col items-end gap-1">
                         <span class="inline-flex items-center bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 text-xs font-bold px-2.5 py-1 rounded-lg border border-purple-200 dark:border-purple-800">
                             <i class="bi bi-tags text-sm me-1"></i> Tipe
                         </span>
                         <span class="text-xs text-zinc-400 font-medium">kategori</span>
                     </div>
                 </div>
                 <div class="space-y-1">
                     <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Kategori</p>
                     <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $categories }}</h3>
                 </div>
                 <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                     <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Produk & Layanan</span>
                     <i class="bi bi-arrow-right text-purple-500 group-hover:translate-x-1 transition-transform"></i>
                 </div>
            </div>

        </div>
    @endcan

    {{-- QUICK ACTIONS (Aksi Cepat) --}}
    <div class="mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg border-l-4 border-indigo-500 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="bi bi-lightning-charge-fill text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">Aksi Cepat</h4>
                    <p class="text-xs text-zinc-500 font-medium">Jalan pintas untuk tugas yang sering dilakukan</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('products.create') }}" class="group flex items-center p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-blue-50 hover:border-blue-200 hover:shadow-sm transition-all">
                    <div class="w-10 h-10 rounded-lg bg-white text-blue-600 shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-plus-lg text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-zinc-700 dark:text-zinc-200 group-hover:text-blue-700">Produk</span>
                        <span class="block text-[10px] text-zinc-400 group-hover:text-blue-500">Tambah Baru</span>
                    </div>
                </a>
                
                <a href="{{ route('stock-opnames.create') }}" class="group flex items-center p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm transition-all">
                    <div class="w-10 h-10 rounded-lg bg-white text-emerald-600 shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-clipboard-check text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-zinc-700 dark:text-zinc-200 group-hover:text-emerald-700">Stok Opname</span>
                        <span class="block text-[10px] text-zinc-400 group-hover:text-emerald-500">Cek Stok</span>
                    </div>
                </a>
                
                <a href="{{ route('expenses.create') }}" class="group flex items-center p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-red-50 hover:border-red-200 hover:shadow-sm transition-all">
                    <div class="w-10 h-10 rounded-lg bg-white text-red-600 shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-wallet2 text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-zinc-700 dark:text-zinc-200 group-hover:text-red-700">Pengeluaran</span>
                        <span class="block text-[10px] text-zinc-400 group-hover:text-red-500">Catat Beban</span>
                    </div>
                </a>
                
                <a href="{{ route('reports.daily.index') }}" class="group flex items-center p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:border-purple-200 hover:shadow-sm transition-all">
                    <div class="w-10 h-10 rounded-lg bg-white text-purple-600 shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-zinc-700 dark:text-zinc-200 group-hover:text-purple-700">Laporan</span>
                        <span class="block text-[10px] text-zinc-400 group-hover:text-purple-500">Kas Harian</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- DASHBOARD WIDGETS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- LOW STOCK WIDGET --}}
        <livewire:dashboard.low-stock-widget />

        {{-- TOP PRODUCTS WIDGET --}}
        <livewire:dashboard.top-products-widget />
    </div>

    {{-- =========================
         CHART SECTION
    ========================== --}}
    @can('show_weekly_sales_purchases|show_month_overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            {{-- LINE CHART --}}
            @can('show_weekly_sales_purchases')
                <div class="lg:col-span-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                                    <i class="bi bi-graph-up text-lg"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Tren Penjualan</h5>
                                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-widest">7 Hari Terakhir</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-lg">Mingguan</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative h-[300px]">
                            <canvas id="salesPurchasesChart"></canvas>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- DOUGHNUT CHART --}}
            @can('show_month_overview')
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white shadow-md">
                                <i class="bi bi-pie-chart text-lg"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Ringkasan Bulanan</h5>
                                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-widest">{{ now()->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center">
                        <div class="relative h-[220px] w-[220px] mb-6">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                        <div class="w-full grid grid-cols-2 gap-3">
                            <div class="p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-center">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">Pemasukan</span>
                                </div>
                                <div class="text-sm font-black text-emerald-700 dark:text-emerald-300 truncate">{{ format_currency($revenue) }}</div>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-xl text-center">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span class="text-xs font-bold text-red-700 dark:text-red-400 uppercase">Pengeluaran</span>
                                </div>
                                <div class="text-sm font-black text-red-700 dark:text-red-300 truncate">{{ format_currency($profit) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
    @endcan

    {{-- =========================
         CASH FLOW CHART
    ========================== --}}
    @can('show_monthly_cashflow')
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-lg hover:shadow-xl transition-shadow mb-8">
            <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                            <i class="bi bi-cash-stack text-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Analisis Arus Kas</h5>
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-widest">Laporan Tahunan {{ now()->year }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold flex items-center border border-emerald-200 dark:border-emerald-800">
                            <i class="bi bi-arrow-down-circle-fill text-sm me-2"></i> Pemasukan
                        </span>
                        <span class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-xs font-bold flex items-center border border-red-200 dark:border-red-800">
                            <i class="bi bi-arrow-up-circle-fill text-sm me-2"></i> Pengeluaran
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="relative h-[300px]">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('third_party_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js" integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@push('page_scripts')
    @vite('resources/js/chart-config.js')
@endpush
