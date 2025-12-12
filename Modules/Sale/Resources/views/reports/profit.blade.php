@extends('layouts.app-flowbite')

@section('title', 'Laporan Laba Kotor')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <i class="bi bi-house-door-fill mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Laporan</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Laba Kotor</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="px-4 pt-6">
        {{-- Filter Section --}}
        <div class="p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Laporan</h3>
            </div>
            
            <form action="{{ route('sales.reports.profit') }}" method="GET">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Mulai</label>
                        <div class="relative max-w-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                        </div>
                    </div>
                    <div>
                        <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Selesai</label>
                        <div class="relative max-w-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-0.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 w-full flex items-center justify-center gap-2">
                            <i class="bi bi-funnel"></i>
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Penjualan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-all hover:shadow-md hover:-translate-y-1 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-400 to-indigo-600"></div>
                <div class="p-4 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <i class="bi bi-cash-stack text-3xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ format_currency($totalPenjualan) }}</h3>
                </div>
            </div>

            <!-- Total Modal (HPP) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-all hover:shadow-md hover:-translate-y-1 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-yellow-400 to-orange-500"></div>
                <div class="p-4 rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <i class="bi bi-wallet2 text-3xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Modal (HPP)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ format_currency($totalHpp) }}</h3>
                </div>
            </div>

            <!-- Total Laba Kotor -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-all hover:shadow-md hover:-translate-y-1 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-green-400 to-emerald-600"></div>
                <div class="p-4 rounded-xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                    <i class="bi bi-graph-up-arrow text-3xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Laba Kotor</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ format_currency($totalLaba) }}</h3>
                </div>
            </div>
        </div>

        {{-- Detail Table --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rincian Laba per Jenis Sumber</h3>
            </div>
            
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Jenis Sumber</th>
                            <th scope="col" class="px-6 py-3">Jumlah Item Terjual</th>
                            <th scope="col" class="px-6 py-3">Total Penjualan</th>
                            <th scope="col" class="px-6 py-3">Total Laba</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($labaBreakdown as $type => $data)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ ucfirst($type) }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $data['count'] }} item
                                </td>
                                <td class="px-6 py-4">
                                    {{ format_currency($data['total_penjualan']) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    {{ format_currency($data['total_laba']) }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p>Tidak ada data penjualan pada rentang tanggal ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection