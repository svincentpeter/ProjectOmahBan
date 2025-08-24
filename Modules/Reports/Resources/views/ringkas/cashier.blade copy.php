@extends('layouts.app')

@section('title', 'Report Ringkas (Per Kasir)')
@section('content')
@php
  $from     = $from     ?? request('from', now()->startOfMonth()->toDateString());
  $to       = $to       ?? request('to', now()->toDateString());
  $userId   = $userId   ?? request('user_id');
  $onlyPaid = isset($onlyPaid) ? $onlyPaid : (bool) request('only_paid', 1);
  $cashiers = $cashiers ?? collect();
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('ringkas-report.cashier') }}" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="date" name="from" value="{{ $from ?? now()->startOfMonth()->toDateString() }}" class="form-control">
            </div>
            <div class="col-auto">
                <input type="date" name="to"   value="{{ $to   ?? now()->toDateString() }}"              class="form-control">
            </div>
            <div class="col-auto">
                <select name="user_id" class="form-control">
                    <option value="">— Semua Kasir —</option>
                    @foreach(($cashiers ?? collect()) as $c)
  <option value="{{ $c->id }}" {{ (string)$c->id === (string)($userId ?? '') ? 'selected' : '' }}>
    {{ $c->name }}
  </option>
@endforeach
                </select>
            </div>
            <div class="col-auto form-check mt-2">
                <input class="form-check-input" type="checkbox" name="only_paid" id="only_paid" value="1"
                       {{ request('only_paid',1) ? 'checked' : '' }}>
                <label class="form-check-label" for="only_paid">Hanya Lunas</label>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Terapkan</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
  <thead class="table-light">
    <tr>
      <th>Kasir</th>
      <th class="text-end">Transaksi</th>
      <th class="text-end">Omset</th>
      <th class="text-end">HPP</th>
      <th class="text-end">Profit</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $r)
      <tr>
        <td>{{ optional($r->user)->name ?? '—' }}</td>
        <td class="text-end">{{ number_format($r->trx_count,0,',','.') }}</td>
        <td class="text-end">{{ format_currency($r->omset) }}</td>
        <td class="text-end">{{ format_currency($r->total_hpp) }}</td>
        <td class="text-end">{{ format_currency($r->total_profit) }}</td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-muted">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>

        </div>
    </div>
</div>
@endsection
