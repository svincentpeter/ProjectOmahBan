<div>

    {{-- ===== Page Header Card ===== --}}
    <div class="card page-head-card shadow-sm mb-4">
        <div class="card-body py-4 px-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h4 class="mb-1 d-flex align-items-center">
                        <i class="cil-notes mr-2 text-primary"></i>
                        Jasa / Input Manual
                    </h4>
                    <div class="text-muted">
                        Pilih jasa dari master data atau input manual untuk kasus khusus (non-master).
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="chip chip-info mr-2">
                        <i class="cil-settings mr-1"></i> {{ $services->count() }} Jasa Tersedia
                    </div>

                    <a href="{{ route('service-masters.index') }}" target="_blank" class="btn btn-primary">
                        <i class="cil-plus mr-1"></i> Kelola Master Jasa
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Card: Daftar Jasa (Master Data) ===== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h6 class="mb-1 font-weight-bold d-flex align-items-center">
                        <i class="cil-settings mr-2 text-primary"></i>
                        Daftar Jasa (Master Data)
                    </h6>
                    <small class="text-muted">Klik kartu atau tombol <em>Tambah</em> untuk memasukkan ke
                        keranjang</small>
                </div>
                <div class="svc-search">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="cil-magnifying-glass"></i></span>
                        </div>
                        <input id="svcSearch" type="text" class="form-control" placeholder="Cari jasa…">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if ($services->isEmpty())
                <div class="pos-callout pos-callout--info mb-0">
                    <i class="bi bi-info-circle mr-2"></i> Belum ada jasa di master data.
                    <a href="{{ route('service-masters.index') }}" target="_blank" class="font-weight-semibold">Tambah
                        jasa di sini</a>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($services as $service)
                        @php
                            $cat = $service->category ?? 'service';
                            $catLabel =
                                ['service' => 'Service', 'goods' => 'Goods', 'custom' => 'Custom'][$cat] ??
                                ucfirst($cat);
                            $catClass =
                                [
                                    'service' => 'badge-info',
                                    'goods' => 'badge-success',
                                    'custom' => 'badge-secondary',
                                ][$cat] ?? 'badge-light';
                        @endphp

                        <div class="col-md-6" wire:key="svc-{{ $service->id }}">
                            <div class="svc-card card shadow-sm h-100" role="button" tabindex="0"
                                data-q="{{ Str::lower(trim($service->service_name . ' ' . $cat . ' ' . ($service->description ?? ''))) }}"
                                wire:click="addServiceToCart({{ $service->id }})" wire:loading.class="opacity-50"
                                wire:target="addServiceToCart">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="pr-2">
                                            <h6 class="mb-1 svc-name text-truncate">{{ $service->service_name }}</h6>
                                            <span class="badge {{ $catClass }}">{{ $catLabel }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="price-chip">{{ format_currency($service->standard_price) }}</span>
                                        </div>
                                    </div>

                                    @if ($service->description)
                                        <p class="text-muted small mb-3 svc-desc">
                                            {{ Str::limit($service->description, 120) }}
                                        </p>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-sm btn-primary"
                                            wire:click.stop="addServiceToCart({{ $service->id }})"
                                            wire:loading.attr="disabled" wire:target="addServiceToCart">
                                            <span wire:loading wire:target="addServiceToCart"
                                                class="spinner-border spinner-border-sm mr-1"></span>
                                            <i class="cil-plus mr-1" wire:loading.remove
                                                wire:target="addServiceToCart"></i>
                                            Tambah
                                        </button>
                                        <small class="text-muted">Klik kartu atau tombol untuk menambah</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <hr class="my-4">

            <div class="pos-callout pos-callout--warning">
                <small>
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan: </strong> Harga di atas adalah harga standar. Anda dapat mengubah harga di
                    keranjang.
                    Perubahan harga &gt; 30% memerlukan alasan.
                </small>
            </div>
        </div>
    </div>

    {{-- ===== Card: Input Manual (Non Master) ===== --}}
    <div class="card shadow-sm">
        

        
    </div>

</div>

@push('page_styles')
    <style>
        /* Header card */
        .page-head-card {
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
            border: 1px solid #edf2f7;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .35rem .65rem;
            font-weight: 600;
            font-size: .825rem;
            border: 1px solid transparent
        }

        .chip-info {
            background: #e7f1ff;
            border-color: #cfe0ff;
            color: #2477ff
        }

        /* Callouts */
        .pos-callout {
            border-radius: 10px;
            padding: 12px 14px;
            border-left: 4px solid
        }

        .pos-callout--info {
            border-color: #39f;
            background: #f1f7ff
        }

        .pos-callout--warning {
            border-color: #f9b115;
            background: #fff7e6
        }

        .pos-callout--danger {
            border-color: #e55353;
            background: #ffecec
        }

        /* Service cards */
        .svc-card {
            transition: .2s;
            border-radius: 12px
        }

        .svc-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08)
        }

        .svc-name {
            font-weight: 600;
            color: #2d3748
        }

        .svc-desc {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        .price-chip {
            display: inline-block;
            padding: .35rem .6rem;
            border-radius: 999px;
            background: #e9f7ef;
            border: 1px solid #cfe9da;
            font-weight: 700;
            color: #2eb85c;
            font-size: .85rem;
            white-space: nowrap
        }

        .badge-info {
            background: #e7f1ff;
            color: #2477ff;
            border: 1px solid #cfe0ff
        }

        .badge-success {
            background: #e9f7ef;
            color: #2eb85c;
            border: 1px solid #cfe9da
        }

        .badge-secondary {
            background: #f1f3f5;
            color: #495057;
            border: 1px solid #e9ecef
        }

        .badge-light {
            background: #fff;
            color: #6c757d;
            border: 1px solid #e9ecef
        }

        /* Search box width on small screens */
        .svc-search {
            min-width: 240px
        }

        @media (max-width:768px) {
            .svc-search {
                width: 100%;
                margin-top: .5rem
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        // Simple client-side filter untuk grid jasa (tanpa request baru)
        (function() {
            const input = document.getElementById('svcSearch');
            if (!input) return;
            input.addEventListener('input', function() {
                const q = (this.value || '').toLowerCase();
                document.querySelectorAll('.svc-card').forEach(function(card) {
                    const hay = (card.getAttribute('data-q') || '').toLowerCase();
                    card.parentElement.style.display = hay.includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
@endpush
