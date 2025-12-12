@extends('layouts.app-flowbite')

@section('title', 'Detail Penyesuaian Stok')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penyesuaian Stok', 'url' => route('adjustments.index')],
            ['text' => $adjustment->reference, 'url' => '#', 'icon' => 'bi bi-file-text'],
        ]
    ])
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header / Action Bar --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="bi bi-file-earmark-text text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-zinc-900 leading-tight">Detail Penyesuaian: <span class="font-mono text-blue-600">{{ $adjustment->reference }}</span></h1>
                    <p class="text-zinc-500 text-sm mt-1">Informasi lengkap penyesuaian stok dan riwayat approval.</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('adjustments.index') }}" class="text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-100 font-medium rounded-xl text-sm px-4 py-2 text-center inline-flex items-center gap-2 transition-all">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                @if ($adjustment->status === 'pending')
                    @can('update_adjustments')
                        <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="text-yellow-800 bg-yellow-100 hover:bg-yellow-200 focus:ring-4 focus:ring-yellow-100 font-medium rounded-xl text-sm px-4 py-2 text-center inline-flex items-center gap-2 transition-all">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endcan
                @endif
                
                {{-- Print & Download --}}
                <div class="flex gap-2">
                    <a href="{{ route('adjustments.pdf', $adjustment->id) }}?inline=1" target="_blank" class="text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 focus:ring-4 focus:ring-blue-100 font-medium rounded-xl text-sm px-4 py-2 text-center inline-flex items-center gap-2 transition-all">
                        <i class="bi bi-printer"></i> Print
                    </a>
                    <a href="{{ route('adjustments.pdf', $adjustment->id) }}?download=1" class="text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 focus:ring-4 focus:ring-zinc-100 font-medium rounded-xl text-sm px-4 py-2 text-center inline-flex items-center gap-2 transition-all">
                        <i class="bi bi-download"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Products List --}}
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50 flex justify-between items-center">
                        <h6 class="font-bold text-zinc-800 flex items-center gap-2">
                            <i class="bi bi-box-seam text-blue-600"></i>
                            Daftar Produk
                        </h6>
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                            {{ $adjustment->adjustedProducts->count() }} Item
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500">
                            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 border-b border-zinc-200">
                                <tr>
                                    <th class="px-6 py-3">Produk</th>
                                    <th class="px-6 py-3 text-center">Tipe</th>
                                    <th class="px-6 py-3 text-center">Jumlah</th>
                                    <th class="px-6 py-3 text-right">Stok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($adjustment->adjustedProducts as $item)
                                    <tr class="bg-white hover:bg-zinc-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-zinc-900">
                                            <div class="flex flex-col">
                                                <span>{{ $item->product->product_name ?? '-' }}</span>
                                                <small class="text-zinc-500 font-mono">{{ $item->product->product_code ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($item->type == 'add')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200">
                                                    <i class="bi bi-plus"></i> Penambahan
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200">
                                                    <i class="bi bi-dash"></i> Pengurangan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-zinc-700">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            {{ $item->product->product_quantity ?? 0 }} {{ $item->product->product_unit ?? 'PC' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-zinc-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="bi bi-inbox text-4xl text-zinc-200 mb-2"></i>
                                                <p>Tidak ada produk data.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Photo Evidence --}}
                @if ($adjustment->adjustmentFiles->count() > 0)
                    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50">
                            <h6 class="font-bold text-zinc-800 flex items-center gap-2">
                                <i class="bi bi-image text-blue-600"></i>
                                Foto Bukti
                            </h6>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($adjustment->adjustmentFiles as $file)
                                    <div class="group relative aspect-square bg-zinc-100 rounded-xl overflow-hidden border border-zinc-200">
                                        <img src="{{ $file->file_url }}" alt="{{ $file->file_name }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                                            <a href="{{ $file->file_url }}" target="_blank" class="text-white text-xs font-medium hover:underline truncate w-full text-center">
                                                {{ $file->file_name }}
                                            </a>
                                            <a href="{{ $file->file_url }}" download class="text-white border border-white/50 rounded-lg px-3 py-1 text-xs hover:bg-white/20 transition-colors">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- Notes & Description --}}
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
                     <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50">
                        <h6 class="font-bold text-zinc-800 flex items-center gap-2">
                            <i class="bi bi-card-text text-blue-600"></i>
                            Keterangan
                        </h6>
                    </div>
                    <div class="p-6 space-y-4">
                        @if ($adjustment->description)
                            <div>
                                <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Deskripsi</h4>
                                <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm text-zinc-700">
                                    {{ $adjustment->description }}
                                </div>
                            </div>
                        @endif
                        
                        @if ($adjustment->note)
                             <div>
                                <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Catatan Tambahan</h4>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                                    {{ $adjustment->note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Sidebar Status --}}
            <div class="space-y-6">
                {{-- Status Card --}}
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 text-center border-b border-zinc-100">
                         @php
                            $statusConfig = [
                                'pending' => ['color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'icon' => 'bi-hourglass-split', 'label' => 'PENDING'],
                                'approved' => ['color' => 'text-green-600', 'bg' => 'bg-green-50', 'icon' => 'bi-check-circle-fill', 'label' => 'DISETUJUI'],
                                'rejected' => ['color' => 'text-red-600', 'bg' => 'bg-red-50', 'icon' => 'bi-x-circle-fill', 'label' => 'DITOLAK'],
                            ];
                            $curr = $statusConfig[$adjustment->status] ?? $statusConfig['pending'];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full {{ $curr['bg'] }} {{ $curr['color'] }} mb-4">
                            <i class="bi {{ $curr['icon'] }} text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold {{ $curr['color'] }}">{{ $curr['label'] }}</h3>
                        <p class="text-zinc-500 text-sm mt-1">Status Pengajuan Saat Ini</p>
                    </div>
                    <div class="p-4 bg-zinc-50/50">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-xs text-zinc-500 uppercase font-bold">Tanggal</div>
                                <div class="font-bold text-zinc-800">{{ $adjustment->date->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-zinc-500 uppercase font-bold">Total Item</div>
                                <div class="font-bold text-zinc-800">{{ $adjustment->adjustedProducts->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approval Action (If Pending) --}}
                @can('approve_adjustments')
                    @if ($adjustment->status === 'pending')
                        <div class="bg-white border border-yellow-300 rounded-2xl shadow-sm overflow-hidden relative">
                            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-yellow-400 to-orange-400"></div>
                            <div class="p-6">
                                <h6 class="font-bold text-yellow-800 mb-2 flex items-center gap-2">
                                    <i class="bi bi-shield-lock-fill"></i> Area Approval
                                </h6>
                                <p class="text-xs text-yellow-700 mb-4">
                                    Sebagai admin, Anda dapat menyetujui atau menolak pengajuan ini. Tindakan ini tidak dapat dibatalkan.
                                </p>
                                <form id="approvalForm" method="POST" action="{{ route('adjustments.approve', $adjustment->id) }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="action" id="approvalAction" value="">
                                    
                                    <textarea name="approval_notes" id="approvalNotes" rows="2" 
                                        class="w-full text-sm bg-yellow-50 border-yellow-200 rounded-lg placeholder-yellow-700/50 focus:ring-yellow-500 focus:border-yellow-500 text-yellow-900" 
                                        placeholder="Catatan approval (opsional)..."></textarea>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <button type="button" onclick="submitApproval('reject')" class="w-full py-2 px-3 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 text-sm font-bold transition-all">
                                            <i class="bi bi-x-lg mr-1"></i> Tolak
                                        </button>
                                        <button type="button" onclick="submitApproval('approve')" class="w-full py-2 px-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl shadow-lg shadow-green-500/30 hover:shadow-green-500/50 text-sm font-bold transition-all hover:-translate-y-0.5">
                                            <i class="bi bi-check-lg mr-1"></i> Setujui
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endcan

                 {{-- Approval Info (If not pending) --}}
                 @if ($adjustment->status !== 'pending')
                    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
                        <h6 class="font-bold text-zinc-800 mb-4 flex items-center gap-2">
                            <i class="bi bi-check-all text-blue-600"></i> Informasi Approval
                        </h6>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-zinc-500">Oleh:</dt>
                                <dd class="font-bold text-zinc-800">{{ $adjustment->approver->name ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-zinc-500">Tanggal:</dt>
                                <dd class="font-bold text-zinc-800">{{ $adjustment->approval_date ? $adjustment->approval_date->format('d M Y H:i') : '-' }}</dd>
                            </div>
                            @if($adjustment->approval_notes)
                            <div class="pt-3 border-t border-zinc-100">
                                <dt class="text-zinc-500 mb-1">Catatan:</dt>
                                <dd class="text-zinc-700 italic bg-zinc-50 p-2 rounded-lg border border-zinc-100">{{ $adjustment->approval_notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                 @endif

                 {{-- Activity Logs (Tiny Timeline) --}}
                 <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-100 bg-zinc-50/50">
                        <h6 class="font-bold text-zinc-800 flex items-center gap-2">
                            <i class="bi bi-clock-history text-blue-600"></i> Riwayat Aktivitas
                        </h6>
                    </div>
                    <div class="p-6 relative">
                        {{-- Vertical Line --}}
                        <div class="absolute left-8 top-6 bottom-6 w-0.5 bg-zinc-200"></div>
                        
                        <div class="space-y-6">
                            @forelse($adjustment->logs ?? [] as $log)
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm {{ $log->action == 'created' ? 'bg-blue-500' : ($log->action == 'approved' ? 'bg-green-500' : ($log->action == 'rejected' ? 'bg-red-500' : 'bg-zinc-400')) }}"></div>
                                    <p class="text-xs text-zinc-500 mb-0.5">{{ $log->created_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm font-bold text-zinc-800">
                                        {{ ucfirst($log->action) }} <span class="font-normal text-zinc-600">oleh</span> {{ $log->user->name ?? 'System' }}
                                    </p>
                                    @if($log->notes)
                                        <p class="text-xs text-zinc-500 italic mt-1 bg-zinc-50 p-1.5 rounded border border-zinc-100 inline-block">
                                            "{{ $log->notes }}"
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-zinc-500 italic text-sm">Belum ada aktivitas.</div>
                            @endforelse
                        </div>
                    </div>
                 </div>

            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        function submitApproval(action) {
            const form = document.getElementById('approvalForm');
            const notes = document.getElementById('approvalNotes').value;
            const actionInput = document.getElementById('approvalAction');

            Swal.fire({
                title: action === 'approve' ? 'Konfirmasi Setujui?' : 'Konfirmasi Tolak?',
                text: action === 'approve' ? 'Pastikan data sudah valid.' : 'Pengajuan ini akan ditolak permanen.',
                icon: action === 'approve' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonText: action === 'approve' ? 'Ya, Setujui' : 'Ya, Tolak',
                confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    actionInput.value = action;
                    form.submit();
                }
            });
        }
    </script>
@endpush
