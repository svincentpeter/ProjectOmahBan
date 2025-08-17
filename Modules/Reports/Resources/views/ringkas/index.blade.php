@extends('layouts.app')

@section('title', 'Report Ringkas (Harian)')
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
            <table class="table table-bordered mb-3">
                <tr><th>Transaksi</th><td>{{ number_format($sum['trx_count']) }}</td></tr>
                <tr><th>Omset</th><td>{{ format_currency($sum['omset']) }}</td></tr>
                <tr><th>Total HPP</th><td>{{ format_currency($sum['total_hpp']) }}</td></tr>
                <tr class="table-success"><th>Total Profit</th><td><strong>{{ format_currency($sum['total_profit']) }}</strong></td></tr>
            </table>
        </div>

        <h6 class="mt-4">By Metode Pembayaran</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Metode</th><th>Transaksi</th><th>Nominal</th></tr></thead>
                <tbody>
                    @forelse($byMethod as $m)
                        <tr>
                            <td>{{ $m->payment_method ?: '-' }}</td>
                            <td>{{ number_format($m->count) }}</td>
                            <td>{{ format_currency($m->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
