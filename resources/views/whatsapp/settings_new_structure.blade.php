@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">WhatsApp Settings</h1>
        <p class="text-gray-600 dark:text-gray-400">Kelola koneksi, template, dan penerima notifikasi.</p>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <span class="font-medium">Berhasil!</span> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="#connection" class="block px-4 py-2 rounded-lg bg-blue-50 text-blue-700 dark:bg-gray-700 dark:text-white font-medium">
                            <i class="bi bi-whatsapp mr-2"></i> Koneksi WhatsApp
                        </a>
                    </li>
                    <li>
                        <a href="#notifications" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="bi bi-bell mr-2"></i> Template Notifikasi
                        </a>
                    </li>
                    <li>
                        <a href="#recipients" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="bi bi-people mr-2"></i> Daftar Penerima
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Connection Card -->
            <div id="connection" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <!-- Existing Connection Content (truncated/placeholder) -->
                ... (Existing Connection UI) ...
            </div>

            <!-- Notifications Card -->
            <div id="notifications" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <!-- Existing Notifications UI -->
                ...
            </div>

            <!-- Recipients Card (NEW) -->
             <div id="recipients" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Daftar Penerima Notifikasi</h2>
                    <button onclick="openRecipientModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-lg mr-2"></i> Tambah Penerima
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nama / Label</th>
                                <th scope="col" class="px-6 py-3">No. WhatsApp</th>
                                <th scope="col" class="px-6 py-3">Izin Notifikasi</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="recipients-table-body">
                            <!-- Loop recipients here -->
                            @forelse($recipients as $recipient)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $recipient->recipient_name }}</td>
                                <td class="px-6 py-4">{{ $recipient->recipient_phone }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($recipient->permissions as $perm)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ $perm }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="editRecipient({{ $recipient->id }})" class="font-medium text-blue-600 hover:underline mr-3">Edit</button>
                                    <button onclick="deleteRecipient({{ $recipient->id }})" class="font-medium text-red-600 hover:underline">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">Belum ada penerima notifikasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal for Add/Edit Recipient -->
<!-- ... Modal HTML ... -->

@endsection
