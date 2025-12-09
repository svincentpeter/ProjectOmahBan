{{-- Modules/Sale/Resources/views/pos/flowbite-advanced.blade.php --}}
<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>POS Omah Ban - Flowbite Advanced</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CDN (untuk demo; production sebaiknya via Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Flowbite CSS --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ob: {
                            primary: '#4f46e5', // indigo-600
                            soft: '#eef2ff',    // indigo-50
                        },
                    },
                },
            },
        }
    </script>
</head>

<body class="h-full bg-slate-100 font-sans">
<div class="min-h-screen flex flex-col">

    {{-- ================= TOP BAR ================= --}}
    <header class="border-b bg-white/80 backdrop-blur sticky top-0 z-40">
        {{-- ⬇️ FULL WIDTH (OPTION 1) --}}
        <div class="w-full px-3 md:px-6 xl:px-10 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-ob-primary text-white text-lg font-bold shadow-sm">
                    OB
                </div>
                <div class="space-y-0.5">
                    <p class="text-sm font-semibold text-slate-900">
                        Omah Ban • Point of Sale
                    </p>
                    <p class="text-xs text-slate-500">
                        Shift: {{ auth()->user()->name ?? 'Kasir' }} • Lokasi: Workshop
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600">
                <div class="hidden md:flex flex-col items-end">
                    <span class="font-medium text-slate-700">Tanggal</span>
                    <span id="pos-date" class="font-mono text-[11px]"></span>
                </div>
                <div class="hidden md:flex flex-col items-end">
                    <span class="font-medium text-slate-700">No. Transaksi</span>
                    <span class="font-mono text-[11px] text-slate-800">
                        {{ $posNumber ?? 'POS-2025-0001' }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        data-modal-target="shortcut-modal"
                        data-modal-toggle="shortcut-modal"
                        class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:ring-2 focus:ring-ob-primary">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 4h18M3 12h18M3 20h18"/>
                        </svg>
                        Shortcut
                    </button>

                    <button
                        type="button"
                        class="hidden sm:inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500">
                        Simpan Draft
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- ================ MAIN AREA ================ --}}
    <main class="flex-1">
        {{-- ⬇️ FULL WIDTH (OPTION 1) --}}
        <div class="w-full px-3 md:px-6 xl:px-10 py-4 grid gap-4 lg:grid-cols-3">

            {{-- =======================================
                 KIRI (2 kolom): TAB PRODUK + LIST ITEM
            ======================================== --}}
            <section class="space-y-4 lg:col-span-2">

                {{-- HERO POS / HEADER CARD --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4 flex flex-col gap-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                                <span
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-ob-soft text-ob-primary">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 4h3l3 12h11l2-8H9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 19a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM10 19a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                    </svg>
                                </span>
                                Point of Sale
                            </h1>
                            <p class="text-sm text-slate-600">
                                Pilih <span class="font-semibold">Produk Baru</span>,
                                <span class="font-semibold">Produk Bekas</span>, atau
                                <span class="font-semibold">Jasa / Manual</span> pada tab di bawah.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs">
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Mode Online
                            </span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2.5 py-1 text-slate-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                                Auto-sync stok
                            </span>
                        </div>
                    </div>

                    {{-- Tabs Flowbite --}}
                    <ul class="flex text-sm font-medium text-center text-slate-500 border-b border-slate-200"
                        id="posTab"
                        data-tabs-toggle="#posTabContent"
                        role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 rounded-t-lg
                                           text-ob-primary border-ob-primary"
                                    id="tab-new"
                                    data-tabs-target="#panel-new"
                                    type="button" role="tab" aria-controls="panel-new"
                                    aria-selected="true">
                                <span class="text-base">🆕</span>
                                Produk Baru
                            </button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 border-transparent rounded-t-lg hover:text-slate-800 hover:border-slate-200"
                                    id="tab-second"
                                    data-tabs-target="#panel-second"
                                    type="button" role="tab" aria-controls="panel-second"
                                    aria-selected="false">
                                ♻ Produk Bekas
                            </button>
                        </li>
                        <li role="presentation">
                            <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 border-transparent rounded-t-lg hover:text-slate-800 hover:border-slate-200"
                                    id="tab-service"
                                    data-tabs-target="#panel-service"
                                    type="button" role="tab" aria-controls="panel-service"
                                    aria-selected="false">
                                🛠 Jasa / Manual
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- SEARCH BAR --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-3">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            class="w-full rounded-xl border-slate-200 pl-11 pr-4 py-3 text-sm md:text-base placeholder:text-slate-400 focus:border-ob-primary focus:ring-ob-primary"
                            placeholder="Ketik nama produk, kode, ukuran ban, atau scan barcode..."
                        >
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-slate-500">
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5">
                            F2: Fokus pencarian
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5">
                            F4: Input diskon transaksi
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5">
                            F9: Selesaikan & cetak struk
                        </span>
                    </div>
                </div>

                {{-- FILTER ROW --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4">
                    <div class="grid gap-3 md:grid-cols-3 text-sm">
                        {{-- Kategori --}}
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-slate-600">
                                Product Category
                            </label>
                            <select
                                class="bg-slate-50 border border-slate-200 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full px-3 py-2">
                                <option>All Products</option>
                                <option>Ban Penumpang</option>
                                <option>Ban Niaga</option>
                                <option>Velg</option>
                            </select>
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-slate-600">
                                Product Brand
                            </label>
                            <select
                                class="bg-slate-50 border border-slate-200 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full px-3 py-2">
                                <option>All Brands</option>
                                <option>Dunlop</option>
                                <option>Bridgestone</option>
                                <option>GT Radial</option>
                            </select>
                        </div>

                        {{-- Count per page --}}
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-slate-600">
                                Product Count
                            </label>
                            <select
                                class="bg-slate-50 border border-slate-200 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full px-3 py-2">
                                <option>9 Products</option>
                                <option>12 Products</option>
                                <option>24 Products</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- TAB CONTENT: GRID PRODUK --}}
                <div id="posTabContent">

                    {{-- PANEL: PRODUK BARU --}}
                    <div id="panel-new" role="tabpanel" aria-labelledby="tab-new">
                        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-3">
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 max-h-[470px] overflow-y-auto pr-1.5">

                                {{-- Contoh kartu produk --}}
                                @for ($i = 0; $i < 6; $i++)
                                    <article
                                        class="group flex flex-col bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md hover:border-ob-primary transition-all overflow-hidden">
                                        <div class="relative bg-slate-50 h-44 flex items-center justify-center">
                                            <img
                                                src="https://images.pexels.com/photos/1592384/pexels-photo-1592384.jpeg?auto=compress&cs=tinysrgb&w=600"
                                                alt="Ban GT Savero"
                                                class="h-36 object-contain drop-shadow-sm"/>

                                            <span
                                                class="absolute top-2 left-2 inline-flex items-center rounded-full bg-white/90 px-2 py-1 text-[11px] text-slate-700 border border-slate-200">
                                                GT Radial
                                            </span>

                                            <button
                                                type="button"
                                                data-modal-target="product-modal"
                                                data-modal-toggle="product-modal"
                                                class="absolute top-2 right-2 inline-flex items-center justify-center rounded-full bg-white/90 text-slate-600 hover:text-ob-primary w-8 h-8 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="flex-1 px-4 pt-3 pb-2">
                                            <h3 class="text-sm md:text-[15px] font-semibold text-slate-900 line-clamp-2">
                                                Ban GT Savero 205/65 R15
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M3 7h18M3 12h18M3 17h18"/>
                                                </svg>
                                                GT_SAVERO-2056515 • Rak A-01
                                            </p>

                                            <div class="mt-3 flex items-end justify-between">
                                                <div>
                                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">
                                                        Harga
                                                    </p>
                                                    <p class="text-base md:text-lg font-bold text-ob-primary">
                                                        Rp 1.425.000
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[11px] text-slate-500">
                                                        Stok
                                                    </p>
                                                    <p class="text-sm font-semibold text-emerald-600 flex items-center justify-end gap-1">
                                                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                                        51 Tersedia
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <footer
                                            class="px-4 py-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/60">
                                            <span class="text-[11px] text-slate-500">
                                                Klik <span class="font-semibold">+</span> untuk menambahkan ke keranjang
                                            </span>
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center rounded-full bg-ob-primary text-white w-8 h-8 shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-ob-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M12 5v14m7-7H5"/>
                                                </svg>
                                            </button>
                                        </footer>
                                    </article>
                                @endfor

                            </div>
                        </div>
                    </div>

                    {{-- PANEL: PRODUK BEKAS (dummy) --}}
                    <div id="panel-second" class="hidden" role="tabpanel" aria-labelledby="tab-second">
                        <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-6 text-center text-sm text-slate-500">
                            Contoh tab <strong>Produk Bekas</strong> di sini. Nanti bisa kamu isi grid kartu
                            yang sama tetapi sumber datanya dari tabel ban bekas.
                        </div>
                    </div>

                    {{-- PANEL: JASA / MANUAL (dummy) --}}
                    <div id="panel-service" class="hidden" role="tabpanel" aria-labelledby="tab-service">
                        <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-6 text-center text-sm text-slate-500">
                            Contoh tab <strong>Jasa / Manual</strong> di sini. Bisa untuk jasa spooring,
                            balancing, atau item manual lain.
                        </div>
                    </div>
                </div>
            </section>

            {{-- =======================================
                 KANAN (1 kolom): KERANJANG & CHECKOUT
            ======================================== --}}
            <aside class="space-y-4 lg:sticky lg:top-20 h-fit">

                {{-- KERANJANG RINGKAS --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-slate-900">
                                Keranjang
                            </h2>
                            <span
                                class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-xs text-slate-600">
                                3 item
                            </span>
                        </div>
                        <button
                            type="button"
                            class="text-xs text-slate-500 hover:text-red-600 inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 6h18M9 6v12m6-12v12M4 6l1 14h14l1-14"/>
                            </svg>
                            Kosongkan
                        </button>
                    </div>

                    <div class="max-h-52 overflow-y-auto">
                        <ul class="divide-y divide-slate-100 text-sm">
                            <li class="px-4 py-3 flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900 leading-snug">
                                        185/65 R15 Dunlop Enasave EC300+ (x2)
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Rp 850.000 • Subtotal: Rp 1.700.000
                                    </p>
                                    <div class="mt-1 inline-flex items-center rounded-lg border border-slate-200 bg-slate-50">
                                        <button class="px-2 py-1 text-slate-500 hover:bg-slate-100 text-xs">−</button>
                                        <span class="w-8 text-center text-xs">2</span>
                                        <button class="px-2 py-1 text-slate-500 hover:bg-slate-100 text-xs">+</button>
                                    </div>
                                </div>
                                <button class="text-xs text-red-500 hover:text-red-600">
                                    Hapus
                                </button>
                            </li>

                            <li class="px-4 py-3 flex items-start justify-between gap-2 bg-slate-50/60">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900 leading-snug">
                                        Jasa Spooring & Balancing (x1)
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Rp 250.000 • Jasa
                                    </p>
                                </div>
                                <button class="text-xs text-red-500 hover:text-red-600">
                                    Hapus
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="px-4 py-3 border-t border-slate-100">
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Catatan untuk struk / mekanik
                        </label>
                        <textarea
                            rows="2"
                            class="w-full rounded-xl border-slate-200 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-ob-primary focus:ring-ob-primary"
                            placeholder="Contoh: ban lama disimpan, balancing belakang saja, cek tekanan nitrogen, dll."></textarea>
                    </div>
                </div>

                {{-- RINGKASAN & PEMBAYARAN --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Ringkasan Pembayaran
                        </h2>
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5 text-xs text-slate-500">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                            POS • Kasir Utama
                        </span>
                    </div>

                    <dl class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Sub Total</dt>
                            <dd class="font-medium text-slate-800">Rp 1.950.000</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Diskon</dt>
                            <dd class="font-medium text-emerald-600">- Rp 50.000</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">PPN 11%</dt>
                            <dd class="font-medium text-slate-800">Rp 209.500</dd>
                        </div>
                    </dl>

                    <div class="border-t border-dashed border-slate-200 pt-3 flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Total Bayar
                        </span>
                        <span class="text-xl font-bold text-slate-900">
                            Rp 2.109.500
                        </span>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                            Metode Pembayaran
                        </p>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-ob-primary bg-ob-primary px-2.5 py-2 font-semibold text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-ob-primary">
                                Tunai
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-2.5 py-2 font-semibold text-slate-700 hover:bg-slate-50 focus:ring-2 focus:ring-ob-primary">
                                Transfer
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-emerald-500 bg-emerald-50 px-2.5 py-2 font-semibold text-emerald-700 hover:bg-emerald-100 focus:ring-2 focus:ring-emerald-500">
                                QRIS
                            </button>
                        </div>
                    </div>

                    {{-- Input bayar & kembalian --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Dibayar (Rp)
                            </label>
                            <input
                                type="number"
                                class="w-full rounded-xl border-slate-200 px-3 py-2 text-sm text-right font-semibold focus:border-ob-primary focus:ring-ob-primary"
                                value="2200000">
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Kembalian</span>
                            <span class="font-semibold text-emerald-600">
                                Rp 90.500
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        data-modal-target="checkout-modal"
                        data-modal-toggle="checkout-modal"
                        class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500">
                        Selesaikan & Cetak Struk
                    </button>
                </div>
            </aside>
        </div>
    </main>
</div>

{{-- ========== MODAL: SHORTCUT (FLOWBITE) ========== --}}
<div id="shortcut-modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-sm">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-200">
            <div class="flex items-center justify-between p-3 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">
                    Shortcut Kasir
                </h3>
                <button type="button"
                        class="text-slate-400 hover:text-slate-600"
                        data-modal-hide="shortcut-modal">
                    ✕
                </button>
            </div>
            <div class="p-4 space-y-2 text-xs text-slate-700">
                <p class="text-[11px] text-slate-500">
                    Gunakan shortcut keyboard untuk mempercepat input.
                </p>
                <ul class="space-y-1.5">
                    <li class="flex justify-between">
                        <span class="text-slate-500">F2</span>
                        <span class="font-medium">Fokus ke kolom pencarian</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-slate-500">F4</span>
                        <span class="font-medium">Input diskon transaksi</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-slate-500">F8</span>
                        <span class="font-medium">Pilih customer (kalau sudah integrasi)</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-slate-500">F9</span>
                        <span class="font-medium">Selesaikan & cetak struk</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL: DETAIL PRODUK ========== --}}
<div id="product-modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-xl">
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">
                        Detail Produk
                    </h3>
                    <p class="text-xs text-slate-500">
                        Preview contoh tampilan detail sebelum ditambahkan ke keranjang.
                    </p>
                </div>
                <button type="button"
                        class="text-slate-400 hover:text-slate-600"
                        data-modal-hide="product-modal">
                    ✕
                </button>
            </div>

            <div class="p-4 grid gap-4 md:grid-cols-2">
                <div class="bg-slate-50 rounded-xl flex items-center justify-center p-4">
                    <img
                        src="https://images.pexels.com/photos/1592384/pexels-photo-1592384.jpeg?auto=compress&cs=tinysrgb&w=800"
                        alt="Ban detail"
                        class="max-h-64 object-contain drop-shadow"/>
                </div>
                <div class="space-y-2 text-sm">
                    <h4 class="text-base font-semibold text-slate-900">
                        Ban GT Savero 205/65 R15
                    </h4>
                    <p class="text-xs text-slate-500">
                        Kode: GT_SAVERO-2056515 • Rak: A-01 • Kategori: Ban Penumpang
                    </p>

                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-xs uppercase text-slate-500">Harga</span>
                        <span class="text-lg font-bold text-ob-primary">Rp 1.425.000</span>
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            51 Tersedia
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-0.5 text-slate-600">
                            Garansi toko 1 tahun
                        </span>
                    </div>

                    <p class="mt-2 text-xs text-slate-600">
                        Ban all-terrain yang cocok untuk penggunaan harian maupun perjalanan jauh.
                        Pola telapak dirancang untuk grip baik di jalan basah maupun kering.
                    </p>

                    <div class="mt-3 space-y-1 text-xs text-slate-600">
                        <p class="font-semibold text-slate-700">Catatan teknis:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Ukuran: 205/65 R15</li>
                            <li>Load index: 94 • Speed rating: H</li>
                            <li>Tekanan standard: 32–35 psi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-600">Qty:</span>
                    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50">
                        <button class="px-2 py-1 text-xs text-slate-500 hover:bg-slate-100">−</button>
                        <input type="number" value="1"
                               class="w-12 border-x border-slate-200 bg-white text-center text-xs focus:outline-none">
                        <button class="px-2 py-1 text-xs text-slate-500 hover:bg-slate-100">+</button>
                    </div>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl bg-ob-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-ob-primary">
                    Tambahkan ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL: CHECKOUT KONFIRMASI ========== --}}
<div id="checkout-modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-md">
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">
                    Konfirmasi Pembayaran
                </h3>
                <button type="button"
                        class="text-slate-400 hover:text-slate-600"
                        data-modal-hide="checkout-modal">
                    ✕
                </button>
            </div>
            <div class="p-4 space-y-3 text-sm text-slate-700">
                <p>
                    Pastikan jumlah yang diterima dari customer sudah
                    <span class="font-semibold">Rp 2.200.000</span> dan kembalian yang diberikan
                    <span class="font-semibold text-emerald-600">Rp 90.500</span>.
                </p>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span>Sub Total</span><span>Rp 1.950.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Diskon</span><span class="text-emerald-600">- Rp 50.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>PPN 11%</span><span>Rp 209.500</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-1 flex justify-between font-semibold">
                        <span>Total Bayar</span><span>Rp 2.109.500</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500">
                    Setelah dikonfirmasi, sistem akan menyimpan transaksi dan otomatis membuka
                    halaman cetak struk POS.
                </p>
            </div>
            <div class="px-4 py-3 border-t border-slate-100 flex gap-2 justify-end">
                <button type="button"
                        class="px-4 py-2 text-sm rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50"
                        data-modal-hide="checkout-modal">
                    Batal
                </button>
                <button type="button"
                        class="px-4 py-2 text-sm rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                    Konfirmasi & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Flowbite JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('pos-date');
        if (el) {
            el.textContent = new Date().toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short'
            });
        }
    });
</script>
</body>
</html>