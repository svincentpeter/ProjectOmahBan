@extends('layouts.app-flowbite')

@section('title', 'Notification Center')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Operasional', 'url' => '#'],
        ['text' => 'Notifikasi', 'url' => route('notifications.index'), 'icon' => 'bi bi-bell-fill']
    ]])
@endsection

@section('content')
    <!-- Stats Grid (Line Color - bukan full gradient) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Belum Dibaca (clickable) --}}
    <a href="{{ route('notifications.index', ['is_read' => '0']) }}"
       class="group bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
              border-l-4 border-l-blue-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 dark:text-gray-300 mb-1">Pesan Belum Dibaca</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ $stats['unread_count'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Klik untuk melihat yang baru</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100
                        dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-900/50">
                <i class="bi bi-envelope text-xl"></i>
            </div>
        </div>
    </a>

    {{-- Hari Ini --}}
    <div class="group bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-violet-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 dark:text-gray-300 mb-1">Aktivitas Hari Ini</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ $stats['today_count'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Masuk hari ini</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-violet-50 text-violet-700 ring-1 ring-violet-100
                        dark:bg-violet-900/30 dark:text-violet-300 dark:ring-violet-900/50">
                <i class="bi bi-calendar4-week text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Menunggu Review (clickable) --}}
    <a href="{{ route('notifications.index', ['is_reviewed' => '0']) }}"
       class="group bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
              border-l-4 border-l-amber-500">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 dark:text-gray-300 mb-1">Menunggu Review</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ $stats['unreviewed_count'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Butuh tindakan</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100
                        dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-900/50">
                <i class="bi bi-clipboard-check text-xl"></i>
            </div>
        </div>
    </a>

    {{-- Critical (clickable) --}}
    <a href="{{ route('notifications.index', ['severity' => 'critical']) }}"
       class="group bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
              border-l-4 border-l-rose-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 dark:text-gray-300 mb-1">Notifikasi Penting</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ $stats['critical_count'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Prioritas tinggi</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-rose-50 text-rose-700 ring-1 ring-rose-100
                        dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-900/50">
                <i class="bi bi-shield-exclamation text-xl"></i>
            </div>
        </div>
    </a>

</div>


    <!-- Manual Flowbite Table Section -->
    <section class="bg-gray-50 dark:bg-gray-900 p-0 sm:p-0">
        <div class="mx-auto max-w-screen-xl relative shadow-md sm:rounded-lg overflow-hidden bg-white dark:bg-gray-800">
            <!-- Table Header -->
            <div class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4 border-b dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-100 rounded-lg dark:bg-primary-900">
                        <i class="bi bi-bell-fill text-2xl text-primary-600 dark:text-primary-300"></i>
                    </div>
                    <div>
                        <h5 class="mr-3 text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Daftar Notifikasi</h5>
                        <p class="text-base text-gray-500 dark:text-gray-400">Pantau dan kelola semua notifikasi sistem Anda</p>
                    </div>
                </div>
                <!-- Export Button (Moved from Toolbar) -->
                <button type="button" onclick="window.location.href='{{ route('notifications.export') }}'" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                    <svg class="h-3.5 w-3.5 mr-2 -ml-1" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                    </svg>
                    Export Data
                </button>
            </div>

            <!-- Toolbar (Search & Filters) -->
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center" method="GET" action="{{ route('notifications.index') }}">
                        <!-- Preserve other filters as hidden inputs -->
                        @if(request('is_read')) <input type="hidden" name="is_read" value="{{ request('is_read') }}"> @endif
                        @if(request('is_reviewed')) <input type="hidden" name="is_reviewed" value="{{ request('is_reviewed') }}"> @endif
                        @if(request('severity')) <input type="hidden" name="severity" value="{{ request('severity') }}"> @endif

                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="simple-search" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Cari notifikasi..." required="">
                        </div>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    
                    <div class="flex items-center space-x-3 w-full md:w-auto">
                        <!-- Actions Dropdown -->
                        <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                            Aksi
                        </button>
                        <div id="actionsDropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actionsDropdownButton">
                                <li>
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                            Tandai Semua Dibaca
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Filter Dropdown -->
                        <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                            </svg>
                            Filter
                            <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                        </button>
                        <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Filter Status</h6>
                            <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                <li class="flex items-center">
                                    <a href="{{ route('notifications.index', ['is_read' => '0']) }}" class="flex items-center w-full hover:text-primary-600">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                            <span class="text-gray-700 dark:text-gray-200">Belum Dibaca</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="flex items-center">
                                    <a href="{{ route('notifications.index', ['is_read' => '1']) }}" class="flex items-center w-full hover:text-primary-600">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                            <span class="text-gray-700 dark:text-gray-200">Sudah Dibaca</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="flex items-center">
                                    <a href="{{ route('notifications.index', ['severity' => 'critical']) }}" class="flex items-center w-full hover:text-primary-600">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                            <span class="text-gray-700 dark:text-gray-200">Kritis</span>
                                        </div>
                                    </a>
                                </li>
                                 <li class="flex items-center border-t pt-2 mt-2">
                                    <a href="{{ route('notifications.index') }}" class="text-primary-600 hover:underline text-xs w-full text-center block">Reset Filter</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Waktu</th>
                            <th scope="col" class="px-4 py-3">Tingkat</th>
                            <th scope="col" class="px-4 py-3">Tipe</th>
                            <th scope="col" class="px-4 py-3">Judul & Pesan</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Review</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $row)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$row->is_read ? 'bg-blue-50/50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $row->created_at->diffForHumans() }}</div>
                                <div class="text-xs text-gray-500">{{ $row->created_at->format('d M H:i') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $severityColor = match($row->severity) {
                                        'critical' => 'text-red-700 bg-red-100 border-red-300',
                                        'warning' => 'text-yellow-700 bg-yellow-100 border-yellow-300',
                                        default => 'text-blue-700 bg-blue-100 border-blue-300',
                                    };
                                @endphp
                                <span class="text-xs font-medium px-2 py-0.5 rounded border {{ $severityColor }}">
                                    {{ ucfirst($row->severity) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ str_replace('_', ' ', $row->notification_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-sm">
                                <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $row->title }}</div>
                                <p class="text-gray-500 truncate">{{ Str::limit(strip_tags($row->message), 50) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($row->is_read)
                                    <span class="text-green-600 flex items-center gap-1 text-xs font-semibold">
                                        <i class="bi bi-check-all"></i> Dibaca
                                    </span>
                                @else
                                    <span class="text-blue-600 flex items-center gap-1 text-xs font-semibold">
                                        <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Baru
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($row->is_reviewed)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-green-400">Selesai</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-yellow-300">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button id="dropdown-{{ $row->id }}-button" data-dropdown-toggle="dropdown-{{ $row->id }}" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div id="dropdown-{{ $row->id }}" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-{{ $row->id }}-button">
                                        <li>
                                            <a href="{{ route('notifications.show', $row->id) }}" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Lihat Detail</a>
                                        </li>
                                    </ul>
                                    <div class="py-1">
                                        <form action="{{ route('notifications.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-inbox text-4xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada notifikasi ditemukan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                {{ $notifications->links() }}
            </div>
        </div>
    </section>
@endsection

@push('page_scripts')
    <!-- Init Flowbite Dropdowns manually if needed handled by default layout script -->
    <!-- SweetAlert Success/Error handling from Session -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('swal-success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session("swal-success") }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
