@extends('layouts.app-flowbite')

@section('title', 'Buat Stock Opname Baru')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('breadcrumb_items')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.index') }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">Stock Opname</a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <span class="text-sm font-bold text-zinc-900">Buat Baru</span>
        </div>
    </li>
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <div class="max-w-5xl mx-auto">
        {{-- HEADER CARD --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 mb-6">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl">
                <h4 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="bi bi-clipboard-check"></i> Buat Stock Opname Baru
                </h4>
                <p class="text-blue-100 text-sm mt-1">Penghitungan fisik stok untuk mencocokkan dengan data sistem</p>
            </div>

            <div class="p-6">
                {{-- INFO ALERT --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600">
                            <i class="bi bi-info-circle-fill text-xl"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-blue-800 mb-1">Apa itu Stock Opname?</h5>
                            <p class="text-sm text-blue-700">
                                Stock opname adalah proses penghitungan fisik seluruh atau sebagian produk di gudang/toko. 
                                Hasil hitungan akan dibandingkan dengan stok di sistem. 
                                Jika ada <strong>selisih (variance)</strong>, sistem akan otomatis membuat 
                                <strong>Adjustment</strong> yang perlu disetujui.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- INCLUDE PARTIAL FORM --}}
                @include('adjustment::stock-opname.partials._form', [
                    'isEdit' => false,
                    'stockOpname' => null,
                    'categories' => $categories
                ])
            </div>
        </div>

        {{-- TIPS CARD --}}
        <div class="bg-white border-l-4 border-emerald-500 rounded-2xl shadow-md p-6">
            <h6 class="font-bold text-emerald-700 mb-3 flex items-center gap-2">
                <i class="bi bi-lightbulb-fill"></i> Tips Stock Opname
            </h6>
            <ul class="space-y-2 text-sm text-zinc-700">
                <li class="flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5"></i>
                    <span><strong>Pilih waktu yang tepat:</strong> Lakukan saat toko tutup atau stok tidak banyak bergerak</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5"></i>
                    <span><strong>Per kategori lebih mudah:</strong> Untuk toko ban & velg, bisa opname per kategori dulu (Ban minggu ini, Velg minggu depan)</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5"></i>
                    <span><strong>Siapkan barcode scanner:</strong> Akan mempercepat proses counting (opsional)</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5"></i>
                    <span><strong>Tim 2 orang:</strong> 1 orang hitung fisik, 1 orang input ke sistem untuk akurasi lebih baik</span>
                </li>
            </ul>
        </div>
    </div>
@endsection
