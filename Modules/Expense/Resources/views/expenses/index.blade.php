@extends('layouts.app')

@section('title', 'Pengeluaran Harian')

{{-- Kalau mau DataTables client-side untuk tabel ini, boleh push script-inisialisasi:
@push('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const t = $('#tbl-expenses');
  if (t.length && $.fn.DataTable) t.DataTable({ pageLength: 25 });
});
</script>
@endpush
--}}

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Pengeluaran Harian</h3>
        @can('create_expenses')
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">+ Tambah</a>
        @endcan
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="Dari tanggal">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Sampai tanggal">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-outline-secondary" type="submit">Filter</button>
            <a class="btn btn-outline-dark" href="{{ route('expenses.index') }}">Reset</a>
        </div>
    </form>

    {{-- Ringkasan total --}}
    <div class="alert alert-info">Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table id="tbl-expenses" class="table table-sm table-striped">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Ref</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Metode</th>
                <th>Bank</th>
                <th class="text-end">Nominal (Rp)</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td>{{ $e->date->format('d/m/Y') }}</td>
                    <td>{{ $e->reference }}</td>
                    <td>{{ $e->category->category_name ?? '-' }}</td>
                    <td>{{ $e->details }}</td>
                    <td>{{ $e->payment_method }}</td>
                    <td>{{ $e->bank_name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($e->amount,0,',','.') }}</td>
                    <td class="text-end">
                        @include('expense::expenses.partials.actions', ['e' => $e])
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->links() }}
</div>
@endsection
