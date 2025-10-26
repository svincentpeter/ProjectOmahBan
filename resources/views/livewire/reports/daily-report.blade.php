<div wire:key="daily-root">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 font-weight-bold">
                <i class="cil-chart-line mr-2 text-primary"></i>
                Laporan Kas Harian
            </h3>
            <p class="text-muted mb-0">Real-time daily cash flow monitoring</p>
        </div>
        <div class="btn-group" role="group">
            <button type="button" wire:click="exportCsv" class="btn btn-outline-primary">
                <i class="cil-cloud-download mr-1"></i> Export
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="cil-print mr-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 font-weight-bold">
                <i class="cil-filter mr-2 text-primary"></i>
                Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-calendar mr-1 text-muted"></i> Tanggal
                        </label>
                        <input wire:model.debounce.500ms="date" 
                               type="date" 
                               class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-user mr-1 text-muted"></i> Kasir
                        </label>
                        <select wire:model="cashierId" class="form-control">
                            <option value="">Semua Kasir</option>
                            @foreach($this->cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-credit-card mr-1 text-muted"></i> Metode Pembayaran
                        </label>
                        <select wire:model="paymentMethod" class="form-control">
                            <option value="">Semua</option>
                            @foreach($this->methodOptions as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label font-weight-semibold">
                            <i class="cil-bank mr-1 text-muted"></i> Bank (Opsional)
                        </label>
                        <input wire:model.debounce.500ms="bankName" 
                               type="text" 
                               class="form-control" 
                               placeholder="e.g., BCA, QRIS...">
                    </div>
                </div>
            </form>

            {{-- Loading Indicator --}}
            <div wire:loading.flex class="align-items-center mt-3 text-primary">
                <div class="spinner-border spinner-border-sm mr-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span>Memuat ulang data laporan...</span>
            </div>
        </div>
    </div>

    {{-- Net Income Card --}}
    <div class="card shadow-sm mb-4 overflow-hidden">
        <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
            <h6 class="text-uppercase text-muted mb-2" style="letter-spacing: 1px;">Income Bersih Hari Ini</h6>
            <div class="net-income-display mb-3">
                <span class="gradient-text">{{ format_currency($incomeBersih) }}</span>
            </div>
            <div class="income-breakdown">
                <span class="badge badge-primary px-3 py-2 mr-2">
                    <i class="cil-arrow-top mr-1"></i> Omzet: {{ format_currency($omzet) }}
                </span>
                <span class="badge badge-danger px-3 py-2">
                    <i class="cil-arrow-bottom mr-1"></i> Pengeluaran: {{ format_currency($pengeluaran) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tabbed Report Panel --}}
    <div class="card shadow-sm">
        {{-- Nav Tabs --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-modern border-0" id="reportTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" 
                       id="summary-tab" 
                       data-toggle="tab" 
                       href="#summary-pane" 
                       role="tab" 
                       aria-controls="summary-pane" 
                       aria-selected="true">
                        <i class="cil-grid mr-2"></i>Ringkasan Penerimaan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" 
                       id="transactions-tab" 
                       data-toggle="tab" 
                       href="#transactions-pane" 
                       role="tab" 
                       aria-controls="transactions-pane" 
                       aria-selected="false">
                        <i class="cil-receipt mr-2"></i>Detail Transaksi
                    </a>
                </li>
            </ul>
        </div>

        {{-- Tab Content --}}
        <div class="card-body p-0">
            <div class="tab-content" id="reportTabContent">
                {{-- Summary Tab --}}
                <div class="tab-pane fade show active" 
                     id="summary-pane" 
                     role="tabpanel" 
                     aria-labelledby="summary-tab">
                    <div class="table-responsive">
                        @php
                            $sumTotal = $ringkasanPembayaran->sum('total_amount');
                            $sumTrx   = $ringkasanPembayaran->sum('trx_count');
                        @endphp
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0">Metode Pembayaran</th>
                                    <th class="border-0">Bank</th>
                                    <th class="border-0 text-center">Jumlah Transaksi</th>
                                    <th class="border-0 text-right">Total Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ringkasanPembayaran as $row)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="payment-icon mr-2">
                                                <i class="cil-credit-card"></i>
                                            </div>
                                            <strong>{{ $row->payment_method }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">
                                            {{ $row->bank_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $row->trx_count }}</span>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ format_currency($row->total_amount) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                            <p class="mb-0 mt-3">Tidak ada data penerimaan</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($ringkasanPembayaran) > 0)
                            <tfoot style="background-color: #f8f9fa;">
                                <tr>
                                    <td colspan="2" class="font-weight-bold">TOTAL</td>
                                    <td class="text-center font-weight-bold">{{ $sumTrx }}</td>
                                    <td class="text-right font-weight-bold text-primary">
                                        {{ format_currency($sumTotal) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Transactions Tab --}}
                <div class="tab-pane fade" 
                     id="transactions-pane" 
                     role="tabpanel" 
                     aria-labelledby="transactions-tab">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0">Tanggal & Waktu</th>
                                    <th class="border-0">Nomor Referensi</th>
                                    <th class="border-0">Kasir</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="border-0 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksi as $s)
                                <tr>
                                    <td>
                                        <div class="font-weight-semibold">
                                            {{ \Illuminate\Support\Carbon::parse($s->date)->format('d/m/Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Illuminate\Support\Carbon::parse($s->date)->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $s->reference }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm mr-2">
                                                <i class="cil-user"></i>
                                            </div>
                                            {{ $s->user->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $status = $s->payment_status ?? $s->status;
                                            $isPaid = $status === 'Paid';
                                        @endphp
                                        <span class="badge {{ $isPaid ? 'badge-success' : 'badge-secondary' }}">
                                            @if($isPaid)
                                                <i class="cil-check-circle mr-1"></i> Lunas
                                            @else
                                                <i class="cil-clock mr-1"></i> {{ $status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ format_currency($s->total_amount) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                            <p class="mb-0 mt-3">Tidak ada transaksi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    /* ========== Net Income Display ========== */
    .net-income-display {
        font-size: clamp(2.5rem, 8vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
    }

    .gradient-text {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .income-breakdown .badge {
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* ========== Modern Tabs ========== */
    .nav-tabs-modern {
        padding: 0 1rem;
    }

    .nav-tabs-modern .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }

    .nav-tabs-modern .nav-link:hover {
        color: #4834DF;
        background-color: rgba(72, 52, 223, 0.05);
    }

    .nav-tabs-modern .nav-link.active {
        color: #4834DF;
        border-bottom-color: #4834DF;
        background-color: transparent;
    }

    /* ========== Payment Icon ========== */
    .payment-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        flex-shrink: 0;
    }

    /* ========== User Avatar ========== */
    .user-avatar-sm {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    /* ========== Table Styling ========== */
    .table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
    }

    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    .table tfoot td {
        font-size: 0.9375rem;
        padding: 14px 12px;
    }

    /* ========== Badge Styling ========== */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }

    /* ========== Loading Spinner ========== */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .net-income-display {
            font-size: 2rem;
        }

        .income-breakdown {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-tabs-modern .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }

        .payment-icon,
        .user-avatar-sm {
            width: 28px;
            height: 28px;
            font-size: 0.875rem;
        }
    }

    /* ========== Print Styles ========== */
    @media print {
        body {
            background: #fff !important;
        }

        .btn,
        .btn-group,
        .card-header:has(.nav-tabs),
        nav,
        .sidebar {
            display: none !important;
        }

        .card,
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .tab-content > .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }

        .net-income-display {
            font-size: 2rem;
        }

        @page {
            margin: 15mm;
        }
    }
</style>
