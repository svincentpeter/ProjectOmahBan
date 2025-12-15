@extends('layouts.app-flowbite')

@section('title', 'Detail Notifikasi')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Operasional', 'url' => '#'],
        ['text' => 'Notifikasi', 'url' => route('notifications.index'), 'icon' => 'bi bi-bell-fill'],
        ['text' => 'Detail #' . $notification->id, 'url' => '#']
    ]])
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Main Detail -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-start bg-white">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            @php
                                $badgeClass = match($notification->severity) {
                                    'critical' => 'bg-red-50 text-red-600 border-red-100',
                                    'warning' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    default => 'bg-blue-50 text-blue-600 border-blue-100'
                                };
                            @endphp
                            <span class="{{ $badgeClass }} text-[10px] font-bold px-2.5 py-1 rounded-full border uppercase tracking-wider">
                                {{ $notification->severity }}
                            </span>
                            <span class="text-xs text-zinc-500 flex items-center font-bold">
                                <i class="bi bi-clock me-1.5"></i> {{ $notification->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-extrabold text-black dark:text-white leading-tight">
                            {{ $notification->title }}
                        </h1>
                    </div>
                    <div class="text-right">
                         @if (!$notification->is_read)
                            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-bold bg-blue-50 text-blue-800">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Belum Dibaca
                            </span>
                        @else
                             <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-bold bg-green-50 text-green-800">
                                <i class="bi bi-check-all text-lg"></i> Dibaca
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    <h3 class="text-xs font-extrabold text-black uppercase tracking-widest mb-4">Isi Pesan</h3>
                    <div class="prose max-w-none text-black dark:text-gray-300">
                        <div class="bg-zinc-50 rounded-xl p-6 text-sm leading-relaxed whitespace-pre-line font-semibold text-zinc-900 shadow-inner border border-zinc-200">
                            {!! $notification->message !!}
                        </div>
                    </div>

                    <!-- Related Transaction -->
                    @if ($notification->sale_id && $notification->sale)
                    <div class="mt-8">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Transaksi Terkait</h3>
                        <a href="{{ url('/sales/' . $notification->sale_id) }}" class="group block p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                                        <i class="bi bi-receipt text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600">Faktur #{{ $notification->sale->reference ?? $notification->sale_id }}</p>
                                        <p class="text-xs text-slate-500 font-medium">Lihat detail struk transaksi</p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-2 transition-colors">
                        <i class="bi bi-arrow-left"></i> Kembali ke daftar
                    </a>
                    
                    <div class="flex gap-3">
                        @if (!$notification->is_read)
                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-semibold rounded-xl text-sm px-5 py-2.5 focus:outline-none transition-all shadow-sm hover:shadow-md">
                                    Tandai Dibaca
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 bg-white border border-red-100 hover:bg-red-50 focus:ring-4 focus:ring-red-100 font-semibold rounded-xl text-sm px-5 py-2.5 focus:outline-none transition-all hover:border-red-200">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar Info -->
        <div class="space-y-6">
            
            <!-- Review Status Card -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
                <h5 class="text-xs font-extrabold text-black uppercase tracking-widest mb-5 border-b border-slate-50 pb-3">Status Review</h5>
                
                @if ($notification->is_reviewed)
                    <div class="bg-green-50 border border-green-100 rounded-xl p-5 mb-4 text-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600 mb-2">
                            <i class="bi bi-check-lg text-xl"></i>
                        </div>
                        <h4 class="text-green-900 font-bold text-sm">Sudah Direview</h4>
                        <p class="text-xs text-green-700 mt-1 font-semibold">
                            oleh {{ $notification->reviewer->name ?? 'Unknown' }}<br>
                            pada {{ $notification->reviewed_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @if($notification->review_notes)
                        <div class="text-sm text-zinc-800 italic bg-zinc-50 p-4 rounded-xl border border-zinc-200 text-center font-medium">
                            &ldquo;{{ $notification->review_notes }}&rdquo;
                        </div>
                    @endif
                @else
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <p class="text-amber-900 font-bold text-sm">Menunggu Review</p>
                            <p class="text-xs text-amber-700 font-medium">Butuh tindakan supervisor.</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('notifications.mark-as-reviewed', $notification->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="review_notes" class="block mb-2 text-xs font-bold text-black uppercase">Tambah Catatan</label>
                            <textarea id="review_notes" name="review_notes" rows="4" class="block p-3 w-full text-sm text-zinc-900 bg-zinc-50 rounded-xl border border-zinc-300 focus:ring-blue-500 focus:border-blue-500 shadow-inner placeholder-zinc-500 font-medium" placeholder="Tulis catatan review Anda di sini..."></textarea>
                        </div>
                        <button type="submit" class="w-full text-white bg-zinc-900 hover:bg-black focus:ring-4 focus:ring-zinc-200 font-bold rounded-xl text-sm px-5 py-3 focus:outline-none transition-all shadow-lg shadow-zinc-200">
                            Kirim Review
                        </button>
                    </form>
                @endif
            </div>

            <!-- WhatsApp Status -->
            @if ($notification->whatsapp_message_id)
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
                <h5 class="text-xs font-extrabold text-black uppercase tracking-widest mb-5 border-b border-slate-50 pb-3">Integrasi</h5>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366]">
                            <i class="bi bi-whatsapp text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-400 font-bold uppercase">WhatsApp</p>
                            <p class="text-sm font-bold text-zinc-900">{{ ucfirst($notification->whatsapp_status ?? 'Terkirim') }}</p>
                        </div>
                    </div>
                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection
