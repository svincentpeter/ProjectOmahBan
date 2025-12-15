@extends('layouts.app-flowbite')

@section('title', 'Laporan Penjualan')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Laporan Penjualan', 'url' => '#', 'icon' => 'bi bi-receipt'],
        ]
    ])
@endsection

@section('content')
    {{-- Main Control Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
             <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-receipt text-blue-600"></i>
                    Laporan Penjualan
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                        <i class="bi bi-calendar-range mr-1.5"></i>
                        Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all shadow-sm">
                    <i class="bi bi-printer mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                         <label for="from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dari Tanggal</label>
                         <input type="date" id="from" name="from" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ $from }}" required>
                    </div>
                    <div>
                        <label for="to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sampai Tanggal</label>
                        <input type="date" id="to" name="to" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ $to }}" required>
                    </div>
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="status" name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Semua Status</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="md:col-span-1">
                     <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">
                        <i class="bi bi-filter mr-2"></i> Tampilkan
                    </button>
                </div>
            </div>
            {{-- Quick Filters --}}
            <div class="mt-4 flex flex-wrap gap-2">
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Hari Ini</a>
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">7 Hari Terakhir</a>
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Bulan Ini</a>
            </div>
        </form>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="relative bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-receipt text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-list-check text-2xl"></i>
                </div>
                <p class="text-purple-100 text-sm font-medium mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-bold">{{ number_format($summary['count']) }}</h3>
            </div>
        </div>

        <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-wallet2 text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-cash-coin text-2xl"></i>
                </div>
                <p class="text-blue-100 text-sm font-medium mb-1">Total Penjualan</p>
                <h3 class="text-2xl font-bold">{{ format_currency($summary['total']) }}</h3>
            </div>
        </div>

        <div class="relative bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-check-circle text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-cash-stack text-2xl"></i>
                </div>
                <p class="text-green-100 text-sm font-medium mb-1">Terbayar</p>
                <h3 class="text-2xl font-bold">{{ format_currency($summary['paid']) }}</h3>
            </div>
        </div>

        <div class="relative bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-exclamation-circle text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-hourglass-split text-2xl"></i>
                </div>
                <p class="text-orange-100 text-sm font-medium mb-1">Belum Lunas</p>
                <h3 class="text-2xl font-bold">{{ format_currency($summary['due']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg">
                    <i class="bi bi-table"></i>
                </div>
                Daftar Penjualan
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left mt-4">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 border-b">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Dibayar</th>
                        <th class="px-4 py-3 text-right">Sisa</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:underline">{{ $sale->reference }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $sale->customer_name ?: 'Walk-in' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ format_currency($sale->total_amount) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ format_currency($sale->paid_amount) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ format_currency($sale->due_amount) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($sale->payment_status == 'Paid')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                            @elseif($sale->payment_status == 'Partial')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Sebagian</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                            Tidak ada data penjualan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
