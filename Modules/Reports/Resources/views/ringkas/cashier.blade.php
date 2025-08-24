@extends('layouts.app')

@section('title', 'Report Ringkas (Per Kasir)')

@section('content')
@php
  // fallback aman
  $from     = $from     ?? request('from', now()->startOfMonth()->toDateString());
  $to       = $to       ?? request('to', now()->toDateString());
  $userId   = $userId   ?? request('user_id');
  $onlyPaid = isset($onlyPaid) ? $onlyPaid : (bool) request('only_paid', 1);
  /** @var \Illuminate\Support\Collection $rows */
  $rows     = $rows     ?? collect();
  $cashiers = $cashiers ?? collect();

  // agregat
  $totTrx    = (int) $rows->sum('trx_count');
  $totOmset  = (int) $rows->sum('omset');
  $totHpp    = (int) $rows->sum('total_hpp');
  $totProfit = (int) $rows->sum('total_profit');
  $npMargin  = $totOmset > 0 ? round($totProfit / max($totOmset,1) * 100, 1) : 0;
@endphp

{{-- ===== Filter ===== --}}
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('ringkas-report.cashier') }}" class="row g-2 align-items-end">
      <div class="col-12 col-md-auto">
        <label class="form-label mb-1">Tanggal Awal</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
      </div>
      <div class="col-12 col-md-auto">
        <label class="form-label mb-1">Tanggal Akhir</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
      </div>
        
         <div class="col-auto">
            <label class="form-label mb-1">Kasir</label>
                <select name="user_id" class="form-control">
                    <option value="">— Semua Kasir —</option>
                    @foreach(($cashiers ?? collect()) as $c)
  <option value="{{ $c->id }}" {{ (string)$c->id === (string)($userId ?? '') ? 'selected' : '' }}>
    {{ $c->name }}
  </option>
@endforeach
                </select>
            </div>
      <div class="col-12 col-md-auto">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="only_paid" id="only_paid" value="1"
                 {{ $onlyPaid ? 'checked' : '' }}>
          <label class="form-check-label" for="only_paid">Hanya Lunas</label>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-auto d-flex gap-2">
        <button class="btn btn-primary">
          <i class="bi bi-funnel"></i> Terapkan
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
          <i class="bi bi-printer"></i> Cetak
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ===== KPI ===== --}}
<div class="row g-3 align-items-stretch mb-3">
  <div class="col-12 col-lg-3">
    <div class="kpi-card h-100 kpi-blue">
      <div class="kpi-icon"><i class="bi bi-people"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Total Transaksi</div>
        <div class="kpi-value">{{ number_format($totTrx, 0, ',', '.') }}</div>
        <div class="kpi-sub">Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="kpi-card h-100 kpi-indigo">
      <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Total Omset</div>
        <div class="kpi-value">{{ format_currency($totOmset) }}</div>
        <div class="kpi-sub">Akumulasi semua kasir</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="kpi-card h-100 kpi-amber">
      <div class="kpi-icon"><i class="bi bi-basket3"></i></div>
      <div class="kpi-body">
        <div class="kpi-title">Total HPP</div>
        <div class="kpi-value">({{ format_currency($totHpp) }})</div>
        <div class="kpi-sub">Harga pokok terjual</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="kpi-card h-100 kpi-green">
      <div class="kpi-icon"><i class="bi bi-coin"></i></div>
      <div class="kpi-body w-100">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="kpi-title">Total Profit</div>
          <span class="badge bg-light text-dark">Net Margin: {{ $npMargin }}%</span>
        </div>
        <div class="kpi-value {{ $totProfit >= 0 ? 'text-success' : 'text-danger' }}">
          {{ format_currency($totProfit) }}
        </div>
        <div class="progress mt-2 kpi-progress">
          <div class="progress-bar {{ $totProfit >= 0 ? 'bg-success' : 'bg-danger' }}"
               style="width: {{ min(max(abs($npMargin),0),100) }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== Tabel ===== --}}
<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light sticky-top">
          <tr>
            <th style="width:35%">Kasir</th>
            <th class="text-end" style="width:10%">Transaksi</th>
            <th class="text-end" style="width:18%">Omset</th>
            <th class="text-end" style="width:18%">HPP</th>
            <th class="text-end" style="width:19%">Profit</th>
          </tr>
        </thead>
        <tbody>
        @forelse($rows as $r)
          @php
            $p = (int) ($r->total_profit ?? 0);
            $m = ($r->omset ?? 0) > 0 ? round($p / max($r->omset,1) * 100, 1) : 0;
          @endphp
          <tr>
            <td>{{ optional($r->user)->name ?? '—' }}</td>
            <td class="text-end">{{ number_format($r->trx_count,0,',','.') }}</td>
            <td class="text-end">{{ format_currency($r->omset) }}</td>
            <td class="text-end">{{ format_currency($r->total_hpp) }}</td>
            <td class="text-end">
              <span class="fw-semibold {{ $p >= 0 ? 'text-success' : 'text-danger' }}">
                {{ format_currency($p) }}
              </span>
              <span class="ms-1 badge bg-light text-dark">{{ $m }}%</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
          <tr class="table-light">
            <th class="text-end">TOTAL</th>
            <th class="text-end">{{ number_format($totTrx,0,',','.') }}</th>
            <th class="text-end">{{ format_currency($totOmset) }}</th>
            <th class="text-end">{{ format_currency($totHpp) }}</th>
            <th class="text-end fw-bold {{ $totProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ format_currency($totProfit) }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- ===== Styles kecil ===== --}}
<style>
  .kpi-card{display:flex;gap:.9rem;padding:1rem 1.1rem;border-radius:14px;border:1px solid rgba(0,0,0,.05);
    background:linear-gradient(180deg,#fff,#fbfbfc);box-shadow:0 6px 16px rgba(0,0,0,.06);min-height:120px}
  .kpi-icon{width:48px;height:48px;display:grid;place-items:center;border-radius:12px;background:rgba(0,0,0,.06);
    color:#374151;font-size:1.25rem;flex:0 0 48px}
  .kpi-body .kpi-title{font-size:.9rem;color:#6b7280;margin-bottom:.2rem}
  .kpi-body .kpi-value{font-size:1.35rem;font-weight:800;letter-spacing:.2px;line-height:1.15}
  .kpi-body .kpi-sub{font-size:.8rem;color:#6b7280}
  .kpi-progress{height:7px;border-radius:6px;overflow:hidden}

  .kpi-blue   .kpi-icon{background:rgba(59,130,246,.14);  color:#2563eb}
  .kpi-indigo .kpi-icon{background:rgba(99,102,241,.15);  color:#4f46e5}
  .kpi-amber  .kpi-icon{background:rgba(251,191,36,.22); color:#b45309}
  .kpi-green  .kpi-icon{background:rgba(34,197,94,.14);  color:#16a34a}

  .table{font-size:.92rem}
  .table td,.table th{padding-top:.5rem;padding-bottom:.5rem}
  .sticky-top{top:0}
  @media print{
    .card,.kpi-card{box-shadow:none !important}
    .btn, form, nav, .breadcrumb{display:none !important}
    body, .table{font-size:11px}
    @page{margin:10mm}
  }
</style>
@endsection
