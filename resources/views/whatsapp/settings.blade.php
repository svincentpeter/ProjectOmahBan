@extends('layouts.app-flowbite')

@section('title', 'WhatsApp Settings')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Home', 'url' => route('home')],
        ['text' => 'Settings', 'url' => '#'],
        ['text' => 'WhatsApp', 'url' => '#'],
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <div class="space-y-6">
        {{-- Header Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="bi bi-whatsapp text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">WhatsApp Integration</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola koneksi WhatsApp untuk notifikasi otomatis</p>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div id="statusBadge">
                        @if($status['connected'] ?? false)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Connected
                            </span>
                        @elseif($status['status'] === 'waiting_qr')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                                Waiting QR Scan
                            </span>
                        @elseif($status['status'] === 'offline')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Service Offline
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                Disconnected
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Connection Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-qr-code text-blue-600"></i>
                        Koneksi WhatsApp
                    </h6>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Scan QR code untuk menghubungkan WhatsApp</p>
                </div>

                <div class="p-6">
                    @if($status['connected'] ?? false)
                        {{-- Connected State --}}
                        <div class="text-center py-8">
                            <div class="w-24 h-24 mx-auto rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                                <i class="bi bi-check-circle-fill text-5xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">WhatsApp Terhubung!</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Bot siap mengirim notifikasi otomatis</p>
                            
                            <div class="flex justify-center gap-3">
                                <button type="button" onclick="reconnectWhatsApp()" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                    <i class="bi bi-arrow-clockwise mr-2"></i>
                                    Reconnect
                                </button>
                                <button type="button" onclick="disconnectWhatsApp()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                    <i class="bi bi-x-circle mr-2"></i>
                                    Disconnect
                                </button>
                            </div>
                        </div>
                    @elseif($status['status'] === 'offline')
                        {{-- Service Offline --}}
                        <div class="text-center py-8">
                            <div class="w-24 h-24 mx-auto rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
                                <i class="bi bi-exclamation-triangle-fill text-5xl text-red-600 dark:text-red-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Service Offline</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">WhatsApp service tidak berjalan</p>
                            
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-left">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Cara menjalankan:</p>
                                <code class="block bg-gray-900 text-green-400 p-3 rounded-lg text-sm">
                                    cd whatsapp-service<br>
                                    npm install<br>
                                    npm start
                                </code>
                            </div>
                        </div>
                    @else
                        {{-- QR Code State --}}
                        <div class="text-center" id="qrContainer">
                            @if($qrData && ($qrData['qrCode'] ?? null))
                                <img src="{{ $qrData['qrCode'] }}" alt="WhatsApp QR Code" class="w-64 h-64 mx-auto rounded-lg shadow-md mb-4" id="qrImage">
                            @else
                                <div class="w-64 h-64 mx-auto rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4" id="qrPlaceholder">
                                    <div class="text-center">
                                        <i class="bi bi-arrow-clockwise text-4xl text-gray-400 animate-spin mb-2"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Generating QR Code...</p>
                                    </div>
                                </div>
                            @endif
                            
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Scan QR Code</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
                                Buka WhatsApp > Menu > Linked Devices > Link a Device
                            </p>
                            
                            <button type="button" onclick="refreshQrCode()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                <i class="bi bi-arrow-clockwise mr-2"></i>
                                Refresh QR
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Test Message Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-send text-blue-600"></i>
                        Test Kirim Pesan
                    </h6>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kirim pesan test untuk memastikan koneksi</p>
                </div>

                <div class="p-6">
                    <form id="testMessageForm" class="space-y-4">
                        <div>
                            <label for="testPhone" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                Nomor Tujuan
                            </label>
                            <input type="text" id="testPhone" name="phone" value="{{ $ownerPhone }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="6282227863969">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: 62xxx (tanpa +)</p>
                        </div>

                        <div>
                            <label for="testMessage" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                Pesan
                            </label>
                            <textarea id="testMessage" name="message" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Tulis pesan test...">🔔 Test dari Omah Ban POS

Ini adalah pesan test untuk memastikan WhatsApp bot berfungsi dengan baik.</textarea>
                        </div>

                        <button type="submit" id="sendTestBtn"
                            class="w-full inline-flex items-center justify-center gap-2 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-xl text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700"
                            @if(!($status['connected'] ?? false)) disabled @endif>
                            <i class="bi bi-send"></i>
                            Kirim Test Message
                        </button>
                    </form>
                </div>
            </div>

            {{-- Configuration Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-gear text-blue-600"></i>
                        Konfigurasi
                    </h6>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-1">Driver</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ ucfirst($driver) }}
                                </span>
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-1">Owner Phone</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">+{{ $ownerPhone }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-1">Service URL</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $baileysUrl }}</p>
                        </div>
                    </div>

                    <div class="mt-6 p-4 border border-blue-100 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-900/50 rounded-xl">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-bold text-blue-900 dark:text-blue-200 mb-1">Konfigurasi via .env</p>
                                <p class="text-blue-800 dark:text-blue-300">
                                    Edit file <code class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-800 rounded">.env</code> untuk mengubah konfigurasi:
                                </p>
                                <pre class="mt-2 p-3 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">WHATSAPP_DRIVER=baileys
BAILEYS_SERVICE_URL=http://localhost:3001
BAILEYS_API_KEY=omahban-wa-secret-2024
WHATSAPP_OWNER_PHONE=6282227863969</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notification Templates Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-chat-square-text text-purple-600"></i>
                        Pengaturan Notifikasi
                    </h6>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola jenis notifikasi dan template pesan WhatsApp</p>
                </div>

                <div class="p-6">
                    @if(isset($notificationSettings) && $notificationSettings->count() > 0)
                        <div class="space-y-4">
                            @foreach($notificationSettings as $setting)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden" x-data="{ open: false }">
                                    {{-- Header --}}
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                                <i class="bi {{ $setting->icon ?? 'bi-bell' }} text-lg text-gray-600 dark:text-gray-300"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-bold text-gray-900 dark:text-white text-sm">{{ $setting->label }}</h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            {{-- Toggle Switch - More Visible --}}
                                            <div class="flex items-center">
                                                <label class="relative inline-flex items-center cursor-pointer" style="min-width: 44px;">
                                                    <input type="checkbox" 
                                                        class="sr-only notification-toggle" 
                                                        data-id="{{ $setting->id }}"
                                                        id="toggle-{{ $setting->id }}"
                                                        {{ $setting->is_enabled ? 'checked' : '' }}>
                                                    <div class="toggle-track w-11 h-6 rounded-full border border-gray-300 dark:border-gray-600 transition-colors duration-200 {{ $setting->is_enabled ? 'bg-green-500 border-green-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                                                        <div class="toggle-thumb absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm border border-gray-200 transition-transform duration-200 {{ $setting->is_enabled ? 'translate-x-5' : 'translate-x-0' }}"></div>
                                                    </div>
                                                </label>
                                                <span class="ml-2 text-xs font-medium {{ $setting->is_enabled ? 'text-green-600' : 'text-gray-500' }}" id="status-{{ $setting->id }}">
                                                    {{ $setting->is_enabled ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                            {{-- Expand Button --}}
                                            <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Edit Template">
                                                <i class="bi text-lg" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Template Editor (Collapsible) --}}
                                    <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                                        <div class="p-4 space-y-4">
                                            {{-- Placeholders Info --}}
                                            @if($setting->placeholders)
                                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-lg">
                                                <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-2">
                                                    <i class="bi bi-info-circle mr-1"></i> Variabel yang tersedia (Klik untuk sisipkan):
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($setting->placeholders as $key => $desc)
                                                        <button type="button" 
                                                            onclick="insertPlaceholder('template-{{ $setting->id }}', '{{ '{'.$key.'}' }}')"
                                                            class="inline-flex items-center px-2 py-1 text-xs font-mono bg-amber-100 dark:bg-amber-800 text-amber-800 dark:text-amber-200 rounded hover:bg-amber-200 dark:hover:bg-amber-700 transition-colors" 
                                                            title="{{ $desc }}">
                                                            {{"{"}}{{ $key }}{{"}"}}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Template Textarea --}}
                                            <div>
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="text-sm font-semibold text-gray-900 dark:text-white">Template Pesan</label>
                                                    {{-- Formatting Toolbar --}}
                                                    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                                        <button type="button" onclick="insertFormat('template-{{ $setting->id }}', 'bold')" class="p-1.5 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded" title="Bold (*text*)">
                                                            <i class="bi bi-type-bold"></i>
                                                        </button>
                                                        <button type="button" onclick="insertFormat('template-{{ $setting->id }}', 'italic')" class="p-1.5 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded" title="Italic (_text_)">
                                                            <i class="bi bi-type-italic"></i>
                                                        </button>
                                                        <button type="button" onclick="insertFormat('template-{{ $setting->id }}', 'strike')" class="p-1.5 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded" title="Strikethrough (~text~)">
                                                            <i class="bi bi-type-strikethrough"></i>
                                                        </button>
                                                        <button type="button" onclick="insertFormat('template-{{ $setting->id }}', 'mono')" class="p-1.5 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded" title="Monospace (```text```)">
                                                            <i class="bi bi-code"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <textarea 
                                                    id="template-{{ $setting->id }}"
                                                    rows="15"
                                                    class="block w-full p-3 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono leading-relaxed"
                                                    style="min-height: 350px;"
                                                    placeholder="Template pesan...">{{ $setting->template }}</textarea>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="resetTemplate({{ $setting->id }})" 
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                                    <i class="bi bi-arrow-counterclockwise mr-2"></i>
                                                    Reset Default
                                                </button>
                                                <button type="button" onclick="saveTemplate({{ $setting->id }})" 
                                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                                    <i class="bi bi-check2 mr-2"></i>
                                                    Simpan Template
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                <i class="bi bi-database-x text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada pengaturan notifikasi.</p>
                            <p class="text-sm text-gray-400 mt-2">Jalankan perintah berikut di terminal:</p>
                            <code class="block mt-2 px-4 py-2 bg-gray-900 text-green-400 rounded-lg text-sm">php artisan db:seed --class=NotificationSettingSeeder</code>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recipients Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden lg:col-span-2" id="recipients">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-people-fill text-teal-600 dark:text-teal-400 text-lg"></i>
                        <div>
                            <h6 class="text-base font-bold text-gray-900 dark:text-white">Daftar Penerima</h6>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola nomor WhatsApp tambahan yang menerima notifikasi</p>
                        </div>
                    </div>
                    <button type="button" onclick="openRecipientModal()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="bi bi-plus-lg mr-2"></i> Tambah
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nama / Label</th>
                                <th scope="col" class="px-6 py-3">No. WhatsApp</th>
                                <th scope="col" class="px-6 py-3">Izin Notifikasi</th>
                                <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recipients ?? [] as $recipient)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $recipient->recipient_name }}
                                    @if(!$recipient->is_active)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono">{{ $recipient->recipient_phone }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($recipient->permissions ?? [] as $perm)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ str_replace('_', ' ', ucfirst($perm)) }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Tidak ada izin</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="editRecipient({{ $recipient->id }}, '{{ $recipient->recipient_name }}', '{{ $recipient->recipient_phone }}', {{ json_encode($recipient->permissions ?? []) }}, {{ $recipient->is_active ? 'true' : 'false' }})" 
                                        class="font-medium text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</button>
                                    <button onclick="deleteRecipient({{ $recipient->id }})" class="font-medium text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="bi bi-people text-2xl mb-2 block text-gray-300"></i>
                                    Belum ada penerima tambahan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Hidden Trigger for Modal --}}
    <button id="recipientModalTrigger" data-modal-target="recipientModal" data-modal-toggle="recipientModal" class="hidden"></button>

    <!-- Recipient Modal -->
    <div id="recipientModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeRecipientModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white" id="modalTitle">Tambah Penerima</h3>
                    <form id="recipientForm" action="{{ route('whatsapp.recipients.store') }}" method="POST">
                        @csrf
                        <div id="methodField"></div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="recipient_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama / Label</label>
                                <input type="text" name="recipient_name" id="recipient_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Contoh: Pak Budi (Gudang)" required>
                            </div>
                            <div>
                                <label for="recipient_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor WhatsApp</label>
                                <input type="text" name="recipient_phone" id="recipient_phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="628..." required>
                                <p class="mt-1 text-xs text-gray-500">Awali dengan kode negara (62). Jangan pakai 08.</p>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Izin Notifikasi</label>
                                <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    @foreach(['manual_input' => 'Input Manual', 'low_stock' => 'Stok Rendah', 'daily_report' => 'Laporan Harian', 'login_alert' => 'Login Alert'] as $key => $label)
                                    <div class="flex items-center">
                                        <input id="perm-{{ $key }}" type="checkbox" value="{{ $key }}" name="permissions[]" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="perm-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input id="is_active" type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Aktifkan Penerima ini</label>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
<script>
    // Auto refresh status every 5 seconds
    let statusInterval;
    
    $(document).ready(function() {
        statusInterval = setInterval(checkStatus, 5000);
        
        // Test message form
        $('#testMessageForm').on('submit', function(e) {
            e.preventDefault();
            sendTestMessage();
        });
    });

    function checkStatus() {
        $.get('{{ route("whatsapp.status") }}')
            .done(function(data) {
                if (data.connected) {
                    // If just connected, reload page
                    if (!$('#statusBadge').find('.bg-green-100').length) {
                        location.reload();
                    }
                }
            });
    }

    function refreshQrCode() {
        Swal.fire({
            title: 'Refreshing...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.get('{{ route("whatsapp.qr") }}')
            .done(function(data) {
                Swal.close();
                if (data.qrCode) {
                    $('#qrImage').attr('src', data.qrCode);
                    $('#qrPlaceholder').hide();
                    $('#qrImage').show();
                } else if (data.connected) {
                    location.reload();
                } else {
                    Swal.fire('Info', data.message || 'QR belum tersedia, coba lagi.', 'info');
                }
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal refresh QR code', 'error');
            });
    }

    function sendTestMessage() {
        const phone = $('#testPhone').val();
        const message = $('#testMessage').val();

        if (!phone || !message) {
            Swal.fire('Error', 'Nomor dan pesan harus diisi', 'warning');
            return;
        }

        $('#sendTestBtn').prop('disabled', true).html('<i class="bi bi-arrow-clockwise animate-spin mr-2"></i> Mengirim...');

        $.ajax({
            url: '{{ route("whatsapp.test") }}',
            method: 'POST',
            data: { phone, message, _token: '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terkirim!',
                        text: 'Pesan berhasil dikirim ke ' + phone,
                        timer: 3000
                    });
                } else {
                    Swal.fire('Gagal', data.error || 'Gagal mengirim pesan', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.error || 'Terjadi kesalahan', 'error');
            },
            complete: function() {
                $('#sendTestBtn').prop('disabled', false).html('<i class="bi bi-send mr-2"></i> Kirim Test Message');
            }
        });
    }

    function reconnectWhatsApp() {
        Swal.fire({
            title: 'Reconnecting...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.post('{{ route("whatsapp.reconnect") }}', { _token: '{{ csrf_token() }}' })
            .done(function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reconnecting',
                    text: 'WhatsApp sedang reconnect...',
                    timer: 2000
                }).then(() => location.reload());
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal reconnect', 'error');
            });
    }

    function disconnectWhatsApp() {
        Swal.fire({
            title: 'Disconnect WhatsApp?',
            text: 'Anda harus scan QR ulang untuk menghubungkan kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Disconnect',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('{{ route("whatsapp.disconnect") }}', { _token: '{{ csrf_token() }}' })
                    .done(function() {
                        Swal.fire('Disconnected', 'WhatsApp telah diputus.', 'success')
                            .then(() => location.reload());
                    })
                    .fail(function() {
                        Swal.fire('Error', 'Gagal disconnect', 'error');
                    });
            }
        });
    }

    // ========================================
    // Notification Settings Functions
    // ========================================

    // Toggle notification on/off
    $(document).on('change', '.notification-toggle', function() {
        const settingId = $(this).data('id');
        const checkbox = $(this);
        const isChecked = checkbox.prop('checked');
        const track = checkbox.siblings('.toggle-track');
        const thumb = track.find('.toggle-thumb');
        const statusLabel = $(`#status-${settingId}`);
        
        // Immediately update visual
        if (isChecked) {
            track.removeClass('bg-gray-200 dark:bg-gray-700').addClass('bg-green-500 border-green-500');
            thumb.removeClass('translate-x-0').addClass('translate-x-5');
            statusLabel.removeClass('text-gray-500').addClass('text-green-600').text('ON');
        } else {
            track.removeClass('bg-green-500 border-green-500').addClass('bg-gray-200 dark:bg-gray-700');
            thumb.removeClass('translate-x-5').addClass('translate-x-0');
            statusLabel.removeClass('text-green-600').addClass('text-gray-500').text('OFF');
        }
        
        $.ajax({
            url: `/whatsapp/notifications/${settingId}/toggle`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: data.is_enabled ? 'Diaktifkan' : 'Dinonaktifkan',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function() {
                // Revert checkbox and visual
                checkbox.prop('checked', !isChecked);
                if (!isChecked) {
                    track.removeClass('bg-gray-200 dark:bg-gray-700').addClass('bg-green-500 border-green-500');
                    thumb.removeClass('translate-x-0').addClass('translate-x-5');
                    statusLabel.removeClass('text-gray-500').addClass('text-green-600').text('ON');
                } else {
                    track.removeClass('bg-green-500 border-green-500').addClass('bg-gray-200 dark:bg-gray-700');
                    thumb.removeClass('translate-x-5').addClass('translate-x-0');
                    statusLabel.removeClass('text-green-600').addClass('text-gray-500').text('OFF');
                }
                Swal.fire('Error', 'Gagal mengubah status notifikasi', 'error');
            }
        });
    });

    // Save template
    function saveTemplate(id) {
        const template = $(`#template-${id}`).val();
        
        if (!template.trim()) {
            Swal.fire('Error', 'Template tidak boleh kosong', 'warning');
            return;
        }

        $.ajax({
            url: `/whatsapp/notifications/${id}/template`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                template: template
            },
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Template berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal menyimpan template', 'error');
            }
        });
    }

    function resetTemplate(id) {
        Swal.fire({
            title: 'Reset Template?',
            text: "Template akan dikembalikan ke default sistem",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/whatsapp/notifications/${id}/reset`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(data) {
                        if (data.success) {
                            $(`#template-${id}`).val(data.template);
                            Swal.fire(
                                'Tereset!',
                                'Template telah dikembalikan ke default.',
                                'success'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mereset template', 'error');
                    }
                });
            }
        });
    }

    // Helper to insert text at cursor position
    function insertAtCursor(textarea, text, wrapStart = '', wrapEnd = '') {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const value = textarea.value;
        const selectedText = value.substring(start, end);

        const replacement = wrapStart + (selectedText.length > 0 ? selectedText : text) + wrapEnd;

        textarea.value = value.substring(0, start) + replacement + value.substring(end);

        // Restore cursor/selection
        const newCursorPos = start + (selectedText.length > 0 ? replacement.length : (wrapStart.length + text.length));
        textarea.focus();
        textarea.setSelectionRange(newCursorPos, newCursorPos);
    }

    function insertPlaceholder(textareaId, placeholder) {
        const textarea = document.getElementById(textareaId);
        insertAtCursor(textarea, placeholder);
    }

    function insertFormat(textareaId, format) {
        const textarea = document.getElementById(textareaId);
        let start = '', end = '';

        switch (format) {
            case 'bold': start = '*'; end = '*'; break;
            case 'italic': start = '_'; end = '_'; break;
            case 'strike': start = '~'; end = '~'; break;
            case 'mono': start = '```'; end = '```'; break;
        }

        insertAtCursor(textarea, '', start, end);
    }

    // Modal Logic via Hidden Trigger (Workaround for Modal class availability)
    function toggleRecipientModal() {
        document.getElementById('recipientModalTrigger').click();
    }

    function openRecipientModal() {
        document.getElementById('recipientForm').reset();
        document.getElementById('recipientForm').action = "{{ route('whatsapp.recipients.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('modalTitle').innerText = 'Tambah Penerima';
        
        toggleRecipientModal();
    }

    function closeRecipientModal() {
        toggleRecipientModal();
    }

    function editRecipient(id, name, phone, permissions, isActive) {
        document.getElementById('recipient_name').value = name;
        document.getElementById('recipient_phone').value = phone;
        document.getElementById('is_active').checked = isActive;
        
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            cb.checked = permissions.includes(cb.value);
        });

        const updateUrl = "{{ route('whatsapp.recipients.update', ':id') }}".replace(':id', id);
        document.getElementById('recipientForm').action = updateUrl;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('modalTitle').innerText = 'Edit Penerima';
        
        toggleRecipientModal();
    }

    function deleteRecipient(id) {
        Swal.fire({
            title: 'Hapus Penerima?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('whatsapp.recipients.delete', ':id') }}".replace(':id', id);
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
