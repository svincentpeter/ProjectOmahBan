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
</script>
@endpush
