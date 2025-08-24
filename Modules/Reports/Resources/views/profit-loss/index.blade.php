@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="container-fluid">

    {{-- ========== FILTER PERIODE ========== --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('reports.profit_loss.generate') }}" method="POST" class="row g-3 align-items-end">
                @csrf

                <div class="col-12 col-md-3">
                    <label for="start_date" class="form-label mb-1">Tanggal Awal</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', $startDate) }}"
                           class="form-control" required>
                    @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label for="end_date" class="form-label mb-1">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date', $endDate) }}"
                           class="form-control" required>
                    @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6 d-flex gap-2">
                    <div class="flex-grow-1"></div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-graph-up"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>

                {{-- PRESET RANGE --}}
                <div class="col-12">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('reports.profit_loss.index', [
                            'start_date' => now()->toDateString(),
                            'end_date'   => now()->toDateString(),
                        ]) }}" class="btn btn-outline-secondary">Hari ini</a>

                        <a href="{{ route('reports.profit_loss.index', [
                            'start_date' => now()->subDays(6)->toDateString(),
                            'end_date'   => now()->toDateString(),
                        ]) }}" class="btn btn-outline-secondary">7 hari</a>

                        <a href="{{ route('reports.profit_loss.index', [
                            'start_date' => now()->startOfMonth()->toDateString(),
                            'end_date'   => now()->toDateString(),
                        ]) }}" class="btn btn-outline-secondary">Bulan ini</a>

                        <a href="{{ route('reports.profit_loss.index', [
                            'start_date' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                            'end_date'   => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                        ]) }}" class="btn btn-outline-secondary">Bulan lalu</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!empty($generated))
    @php
        $gpMargin = ($revenue ?? 0) > 0 ? round(($grossProfit / max($revenue,1)) * 100, 1) : 0;
        $npMargin = ($revenue ?? 0) > 0 ? round(($netProfit   / max($revenue,1)) * 100, 1) : 0;
        $gpPos    = ($grossProfit ?? 0) >= 0;
        $npPos    = ($netProfit   ?? 0) >= 0;
    @endphp

    {{-- ========== HEADER & PERIODE ========== --}}
    <div class="mb-2">
        <h4 class="mb-0">Laporan Laba Rugi</h4>
        <div class="text-muted">Periode:
            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
            – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
        </div>
    </div>

    {{-- ========== KPI CARDS (grid rapi & tinggi seragam) ========== --}}
    {{-- ===== KPI BARIS 1: Revenue, COGS, Gross ===== --}}
<div class="row g-3 mb-3 align-items-stretch">
  <div class="col-12 col-lg-4">
    <div class="kpi-card h-100 kpi-blue">
      <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Pendapatan (Revenue)</div>
        <div class="kpi-value">{{ format_currency($revenue) }}</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="kpi-card h-100 kpi-red">
      <div class="kpi-icon"><i class="bi bi-basket3"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">HPP / COGS</div>
        <div class="kpi-value">( {{ format_currency($cogs) }} )</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="kpi-card h-100 kpi-teal">
      <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Laba Kotor</div>
        <div class="kpi-value {{ ($grossProfit??0)>=0?'text-success':'text-danger' }}">
          {{ format_currency($grossProfit) }}
        </div>
        <div class="kpi-sub">Margin Kotor: <strong>{{ $gpMargin }}%</strong></div>
      </div>
    </div>
  </div>
</div>

{{-- ===== KPI BARIS 2: Opex, Net Profit (2 kolom) ===== --}}
<div class="row g-3 mb-3 align-items-stretch">
  <div class="col-12 col-lg-4">
    <div class="kpi-card h-100 kpi-amber">
      <div class="kpi-icon"><i class="bi bi-building-gear"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Beban Operasional</div>
        <div class="kpi-value">( {{ format_currency($operatingExpenses) }} )</div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <div class="kpi-card h-100 kpi-green">
      <div class="kpi-icon"><i class="bi bi-coin"></i></div>
      <div class="kpi-body w-100">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="kpi-title">Laba Bersih Sebelum Pajak</div>
          <span class="badge bg-light text-dark">Net Margin: {{ $npMargin }}%</span>
        </div>
        <div class="kpi-value {{ ($netProfit??0)>=0?'text-success':'text-danger' }}">
          {{ format_currency($netProfit) }}
        </div>

        {{-- progress bar ADA DI DALAM KARTU --}}
        <div class="progress mt-2 kpi-progress">
          <div class="progress-bar {{ ($netProfit??0)>=0?'bg-success':'bg-danger' }}"
               role="progressbar"
               style="width: {{ min(max(abs($npMargin),0),100) }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>


    {{-- ========== TABEL RINGKAS ========== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <tbody>
                    <tr>
                        <th class="summary-label"><i class="bi bi-cash-stack me-1 text-primary"></i> Pendapatan (Revenue)</th>
                        <td class="text-end fw-semibold">{{ format_currency($revenue) }}</td>
                    </tr>
                    <tr>
                        <th class="summary-label"><i class="bi bi-basket3 me-1 text-danger"></i> Harga Pokok Penjualan (HPP / COGS)</th>
                        <td class="text-end text-danger">({{ format_currency($cogs) }})</td>
                    </tr>
                    <tr class="table-light">
                        <th class="summary-label"><i class="bi bi-graph-up-arrow me-1 text-teal"></i> Laba Kotor (Gross Profit)</th>
                        <td class="text-end fw-bold {{ $gpPos ? 'text-success' : 'text-danger' }}">{{ format_currency($grossProfit) }}</td>
                    </tr>
                    <tr>
                        <th class="summary-label"><i class="bi bi-building-gear me-1 text-warning"></i> Beban Operasional</th>
                        <td class="text-end text-danger">({{ format_currency($operatingExpenses) }})</td>
                    </tr>
                    <tr class="table-light">
                        <th class="summary-label"><i class="bi bi-coin me-1 text-success"></i> Laba Bersih Sebelum Pajak</th>
                        <td class="text-end fw-bold {{ $npPos ? 'text-success' : 'text-danger' }}">{{ format_currency($netProfit) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- ========== STYLES ========== --}}
<style>
    /* grid card seragam */
    .kpi-card {
        display: flex;
        gap: .9rem;
        padding: 1rem 1.1rem;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,.05);
        background: linear-gradient(180deg, #fff, #fbfbfc);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
        min-height: 120px;
    }
    .kpi-icon{
        width: 48px; height: 48px;
        display: grid; place-items: center;
        border-radius: 12px; font-size: 1.25rem;
        background: rgba(0,0,0,.06); color: #374151;
        flex: 0 0 48px;
    }
    .kpi-body .kpi-title{ font-size:.9rem; color:#6b7280; margin-bottom:.2rem;}
    .kpi-body .kpi-value{ font-size:1.35rem; font-weight:800; letter-spacing:.2px; line-height:1.2;}
    .kpi-body .kpi-sub{ font-size:.8rem; color:#6b7280;}

    /* warna ikon lembut */
    .kpi-blue  .kpi-icon{ background: rgba(59,130,246,.14);  color:#2563eb; }
    .kpi-red   .kpi-icon{ background: rgba(239,68,68,.14);   color:#dc2626; }
    .kpi-teal  .kpi-icon{ background: rgba(20,184,166,.14);  color:#0d9488; }
    .kpi-amber .kpi-icon{ background: rgba(251,191,36,.22);  color:#b45309; }
    .kpi-green .kpi-icon{ background: rgba(34,197,94,.14);   color:#16a34a; }

    .text-teal { color:#0d9488!important; }
    .summary-label{ width:55%; }

    .table { font-size:.95rem; }
    .table tbody tr>th{ font-weight:600; }

    @media (max-width: 575.98px){
        .kpi-card{ min-height: 110px; }
        .summary-label{ width:auto; }
    }

    /* print */
    @media print {
        .card, .card-body, .kpi-card { box-shadow: none !important; }
        .btn, form, nav, .breadcrumb, .btn-group { display: none !important; }
        body, .table { font-size: 11px; }
        @page { margin: 10mm; }
    }

    .kpi-card{display:flex;gap:.9rem;padding:1rem 1.1rem;border-radius:14px;border:1px solid rgba(0,0,0,.05);
  background:linear-gradient(180deg,#fff,#fbfbfc);box-shadow:0 6px 16px rgba(0,0,0,.06);min-height:120px}
.kpi-icon{width:48px;height:48px;display:grid;place-items:center;border-radius:12px;background:rgba(0,0,0,.06);
  color:#374151;font-size:1.25rem;flex:0 0 48px}
.kpi-body .kpi-title{font-size:.9rem;color:#6b7280;margin-bottom:.2rem}
.kpi-body .kpi-value{font-size:1.35rem;font-weight:800;letter-spacing:.2px;line-height:1.15}
.kpi-body .kpi-sub{font-size:.8rem;color:#6b7280}
.kpi-progress{height:7px;border-radius:6px;overflow:hidden}
.kpi-blue  .kpi-icon{background:rgba(59,130,246,.14); color:#2563eb}
.kpi-red   .kpi-icon{background:rgba(239,68,68,.14);  color:#dc2626}
.kpi-teal  .kpi-icon{background:rgba(20,184,166,.14); color:#0d9488}
.kpi-amber .kpi-icon{background:rgba(251,191,36,.22); color:#b45309}
.kpi-green .kpi-icon{background:rgba(34,197,94,.14);  color:#16a34a}

</style>
@endsection
