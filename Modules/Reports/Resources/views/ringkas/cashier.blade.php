@extends('layouts.app')

@section('title', 'Report Ringkas (Per Kasir)')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form class="row g-2 mb-3">
            <div class="col-auto">
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-auto">
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-auto">
                <select name="user_id" class="form-control">
                    <option value="">— Semua Kasir —</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ (string)$c->id === (string)$userId ? 'selected' : '' }}>
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kasir</th>
                        <th>Transaksi</th>
                        <th>Omset</th>
                        <th>Total HPP</th>
                        <th>Total Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->cashier }}</td>
                            <td>{{ number_format($r->trx_count) }}</td>
                            <td>{{ format_currency($r->omset) }}</td>
                            <td>{{ format_currency($r->total_hpp) }}</td>
                            <td class="text-success"><strong>{{ format_currency($r->total_profit) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                    <tr class="table-secondary">
                        <th>Total</th>
                        <th>{{ number_format($grand['trx_count']) }}</th>
                        <th>{{ format_currency($grand['omset']) }}</th>
                        <th>{{ format_currency($grand['total_hpp']) }}</th>
                        <th>{{ format_currency($grand['total_profit']) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
