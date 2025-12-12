@extends('layouts.app-flowbite')

@section('title', 'Buat Pengajuan Penyesuaian Stok')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penyesuaian Stok', 'url' => route('adjustments.index')],
            ['text' => 'Buat Pengajuan', 'url' => '#', 'icon' => 'bi bi-file-earmark-plus'],
        ]
    ])
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="bi bi-file-earmark-plus text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-zinc-900 leading-tight">Form Pengajuan Penyesuaian Stok</h1>
                    <p class="text-zinc-500 text-sm mt-1">Isi formulir berikut untuk mengajukan penyesuaian stok baru. Pengajuan akan menunggu approval dari Owner.</p>
                </div>
            </div>

            <div class="mt-4 bg-blue-50 rounded-xl p-4 border border-blue-100">
                <h4 class="text-sm font-bold text-blue-800 flex items-center gap-2 mb-2">
                    <i class="bi bi-info-circle-fill"></i> Panduan Pengisian:
                </h4>
                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1 ml-1">
                    <li>Pilih tanggal & isi alasan serta keterangan singkat.</li>
                    <li>Tambah produk, tentukan jumlah & tipe (Penambahan/Pengurangan).</li>
                    <li>Upload bukti foto (maks 3 file), lalu klik <strong>Ajukan</strong>.</li>
                </ol>
            </div>
        </div>

        {{-- Form Content --}}
        <form action="{{ route('adjustments.store') }}" method="POST" enctype="multipart/form-data" id="adjustment-form" class="space-y-6">
            @csrf
            
            {{-- Include Form Partial --}}
            @include('adjustment::adjustments.partials._form', ['isEdit' => false])

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center gap-2 shadow-lg shadow-blue-600/20 transition-all" id="submit-btn">
                    <i class="bi bi-send-fill"></i>
                    Ajukan Penyesuaian
                </button>
                <a href="{{ route('adjustments.index') }}" class="text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-100 font-medium rounded-xl text-sm px-5 py-2.5 text-center inline-flex items-center gap-2 transition-all">
                    <i class="bi bi-x-circle"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
