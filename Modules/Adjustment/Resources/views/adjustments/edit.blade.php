@extends('layouts.app-flowbite')

@section('title', 'Edit Pengajuan Penyesuaian Stok')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penyesuaian Stok', 'url' => route('adjustments.index')],
            ['text' => $adjustment->reference, 'url' => '#', 'icon' => 'bi bi-pencil-square'],
        ]
    ])
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Status Alert --}}
        @if ($adjustment->status !== 'pending')
            <div class="p-4 mb-4 text-orange-800 border border-orange-200 rounded-xl bg-orange-50 flex items-center gap-3">
                <i class="bi bi-info-circle-fill text-xl"></i>
                <div class="text-sm">
                    <span class="font-bold">Perhatian:</span> Pengajuan berstatus <strong class="uppercase">{{ $adjustment->status }}</strong>.
                    Anda tetap dapat melihat data & mengunduh lampiran, namun tidak bisa menyimpan perubahan.
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
                    <i class="bi bi-pencil-square text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-zinc-900 leading-tight">Edit Pengajuan Penyesuaian</h1>
                    <div class="flex items-center gap-2 mt-1 text-sm text-zinc-500">
                        <span>Ref: <span class="font-mono font-bold text-zinc-800">{{ $adjustment->reference }}</span></span>
                        <span>•</span>
                        <span>Status: 
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $cls = $colors[$adjustment->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $cls }}">
                                {{ ucfirst($adjustment->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('adjustments.update', $adjustment->id) }}" method="POST" enctype="multipart/form-data" id="adjustment-form" class="space-y-6">
            @csrf
            @method('PUT')
            
            @include('adjustment::adjustments.partials._form', [
                'isEdit' => true,
                'adjustment' => $adjustment,
            ])

            {{-- Buttons --}}
            <div class="flex items-center gap-3">
                @if ($adjustment->status === 'pending')
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center gap-2 shadow-lg shadow-blue-600/20 transition-all" id="submit-btn">
                        <i class="bi bi-save-fill"></i>
                        Update Pengajuan
                    </button>
                @else
                    <button type="button" class="text-white bg-zinc-400 cursor-not-allowed font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center gap-2 shadow-sm transition-all" disabled>
                        <i class="bi bi-lock-fill"></i>
                        Tidak Dapat Diubah
                    </button>
                @endif
                <a href="{{ route('adjustments.index') }}" class="text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-100 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center gap-2 transition-all">
                    <i class="bi bi-arrow-left-circle"></i>
                    Kembali
                </a>
            </div>
        </form>
    </div>
@endsection
