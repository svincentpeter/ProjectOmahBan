@extends('layouts.app')

@section('title', 'Laporan Kas Harian')

@section('content')
<div class="container-fluid">

    {{-- ================== FILTER TANGGAL ================== --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('reports.daily.generate') }}" method="POST" class="row g-3 align-items-end">
                @csrf

                <div class="col-12 col-md-3">
                    <label for="report_date" class="form-label mb-1">Tanggal Laporan</label>
                    <input type="date" id="report_date" name="report_date"
                           value="{{ old('report_date', $reportDate) }}"
                           class="form-control" required>
                    @error('report_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-9 d-flex gap-2">
                    <div class="flex-grow-1"></div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-text"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($generated)
    {{-- ================== HEADER & KPI ================== --}}
    @php
    $netPos   = ($netIncome ?? 0) >= 0;
    $gpMargin = ($totalOmset ?? 0) > 0 ? round(($netIncome / max($totalOmset,1)) * 100, 1) : 0;
@endphp


    <div class="mb-2">
        <h5 class="mb-0">Laporan Kas Harian</h5>
        <div class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($reportDate)->translatedFormat('d M Y') }}</div>
    </div>

    <div class="row g-3 align-items-stretch mb-3">
  <div class="col-12 col-md-4">
    <div class="kpi-card h-100 kpi-blue">
      <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Total Omset Kotor</div>
        <div class="kpi-value">{{ format_currency($totalOmset) }}</div>
        <div class="kpi-sub text-muted">Semua transaksi hari ini</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="kpi-card h-100 kpi-amber">
      <div class="kpi-icon"><i class="bi bi-building-gear"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Total Pengeluaran</div>
        <div class="kpi-value">( {{ format_currency($totalPengeluaran) }} )</div>
        <div class="kpi-sub text-muted">Biaya operasional hari ini</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="kpi-card h-100 kpi-green">
      <div class="kpi-icon"><i class="bi bi-coin"></i></div>
      <div class="kpi-body w-100">
        <div class="d-flex justify-content-between">
          <div class="kpi-title">Pendapatan Bersih</div>
          <span class="badge bg-light text-dark">Net Ratio: {{ $gpMargin }}%</span>
        </div>
        <div class="kpi-value {{ ($netIncome??0)>=0?'text-success':'text-danger' }}">
          {{ format_currency($netIncome) }}
        </div>
        {{-- progress bar DI DALAM KARTU --}}
        <div class="progress mt-2 kpi-progress">
          <div class="progress-bar {{ ($netIncome??0)>=0?'bg-success':'bg-danger' }}"
               style="width: {{ min(max(abs($gpMargin),0),100) }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>


    {{-- ================== TABEL PENJUALAN ================== --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header py-2"><strong>Daftar Penjualan</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:16%">Reference</th>
                        <th style="width:12%">Waktu</th>
                        <th>Item Terjual</th>
                        <th class="text-end" style="width:18%">Total</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="fw-semibold">{{ $sale->reference }}</td>
                        <td>{{ optional($sale->created_at)->format('H:i') ?? '-' }}</td>
                        <td>
                            @if ($sale->saleDetails && $sale->saleDetails->count())
                                <ul class="mb-0 ps-3">
                                    @foreach ($sale->saleDetails as $d)
                                        @php
                                            $name  = $d->item_name ?? optional($d->product)->name ?? ($d->product_name ?? 'Item');
                                            $qty   = (int) ($d->quantity ?? $d->qty ?? 1);
                                            $price = (int) ($d->unit_price ?? $d->price ?? 0);
                                            $sub   = (int) ($d->sub_total ?? ($qty * $price));
                                        @endphp
                                        <li>
                                            <span class="fw-semibold">{{ $name }}</span>
                                            <span class="text-muted">× {{ $qty }}</span>
                                            <span class="text-muted"> @ {{ format_currency($price) }}</span>
                                            <span class="ms-2">= <strong>{{ format_currency($sub) }}</strong></span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <em class="text-muted">Tidak ada item</em>
                            @endif
                        </td>
                        <td class="text-end">{{ format_currency($sale->total_amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada penjualan pada tanggal ini.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total Omset Kotor</th>
                        <th class="text-end fw-bold">{{ format_currency($totalOmset) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ================== TABEL PENGELUARAN ================== --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header py-2"><strong>Daftar Pengeluaran</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:16%">Reference</th>
                        <th style="width:18%">Kategori</th>
                        <th>Detail</th>
                        <th class="text-end" style="width:18%">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($expenses as $ex)
                    <tr>
                        <td class="fw-semibold">{{ $ex->reference }}</td>
                        <td>{{ optional($ex->category)->category_name ?? '-' }}</td>
                        <td>
                            {{ $ex->details ?? '-' }}
                            <div class="text-muted small">
                                Dicatat oleh: {{ optional($ex->user)->name ?? '—' }}
                                • {{ optional($ex->created_at)->format('H:i') ?? '-' }}
                            </div>
                        </td>
                        <td class="text-end">{{ format_currency($ex->amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada pengeluaran pada tanggal ini.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total Pengeluaran</th>
                        <th class="text-end fw-bold">{{ format_currency($totalPengeluaran) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ================== RINGKASAN & PENERIMAAN ================== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header py-2"><strong>Ringkasan</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0 align-middle">
                <tbody>
                    <tr>
                        <th style="width:35%">Total Omset Kotor</th>
                        <td class="text-end fw-semibold">{{ format_currency($totalOmset) }}</td>
                    </tr>
                    <tr>
                        <th>Total Pengeluaran</th>
                        <td class="text-end text-danger">({{ format_currency($totalPengeluaran) }})</td>
                    </tr>
                    <tr class="table-light">
                        <th>Pendapatan Bersih</th>
                        <td class="text-end fw-bold {{ $netPos ? 'text-success' : 'text-danger' }}">{{ format_currency($netIncome) }}</td>
                    </tr>
                    <tr>
    <th>Rincian Penerimaan per Metode/Bank</th>
    <td>
        @include('reports::components.payments-summary', ['receipts' => $receipts])
    </td>
</tr>
                </tbody>
            </table>
        </div>
    </div>

    @endif
</div>

{{-- ================== STYLES ================== --}}
<style>
    .kpi-card{
        display:flex; gap:.9rem; padding:1rem 1.1rem;
        border-radius:14px; border:1px solid rgba(0,0,0,.05);
        background: linear-gradient(180deg,#fff,#fbfbfc);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
        min-height:110px;
    }
    .kpi-icon{
        width:46px;height:46px; display:grid; place-items:center;
        border-radius:12px; background:rgba(0,0,0,.06); color:#374151; font-size:1.2rem;
        flex:0 0 46px;
    }
    .kpi-body .kpi-title{ font-size:.9rem; color:#6b7280; margin-bottom:.2rem;}
    .kpi-body .kpi-value{ font-size:1.25rem; font-weight:800; letter-spacing:.2px; line-height:1.2;}
    .kpi-body .kpi-sub{ font-size:.8rem; color:#6b7280;}

    .kpi-blue  .kpi-icon{ background: rgba(59,130,246,.14);  color:#2563eb; }
    .kpi-amber .kpi-icon{ background: rgba(251,191,36,.22);  color:#b45309; }
    .kpi-green .kpi-icon{ background: rgba(34,197,94,.14);   color:#16a34a; }

    .table { font-size:.92rem; }
    .table td,.table th{ padding-top:.45rem; padding-bottom:.45rem; }

    @media print{
        .card, .card-body, .kpi-card { box-shadow:none !important; }
        .btn, form, nav, .breadcrumb, .btn-group { display:none !important; }
        body, .table { font-size:11px; }
        @page { margin: 10mm; }
    }
</style>
@endsection
