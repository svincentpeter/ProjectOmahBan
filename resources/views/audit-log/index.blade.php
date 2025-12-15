@extends('layouts.app-flowbite')

@section('title', 'Audit Log')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pengaturan', 'url' => route('settings.index')],
            ['text' => 'Audit Log', 'url' => '#', 'icon' => 'bi bi-clock-history'],
        ]
    ])
@endsection

@section('content')
    {{-- Header --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-clock-history text-blue-600"></i>
                    Audit Log
                </h2>
                <p class="text-sm text-gray-500 mt-1">Riwayat perubahan data sistem</p>
            </div>
        </div>

        {{-- Filters --}}
        <form action="{{ url()->current() }}" method="GET" class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Modul</label>
                    <select name="log_name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Semua</option>
                        @foreach($logNames as $name)
                            <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>{{ ucfirst($name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Event</label>
                    <select name="event" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Semua</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Dari</label>
                    <input type="date" name="from" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" value="{{ request('from') }}">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Sampai</label>
                    <input type="date" name="to" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" value="{{ request('to') }}">
                </div>
                <div>
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                        <i class="bi bi-filter mr-2"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Log Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Modul</th>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-gray-900 font-medium">{{ $log->created_at->format('d/m/Y') }}</span>
                            <span class="text-gray-500 text-xs">{{ $log->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ ucfirst($log->log_name) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->event == 'created')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Created</span>
                            @elseif($log->event == 'updated')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Updated</span>
                            @elseif($log->event == 'deleted')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Deleted</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $log->event }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900">{{ optional($log->causer)->name ?? 'System' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->properties && ($log->properties['old'] ?? $log->properties['attributes'] ?? null))
                                <button onclick="showChanges({{ json_encode($log->properties) }})" class="text-blue-600 hover:underline text-xs">
                                    <i class="bi bi-eye mr-1"></i> Lihat
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                            Belum ada log aktivitas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Changes Modal --}}
    <div id="changesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Detail Perubahan</h3>
                <button onclick="closeChangesModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]" id="changesContent"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showChanges(properties) {
            const modal = document.getElementById('changesModal');
            const content = document.getElementById('changesContent');
            
            let html = '<div class="space-y-4">';
            
            if (properties.old && properties.attributes) {
                html += '<div class="grid grid-cols-2 gap-4">';
                html += '<div class="bg-red-50 p-4 rounded-xl"><h4 class="font-bold text-red-800 mb-2">Sebelum</h4><pre class="text-xs whitespace-pre-wrap text-red-700">' + JSON.stringify(properties.old, null, 2) + '</pre></div>';
                html += '<div class="bg-green-50 p-4 rounded-xl"><h4 class="font-bold text-green-800 mb-2">Sesudah</h4><pre class="text-xs whitespace-pre-wrap text-green-700">' + JSON.stringify(properties.attributes, null, 2) + '</pre></div>';
                html += '</div>';
            } else if (properties.attributes) {
                html += '<div class="bg-blue-50 p-4 rounded-xl"><h4 class="font-bold text-blue-800 mb-2">Data</h4><pre class="text-xs whitespace-pre-wrap text-blue-700">' + JSON.stringify(properties.attributes, null, 2) + '</pre></div>';
            } else {
                html += '<pre class="text-xs whitespace-pre-wrap">' + JSON.stringify(properties, null, 2) + '</pre>';
            }
            
            html += '</div>';
            content.innerHTML = html;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeChangesModal() {
            const modal = document.getElementById('changesModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    @endpush
@endsection
